<?php
/** @var $model Good */

Yii::app()->getClientScript()->registerCssFile('/css/purchases.css');
Yii::app()->getClientScript()->registerScriptFile('/js/purchase.js');

$this->pageTitle = Yii::app()->name .' - Добавление нового товара';

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

?>
<div class="breadcrumbs">
    <?php echo ActiveHtml::link($purchase->name, '/purchase'. $purchase->purchase_id) ?> &raquo;
    Добавить новый товар
</div>

<div class="create">
<h1>Добавить новый товар</h1>

<?php
/** @var $form ActiveForm */

$form = $this->beginWidget('ext.ActiveHtml.ActiveForm', array(
    'id' => 'addgoodform',
    'action' => $this->createUrl('/purchase'. $id .'/addgood'),
)); ?>
<input type="hidden" id="direction" name="direction" value="0" />
<div class="purchase_columns clearfix">
  <div class="left purchase_column">
    <div class="row">
      <?php echo $form->inputPlaceholder($model, 'name') ?>
    </div>
    <div class="row">
      <?php echo $form->inputPlaceholder($model, 'artikul') ?>
    </div>
    <div class="row">
      <?php echo $form->inputPlaceholder($model, 'price') ?>
    </div>
    <?php if (Yii::app()->user->getId() == 1): ?>
    <div class="row">
      <?php echo $form->inputPlaceholder($model, 'delivery') ?>
    </div>
    <?php endif; ?>
    <div class="row">
      <?php echo $form->inputPlaceholder($model, 'url') ?>
    </div>
    <div class="row">
      <?php echo ActiveHtml::upload('image', '', 'Прикрепить фотографию', array('data-image' => 'a')) ?>
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
        <?php echo ActiveHtml::inputPlaceholder('sizes', '', array('placeholder' => 'Размеры')) ?>
      </div>
      <div class="row">
        <?php echo ActiveHtml::inputPlaceholder('colors', '', array('placeholder' => 'Цвета')) ?>
      </div>
      <div>
          <?php echo $form->checkBox($model, 'is_range') ?>
          <?php echo $form->label($model, 'is_range') ?>
      </div>
      <div rel="range" class="row" style="display: none">
        <p>Коробка закупается по <a class="button" onclick="fillBySize()">размеру</a> <a class="button" onclick="fillByColor()">цвету</a></p>
        <?php echo $form->smartTextarea($model, 'range') ?>
      </div>
    </div>
</div>
<div class="row">
    <?php echo $form->smartTextarea($model, 'description', array('style' => 'width: 520px')) ?>
</div>
<div class="row">
  <?php echo ActiveHtml::submitButton('Добавить товар и перейти к закупке', array('class' => 'btn light_blue', 'onclick' => 'return Purchase.addgood(0)')); ?>
  <?php echo ActiveHtml::submitButton('Добавить товар и приступить к новому', array('class' => 'btn light_blue', 'onclick' => 'return Purchase.addgood(1)')); ?>
</div>
<?php $this->endWidget(); ?>
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
  $('#sizes').popupHelp('Все доступные размеры перечисляются через точку с запятой. Чтобы указать цену к конкретному размеру,' +
      'напишите ее в квадратных скобках. К примеру, 36;37;39;43 или 36[415];37[430];39-41[505]');
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