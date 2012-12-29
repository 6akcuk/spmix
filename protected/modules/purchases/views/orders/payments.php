<?php
/**
 * @var $payment OrderPayment
 */

Yii::app()->getClientScript()->registerCssFile('/css/purchases.css');
Yii::app()->getClientScript()->registerScriptFile('/js/purchase.js');

$this->pageTitle = Yii::app()->name .' - Платежи';
?>

<h1>Платежи</h1>

<div id="tabs">
    <?php echo ActiveHtml::link('Текущие заказы', '/orders') ?>
    <?php echo ActiveHtml::link('Ожидают оплаты'. (($awaitingNum > 0) ? ' ('. $awaitingNum .')' : ''), '/orders/awaiting') ?>
    <?php echo ActiveHtml::link('Платежи', '/orders/payments', array('class' => 'selected')) ?>
</div>
<table class="data">
    <thead>
    <tr>
        <th>№</th>
        <th>Дата платежа</th>
        <th>Заказ</th>
        <th>Статус</th>
        <th>Реквизиты</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($payments as $payment): ?>
    <tr>
        <td><?php echo $payment->payment_id ?></td>
        <td><?php echo ActiveHtml::date($payment->datetime) ?></td>
        <td><?php echo ActiveHtml::link($payment->order->good->name, '/order'. $payment->order_id) ?></td>
        <td><?php echo Yii::t('purchase', $payment->status) ?></td>
        <td>
            <?php if ($payment->paydetails): ?>
            <?php echo $payment->paydetails->paysystem_nam ?>
            <?php echo nl2br($payment->paydetails->paysystem_details) ?>
            <br/><br/>
            <?php endif; ?>
            <?php echo nl2br($payment->description) ?>
        </td>
    </tr>
        <?php endforeach; ?>
    </tbody>
</table>