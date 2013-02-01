<?php
/**
 * @var $purchase Purchase
 * @var $order Order
 */

Yii::app()->getClientScript()->registerCssFile('/css/purchases.css');
Yii::app()->getClientScript()->registerScriptFile('/js/purchase.js');

$this->pageTitle = Yii::app()->name .' - Мои покупки, ожидающие оплаты';
?>

<div class="tabs">
    <?php echo ActiveHtml::link('Текущие заказы', '/orders') ?>
    <?php echo ActiveHtml::link('Ожидают оплаты'. (($awaitingNum > 0) ? ' ('. $awaitingNum .')' : ''), '/orders/awaiting',  array('class' => 'selected')) ?>
    <?php echo ActiveHtml::link('Платежи', '/orders/payments') ?>
</div>
<table class="user_orders">
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
    <?php foreach ($orders as $pid => $_orders): ?>
    <?php $purchase = $purchases[$pid]; ?>
    <tr class="order_purchase_row">
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
          <td><?php echo $order->amount ?></td>
          <td><?php echo ActiveHtml::price($order->total_price) ?></td>
          <td>
          <?php if ($order->payment): ?>
              <?php echo ActiveHtml::link('Платеж #'. $order->payment->payment_id .' от '. ActiveHtml::date($order->payment->datetime, true, true), '/payment'. $order->payment->payment_id) ?>
          <?php else: ?>
              <?php echo ActiveHtml::link('Оплатить', '/orders/createPayment?id='. $order->order_id, array('class' => 'button')) ?>
          <?php endif; ?>
          </td>
      </tr>
      <?php endforeach; ?>
    <?php endforeach; ?>
    </tbody>
</table>