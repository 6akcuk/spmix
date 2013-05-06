<?php
/**
 * @var DiscussPost $post
 */
$delta = Yii::app()->getModule('discuss')->postsPerPage;
?>
<?php foreach ($posts as $post): ?>
<div id="discuss_post<?php echo $post->post_id ?>" class="discuss_post">
  <table>
    <tr>
      <td class="discuss_post_thumb_td">
        <?php echo ActiveHtml::link($post->author->profile->getProfileImage('c'), '/id'. $post->author_id, array('class' => 'discuss_post_thumb')) ?>
      </td>
      <td class="discuss_post_info">
        <div class="discuss_post_author_wrap">
          <?php echo ActiveHtml::link($post->author->getDisplayName(), '/id'. $post->author_id, array('class' => 'discuss_post_author')) ?>
        </div>
        <div id="discuss_post_data<?php echo $post->post_id ?>">
          <div class="discuss_post_text">
            <?php echo nl2br($post->post) ?>
          </div>
        </div>
        <div class="discuss_post_bottom clearfix">
          <div class="left">
            <?php echo ActiveHtml::link(ActiveHtml::date($post->add_date), '/discuss'. $post->forum_id .'_'. $post->theme_id .'?post='. $post->post_id, array('class' => 'discuss_post_date')) ?>
            <span class="divide">|</span>
            <a onclick="Discuss.replyPost(<?php echo $post->post_id ?>)">Ответить</a>
          </div>
        </div>
      </td>
    </tr>
  </table>
</div>
<?php endforeach; ?>