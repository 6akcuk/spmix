<?php
/**
 * @var $purchase Purchase
 * @var $good Good
 * @var $image GoodImages
 * @var $grid GoodGrid
 */

Yii::app()->getClientScript()->registerCssFile('/css/purchases.css');
Yii::app()->getClientScript()->registerScriptFile('/js/purchase.js');

$this->pageTitle = Yii::app()->name .' - Редактирование товара';

if ($configs) {
  $cfgArray = array();
  $cfgJs = array();

  /** @var $config PurchaseGoodConfig */
  foreach ($configs as $config) {
    $cfg = json_decode($config->config, true);
    $cfgArray[$config->name] = $config->conf_id;
    $cfgJs[] = $config->conf_id .": ['". $cfg['sizes'] ."', '". $cfg['colors'] ."', '". $cfg['range'] ."']";
  }
}

$sizes = array();
foreach ($good->sizes as $size) {
  $sizes[] = $size->size . (($size->adv_price > 0) ? '['. intval($size->adv_price) .']' : '');
}
$colors = array();
foreach ($good->colors as $color) {
  $colors[] = $color->color;
}

?>
<div class="breadcrumbs">
  <?php echo ActiveHtml::link($good->purchase->name, '/purchase'. $good->purchase_id) ?> &raquo;
  <?php echo ActiveHtml::link($good->name, '/good'. $good->purchase_id .'_'. $good->good_id) ?> &raquo;
  Редактирование товара
</div>
<h1>Редактировать товар</h1>

<?php
/** @var $form ActiveForm */

$form = $this->beginWidget('ext.ActiveHtml.ActiveForm', array(
    'id' => 'purchaseform',
    'action' => $this->createUrl('/good'. $purchase->purchase_id .'_'. $good->good_id .'/edit'),
)); ?>
<div class="purchase_columns clearfix">
    <div class="left purchase_column">
        <div class="row">
            <?php echo $form->inputPlaceholder($good, 'name') ?>
        </div>
        <div class="row">
            <?php echo $form->inputPlaceholder($good, 'artikul') ?>
        </div>
        <div class="row">
            <?php echo $form->inputPlaceholder($good, 'price') ?>
        </div>
        <div class="row">
            <?php echo $form->inputPlaceholder($good, 'url') ?>
        </div>
    </div>
    <div class="left purchase_column">
      <?php if ($configs): ?>
      <?php  ?>
      <div class="row">
        <?php echo ActiveHtml::dropdown('config_id', 'Конфигурация', '', $cfgArray) ?>
      </div>
      <script>
        var _configs = {<?php echo implode(', ', $cfgJs) ?>};
      </script>
      <?php endif; ?>
      <div id="config_container" class="row" style="display: none">
        <?php echo ActiveHtml::inputPlaceholder('config', '', array('placeholder' => 'Имя конфигурации')) ?>
      </div>
      <div class="row">
        <?php echo ActiveHtml::inputPlaceholder('sizes', implode(';', $sizes), array('placeholder' => 'Размеры')) ?>
      </div>
      <div class="row">
        <?php echo ActiveHtml::inputPlaceholder('colors', implode(';', $colors), array('placeholder' => 'Цвета')) ?>
      </div>
      <div>
        <?php echo $form->checkBox($good, 'is_range') ?>
        <?php echo $form->label($good, 'is_range') ?>
      </div>
      <div rel="range" class="row" style="display: <?php echo ($good->is_range) ? 'block' : 'none' ?>">
        <p>Коробка закупается по <a class="button" onclick="fillBySize()">размеру</a> <a class="button" onclick="fillByColor()">цвету</a></p>
        <?php echo $form->smartTextarea($good, 'range') ?>
      </div>
    </div>
</div>
<div class="row">
    <?php echo $form->smartTextarea($good, 'description', array('style' => 'width: 520px')) ?>
</div>
<div class="row">
    <?php echo ActiveHtml::submitButton('Сохранить изменения', array('class' => 'btn light_blue', 'onclick' => 'return Purchase.editgood()')); ?>
