<?php
/** @var Feed $feed */
?>
<?php foreach ($feeds as $feed): ?>
  <div class="feed_row">
    <?php if ($feed->event_type == 'new reply'): ?>
      <?php $this->renderPartial('application.modules.users.views.profiles._wallpost', array('post' => $feed->content, 'timeback' => true)) ?>
    <?php elseif ($feed->event_type == 'new comment'): ?>

    <?php endif; ?>
  </div>
<?php endforeach; ?>