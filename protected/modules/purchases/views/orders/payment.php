<?php
/**
 * @var $payment OrderPayment
 */

Yii::app()->getClientScript()->registerCssFile('/css/purchases.css');
Yii::app()->getClientScript()->registerScriptFile('/js/purchase.js');

$this->pageTitle = Yii::app()->name .' - Платеж №'. $payment->payment_id;
?>

<h1>Платеж №<?php echo $payment->payment_id ?></h1>

<?php
/** @var $form ActiveForm */
$form = $this->beginWidget('ext.ActiveHtml.ActiveForm', array(
    'id' => 'paymentform',
    'action' => $this->createUrl('/payment'. $payment->payment_id),
)); ?>
<div class="row">
    Заказ: <?php echo ActiveHtml::link($payment->order->good->name, '/good'. $payment->order->purchase_id .'_'. $payment->order->good_id) ?>
</div>
<div class="row">
    Дата платежа: <?php echo ActiveHtml::date($payment->datetime) ?>
</div>
<div class="row">
    Оплата произведена на: <?php echo $payment->paydetails->paysystem_name ?> <br/>
    <?php echo nl2br($payment->paydetails->paysystem_details) ?>
</div>
<div class="row">
    Данные по платежу: <br/>
    <?php echo nl2br($payment->description) ?>
</div>
<div class="row">
    <?php echo $form->dropdown($payment, 'status', OrderPayment::getStatusArray()) ?>
</div>
<div class="row">
    <?php echo ActiveHtml::submitButton('Сохранить изменения', array('class' => 'btn light_blue', 'onclick' => 'return FormMgr.submit(\'#paymentform\')')) ?>
</div>
<?php $this->endWidget(); ?>

<script type="text/javascript">
    function showDetails(o) {
        $('div.details').hide();
        $('#det'+ o.attr('data-value')).show();
    }
</script>