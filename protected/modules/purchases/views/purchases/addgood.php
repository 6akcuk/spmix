<?php
/** @var $model Good */

Yii::app()->getClientScript()->registerCssFile('/css/purchases.css');
Yii::app()->getClientScript()->registerScriptFile('/js/purchase.js');
Yii::app()->getClientScript()->registerScriptFile('/js/addgood.js');

$this->pageTitle = Yii::app()->name .' - Добавление нового товара';
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
        <div class="row">
            <?php echo $form->inputPlaceholder($model, 'url') ?>
        </div>
      <div class="row">
        <?php echo ActiveHtml::inputPlaceholder('sizes', '', array('placeholder' => 'Размеры')) ?>
      </div>
      <div class="row">
        <?php echo ActiveHtml::inputPlaceholder('colors', '', array('placeholder' => 'Цвета')) ?>
      </div>
        <div class="row">
            <?php echo ActiveHtml::upload('image', '', 'Прикрепить фотографию', array('data-image' => 'a')) ?>
        </div>
    </div>
    <div class="left purchase_column">
      <div>
          <?php echo $form->checkBox($model, 'is_range') ?>
          <?php echo $form->label($model, 'is_range') ?>
      </div>
      <div rel="range" class="row" style="display: none">
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
$().ready(function() {
    $('#Good_is_range').click(function() {
        ($(this).attr('checked')) ? $('[rel="range"]').show() : $('[rel="range"]').hide();
    });
});
</script>