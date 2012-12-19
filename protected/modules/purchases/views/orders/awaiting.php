<?php
/**
 * @var $purchase Purchase
 * @var $order Order
 */

Yii::app()->getClientScript()->registerCssFile('/css/purchases.css');
Yii::app()->getClientScript()->registerScriptFile('/js/purchase.js');

$this->pageTitle = Yii::app()->name .' - Мои покупки, ожидающие оплаты';
?>

<h1>Мои покупки</h1>

<div id="tabs">
    <?php echo ActiveHtml::link('Текущие заказы', '/orders') ?>
    <?php echo ActiveHtml::link('Ожидают оплаты'. (($awaitingNum > 0) ? ' ('. $awaitingNum .')' : ''), '/orders/awaiting',  array('class' => 'selected')) ?>
    <?php echo ActiveHtml::link('Платежи', '/orders/payments') ?>
</div>
<table class="data">
    <thead>
    <tr>
        <th>№</th>
        <th>Заказ</th>
        <th>Кол-во</th>
        <th>Цена</th>
        <th></th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($orders as $order): ?>
    <tr>
        <td><?php echo $order->order_id ?></td>
        <td><?php echo ActiveHtml::link($order->good->name, '/order'. $order->order_id) ?></td>
        <td><?php echo $order->amount ?></td>
        <td><?php echo ActiveHtml::price($order->total_price) ?></td>
        <td>
            <?php echo ActiveHtml::link('Оплатить', '/orders/createPayment?id='. $order->order_id, array('class' => 'button')) ?>
        </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>