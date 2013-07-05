<?php
/** @var Wishlist $wishlist */
?>
<div class="box_header">
  Добавить пожелание
  <a onclick="curBox().hide()" class="box_close right">Закрыть</a>
</div>
<div class="box_cont wishlist_form clearfix">
  <div id="wishlist_error" class="op_error"></div>
  <input id="type" type="hidden" name="type" value="<?php echo $wishlist->type ?>" />
  <?php echo ActiveHtml::smartTextarea('shortstory', $wishlist->shortstory, array('placeholder' => 'Описание')) ?>
  <div id="box_progress" class="post_progress left">
    <img src="/images/upload.gif" />
  </div>
  <div class="button_submit right">
    <button onclick="Wishlist.attemptAdd()">Добавить</button>
  </div>
</div>