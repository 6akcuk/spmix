<?php
/**
 * @var $purchase Purchase
 * @var $order Order
 */
Yii::app()->getClientScript()->registerCssFile('/css/orders.css');
Yii::app()->getClientScript()->registerCssFile('/css/purchases.css');
Yii::app()->getClientScript()->registerScriptFile('/js/purchase.js');

Yii::app()->getClientScript()->registerCssFile('/css/pagination.css');
Yii::app()->getClientScript()->registerScriptFile('/js/pagination.js');

$this->pageTitle = Yii::app()->name .' - Мои покупки';
?>

<div class="tabs">
  <?php echo ActiveHtml::link('Текущие заказы', '/orders', array('class' => (Yii::app()->controller->action->id == 'index') ? 'selected' : '')) ?>
  <?php echo ActiveHtml::link('Ожидают оплаты'. (($awaitingNum > 0) ? ' ('. $awaitingNum .')' : ''), '/orders/awaiting') ?>
  <?php echo ActiveHtml::link('Ожидают выдачи'. (($deliveringNum > 0) ? ' ('. $deliveringNum .')' : ''), '/orders/delivering', array('class' => (Yii::app()->controller->action->id == 'delivering') ? 'selected' : '')) ?>
  <?php echo ActiveHtml::link('Полученные заказы', '/orders/delivered', array('class' => (Yii::app()->controller->action->id == 'delivered') ? 'selected' : '')) ?>
  <?php echo ActiveHtml::link('Платежи', '/orders/payments') ?>
</div>

<table class="user_orders">
    <thead>
    <tr>
      <th>№</th>
      <th>Закупка / Заказ</th>
      <th>Статус</th>
      <th>Кол-во</th>
      <th>Цена</th>
      <th>Долг</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($orders as $pid => $_orders): ?>
    <?php $purchase = $purchases[$pid]; ?>
    <tr class="order_purchase_row">
      <td></td>
      <td>#<?php echo $purchase->purchase_id ?> <?php echo ActiveHtml::link($purchase->name, '/purchase'. $purchase->purchase_id) ?></td>
      <td><?php echo Yii::t('purchase', $purchase->state) ?></td>
      <td><?php echo $stat[$pid]['num'] ?></td>
      <td><?php echo ActiveHtml::price($stat[$pid]['sum']) ?></td>
      <td><?php echo ActiveHtml::price($stat[$pid]['credit']) ?></td>
    </tr>
    <?php if ($purchase->user_oic->oic_price > 0): ?>
    <tr class="order_user_oic">
      <td></td>
      <td>Место выдачи: <?php echo $purchase->user_oic->oic_name ?></td>
      <td><?php echo ($purchase->user_oic->payed == 1) ? 'Оплачено' : 'Не оплачено' ?></td>
      <td></td>
      <td><?php echo ActiveHtml::price($purchase->user_oic->oic_price) ?></td>
      <td><?php echo ($purchase->user_oic->payed == 0) ? ActiveHtml::price($purchase->user_oic->oic_price) : ActiveHtml::price(0) ?></td>
    </tr>
    <?php endif; ?>
        <?php foreach ($_orders as $order): ?>
        <tr class="order_row">
          <td><?php echo $order->order_id ?></td>
          <td class="good_name"><?php echo ActiveHtml::link($order->good->name, '/order'. $order->order_id) ?></td>
          <td><?php echo Yii::t('purchase', $order->status) ?></td>
          <td><?php echo $order->amount ?></td>
          <td><?php echo ActiveHtml::price($order->total_price) ?></td>
          <td><?php echo ActiveHtml::price($order->total_price - $order->payed) ?></td>
        </tr>
        <?php endforeach; ?>
    <?php endforeach; ?>
    </tbody>
</table>