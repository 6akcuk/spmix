<?php
/**
 * @var $payment OrderPayment
 */
Yii::app()->getClientScript()->registerCssFile('/css/orders.css');
Yii::app()->getClientScript()->registerCssFile('/css/purchases.css');
Yii::app()->getClientScript()->registerScriptFile('/js/purchase.js');

$this->pageTitle = Yii::app()->name .' - Полученные заказы';
$delta = Yii::app()->controller->module->paymentsPerPage;
?>

<div class="tabs">
  <?php echo ActiveHtml::link('Текущие заказы', '/orders') ?>
  <?php echo ActiveHtml::link('Ожидают оплаты'. (($awaitingNum > 0) ? ' ('. $awaitingNum .')' : ''), '/orders/awaiting') ?>
  <?php echo ActiveHtml::link('Ожидают выдачи'. (($deliveringNum > 0) ? ' ('. $deliveringNum .')' : ''), '/orders/delivering') ?>
  <?php echo ActiveHtml::link('Полученные заказы', '/orders/delivered', array('class' => 'selected')) ?>
  <?php echo ActiveHtml::link('Платежи', '/orders/payments') ?>
</div>
<div class="clearfix">
  <div class="right">
    <?php $this->widget('Paginator', array(
    'url' => '/orders/delivered',
    'offset' => $offset,
    'offsets' => $offsets,
    'delta' => $delta,
  )); ?>
  </div>
</div>
<table class="user_orders">
  <thead>
  <tr>
    <th>№</th>
    <th>Заказ</th>
    <th>Статус</th>
    <th>Кол-во</th>
    <th>Сумма</th>
    <th>Долг</th>
  </tr>
  </thead>
  <tbody rel="pagination">
  <?php echo $this->renderPartial('_delivered', array('orders' => $orders, 'offset' => $offset), true) ?>
  </tbody>
</table>
<? if ($offset + $delta < $offsets && $offsets > $delta): ?><a id="pg_more" class="pg_more" onclick="Paginator.showMore()">Еще заказы</a><? endif; ?>