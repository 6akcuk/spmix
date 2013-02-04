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
<div class="order_buttons">
  <a class="button" onclick="">Получить реквизиты для оплаты</a>
  <a class="button">Оплатить выбранные заказы</a>
</div>
<table class="user_orders">
    <thead>
    <tr>
      <th>
        <input type="checkbox" onchange="markAll(this)" />
      </th>
      <th>№</th>
      <th>Закупка / Заказ</th>
      <th>Кол-во</th>
      <th>Цена</th>
      <th>Долг</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($orders as $pid => $_orders): ?>
    <?php $purchase = $purchases[$pid]; ?>
    <tr class="order_purchase_row">
      <td><input type="checkbox" onchange="markOnlyPurchase(this, <?php echo $purchase->purchase_id ?>)" /></td>
      <td><?php echo $purchase->purchase_id ?></td>
      <td><?php echo ActiveHtml::link($purchase->name, '/purchase'. $purchase->purchase_id) ?></td>
      <td><?php echo $stat[$pid]['num'] ?></td>
      <td><?php echo ActiveHtml::price($stat[$pid]['sum']) ?></td>
      <td><?php echo ActiveHtml::price($stat[$pid]['credit']) ?></td>
    </tr>
      <?php foreach ($_orders as $order): ?>
      <tr>
        <td><?php echo ActiveHtml::checkBox('select['. $purchase->purchase_id .']['. $order->order_id .']') ?></td>
        <td><?php echo $order->order_id ?></td>
        <td><?php echo ActiveHtml::link($order->good->name, '/order'. $order->order_id) ?></td>
        <td><?php echo $order->amount ?></td>
        <td><?php echo ActiveHtml::price($order->total_price) ?></td>
        <td><?php echo ActiveHtml::price($order->total_price - $order->payed) ?></td>
      </tr>
      <?php endforeach; ?>
    <?php endforeach; ?>
    </tbody>
</table>
<script>
function markAll(obj) {
  $('input[type="checkbox"][name*="select"]').attr('checked', ($(obj).attr('checked')) ? true : false);
}
function markOnlyPurchase(obj, id) {
  $('input[type="checkbox"][name*="select"]').attr('checked', false);
  $('input[type="checkbox"][name*="select['+ id + ']"]').attr('checked', ($(obj).attr('checked')) ? true : false);
}
</script>