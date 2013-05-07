<?php /** @var DiscussPost $post */ ?>
<?php foreach ($posts as $post): ?>
  <div id="discuss_post<?php echo $post->post_id ?>" data-fid="<?php echo $post->forum_id ?>_<?php echo $post->theme_id ?>" class="reply" onmouseover="Wall.postOver('discuss_post<?php echo $post->post_id ?>', event)" onmouseout="Wall.postOut('discuss_post<?php echo $post->post_id ?>')" onclick="Discuss.replyFeedPost(<?php echo $post->post_id ?>, event)">
    <div class="reply_table">
      <div class="reply_image">
        <?php echo ActiveHtml::link($post->author->profile->getProfileImage('c'), '/id'. $post->author_id, array('class' => 'reply_image')) ?>
      </div>
      <div class="reply_info">
        <?php if (Yii::app()->user->checkAccess('discuss.post.deleteSuper') ||
          Yii::app()->user->checkAccess('discuss.post.deleteOwn', array('post' => $post)) ||
          Yii::app()->user->checkAccess('discuss.post.deleteOwner', array('theme' => $post->theme))): ?>
          <div class="right delete_reply_wrap">
            <div class="delete_post">
              <div title="Удалить" id="delete_discuss_post<?php echo $post->post_id ?>" onmouseover="Wall.postDeleteOver('discuss_post<?php echo $post->post_id ?>')" onmouseout="Wall.postDeleteOut('discuss_post<?php echo $post->post_id ?>')" onclick="event.cancelBubble = true; Discuss.deleteFeedPost(<?php echo $post->post_id ?>);" class="icon-remove" style="opacity:0"></div>
            </div>
          </div>
        <?php endif; ?>
        <div class="reply_text">
          <?php echo ActiveHtml::link($post->author->getDisplayName(), '/id'. $post->author_id, array('class' => 'author', 'data-name' => $post->author->profile->firstname)) ?>
          <div id="wpt<?php echo $post->post_id ?>">
            <div class="wall_reply_text">
              <?php
              $post->post = preg_replace("/\[post(\d+)\|(.*)\]/ui", "$2", $post->post);
              ?>
              <?php echo nl2br($post->post) ?>
            </div>
            <?php if ($post->attaches != '[]'): ?>
              <div class="wall_attaches clearfix">
                <?php
                $attaches = json_decode($post->attaches, true);

                if (isset($attaches['photo'])) {
                  $length = sizeof($attaches['photo']);

                  $photo_sizes = array();
                  $list = array('items' => array(), 'count' => $length);

                  if ($length == 1) {
                    $attaches['photo'][0] = json_decode($attaches['photo'][0], true);
                    $photo_sizes[0] = array('e', min($attaches['photo'][0]['e'][3], 130), min($attaches['photo'][0]['e'][4], 130));
                  }
                  elseif ($length == 2) {
                    $attaches['photo'][0] = json_decode($attaches['photo'][0], true);
                    $attaches['photo'][1] = json_decode($attaches['photo'][1], true);

                    $min_height = min($attaches['photo'][0]['e'][4], $attaches['photo'][1]['e'][4]);

                    $photo_sizes[0] = array('e', min($attaches['photo'][0]['w'][3], 130), $min_height);
                    $photo_sizes[1] = array('e', min($attaches['photo'][1]['w'][3], 130), $min_height);
                  }
                  elseif ($length == 3) {
                    $attaches['photo'][0] = json_decode($attaches['photo'][0], true);
                    $attaches['photo'][1] = json_decode($attaches['photo'][1], true);
                    $attaches['photo'][2] = json_decode($attaches['photo'][2], true);

                    $min_height = min($attaches['photo'][0]['e'][4], $attaches['photo'][1]['e'][4]);

                    $photo_sizes[0] = array('e', min($attaches['photo'][0]['w'][3], 130), $min_height);
                    $photo_sizes[1] = array('e', min($attaches['photo'][0]['w'][3], 130), $min_height);
                    $photo_sizes[2] = array('e', min($attaches['photo'][0]['w'][3], 130), $min_height);
                  }
                  ?>
                  <?php foreach ($attaches['photo'] as $akey => $photo): ?>
                  <?php $list['items'][] = $photo ?>
                  <?php $size = $photo_sizes[$akey] ?>
                  <?php $use = $photo[$size[0]] ?>
                  <a style="width: <?php echo $size[1] ?>px; height: <?php echo $size[2] ?>px" class="left post_attached_photo" onclick="Photoview.show('discuss_post<?php echo $post->post_id ?>', <?php echo $akey ?>)">
                    <img style="margin-left: <?php echo -(($use[3] - $size[1]) / 2) ?>px; margin-top: <?php echo -(($use[4] - $size[2]) / 2) ?>px" src="http://cs<?php echo $use[2] ?>.<?php echo Yii::app()->params['domain'] ?>/<?php echo $use[0] ?>/<?php echo $use[1] ?>" />
                  </a>
                <?php endforeach; ?>
                  <script>
                    Photoview.list('discuss_post<?php echo $post->post_id ?>', <?php echo json_encode($list) ?>);
                  </script>
                <?php
                }
                ?>
              </div>
            <?php endif; ?>
          </div>
        </div>
        <div class="info_footer">
          <span class="rel_date"><?php echo ActiveHtml::timeback($post->add_date) ?></span>
        </div>
      </div>
    </div>
  </div>
<?php endforeach; ?>
              