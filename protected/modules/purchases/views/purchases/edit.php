<?php
/** @var $model Purchase */
Yii::app()->getClientScript()->registerCssFile('/css/purchases.css');
Yii::app()->getClientScript()->registerScriptFile('/js/purchase.js');

$this->pageTitle = Yii::app()->name .' - Редактирование закупки';
?>
<div class="breadcrumbs">
    <?php echo ActiveHtml::link($model->name, '/purchase'. $model->purchase_id) ?> &raquo;
    Редактирование закупки
</div>
<div class="create">
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
            <?php echo $form->inputPlaceholder($model, 'name', array('disabled' => ($model->getScenario() == 'edit_own_confirmed') ? true : false)) ?>
        </div>
        <div class="row">
            <?php echo $form->label($model, 'category_id') ?>
            <?php echo $form->dropdown($model, 'category_id', PurchaseCategory::getDataArray(), array('disabled' => ($model->getScenario() == 'edit_own_confirmed') ? true : false)) ?>
        </div>
        <div class="row clearfix">
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
        <div class="row clearfix">
            <?php echo $form->label($model, 'min_sum') ?>
            <?php echo $form->inputPlaceholder($model, 'min_sum') ?>
        </div>
        <div class="row">
            <?php echo $form->label($model, 'min_num') ?>
            <?php echo $form->inputPlaceholder($model, 'min_num') ?>
        </div>
        <div class="row">
            <?php echo $form->label($model, 'org_tax') ?>
            <?php echo $form->inputPlaceholder($model, 'org_tax', array('disabled' => ($model->getScenario() == 'edit_own_confirmed') ? true : false)) ?>
        </div>
        <div class="row">
            <?php echo $form->label($model, 'supplier_url') ?>
            <?php echo $form->textfield($model, 'supplier_url', array('disabled' => ($model->getScenario() == 'edit_own_confirmed') ? true : false)) ?>
        </div>
        <div class="row">
            <?php echo $form->label($model, 'hide_supplier') ?>
            <?php echo $form->checkBox($model, 'hide_supplier') ?>
        </div>
        <div class="row">
            <?php echo $form->label($model, 'price_url') ?>
            <?php echo $form->textfield($model, 'price_url') ?>
        </div>
        <div class="row">
            <?php echo $form->label($model, 'accept_add') ?>
            <?php echo $form->checkBox($model, 'accept_add') ?>
        </div>
    </div>
</div>
<div class="row">
    <h1>Согласование модератором</h1>
    <div class="purchase_columns clearfix">
        <div class="left purchase_column">
            <div class="row">
                <?php echo $form->label($model, 'mod_confirmation') ?>
                <?php
                    if (Yii::app()->user->checkAccess('purchases.purchases.acquireSuper') ||
                        Yii::app()->user->checkAccess('purchases.purchases.acquireMod', array('purchase' => $model))):
                ?>
                <?php echo $form->checkBox($model, 'mod_confirmation') ?>
                <?php else: ?>
                <?php echo ($model->mod_confirmation) ? 'Да' : 'Нет' ?>
                <?php endif; ?>
            </div>
        </div>
        <div class="left purchase_column">
            <div class="row">
                <?php
                    if (Yii::app()->user->checkAccess('purchases.purchases.acquireSuper') ||
                        Yii::app()->user->checkAccess('purchases.purchases.acquireMod', array('purchase' => $model))):
                ?>
                <?php echo $form->smartTextarea($model, 'mod_reason') ?>
                <?php else: ?>
                <?php echo nl2br($model->mod_reason) ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <?php echo ActiveHtml::submitButton('Сохранить изменения', array('class' => 'btn light_blue', 'onclick' => 'return Purchase.edit()')); ?>
</div>
<?php $this->endWidget(); ?>
    </div>