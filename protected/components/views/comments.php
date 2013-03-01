<?php
/**
 * Created by JetBrains PhpStorm.
 * User: denis
 * Date: 28.02.13
 * Time: 10:11
 * To change this template use File | Settings | File Templates.
 */
/**
 * @var Comment $comment
 */
Yii::app()->getClientScript()->registerCssFile('/css/comments.css');
Yii::app()->getClientScript()->registerScriptFile('/js/comments.js');

Yii::app()->getClientScript()->registerCssFile('/css/photoview.css');
Yii::app()->getClientScript()->registerScriptFile('/js/photoview.js');
Yii::app()->getClientScript()->registerScriptFile('/js/jquery.cookie.js', null, 'after jquery-');

?>
<?php if ($offsets > 10): ?><a class="comment_show_more">Показать <?php echo Yii::t('app', 'предыдущий {n} комментарий|предыдущие {n} комментария|предыдущие {n} комментариев', ($offsets - 3)) ?></a><?php endif; ?>
<div id="hoop<?php echo $this->hoop_id ?>_comments" class="comments_list">
  <?php foreach ($comments as $comment): ?>
  <div id="comment_<?php echo $comment->comment_id ?>" class="comment_block clearfix">
    <div class="left photo">
      <?php echo ActiveHtml::link($comment->author->profile->getProfileImage('c'), '/id'. $comment->author_id) ?>
    </div>
    <div class="left comment_data">
      <?php echo ActiveHtml::link($comment->author->getDisplayName(), '/id'. $comment->author_id, array('class' => 'comment_author')) ?>
      <div class="comment_text">
        <?php echo nl2br($comment->text) ?>
      </div>
      <div class="comment_attaches clearfix">
      <?php
        $photo_sizes = array();
        $attaches = json_decode($comment->attaches, true);
        $length = sizeof($attaches);
        $list = array('items' => array(), 'count' => $length);
        /*
        if ($length == 1) {
          $photo_sizes[0] = array('w', min($attaches[0]['w'][3], 604), min($attaches[0]['w'][4], 604));
        }
        elseif ($length == 2) {
          $min_height = min($attaches[0]['d'][4], $attaches[1]['d'][4]);

          $photo_sizes[0] = array('d', min($attaches[0]['d'][3], 320), $min_height);
          $photo_sizes[1] = array('d', min($attaches[1]['d'][3], 320), $min_height);
        }
        elseif ($length == 3) {
          $master_height = floor(434 * $attaches[0]['w'][4]) / $attaches[0]['w'][3];
          $slave_height = $master_height / 2;

          $photo_sizes[0] = array('w', min($attaches[0]['w'][3], 434), $master_height);
          $photo_sizes[1] = array('d', min($attaches[1]['d'][3], 210), $slave_height);
          $photo_sizes[2] = array('d', min($attaches[2]['d'][3], 210), $slave_height);
        }*/
      ?>
      <?php foreach ($attaches as $akey => $photo): ?>
      <?php $photo = json_decode($photo, true) ?>
      <?php $list['items'][] = $photo ?>
        <a class="left comment_attached_photo" onclick="Photoview.show('comment<?php echo $comment->comment_id ?>', <?php echo $akey ?>)">
          <img src="http://cs<?php echo $photo['w'][2] ?>.<?php echo Yii::app()->params['domain'] ?>/<?php echo $photo['w'][0] ?>/<?php echo $photo['w'][1] ?>" />
        </a>
      <?php endforeach; ?>
      <script>
      Photoview.list('comment<?php echo $comment->comment_id ?>', <?php echo json_encode($list) ?>);
      </script>
      </div>
      <div class="comment_control">
        <span class="comment_date"><?php echo ActiveHtml::date($comment->creation_date, true, true) ?> |</span>
        <a onclick="Comment.edit(<?php echo $comment->comment_id ?>)">Редактировать</a> |
        <a onclick="Comment.delete(<?php echo $comment->comment_id ?>)">Удалить</a>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>
<div class="comment_reply">
  <form id="hoop<?php echo $this->hoop_id ?>_form" action="comment/add?hoop_id=<?php echo $this->hoop_id ?>&hoop_type=<?php echo $this->hoop_type ?>" method="post">
  <h6>Ваш комментарий</h6>
  <?php echo ActiveHtml::smartTextarea('Comment[text]', '', array('placeholder' => 'Комментировать..')) ?>
  <div id="hoop<?php echo $this->hoop_id ?>_attaches" class="comment_post_attaches clearfix">
  </div>
  <div class="comment_post clearfix">
    <div class="left">
      <a class="button" onclick="Comment.add(<?php echo $this->hoop_id ?>)">Отправить</a>
    </div>
    <div id="comment<?php echo $this->hoop_id ?>_progress" class="left comment_post_progress">
      <img src="/images/upload.gif" />
    </div>
    <div class="right">
      <?php echo ActiveHtml::upload('photo', '', 'Прикрепить фотографию', array('onchange' => 'Comment.attachPhoto('. $this->hoop_id .', {id})')) ?>
    </div>
  </div>
  </form>
  <script>
  $.extend(A, {
    commentPhotoAttaches: 0,
    commentHoop: {
      <?php echo $this->hoop_id ?>: [null, 0, 68876]
    }
  });
  </script>
</div>