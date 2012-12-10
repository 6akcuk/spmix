<?php
/** @var $model Purchase */

Yii::app()->getClientScript()->registerCssFile('/css/purchases.css');
Yii::app()->getClientScript()->registerScriptFile('/js/purchase.js');

$this->pageTitle = Yii::app()->name .' - Добавление нового товара';
?>

<h1>Добавить новый товар</h1>

<?php
/** @var $form ActiveForm */

$form = $this->beginWidget('ext.ActiveHtml.ActiveForm', array(
    'id' => 'addgoodform',
    'action' => $this->createUrl('/purchase'. $id .'/addgood'),
)); ?>
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
    <?php echo $form->inputPlaceholder($model, 'description') ?>
</div>
<div class="row">
    <?php echo $form->inputPlaceholder($model, 'sizes') ?>
</div>
<div class="row">
    <?php echo $form->inputPlaceholder($model, 'colors') ?>
</div>
<div class="row">
    <?php echo ActiveHtml::submitButton('Добавить товар', array('class' => 'btn light_blue', 'onclick' => 'return Purchase.addgood()')); ?>
</div>
<?php $this->endWidget(); ?>