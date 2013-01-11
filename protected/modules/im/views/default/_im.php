<?php
/**
* @var $message DialogMessage
*/
?>
<?php $page = ($offset + Yii::app()->controller->module->messagesPerPage) / Yii::app()->controller->module->messagesPerPage ?>
<?php $added = false; ?>
<?php foreach ($messages as $message): ?>
<tr id="mess<?php echo $message->message_id ?>"
    class="<?php echo ($message->author_id == Yii::app()->user->getId()) ? 'im_out' : 'im_in'; ?> im_msg_from<?php echo $message->author_id ?> <?php if($message->isNewIn || $message->isNewOut) echo "im_new_msg" ?>"
    date="<?php echo strtotime($message->creation_date) ?>"
    onmouseover="Im.setLogState(1, <?php echo $message->message_id ?>)"
    onmouseout="Im.setLogState(0, <?php echo $message->message_id ?>)"
    onclick="Im.checkLog(<?php echo $message->message_id ?>)"
>
    <td class="im_log_act">
        <div id="ma<?php echo $message->message_id ?>" class="im_log_check_wrap" style="visibility: hidden;">
            <div class="im_log_check" id="mess_check<?php echo $message->message_id ?>"></div>
        </div>
    </td>
    <td class="im_log_author">
        <div class="im_log_author_chat_thumb">
            <?php echo ActiveHtml::link(
                ($message->author->profile->photo)
                    ? ActiveHtml::showUploadImage($message->author->profile->photo, 'c')
                    : '<img src="/images/camera_a.gif" />',
                '/id'. $message->author_id, array('target' => '_blank')) ?>
        </div>
    </td>
    <td class="im_log_body">
        <div class="wrapped">
            <div class="im_log_author_chat_name">
                <?php echo ActiveHtml::link($message->author->profile->firstname, '/id'. $message->author_id, array('target' => '_blank')) ?>
            </div>
            <?php echo nl2br($message->message) ?>
        </div>
    </td>
    <td class="im_log_date">
        <a class="im_date_link" rel="tooltip" title="Отправлено в <?php echo date("H:i", strtotime($message->creation_date)) ?>">
            <?php echo date("d.m.y", strtotime($message->creation_date)) ?>
        </a>
    </td>
    <td class="im_log_rspacer"></td>
</tr>
<?php endforeach; ?>