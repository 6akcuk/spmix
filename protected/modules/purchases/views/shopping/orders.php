<?php
/**
 * @var $purchase Purchase
 * @var $order Order
 */

Yii::app()->getClientScript()->registerCssFile('/css/purchases.css');
Yii::app()->getClientScript()->registerScriptFile('/js/purchase.js');

$this->pageTitle = Yii::app()->name .' - Заказы';
?>

<h1>Заказы</h1>

<div id="tabs">
    <?php echo (Yii::app()->user->checkAccess('purchases.purchases.create'))
    ? ActiveHtml::link('Покупки', '/shopping')
    : ActiveHtml::link('Заказы', '/shopping') ?>
    <?php echo ActiveHtml::link('Платежи', '/shopping/payments') ?>
    <?php if (Yii::app()->user->checkAccess('purchases.purchases.create'))
    echo ActiveHtml::link('Заказы', '/shopping/orders', array('class' => 'selected')) ?>
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
    <?php foreach ($purchases as $purchase): ?>
    <tr>
        <td><?php echo $purchase->purchase_id ?></td>
        <td>
            <?php echo ActiveHtml::link($purchase->name, '/purchase'. $purchase->purchase_id) ?>
            (<?php echo $purchase->ordersNum ?>)
        </td>
        <td><?php echo Yii::t('purchase', $purchase->state) ?></td>
        <td></td>
        <td><?php echo ActiveHtml::price($purchase->ordersSum) ?></td>
    </tr>
        <?php foreach ($purchase->orders as $order): ?>
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