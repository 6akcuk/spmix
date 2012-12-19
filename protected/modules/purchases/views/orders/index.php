<?php
/**
 * @var $purchase Purchase
 * @var $order Order
 */

Yii::app()->getClientScript()->registerCssFile('/css/purchases.css');
Yii::app()->getClientScript()->registerScriptFile('/js/purchase.js');

$this->pageTitle = Yii::app()->name .' - Мои покупки';
?>

<h1>Мои покупки</h1>

<div id="tabs">
    <?php echo ActiveHtml::link('Текущие заказы', '/orders', array('class' => 'selected')) ?>
    <?php echo ActiveHtml::link('Ожидают оплаты'. (($awaitingNum > 0) ? ' ('. $awaitingNum .')' : ''), '/orders/awaiting') ?>
    <?php echo ActiveHtml::link('Платежи', '/orders/payments') ?>
</div>
<table class="data">
    <thead>
    <tr>
        <th>№</th>
        <th>Закупка / Заказ</th>
        <th>Статус</th>
        <th>Кол-во</th>
        <th>Цена</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($orders as $pid => $_orders): ?>
    <?php $purchase = $purchases[$pid]; ?>
    <tr>
        <td><?php echo $purchase->purchase_id ?></td>
        <td><?php echo ActiveHtml::link($purchase->name, '/purchase'. $purchase->purchase_id) ?></td>
        <td><?php echo Yii::t('purchase', $purchase->state) ?></td>
        <td><?php echo $stat[$pid]['num'] ?></td>
        <td><?php echo ActiveHtml::price($stat[$pid]['sum']) ?></td>
    </tr>
        <?php foreach ($_orders as $order): ?>
        <tr>
            <td><?php echo $order->order_id ?></td>
            <td><?php echo ActiveHtml::link($order->good->name, '/order'. $order->order_id) ?></td>
            <td><?php echo Yii::t('purchase', $order->status) ?></td>
            <td><?php echo $order->amount ?></td>
            <td><?php echo ActiveHtml::price($order->total_price) ?></td>
        </tr>
        <?php endforeach; ?>
    <?php endforeach; ?>
    </tbody>
</table>