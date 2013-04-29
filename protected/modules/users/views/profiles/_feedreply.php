<?php
/** @var Feed $feed */
?>
<div class="feedback_answered_row_wrap" id="feedback_row_wrap_wall_reply<?php echo $feed->content->wall_id ?>_<?php echo $feed->content->post_id ?>">
  <div class="feedback_row_wrap" id="feedback_row_wall_reply<?php echo $feed->content->wall_id ?>_<?php echo $feed->content->post_id ?>">
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
                <div id="wpt<?php echo $feed->content->wall_id ?>_<?php echo $feed->content->post_id ?>">
                  <div class="wall_reply_text">

                  </div>
                </div>
              </div>
            </div>
          </td>
        </tr>
      </table>
    </div>
  </div>
</div>