</div>
<?php $this->endWidget(); ?>

<h1>Галерея изображений товара</h1>
<?php echo ActiveHtml::upload('photo', '', 'Выберите изображение', array('onchange' => 'Purchase.uploadGoodImage('. $good->purchase_id .', '. $good->good_id .', {id})')) ?>
<div id="images_list" class="images clearfix">
<?php foreach ($good->images as $image): ?>
    <div class="left good_image">
        <?php echo ActiveHtml::showUploadImage($image->image, 'b') ?>
        <a class="tt iconify_x_a"
           title="Удалить изображение"
           onclick="Purchase.removeImage.call(this, <?php echo $good->purchase_id ?>, <?php echo $good->good_id ?>, <?php echo $image->image_id ?>);"></a>
    </div>
<?php endforeach; ?>
</div>
<script type="text/javascript">
  function fillBySize() {
    var sizes = $.trim($('#sizes').val()), sz = [], html = [];
    if (!sizes) {
      ajex.show('Заполните поле размеров, чтобы продолжить');
      return;
    }

    sz = sizes.split(';');
    html.push('[cols]');
    $.each(sz, function(i, s) {
      html.push('[col][size]'+ s +'[/size][/col]');
    });
    html.push('[/cols]');

    $('#Good_range').val(html.join('')).focus();
  }
  function fillByColor() {
    var colors = $.trim($('#colors').val()), cs = [], html = [];
    if (!colors) {
      ajex.show('Заполните поле цветов, чтобы продолжить');
      return;
    }

    cs = colors.split(';');
    html.push('[cols]');
    $.each(cs, function(i, c) {
      html.push('[col][color]'+ c +'[/color][/col]');
    });
    html.push('[/cols]');

    $('#Good_range').val(html.join('')).focus();
  }

  $().ready(function() {
    $('#config_id').change(function() {
      var id = parseInt($(this).val());

      if (id && _configs[id]) {
        $('#sizes').val(_configs[id][0]).next().hide();
        $('#colors').val(_configs[id][1]).next().hide();
        if (_configs[id][2]) {
          $('#Good_is_range').attr('checked', true);
          $('[rel="range"]').show();
          $('#Good_range').val(_configs[id][2]).next().hide();
        }
        else {
          $('#Good_is_range').attr('checked', false);
          $('[rel="range"]').hide();
          $('#Good_range').val('').next().show();
        }

        $('#config').val('');
        $('#config_container').hide();
      }
    });

    $('#Good_is_range').click(function() {
      ($(this).attr('checked')) ? $('[rel="range"]').show() : $('[rel="range"]').hide();
    });
    $('#sizes').popupHelp('Все доступные размеры перечисляются через точку с запятой. К примеру, 36;37;39;43');
    $('#colors').popupHelp('Все доступные цвета перечисляются через точку с запятой. К примеру, черный;темно-фиолетовый;как на фото');
    $('#config').popupHelp('Вы можете сохранить данную комбинацию размеров, цветов и рядов, чтобы легче было добавлять на будущие товары.<br><br>' +
      'Старайтесь не создавать одинаковые конфигурации.');
    $('#Good_range').popupHelp('Каждый ряд состоит из колонок. Все колонки обрамляются [cols][/cols]. Колонка начинается с символа [col] и' +
      ' заканчивается [/col]. Обозначение размера [size], обозначение цвета [color]');

    $('#sizes, #colors, #Good_range').blur(function() {
      if ($.trim($(this).val())) {
        if ($('#config_id').val()) {
          var id = parseInt($('#config_id').val());
          if (_configs[id] && (_configs[id][0] != $('#sizes').val() || _configs[id][1] != $('#colors').val() || _configs[id][2] != $('#Good_range').val()))
            $('#config_container').show();
        }
        else $('#config_container').show();
        //$('#config').focus();
      }
    });
  });
</script>