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

<div class="order_box_header">
  Оплата от <?php echo ActiveHtml::link(ActiveHtml::lex(2, $payment->payer->getDisplayName()), '/id'. $payment->payer_id) ?>
  <a onclick="curBox().hide()" class="order_box_close right">Закрыть</a>
</div>

<div class="order_box_cont">
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
  <form id="orgpayment_orders_form">
  <input type="hidden" name="paydirection" value="-1" />
  <table class="user_orders org_payments_orders">
    <thead>
    <tr>
      <th>Номер заказа</th>
      <th>Товар</th>
      <th>Закупка</th>
      <th>Цена</th>
      <th>Кол-во</th>
      <th>Цена с орг.сбором (итог)</th>
      <th>Текущий статус заказа</th>
      <th>Оплачено</th>
      <th>Новый статус заказа</th>
    </tr>
    </thead>
    <tbody>
    <?php if ($oic->oic_price > 0 && $oic->payed == 0): ?>
    <?php $sum += floatval($oic->oic_price) ?>
    <tr>
      <td colspan="3">Место выдачи: <?php echo $oic->oic_name ?></td>
      <td colspan="3"><?php echo ActiveHtml::price($oic->oic_price) ?></td>
      <td>Не оплачен</td>
      <td><?php echo ActiveHtml::price($oic->oic_price) ?></td>
      <td>
        <?php echo ActiveHtml::checkBox('oic_payed', true) ?>
        <?php echo ActiveHtml::label('Оплачен', 'oic_payed') ?>
      </td>
    </tr>
    <?php endif; ?>
    <?php foreach ($payment->orders as $orderlink): ?>
    <tr>
      <td><?php echo $orderlink->order->order_id ?></td>
      <td><?php echo ActiveHtml::link($orderlink->order->good->name, '/good'. $orderlink->order->good->purchase_id .'_'. $orderlink->order->good->good_id) ?></td>
      <td><?php echo ActiveHtml::link($orderlink->order->purchase->name, '/purchase'. $orderlink->order->purchase_id) ?></td>
      <td><?php echo ActiveHtml::price($orderlink->order->price) ?></td>
      <td><?php echo $orderlink->order->amount ?></td>
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
      <th><?php echo ActiveHtml::price($sum) ?></th>
      <td>-</td>
    </tr>
    </tbody>
  </table>
  </form>
</div>
<div class="order_box_buttons">
  <a class="button" onclick="confirmPayment(<?php echo $payment->payment_id ?>)">Принять и сохранить</a>
  <a class="button" onclick="refusePayment(<?php echo $payment->payment_id ?>)">Отметить платеж как не принятый</a>
</div>
<a onclick="$(this).next().toggle()" class="order_box_history_link">Справка</a>
<div class="order_box_history" style="display: none">
  <div>
    - пользователи при добавлении оплаты выбирают заказы к которым относятся оплаты, но лучше все перепроверить<br>
    - внимательно проверьте итоговую сумму оплаты поступившую Вам<br>
    - вы можете отредактировать данные в столбцах "Оплата" и "Статус заказа"<br>
    - если например пользователь остался Вам должен но сумма незначительная, то вы можете сумму оплаты установить на
    фактическую сумму поступления, а статус заказа установить на "Оплачен". Таким образом в дальнейшем сможет
    отследить кто не доплатил и не будет путанницы какие заказы заказывать поставщику
  </div>
  <div>
    При нажатии кнопки <b>"Сохранить"</b> будут изменены следующие данные:<br>
    1. в указанных заказах будет заполнена сумма оплаты (пользователь увидит что заказы оплачены)<br>
    2. в указанных заказах будет изменен статус<br>
    3. статус платежа будет установлен на "Принят", т.е Вы увидели и обработали платеж, это значит что деньги у Вас
  </div>
</div>
<script>
function doPayment(id, dir) {
  if (A.payment) return;
  A.payment = true;

  $('input[name="paydirection"]').val(dir);

  ajax.post('/orgpayment'+ id, $('#orgpayment_orders_form').serialize(), function(r) {
    A.payment = false;
    if (r.success) {
      curBox().hide();
      boxPopup(r.msg);
    }
  }, function(r) {
    A.payment = false;
  });
}
function confirmPayment(id) {
  doPayment(id, 1);
}
function refusePayment(id) {
  doPayment(id, 0);
}
</script>