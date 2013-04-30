<?php
/** @var Feed $feed */
switch ($feed->content->hoop_type) {
  case 'purchase':
    $basic_url = 'purchase'. $feed->content->hoop_id;
    $hoop = 'закупке';
    break;
  case 'good':
    $basic_url = 'good'. $feed->content->good->purchase_id .'_'. $feed->content->hoop_id;
    $hoop = 'товару';
    break;
}
$comment_url = $basic_url .'?reply='. $feed->content->comment_id;
$el_id = $feed->content->comment_id;
?>
<div class="feedback_answered_row_wrap" id="feedback_row_wrap_comment_reply<?php echo $el_id ?>">
  <div class="feedback_row_wrap" id="feedback_row_wall_reply<?php echo $el_id ?>">
    <div class="feedback_row">
      <table class="feedback_row_t">
        <tr>
          <td class="feedback_row_photo">
            <div class="feedback_row_photo" onclick="event.cancelBubble = true">
              <?php echo ActiveHtml::link($feed->content->author->profile->getProfileImage('c'), '/id'. $feed->content->author_id) ?>
            </div>
          </td>
          <td class="feedback_row_content">
            <div class="feedback_row_content">
              <?php echo ActiveHtml::link($feed->content->author->getDisplayName(), '/id'. $feed->content->author_id) ?>
              <div class="feedback_row_text">
                <div id="wpt<?php echo $el_id ?>">
                  <div class="wall_reply_text">
                    <?php echo nl2br($feed->content->text) ?>
                  </div>
                </div>
              </div>
              <div class="feedback_row_date">
                <?php echo ActiveHtml::link('<span class="rel_date">'. ActiveHtml::date($feed->content->creation_date, true, true) .'</span>', $comment_url) ?>
                в комментариях к <?php echo $hoop ?>
                <?php
                if ($feed->content->hoop_type == 'purchase'):
                  echo ActiveHtml::link(mb_substr($feed->content->purchase->name, 0, 40) .'..', $comment_url);
                elseif ($feed->content->hoop_type == 'good'):
                  echo ActiveHtml::link(mb_substr($feed->content->good->name, 0, 40) .'..', $comment_url);
                endif;
                ?>
              </div>
            </div>
          </td>
        </tr>
      </table>
    </div>
  </div>
</div>