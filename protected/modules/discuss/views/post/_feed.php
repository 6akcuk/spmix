<?php
Yii::app()->getClientScript()->registerCssFile('/css/discuss.css');
Yii::app()->getClientScript()->registerScriptFile('/js/discuss.js');

/** @var array $post */
$nullpost = $post['nullpost'];
$posts = $post['posts'];
$offsets = $post['offsets'];

?>
<div id="discuss<?php echo $nullpost->forum_id ?>_<?php echo $nullpost->theme_id ?>" class="wall_post clearfix">
  <div class="post_table">
    <div class="post_image">
      <?php echo ActiveHtml::link(ActiveHtml::showUploadImage($nullpost->theme->forum->icon, 'c'), '/discuss'. $nullpost->forum_id .'_'. $nullpost->theme_id, array('class' => 'post_image')) ?>
    </div>
    <div class="post_info">
      <div class="wall_text wall_lnk">
        <?php echo ActiveHtml::link('<span class="icon-comment"></span> Тема <span class="a">'. $nullpost->theme->title .'</span>', '/discuss'. $nullpost->forum_id .'_'. $nullpost->theme_id, array('class' => 'author')) ?>
      </div>
      <div class="replies">
        <div class="replies_wrap" id="replies_wrap<?php echo $nullpost->forum_id ?>_<?php echo $nullpost->theme_id ?>">
          <div class="reply_fakebox_wrap" id="reply_fakebox" onclick="Comment.showReplyEditor(event, '')">
            <div class="reply_fakebox">Комментировать..</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>