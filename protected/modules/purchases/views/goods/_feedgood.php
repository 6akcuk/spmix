<?php
/**
 * @var Feed $feed
 * @var Good $good
 */
?>
<div id="feed_good<?php echo $feed->feed_id ?>_<?php echo $feed->content->purchase_id ?>" class="wall_post clearfix">
  <div class="post_table">
    <div class="post_image">
      <?php echo ActiveHtml::link(ActiveHtml::showUploadImage($feed->content->image, 'c'), '/purchase'. $feed->event_link_id, array('class' => 'post_image')) ?>
    </div>
    <div class="post_info">
      <div class="wall_text">
        <?php echo ActiveHtml::link($feed->content->name, '/purchase'. $feed->event_link_id, array('class' => 'author')) ?>
        <span class="explain"><?php echo Yii::t('app', 'добавлен {n} новый товар|добавлено {n} новых товара|добавлено {n} новых товаров', $feed->goods_num) ?></span>
        <div class="feed_goods clearfix">
        <?php foreach ($feed->goods as $good): ?>
          <div class="feed_good left">
            <?php echo ActiveHtml::link(ActiveHtml::showUploadImage(($good->image) ? $good->image->image : '', 'c'), '/good'. $good->purchase_id .'_'. $good->good_id, array('class' => 'feed_good_photo')) ?>
            <?php echo ActiveHtml::link((mb_strlen($good->name) > 40) ? mb_substr($good->name, 0, 40, 'utf-8') .'..' : $good->name, '/good'. $good->purchase_id .'_'. $good->good_id, array('class' => 'feed_good_name')) ?>
          </div>
        <?php endforeach; ?>
        </div>
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