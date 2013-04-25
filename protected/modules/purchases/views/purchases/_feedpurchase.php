<?php
/** @var Feed $feed */
?>
<div id="feed_purchase<?php echo $feed->feed_id ?>_<?php echo $feed->content->purchase_id ?>" class="wall_post clearfix">
  <div class="post_table">
    <div class="post_image">
      <?php echo ActiveHtml::link(ActiveHtml::showUploadImage($feed->content->image, 'c'), '/purchase'. $feed->event_link_id, array('class' => 'post_image')) ?>
    </div>
    <div class="post_info">
      <div class="wall_text">
        <?php echo ActiveHtml::link($feed->content->name, '/purchase'. $feed->event_link_id, array('class' => 'author')) ?>
        <div class="desc"><?php echo $feed->content->shortstory ?></div>
      </div>
      <div class="replies">
        <div class="reply_link_wrap">
          <small>
            <span class="rel_date"><?php echo ActiveHtml::timeback($feed->add_date) ?></span>
          </small>
        </div>
      </div>
    </div>
  </div>
</div>