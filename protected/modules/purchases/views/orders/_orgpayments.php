<?php /** @var $payment OrderPayment */ ?>
<?php $page = ($offset + Yii::app()->getModule('purchases')->paymentsPerPage) / Yii::app()->getModule('purchases')->paymentsPerPage ?>
<?php $added = false; ?>
<?php foreach ($payments as $payment): ?>
<tr<?php if(!$added) { echo ' rel="page-'. $page .'"'; $added = true; } ?> >
  <td><?php echo $payment->payment_id ?></td>
  <td><?php echo ActiveHtml::date($payment->datetime) ?></td>
  <td>
    <?php echo ActiveHtml::link('<span class="icon-comment"></span>', '/write'. $payment->payer_id, array('nav' => array('box' => 1))) ?>
    <?php echo ActiveHtml::link($payment->payer->getDisplayName(), '/id'. $payment->payer_id) ?>
  </td>
  <td><?php echo ActiveHtml::price($payment->sum) ?></td>
  <td>
    <?php echo ActiveHtml::link(nl2br($payment->description), '/orgpayment'. $payment->payment_id, array('nav' => array('box' => 1))) ?>
  </td>
  <td><?php echo Yii::t('purchase', $payment->status) ?></td>
</tr>
<?php endforeach; ?>