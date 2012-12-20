<?php
/**
 * @var $order Order
 * @var $paydetail ProfilePaydetail
 */

Yii::app()->getClientScript()->registerCssFile('/css/purchases.css');
Yii::app()->getClientScript()->registerScriptFile('/js/purchase.js');

$this->pageTitle = Yii::app()->name .' - Оплата заказа';

$details = array();
$popup = array();
foreach ($paydetails as $paydetail) {
    $details[$paydetail->paysystem_name] = $paydetail->pay_id;
    $popup[$paydetail->pay_id] = $paydetail->paysystem_details;
}
?>

<h1>Мои покупки - Оплата заказа</h1>

<div id="tabs">
    <?php echo ActiveHtml::link('Текущие заказы', '/orders') ?>
    <?php echo ActiveHtml::link('Ожидают оплаты'. (($awaitingNum > 0) ? ' ('. $awaitingNum .')' : ''), '/orders/awaiting',  array('class' => 'selected')) ?>
    <?php echo ActiveHtml::link('Платежи', '/orders/payments') ?>
</div>

<?php
/** @var $form ActiveForm */
$form = $this->beginWidget('ext.ActiveHtml.ActiveForm', array(
    'id' => 'paymentform',
    'action' => $this->createUrl('/orders/createPayment?id='. $order->order_id),
)); ?>
<div class="row">
    Заказ: <?php echo ActiveHtml::link($order->good->name, '/good'. $order->purchase_id .'_'. $order->good_id) ?>
</div>
<div class="row">
    <?php echo ActiveHtml::dropdown('OrderPayment[pay_id]', 'Реквизиты', '', $details, array('onchange' => 'showDetails($(this))')) ?>
</div>
<div class="row">
<?php foreach ($popup as $id => $pop): ?>
    <div id="det<?php echo $id ?>" class="details" style="display:none">
        <?php echo nl2br($pop) ?>
    </div>
<?php endforeach; ?>
</div>
<div class="row">
    <?php echo ActiveHtml::smartTextarea('OrderPayment[description]', '', array('placeholder' => 'Информация о платеже')) ?>
</div>
<div class="row">
    <?php echo ActiveHtml::submitButton('Оплатить', array('class' => 'btn light_blue', 'onclick' => 'return FormMgr.submit(\'#paymentform\')')) ?>
</div>
<?php $this->endWidget(); ?>

<script type="text/javascript">
function showDetails(o) {
    $('div.details').hide();
    $('#det'+ o.attr('data-value')).show();
}
</script>