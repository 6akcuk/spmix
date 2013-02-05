<?php /** @var $order Order */ ?>
<?php $page = ($offset + Yii::app()->getModule('purchases')->ordersPerPage) / Yii::app()->getModule('purchases')->ordersPerPage ?>
<?php $added = false; ?>
<?php foreach ($orders as $order): ?>
<tr<?php if(!$added) { echo ' rel="page-'. $page .'"'; $added = true; } ?> >
  <td><?php echo $order->order_id ?></td>
  <td><?php echo ActiveHtml::link($order->good->name, '/order'. $order->order_id) ?></td>
  <td><?php echo Yii::t('purchase', $order->status) ?></td>
  <td><?php echo $order->amount ?></td>
  <td><?php echo ActiveHtml::price($order->total_price) ?></td>
  <td><?php echo ActiveHtml::price($order->payed) ?></td>
</tr>
<?php endforeach; ?>