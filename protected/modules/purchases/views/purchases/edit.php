<?php
/** @var $model Purchase */

Yii::app()->getClientScript()->registerCssFile('/css/purchases.css');
Yii::app()->getClientScript()->registerScriptFile('/js/purchase.js');

$this->pageTitle = Yii::app()->name .' - Редактирование закупки';
?>

<h1>Редактирование закупки</h1>

<?php
/** @var $form ActiveForm */
$ava = json_decode($model->image);

$form = $this->beginWidget('ext.ActiveHtml.ActiveForm', array(
    'id' => 'purchaseform',
    'action' => $this->createUrl('/purchases/edit/'. $model->purchase_id),
)); ?>
<div class="purchase_columns clearfix">
    <div class="left purchase_column">
        <div class="row">
            <?php echo $form->label($model, 'name') ?>
            <?php echo $form->inputPlaceholder($model, 'name') ?>
        </div>
        <div class="row">
            <?php echo $form->label($model, 'category_id') ?>
            <?php echo $form->dropdown($model, 'category_id', PurchaseCategory::getDataArray()) ?>
        </div>
        <div class="row">
            <?php echo $form->label($model, 'image') ?>
            <?php echo $form->upload($model, 'image', 'Прикрепить аватар', array('data-image' => 'a')) ?>
        </div>
        <div class="row">
            <?php echo $form->label($model, 'stop_date') ?>
            <?php echo $form->inputCalendar($model, 'stop_date') ?>
        </div>
        <div class="row">
            <?php echo $form->label($model, 'status') ?>
            <?php echo $form->dropdown($model, 'status', Purchase::getStatusDataArray()) ?>
        </div>
    </div>
    <div class="left purchase_column">
        <div class="row">
            <?php echo $form->label($model, 'state') ?>
            <?php echo $form->dropdown($model, 'state', Purchase::getStateDataArray()) ?>
        </div>
        <div class="row">
            <?php echo $form->label($model, 'min_sum') ?>
            <?php echo $form->inputPlaceholder($model, 'min_sum') ?>
        </div>
        <div class="row">
            <?php echo $form->label($model, 'min_num') ?>
            <?php echo $form->inputPlaceholder($model, 'min_num') ?>
        </div>
        <div class="row">
            <?php echo $form->label($model, 'org_tax') ?>
            <?php echo $form->inputPlaceholder($model, 'org_tax') ?>
        </div>
        <div class="row">
            <?php echo $form->label($model, 'supplier_url') ?>
            <?php echo $form->inputPlaceholder($model, 'supplier_url') ?>
        </div>
        <div class="row">
            <?php echo $form->label($model, 'price_url') ?>
            <?php echo $form->inputPlaceholder($model, 'price_url') ?>
        </div>
    </div>
</div>
<div class="row">
    <?php echo ActiveHtml::submitButton('Сохранить изменения', array('class' => 'btn light_blue', 'onclick' => 'return Purchase.edit()')); ?>
</div>
<?php $this->endWidget(); ?>