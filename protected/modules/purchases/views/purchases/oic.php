<?php
/**
 * @var $purchase Purchase
 * @var $oic PurchaseOic
 */

Yii::app()->getClientScript()->registerCssFile('/css/purchases.css');
Yii::app()->getClientScript()->registerScriptFile('/js/purchase.js');

$this->pageTitle = Yii::app()->name .' - Центры выдачи заказов';
?>

<h1>Центры выдачи заказов</h1>

<?php
/** @var $form ActiveForm */
$ava = json_decode($purchase->image);

$form = $this->beginWidget('ext.ActiveHtml.ActiveForm', array(
    'id' => 'oicform',
    'action' => $this->createUrl('/purchase'. $purchase->purchase_id .'/oic'),
)); ?>
<?php if($purchase->oic): ?>
<?php foreach($purchase->oic as $oic): ?>
<div class="row">
    <?php echo ActiveHtml::inputPlaceholder('PurchaseOic[price][]', $oic->price, array('id' => '', 'placeholder' => 'Цена')) ?>
    <?php echo ActiveHtml::inputPlaceholder('PurchaseOic[description][]', $oic->description, array('id' => '', 'placeholder' => 'Описание')) ?>
    <a class="iconify_plus_a" onclick="sfar.add(this)"></a>
    <a class="iconify_x_a" onclick="sfar.del(this)"></a>
</div>
<?php endforeach; ?>
<?php endif; ?>
<div class="row">
    <?php echo ActiveHtml::inputPlaceholder('PurchaseOic[price][]', '', array('id' => '', 'placeholder' => 'Цена')) ?>
    <?php echo ActiveHtml::inputPlaceholder('PurchaseOic[description][]', '', array('id' => '', 'placeholder' => 'Описание')) ?>
    <a class="iconify_plus_a" onclick="sfar.add(this)"></a>
    <a class="iconify_x_a" onclick="sfar.del(this)" style="display:none"></a>
</div>
<div class="row">
    <?php echo ActiveHtml::submitButton('Сохранить изменения', array('class' => 'btn light_blue', 'onclick' => 'return Purchase.saveOic()')); ?>
</div>
<?php $this->endWidget(); ?>