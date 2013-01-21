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
<input type="hidden" name="mod_request" value="0" />
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
            <?php echo $form->dropdown($model, 'state', ($model->mod_confirmation) ? Purchase::getStateDataArray() : Purchase::getNonConfirmedStateArray(), array('onchange' => 'Purchase.stateChanged(this)')) ?>
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
    <?php if ($model->mod_request_id > 0): ?>
        <?php if ($model->mod_confirmation == 0): ?>
            <?php if ($model->mod_request->moderator_id == 0): ?>
        Ожидается рассмотрение заявки №<?php echo $model->mod_request_id ?>,
        отправленной <?php echo ActiveHtml::date($model->mod_request->request_date, true, true) ?>
            <?php else: ?>
                <?php if (!$model->mod_request->message): ?>
        Модератор <?php echo ActiveHtml::link($model->mod_request->moderator->getDisplayName(), '/id'. $model->mod_request->moderator_id) ?>
        просматривает Вашу заявку
                <?php else: ?>
        Модератор <?php echo ActiveHtml::link($model->mod_request->moderator->getDisplayName(), '/id'. $model->mod_request->moderator_id) ?>
        <?php echo ActiveHtml::date($model->mod_request->request_date, true, true) ?>
        <?php echo Yii::t('purchase', '0#оставил|1#оставила', $model->mod_request->moderator->profile->genderToInt()) ?>
        замечание по Вашей закупке:
        <div>
            <?php echo nl2br($model->mod_request->message) ?>
        </div>
                <?php endif; ?>
            <?php endif; ?>
        <?php else: ?>
        Модератор <?php echo ActiveHtml::link($model->mod_request->moderator->getDisplayName(), '/id'. $model->mod_request->moderator_id) ?>
        <?php echo Yii::t('purchase', '0#одобрил|1#одобрила', $model->mod_request->moderator->profile->genderToInt()) ?>
        Вашу закупку
        <?php endif; ?>
    <?php else: ?>

    <?php endif; ?>
</div>
<div class="row">
    <?php echo ActiveHtml::submitButton('Сохранить изменения', array('class' => 'btn light_blue', 'onclick' => 'return Purchase.edit()')); ?>
    <?php if($model->mod_confirmation == 0): ?>
    <?php echo ActiveHtml::submitButton('Сохранить и отправить на согласование', array(
      'id' => 'sc_button',
      'class' => 'btn light_blue',
      'onclick' => 'return Purchase.sendToModerator()',
      'style' => 'display: '. (($model->state == Purchase::STATE_ORDER_COLLECTION) ? 'block' : 'none'),
    )); ?>
    <?php endif; ?>
</div>
<?php $this->endWidget(); ?>
</div>