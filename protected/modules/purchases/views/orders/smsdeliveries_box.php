<?php
/** @var OrderSmsDelivery $delivery */
?>
<div class="box_header">
  Рассылки СМС
  <a onclick="curBox().hide()" class="box_close right">Закрыть</a>
</div>
<div class="box_cont">
<?php if ($deliveries): ?>
  <?php foreach ($deliveries as $delivery): ?>
  <a class="order_sms_delivery_lnk clearfix" href="/smsdeliveries?purchase_id=<?php echo $delivery->purchase_id ?>&delivery_id=<?php echo $delivery->delivery_id ?>" onclick="return nav.go(this, event, {box: 1})">
    <span class="right order_sms_delivery_view">Просмотреть</span>
    <span>Рассылка №<?php echo $delivery->delivery_id ?>
    <br>
    <span class="order_sms_delivery_date"><?php echo ActiveHtml::date($delivery->add_date) ?></span>
  </a>
  <?php endforeach; ?>
<?php else: ?>
  <div id="sms_deliveries_empty">
    Здесь будет выводиться список рассылок, закрепленных за данной закупкой
  </div>
<?php endif; ?>
</div>