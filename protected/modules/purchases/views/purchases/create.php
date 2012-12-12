<?php
/** @var $userinfo User */

Yii::app()->getClientScript()->registerCssFile('/css/purchases.css');
Yii::app()->getClientScript()->registerScriptFile('/js/purchase.js');

$this->pageTitle = Yii::app()->name .' - Создание новой закупки';
?>

<h1>Создание новой закупки</h1>

<?php
/** @var $form ActiveForm */
$form = $this->beginWidget('ext.ActiveHtml.ActiveForm', array(
    'id' => 'purchaseform',
    'action' => $this->createUrl('/purchases/create'),
)); ?>
<div class="row">
    <?php echo $form->inputPlaceholder($model, 'name') ?>
</div>
<div class="row">
    <?php echo $form->dropdown($model, 'category_id', PurchaseCategory::getDataArray()) ?>
</div>
<div class="row">
    <?php echo $form->upload($model, 'image', 'Прикрепить аватар', array('data-image' => 'a')) ?>
</div>
<div class="row">
    <?php echo $form->inputCalendar($model, 'stop_date') ?>
</div>
<div class="row">
    <?php echo $form->dropdown($model, 'status', Purchase::getStatusDataArray()) ?>
</div>
<div class="row">
    <?php echo ActiveHtml::submitButton('Создать закупку', array('class' => 'btn light_blue', 'onclick' => 'return Purchase.create()')); ?>
</div>
<?php $this->endWidget(); ?>