<?php
/** @var $good Good */
?>
<div class="box_header">
  Рассказать друзьям и подписчикам
  <a onclick="curBox().hide()" class="box_close right">Закрыть</a>
</div>
<div class="box_cont clearfix">
  <h4>Сообщение</h4>
  <textarea id="share_message" style="width: 440px; height: 80px;"></textarea>
  <div class="purchase_table clearfix">
    <div class="left" class="share_image"><?php echo ActiveHtml::showUploadImage($good->image->image, 'e') ?></div>
    <div class="left" class="share_text">
      <?php echo ActiveHtml::link($good->name, '/good'. $good->purchase_id .'_'. $good->good_id, array('style' => 'font-size: 13px')) ?>
      <div style="padding-top: 10px;"><?php echo $good->description ?></div>
    </div>
  </div>
  <div class="left box_progress"></div>
  <a class="right button" onclick="Purchase.doShareGoodToFriends(<?php echo $good->good_id ?>)">Отправить</a>
</div>