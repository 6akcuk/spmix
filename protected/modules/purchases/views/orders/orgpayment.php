<?php
/**
 * @var $payment OrderPayment
 */

Yii::app()->getClientScript()->registerCssFile('/css/orders.css');
Yii::app()->getClientScript()->registerCssFile('/css/purchases.css');
Yii::app()->getClientScript()->registerScriptFile('/js/purchase.js');

$this->pageTitle = Yii::app()->name .' - Платеж №'. $payment->payment_id;
$sum = 0.00;
?>

<h1>Оплата от <?php echo ActiveHtml::link(ActiveHtml::lex(2, $payment->payer->getDisplayName()), '/id'. $payment->payer_id) ?></h1>

<table class="org_payment">
<tr>
  <td><b>ID:</b></td>
  <td><?php echo $payment->payment_id ?></td>
  <td><b>Создан:</b></td>
  <td><?php echo ActiveHtml::date($payment->datetime, false) ?></td>
  <td><b>Сумма:</b></td>
  <td><?php echo ActiveHtml::price($payment->sum) ?></td>
  <td><b>Статус:</b></td>
  <td><?php echo Yii::t('purchase', $payment->status) ?></td>
</tr>
</table>

<p><?php echo $payment->description ?></p>

<table class="user_orders">
  <thead>
  <tr>
    <th>Номер заказа</th>
    <th>Товар</th>
    <th>Закупка</th>
    <th>Цена</th>
    <th>Кол-во</th>
    <th>Стоимость доставки</th>
    <th>Цена с орг.сбором (итог)</th>
    <th>Текущий статус заказа</th>
    <th>Оплачено</th>
    <th>Новый статус заказа</th>
  </tr>
  </thead>
  <tbody>
  <?php foreach ($payment->orders as $orderlink): ?>
  <tr>
    <td><?php echo $orderlink->order->order_id ?></td>
    <td><?php echo ActiveHtml::link($orderlink->order->good->name, '/good'. $orderlink->order->good->purchase_id .'_'. $orderlink->order->good->good_id) ?></td>
    <td><?php echo ActiveHtml::link($orderlink->order->purchase->name, '/purchase'. $orderlink->order->purchase_id) ?></td>
    <td><?php echo ActiveHtml::price($orderlink->order->price) ?></td>
    <td><?php echo $orderlink->order->amount ?></td>
    <td><?php echo ActiveHtml::price($orderlink->order->delivery) ?></td>
    <td><?php echo ActiveHtml::price($orderlink->order->total_price) ?></td>
    <td><?php echo Yii::t('purchase', $orderlink->order->status) ?></td>
    <td><?php echo ActiveHtml::textField('Payed['. $orderlink->order_id .']', $orderlink->order->total_price) ?></td>
    <td>
      <?php echo ActiveHtml::dropdown('Status['. $orderlink->order_id .']', '', Order::STATUS_PAID, Order::getStatusDataArray()) ?>
    </td>
  </tr>
  <?php $sum += floatval($orderlink->order->total_price) ?>
  <?php endforeach; ?>
  <tr>
    <th>Итого:</th>
    <td>-</td>
    <td>-</td>
    <td>-</td>
    <td>-</td>
    <td>-</td>
    <td>-</td>
    <td>-</td>
    <th><?php echo ActiveHtml::price($sum) ?></th>
    <td>-</td>
  </tr>
  </tbody>
</table>

<div>
  <a class="button">Принять и сохранить</a>
  <a class="button">Отметить платеж как не принятый</a>
</div>