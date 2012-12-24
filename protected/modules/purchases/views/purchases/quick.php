<?php
/** @var $purchase Purchase */

Yii::app()->getClientScript()->registerCssFile('/css/purchases.css');
Yii::app()->getClientScript()->registerScriptFile('/js/purchase.js');

$this->pageTitle = Yii::app()->name .' - Быстрый заказ';

$dd_oic = array();
if (is_array($purchase->oic)) {
    foreach ($purchase->oic as $oic) {
        $dd_oic[$oic->description .' '. ActiveHtml::price($oic->price)] = $oic->pk;
    }
}

?>

<h1>Быстрый заказ</h1>

<?php
/** @var $form ActiveForm */

$form = $this->beginWidget('ext.ActiveHtml.ActiveForm', array(
    'id' => 'quickform',
    'action' => $this->createUrl('/purchase'. $purchase->purchase_id .'/quick'),
)); ?>
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
    <?php echo $form->inputPlaceholder($good, 'sizes') ?>
</div>
<div class="row">
    <?php echo $form->inputPlaceholder($good, 'colors') ?>
</div>
<div class="row">
    <?php echo $form->inputPlaceholder($order, 'amount') ?>
</div>
<div class="row">
    <?php echo $form->smartTextarea($order, 'client_comment') ?>
</div>
<div class="row">
    Вы можете выбрать Центр Выдачи Заказов, если хотите самостоятельно забрать свой заказ <br/>
</div>
    <div class="row clearfix">
    <?php echo $form->dropdown($order, 'oic', $dd_oic) ?>
    </div>
<div class="row">
    <?php echo ActiveHtml::submitButton('Заказать товар', array('class' => 'btn light_blue', 'onclick' => 'return FormMgr.submit(\'#quickform\')')); ?>
</div>
<?php $this->endWidget(); ?>