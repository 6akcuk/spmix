<?php
/** @var $purchase Purchase */
?>
<div class="box_header">
  Рассказать друзьям и подписчикам
  <a onclick="curBox().hide()" class="box_close right">Закрыть</a>
</div>
<div class="box_cont clearfix">
  <h4>Сообщение</h4>
  <textarea id="share_message" style="width: 440px; height: 80px;"></textarea>
  <div class="purchase_table clearfix">
    <div class="left share_image"><?php echo ActiveHtml::showUploadImage($purchase->image, 'e') ?></div>
    <div class="left share_text">
      <?php echo ActiveHtml::link($purchase->name, '/purchase'. $purchase->purchase_id, array('style' => 'font-size: 13px')) ?>
      <div style="padding-top: 10px;"><?php echo $purchase->shortstory ?></div>
    </div>
  </div>
  <div class="left box_progress"></div>
  <a class="right button" onclick="Purchase.doShareToFriends(<?php echo $purchase->purchase_id ?>)">Отправить</a>
</div>