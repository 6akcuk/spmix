<?php /** @var $order Order */ ?>
<?php $page = ($offset + $c['limit']) / $c['limit'] ?>
<?php $added = false; ?>
<?php if ($orders): ?>
<?php foreach ($orders as $order): ?>
<tr>
  <td><?php echo ActiveHtml::checkBox('select['. $order->order_id .']') ?></td>
  <td>
    <?php echo ActiveHtml::link('Зак№'. $order->order_id, '/order-'. $order->order_id, array('nav' => array('box' => 1))) ?><br/>
    <?php if ($order->payment): ?>
    <?php echo ActiveHtml::link('Платеж №'. $order->payment->payment_id .' от '. ActiveHtml::date($order->payment->datetime, false, true), '/payment'. $order->payment->payment_id) ?>
    <?php echo Yii::t('purchase', $order->payment->status) ?>
    <?php endif; ?>
  </td>
  <td><?php echo ActiveHtml::date($order->creation_date, false, true) ?></td>
  <td><?php echo ActiveHtml::link($order->good->name, '/good'. $order->purchase_id .'_'. $order->good_id) ?></td>
  <td><?php echo $order->good->artikul ?></td>
  <td><?php echo $order->color ?></td>
  <td><?php echo $order->size ?></td>
  <td>
    <?php echo ActiveHtml::link('<span class="icon-comment"></span>', '/write'. $order->customer_id, array('nav' => array('box' => 1))) ?>
    <?php echo ActiveHtml::link($order->customer->login .' '. $order->customer->profile->firstname .' '. $order->customer->profile->lastname, '/id'. $order->customer_id) ?>
  </td>
  <td><?php echo $order->customer->profile->city->name ?></td>
  <td><?php echo $order->customer->profile->positive_rep .' | '. $order->customer->profile->negative_rep ?></td>
  <td id="order<?php echo $order->order_id ?>_status"><?php echo Yii::t('purchase', $order->status) ?></td>
  <td><?php echo $order->amount ?></td>
  <td id="order<?php echo $order->order_id ?>_total_price"><?php echo ActiveHtml::price($order->total_price) ?></td>
</tr>
<?php endforeach; ?>
<?php endif; ?>