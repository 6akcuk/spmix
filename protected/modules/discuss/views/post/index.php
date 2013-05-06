<?php

Yii::app()->getClientScript()->registerCssFile('/css/discuss.css');
Yii::app()->getClientScript()->registerScriptFile('/js/discuss.js');

Yii::app()->getClientScript()->registerCssFile('/css/pagination.css');
Yii::app()->getClientScript()->registerScriptFile('/js/pagination.js');

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
<div class="summary">
  <span>В теме <?php echo Yii::t('app', '{n} сообщение|{n} сообщения|{n} сообщений', $postsNum) ?></span>
  <div class="right">
    <?php $this->widget('Paginator', array(
      'url' => '/discuss'. $forum->forum_id .'_'. $theme->theme_id,
      'offset' => $offset,
      'offsets' => $postsNum,
      'delta' => $delta,
    )); ?>
  </div>
</div>
<div id="posts_rows" rel="pagination">
  <?php $this->renderPartial('_posts', array('posts' => $posts, 'offset' => $offset)) ?>
</div>
<? if ($offset + $delta < $postsNum && $postsNum > $delta): ?><a id="pg_more" class="pg_more" onclick="Paginator.showMore()">Еще сообщения</a><? endif; ?>
<div id="discuss_post">
  <div id="discuss_post_sh"></div>
  <table>
    <tr>
      <td class="discuss_post_thumb_td">
        <?php echo ActiveHtml::link(Yii::app()->user->model->profile->getProfileImage('c'), '/id'. Yii::app()->user->getId(), array('class' => 'discuss_post_thumb')) ?>
      </td>
      <td class="discuss_post_info">
        <textarea id="discuss_text" style="overflow: hidden; resize: none; height: 42px"></textarea>
        <div id="bnt_attaches" class="discuss_attaches clearfix"></div>
        <div class="discuss_buttons clearfix">
          <a class="button left" onclick="Discuss.sendPost()">Отправить</a>
          <div id="bnt_progress" class="upload left"><img src="/images/upload.gif" /></div>
          <div class="right">
            <?php echo ActiveHtml::upload('photo', '', 'Прикрепить фотографию', array('onchange' => 'Discuss.attachPhoto({id})')) ?>
          </div>
        </div>
      </td>
    </tr>
  </table>
</div>