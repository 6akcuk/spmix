<?php
/**
 * @var $payment OrderPayment
 */
Yii::app()->getClientScript()->registerCssFile('/css/orders.css');
Yii::app()->getClientScript()->registerCssFile('/css/purchases.css');
Yii::app()->getClientScript()->registerScriptFile('/js/purchase.js');

$this->pageTitle = Yii::app()->name .' - Поступившая оплата';
$delta = Yii::app()->controller->module->paymentsPerPage;
?>

<h1>Платежи от пользователей по Вашим закупкам</h1>

<div class="clearfix">
  <div class="right">
    <?php $this->widget('Paginator', array(
    'url' => '/orders/orgPayments',
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
    <th>От кого</th>
    <th>Сумма</th>
    <th>Реквизиты</th>
    <th>Статус</th>
  </tr>
  </thead>
  <tbody rel="pagination">
    <?php $this->renderPartial('_orgpayments', array('payments' => $payments, 'offset' => $offset)) ?>
  </tbody>
</table>
<? if ($offset + $delta < $offsets && $offsets > $delta): ?><a id="pg_more" class="pg_more" onclick="Paginator.showMore()">Еще платежи</a><? endif; ?>