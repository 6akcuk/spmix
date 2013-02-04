<?php
/**
 * @var $payment OrderPayment
 */
Yii::app()->getClientScript()->registerCssFile('/css/orders.css');
Yii::app()->getClientScript()->registerCssFile('/css/purchases.css');
Yii::app()->getClientScript()->registerScriptFile('/js/purchase.js');

$this->pageTitle = Yii::app()->name .' - Платежи';
$delta = Yii::app()->controller->module->purchasesPerPage;
?>

<div class="tabs">
  <?php echo ActiveHtml::link('Текущие заказы', '/orders') ?>
  <?php echo ActiveHtml::link('Ожидают оплаты'. (($awaitingNum > 0) ? ' ('. $awaitingNum .')' : ''), '/orders/awaiting') ?>
  <?php echo ActiveHtml::link('Платежи', '/orders/payments', array('class' => 'selected')) ?>
</div>
<div class="clearfix">
  <div class="right">
    <?php $this->widget('Paginator', array(
    'url' => '/orders/payments',
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
    <th>Дата платежа</th>
    <th>Заказы</th>
    <th>Сумма</th>
    <th>Реквизиты</th>
    <th>Статус</th>
  </tr>
  </thead>
  <tbody rel="pagination">
    <?php echo $this->renderPartial('_payments', array('payments' => $payments, 'offset' => $offset), true) ?>
  </tbody>
</table>
<? if ($offset + $delta < $offsets && $offsets > $delta): ?><a id="pg_more" class="pg_more" onclick="Paginator.showMore()">Еще платежи</a><? endif; ?>