<?php /** @var $payment OrderPayment */ ?>
<?php $page = ($offset + Yii::app()->getModule('purchases')->paymentsPerPage) / Yii::app()->getModule('purchases')->paymentsPerPage ?>
<?php $added = false; ?>
<?php foreach ($payments as $payment): ?>
<tr<?php if(!$added) { echo ' rel="page-'. $page .'"'; $added = true; } ?> >
  <td><?php echo $payment->payment_id ?></td>
  <td><?php echo ActiveHtml::date($payment->datetime) ?></td>
  <td>
    <?php foreach ($payment->orders as $order): ?>
    <?php echo ActiveHtml::link('Заказ №'. $order->order_id, '/order'. $order->order_id) ?>
    <?php endforeach; ?>
  </td>
  <td><?php echo ActiveHtml::price($payment->sum) ?></td>
  <td>
    <?php echo nl2br($payment->description) ?>
  </td>
  <td><?php echo Yii::t('purchase', $payment->status) ?></td>
</tr>
<?php endforeach; ?>