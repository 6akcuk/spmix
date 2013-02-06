<?php
/**
 * @var $payment OrderPayment
 */
Yii::app()->getClientScript()->registerCssFile('/css/orders.css');
Yii::app()->getClientScript()->registerCssFile('/css/purchases.css');
Yii::app()->getClientScript()->registerScriptFile('/js/purchase.js');

Yii::app()->getClientScript()->registerCssFile('/css/pagination.css');
Yii::app()->getClientScript()->registerScriptFile('/js/pagination.js');

$this->pageTitle = Yii::app()->name .' - Поступившая оплата';
$delta = Yii::app()->controller->module->paymentsPerPage;
?>
<div class="breadcrumbs">
  <?php echo ActiveHtml::link('Мои закупки', '/purchases/my') ?> &raquo;
  Платежи от пользователей
</div>

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
    <th style="width:50px">№</th>
    <th>Дата платежа</th>
    <th>От кого</th>
    <th>Сумма</th>
    <th>Реквизиты</th>
    <th>Статус</th>
  </tr>
  <tr>
    <td>
      <div rel="filters">
        <?php echo ActiveHtml::textField('c[payment_id]', (isset($c['payment_id'])) ? $c['payment_id'] : '', array('style' => 'width:40px')) ?>
      </div>
    </td>
    <td>
      <div rel="filters">
        <?php echo ActiveHtml::inputCalendar('c[creation_date]', (isset($c['creation_date'])) ? $c['creation_date'] : '') ?>
      </div>
    </td>
    <td>
      <div rel="filters">
        <?php echo ActiveHtml::textField('c[payer]', (isset($c['payer'])) ? $c['payer'] : '') ?>
      </div>
    </td>
    <!--<td>
      <div rel="filters">
        <?php echo ActiveHtml::textField('c[orders]', (isset($c['orders'])) ? $c['orders'] : '') ?>
      </div>
    </td>-->
    <td>
      <div rel="filters">
        <?php echo ActiveHtml::textField('c[sum]', (isset($c['sum'])) ? $c['sum'] : '') ?>
      </div>
    </td>
    <td>
      <div rel="filters">
        <?php echo ActiveHtml::textField('c[description]', (isset($c['description'])) ? $c['description'] : '') ?>
      </div>
    </td>
    <td>
      <div rel="filters">
        <?php echo ActiveHtml::dropdown('c[status]', '', (isset($c['status'])) ? $c['status'] : '', OrderPayment::getStatusArray()) ?>
      </div>
    </td>
  </tr>
  </thead>
  <tbody rel="pagination">
    <?php $this->renderPartial('_orgpayments', array('payments' => $payments, 'offset' => $offset)) ?>
  </tbody>
</table>
<? if ($offset + $delta < $offsets && $offsets > $delta): ?><a id="pg_more" class="pg_more" onclick="Paginator.showMore()">Еще платежи</a><? endif; ?>