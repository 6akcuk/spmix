<?php
/**
 * @var $purchase Purchase
 * @var $order Order
 */
Yii::app()->getClientScript()->registerCssFile('/css/orders.css');
Yii::app()->getClientScript()->registerCssFile('/css/purchases.css');
Yii::app()->getClientScript()->registerScriptFile('/js/purchase.js');

$this->pageTitle = Yii::app()->name .' - Мои покупки, ожидающие оплаты';
?>
<div id="_box_hidden_pay" style="display: none">
  <div class="order_box_header">
    Сообщение об оплате
    <a onclick="curBox().hide()" class="order_box_close right">Закрыть</a>
  </div>
  <div class="order_box_cont">
    <div class="row">
      <?php echo ActiveHtml::inputPlaceholder('sum', '', array('placeholder' => 'Сумма платежа')) ?>
    </div>
    <div class="row">
      <?php echo ActiveHtml::smartTextarea('comment', '', array('placeholder' => 'Реквизиты платежа, комментарии для организатора')) ?>
    </div>
  </div>
  <div class="order_box_buttons">
    <a class="button" onclick="doPayment()">Оплатить</a>
  </div>
</div>

<div class="tabs">
    <?php echo ActiveHtml::link('Текущие заказы', '/orders') ?>
    <?php echo ActiveHtml::link('Ожидают оплаты'. (($awaitingNum > 0) ? ' ('. $awaitingNum .')' : ''), '/orders/awaiting',  array('class' => 'selected')) ?>
    <?php echo ActiveHtml::link('Платежи', '/orders/payments') ?>
</div>
<div class="order_buttons">
  <a class="button" onclick="getPayDetails()">Получить реквизиты для оплаты</a>
  <a class="button" onclick="payOrders()">Сообщить об оплате</a>
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
        <td><?php echo ActiveHtml::checkBox('select['. $purchase->purchase_id .']['. $order->order_id .']', false, array('value' => $order->order_id)) ?></td>
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

function getPayDetails() {
  var orders = $('input[type="checkbox"][name*="select"]:checked'), ids = [];

  if (!orders.length) {
    ajex.show('Выберите для начала заказы');
    return;
  }

  $.each(orders, function(i, v) {
    ids.push(parseInt(v.value));
  })

  showGlobalPrg();

  ajax.post('/purchases/orders/getPayDetails', {ids: ids}, function(r) {
    hideGlobalPrg();

    var box = new Box({
      hideButtons: true,
      bodyStyle: 'padding:0px;border:0px'
    });
    box.content(r.html);
    box.show();
  }, function(r) {
    hideGlobalPrg();
  });
}

function payOrders() {
  var orders = $('input[type="checkbox"][name*="select"]:checked'), ids = [], box;

  if (!orders.length) {
    ajex.show('Выберите для начала заказы');
    return;
  }

  box = Box({
    hideButtons: true,
    bodyStyle: 'padding:0px;border:0px',
    onHide: function() {
      $('#_box_hidden_pay').insertBefore('div.tabs').hide();
      $('#_box_hidden_pay textarea, #_box_hidden_add input').val('');
    }
  });
  $('#_box_hidden_pay').appendTo(box.bodyNode).show();
  box.show();
}

function doPayment() {
  var orders = $('input[type="checkbox"][name*="select"]:checked'), ids = [], box,
      comment, sum;

  $('#_box_hidden_pay .input_error').remove();

  comment = $.trim($('#comment').val());
  sum = $.trim($('#sum').val());

  if (!orders.length) {
    ajex.show('Выберите для начала заказы');
    return;
  }

  if (!sum) {
    inputError($('#sum'), 'Укажите сумму осуществленного платежа');
  }
  if (!comment) {
    inputError($('#comment'), 'Укажите информацию о платеже');
  }

  $.each(orders, function(i, v) {
    ids.push(parseInt(v.value));
  });

  if (A.orderPayment) return;
  A.orderPayment = true;

  ajax.post('/purchases/orders/createPayment', {ids: ids, sum: sum, comment: comment}, function(r) {
    A.orderPayment = false;
    curBox().hide();
    if (r.success)
      boxPopup(r.msg);
  }, function(r) {
    A.orderPayment = false;
  });
}
</script>