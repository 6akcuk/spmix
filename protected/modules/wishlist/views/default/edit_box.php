<?php
/** @var Wishlist $wishlist */
?>
<div class="box_header">
  Редактировать пожелание
  <a onclick="curBox().hide()" class="box_close right">Закрыть</a>
</div>
<div class="box_cont wishlist_form clearfix">
  <div id="wishlist_error" class="op_error"></div>
  <input id="wishlist_id" type="hidden" name="wishlist_id" value="<?php echo $wishlist->wishlist_id ?>" />
  <?php echo ActiveHtml::smartTextarea('shortstory', $wishlist->shortstory, array('placeholder' => 'Описание')) ?>
  <div id="box_progress" class="post_progress left">
    <img src="/images/upload.gif" />
  </div>
  <div class="button_submit right">
    <button onclick="Wishlist.attemptEdit()">Сохранить</button>
  </div>
</div>