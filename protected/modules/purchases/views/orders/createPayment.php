<?php
/**
 * @var $order Order
 * @var $oic OrderOic
 */

Yii::app()->getClientScript()->registerCssFile('/css/purchases.css');
Yii::app()->getClientScript()->registerScriptFile('/js/purchase.js');

$sum = 0.00;
?>
<form id="payment_form">
<div class="order_box_header">
  Сообщение об оплате
  <a onclick="curBox().hide()" class="order_box_close right">Закрыть</a>
</div>
<div class="order_box_cont">
  <div class="row clearfix">
    <div class="left">
      Получатель:
    </div>
    <div class="left" style="padding-left: 10px">
      <?php echo $orders[0]->purchase->author->getDisplayName() ?>
    </div>
  </div>
  <table class="user_orders">
    <thead>
    <tr>
      <th>#</th>
      <th>Товар</th>
      <th>Кол</th>
      <th>Сумма</th>
    </tr>
    </thead>
    <?php foreach ($orders as $order): ?>
    <?php $sum += $order->total_price - $order->payed ?>
    <tr>
      <td>
        <input type="hidden" name="ids[]" value="<?php echo $order->order_id ?>" />
        <?php echo $order->order_id ?>
      </td>
      <td>
        <?php echo $order->good->name ?>
      </td>
      <td>
        <?php echo $order->amount ?>
      </td>
      <td>
        <?php echo ActiveHtml::price($order->total_price - $order->payed) ?>
      </td>
    </tr>
    <?php endforeach; ?>
    <?php if ($oic->oic_price > 0 && $oic->payed == 0): ?>
    <?php $sum += $oic->oic_price ?>
    <tr>
      <td colspan="2">
        Место выдачи: <?php echo $oic->oic_name ?>
      </td>
      <td>-</td>
      <td><?php echo ActiveHtml::price($oic->oic_price) ?></td>
    </tr>
    <?php endif; ?>
    <tr>
      <td colspan="3" align="right">
        Итого:
      </td>
      <td>
        <b><?php echo ActiveHtml::price($sum) ?></b>
      </td>
    </tr>
  </table>
  <div class="row">
    <div style="font-weight: bold; font-size: 1.08em; padding: 5px 0px 10px">Реквизиты платежа, комментарии для организатора:</div>
    <?php echo ActiveHtml::smartTextarea('comment', '', array('style' => 'width: 350px')) ?>
  </div>
</div>
<div class="order_box_buttons">
  <a class="button" onclick="doPayment()">Оплатить</a>
</div>
</form>