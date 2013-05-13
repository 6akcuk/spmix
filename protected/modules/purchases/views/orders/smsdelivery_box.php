<?php
/** @var OrderSmsDelivery $delivery
 * @var OrderSmsDeliveryLog $log
 */
?>
<div class="box_header">
  <a onclick="curBox().hide()" class="box_close right" style="margin-top: 5px">Закрыть</a>
  <span>Рассылка №<?php echo $delivery->delivery_id ?>
  <br>
  <span class="order_sms_delivery_date"><?php echo ActiveHtml::date($delivery->add_date) ?></span>
</div>
<div class="box_cont">
  <div class="order_sms_delivery_message"><?php echo $delivery->message ?></div>
  <table>
    <?php foreach ($logs as $log): ?>
    <tr>
      <td><?php echo $log->phone ?></td>
      <td>
        <span class="order_sms_delivery_status_<?php echo $log->status ?>"><?php echo $log->getStatus() ?></span>
      </td>
    </tr>
    <?php endforeach; ?>
  </table>
</div>
<div class="box_wl_panel_wrap">
  <div class="box_wl_panel">
    <div class="box_wl_panel_sh"></div>
    <div class="box_wl_panel_inner">
      <div class="clearfix">
        <div id="box_progress" class="progress left"></div>
        <a class="button right" href="/smsdeliveries?purchase_id=<?php echo $delivery->purchase_id ?>&delivery_id=<?php echo $delivery->delivery_id ?>" onclick="return nav.go(this, event, {box: 1})">Обновить</a>
      </div>
    </div>
  </div>
</div>