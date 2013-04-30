<?php
/** @var Feed $feed */
?>
<?php foreach ($feeds as $feed): ?>
  <?php if (!$feed->content) continue; ?>
  <div class="feed_row">
    <?php if ($feed->event_type == 'new reply'): ?>
      <?php $this->renderPartial('application.modules.users.views.profiles._feedreply', array('feed' => $feed)) ?>
    <?php elseif ($feed->event_type == 'new comment'): ?>
      <?php $this->renderPartial('application.views.comment._feedcomment', array('feed' => $feed)) ?>
    <?php endif; ?>
  </div>
<?php endforeach; ?>