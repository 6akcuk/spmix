<?php

Yii::app()->getClientScript()->registerCssFile('/css/pagination.css');

Yii::app()->getClientScript()->registerCssFile('/css/wishlist.css');
Yii::app()->getClientScript()->registerScriptFile('/js/wishlist.js');

$this->pageTitle = Yii::app()->name .' - Хотелки';

?>
<div id="wish<?php echo $wish->wishlist_id ?>" class="wish wish_wide clearfix">
  <div class="left photo">
    <a class="left wish_header" href="/id<?php echo $wish->author_id ?>" onclick="return nav.go(this, event)"><?php echo $wish->author->profile->getProfileImage('c') ?></a>
  </div>
  <div class="left info info_wide">
    <div class="clearfix">
      <a class="left wish_header" href="/id<?php echo $wish->author_id ?>" onclick="return nav.go(this, event)"><?php echo $wish->author->getDisplayName() ?></a>
      <a class="left icon-comment" href="/write<?php echo $wish->author_id ?>" onclick="return nav.go(this, event, {box: true})"></a>
      <div class="right wish_city"><?php echo $wish->city->name ?></div>
    </div>
    <a class="wish_body" href="/wish<?php echo $wish->wishlist_id ?>" onclick="return nav.go(this, event)"><?php echo nl2br($wish->shortstory) ?></a>
    <div class="wish_bottom">
      <a class="wish_date" href="/wish<?php echo $wish->wishlist_id ?>" onclick="return nav.go(this, event)"><?php echo ActiveHtml::date($wish->add_date) ?></a>
      <?php if (Yii::app()->user->checkAccess('wishlist.default.editSuper') ||
        Yii::app()->user->checkAccess('wishlist.default.editOwn', array('wishlist' => $wish))): ?>
        |
        <a onclick="Wishlist.edit(<?php echo $wish->wishlist_id ?>)">Редактировать</a>
      <?php endif; ?>
      <?php if (Yii::app()->user->checkAccess('wishlist.default.deleteSuper') ||
        Yii::app()->user->checkAccess('wishlist.default.deleteOwn', array('wishlist' => $wish))): ?>
        |
        <a onclick="Wishlist.delete(<?php echo $wish->wishlist_id ?>)">Удалить</a>
      <?php endif; ?>
    </div>
  </div>
</div>
<?php $this->widget('Comments', array('hoop' => $wish, 'hoop_id' => $wish->wishlist_id, 'hoop_type' => 'wish', 'reply' => $reply)) ?>