<?php

Yii::app()->getClientScript()->registerCssFile('/css/discuss.css');
Yii::app()->getClientScript()->registerScriptFile('/js/discuss.js');

Yii::app()->getClientScript()->registerCssFile('/css/pagination.css');
Yii::app()->getClientScript()->registerScriptFile('/js/pagination.js');

Yii::app()->getClientScript()->registerCssFile('/css/photoview.css');
Yii::app()->getClientScript()->registerScriptFile('/js/photoview.js');

$this->pageTitle = Yii::app()->name .' - '. $theme->title;
$delta = Yii::app()->getModule('discuss')->postsPerPage;
?>
<div class="tabs">
  <?php echo ActiveHtml::link('Обсуждения', '/discuss') ?>
  <?php echo ActiveHtml::link('Просмотр темы', '/discuss'. $forum->forum_id .'_'. $theme->theme_id, array('class' => 'selected')) ?>
</div>
<div class="discuss_header">
  <?php echo ActiveHtml::link($theme->title, '/discuss'. $forum->forum_id .'_'. $theme->theme_id, array('id' => 'discuss_title')) ?>
  <div class="discuss_author">
    <?php echo ActiveHtml::link($theme->author->getDisplayName(), '/id'. $theme->author_id, array('class' => 'mem_link')) ?>
  </div>
</div>
<div class="summary_wrap">
  <div class="right">
    <?php $this->widget('Paginator', array(
      'url' => '/discuss'. $forum->forum_id .'_'. $theme->theme_id,
      'offset' => $offset,
      'offsets' => $postsNum,
      'delta' => $delta,
    )); ?>
  </div>
  <div class="summary">В теме <?php echo Yii::t('app', '{n} сообщение|{n} сообщения|{n} сообщений', $postsNum) ?></div>
</div>
<div id="posts_rows" rel="pagination">
  <?php $this->renderPartial('_posts', array('_post' => $_post, 'posts' => $posts, 'offset' => $offset)) ?>
</div>
<? if ($offset + $delta < $postsNum && $postsNum > $delta): ?><a id="pg_more" class="pg_more" onclick="Paginator.showMore()">Еще сообщения</a><? endif; ?>
<script type="text/javascript">
  A.discussPostFixed = false;
  A.postEditing = false;
  A.postEditingProgress = false;
  A.postDeleting = false;
  <?php if($scroll == 1): ?>A.pageScroll = true; $(window).scrollTop(65500);<?php endif; ?>
</script>
<div id="discuss_fixer"></div>
<div id="discuss_post_wrap">
  <div id="discuss_post">
    <div id="discuss_post_sh"></div>
    <table>
      <tr>
        <td class="discuss_post_thumb_td">
          <?php echo ActiveHtml::link(Yii::app()->user->model->profile->getProfileImage('c'), '/id'. Yii::app()->user->getId(), array('class' => 'discuss_post_thumb')) ?>
        </td>
        <td class="discuss_post_info">
          <?php echo ActiveHtml::smartTextarea('discuss_text', '', array('onkeypress' => 'onCtrlEnter(event, function() { Discuss.sendPost(\''. $forum->forum_id .'_'. $theme->theme_id .'\') })', 'onkeyup' => 'Discuss.onKeyUp()', 'style' => 'overflow: hidden; resize: none; height: 42px')) ?>
          <div id="bnt_attaches" class="discuss_attaches clearfix"></div>
          <div class="discuss_buttons clearfix">
            <a class="button left" onclick="Discuss.sendPost('<?php echo $forum->forum_id ?>_<?php echo $theme->theme_id ?>')">Отправить</a>
            <a id="discuss_cancel" class="left button_cancel" onclick="Discuss.cancelPost()" style="display: none">Отмена</a>
            <div id="bnt_progress" class="upload left"><img src="/images/upload.gif" /></div>
            <div class="right">
              <?php echo ActiveHtml::upload('photo', '', 'Прикрепить фотографию', array('onchange' => 'Discuss.attachPhoto(\'#bnt_attaches\', {id})')) ?>
            </div>
          </div>
        </td>
      </tr>
    </table>
  </div>
</div>