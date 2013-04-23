<?php /** @var $post ProfileWallPost */ ?>
<?php $page = ($offset + Yii::app()->getModule('users')->wallPostsPerPage) / Yii::app()->getModule('users')->wallPostsPerPage ?>
<?php $added = false; ?>
<?php foreach ($posts as $post): ?>
  <div<?php if(!$added) { echo ' rel="page-'. $page .'"'; $added = true; } ?> id="wall<?php echo $post->wall_id ?>_<?php echo $post->post_id ?>" onmouseover="Wall.postOver('<?php echo $post->wall_id ?>_<?php echo $post->post_id ?>', event)" onmouseout="Wall.postOut('<?php echo $post->wall_id ?>_<?php echo $post->post_id ?>')" class="wall_post <?php if ($post->author->isOnline()): ?>wall_post_online <?php endif; ?>clearfix">
    <div class="post_table">
      <div class="post_image">
        <?php echo ActiveHtml::link($post->author->profile->getProfileImage('c'), '/id'. $post->author_id, array('class' => 'post_image')) ?>
        <?php if ($post->author->isOnline()): ?><span class="online">Online</span><?php endif; ?>
      </div>
      <div class="post_info">
      <?php if (Yii::app()->user->getId() == $post->author_id || Yii::app()->user->getId() == $post->wall_id): ?>
        <div class="right delete_post_wrap">
          <div class="delete_post">
            <div title="Удалить запись" id="delete_post<?php echo $post->wall_id ?>_<?php echo $post->post_id ?>" onmouseover="Wall.postDeleteOver('<?php echo $post->wall_id ?>_<?php echo $post->post_id ?>')" onmouseout="Wall.postDeleteOut('<?php echo $post->wall_id ?>_<?php echo $post->post_id ?>')" onclick="Wall.deletePost('<?php echo $post->wall_id ?>_<?php echo $post->post_id ?>')" class="icon-remove" style="opacity:0"></div>
          </div>
        </div>
      <?php endif; ?>
        <div class="wall_text">
          <?php echo ActiveHtml::link($post->author->getDisplayName(), '/id'. $post->author_id, array('class' => 'author')) ?>
          <div id="wpt<?php echo $post->wall_id ?>_<?php echo $post->post_id ?>">
            <div class="wall_post_text"><?php echo nl2br($post->post) ?></div>
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
                  $photo_sizes[0] = array('d', min($attaches['photo'][0]['d'][3], 380), min($attaches['photo'][0]['d'][4], 380));
                }
                elseif ($length == 2) {
                  $attaches['photo'][0] = json_decode($attaches['photo'][0], true);
                  $attaches['photo'][1] = json_decode($attaches['photo'][1], true);

                  $min_height = min($attaches['photo'][0]['a'][4], $attaches['photo'][1]['a'][4]);

                  $photo_sizes[0] = array('a', min($attaches['photo'][0]['w'][3], 190), $min_height);
                  $photo_sizes[1] = array('a', min($attaches['photo'][1]['w'][3], 190), $min_height);
                }
                elseif ($length == 3) {
                  $attaches['photo'][0] = json_decode($attaches['photo'][0], true);
                  $attaches['photo'][1] = json_decode($attaches['photo'][1], true);
                  $attaches['photo'][2] = json_decode($attaches['photo'][2], true);

                  $master_height = floor(320 * $attaches['photo'][0]['w'][4]) / $attaches['photo'][0]['w'][3];
                  $slave_height = $master_height / 2;

                  $photo_sizes[0] = array('d', min($attaches['photo'][0]['w'][3], 380), $master_height);
                  $photo_sizes[1] = array('a', min($attaches['photo'][1]['w'][3], 190), $slave_height);
                  $photo_sizes[2] = array('a', min($attaches['photo'][2]['w'][3], 190), $slave_height);
                }
                ?>
                <?php foreach ($attaches['photo'] as $akey => $photo): ?>
                  <?php $list['items'][] = $photo ?>
                  <?php $size = $photo_sizes[$akey] ?>
                  <?php $use = $photo[$size[0]] ?>
                  <a style="width: <?php echo $size[1] ?>px; height: <?php echo $size[2] ?>px" class="left wall_attached_photo" onclick="Photoview.show('wall<?php echo $post->wall_id ?>_<?php echo $post->post_id ?>', <?php echo $akey ?>)">
                    <img style="margin-left: <?php echo -(($use[3] - $size[1]) / 2) ?>px; margin-top: <?php echo -(($use[4] - $size[2]) / 2) ?>px" src="http://cs<?php echo $use[2] ?>.<?php echo Yii::app()->params['domain'] ?>/<?php echo $use[0] ?>/<?php echo $use[1] ?>" />
                  </a>
                <?php endforeach; ?>
                <script>
                  Photoview.list('wall<?php echo $post->wall_id ?>_<?php echo $post->post_id ?>', <?php echo json_encode($list) ?>);
                </script>
              <?php
              }
              ?>
            </div>
            <?php endif; ?>
          </div>
        </div>
        <div class="replies">
          <div class="reply_link_wrap" id="wpe_bottom<?php echo $post->wall_id ?>_<?php echo $post->post_id ?>">
            <small>
              <span class="rel_date"><?php echo ActiveHtml::date($post->add_date, true, true) ?></span>
            </small>
            <?php if ($post->repliesNum == 0): ?>
            <span id="reply_link<?php echo $post->wall_id ?>_<?php echo $post->post_id ?>" class="reply_link">
              <span class="divide">|</span>
              <a class="reply_link" onclick="Wall.showReplyEditor(event, '<?php echo $post->wall_id ?>_<?php echo $post->post_id ?>')">Комментировать</a>
            </span>
            <?php endif; ?>
          </div>
          <div class="replies_wrap" id="replies_wrap<?php echo $post->wall_id ?>_<?php echo $post->post_id ?>">
            <div id="replies<?php echo $post->wall_id ?>_<?php echo $post->post_id ?>">
              <input type="hidden" name="last_id" value="<?php echo ($post->last_replies) ? $post->last_replies[0]->post_id : '' ?>" />
            <?php if ($post->last_replies): ?>
              <?php $post->last_replies = array_reverse($post->last_replies) ?>
              <?php if ($post->repliesNum > 3): ?>
                <a class="wr_header" onclick="Wall.showReplies('<?php echo $post->wall_id ?>_<?php echo $post->post_id ?>', <?php echo $post->last_replies[0]->post_id ?>)">
                  <div class="wrh_text" id="wrh_text<?php echo $post->wall_id ?>_<?php echo $post->post_id ?>">Показать все <?php echo Yii::t('app', '{n} комментарий|{n} комментария|{n} комментариев', $post->repliesNum) ?></div>
                  <div class="wrh_prg" id="wrh_prg<?php echo $post->wall_id ?>_<?php echo $post->post_id ?>"><img src="/images/upload.gif" /></div>
                </a>
              <?php endif; ?>
              <?php echo $this->renderPartial('_reply', array('replies' => $post->last_replies)) ?>
            <?php endif; ?>
            </div>
            <div style="<?php if ($post->repliesNum == 0): ?>display:none<?php endif; ?>" class="reply_fakebox_wrap" id="reply_fakebox<?php echo $post->wall_id ?>_<?php echo $post->post_id ?>" onclick="Wall.showReplyEditor(event, '<?php echo $post->wall_id ?>_<?php echo $post->post_id ?>')">
              <div class="reply_fakebox">Комментировать..</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
<?php endforeach; ?>
