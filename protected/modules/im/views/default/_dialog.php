<?php /** @var $dialog Dialog */ ?>
<?php $page = ($offset + Yii::app()->controller->module->dialogsPerPage) / Yii::app()->controller->module->dialogsPerPage ?>
<?php $added = false; ?>
<?php foreach ($dialogs as $dialog): ?>
<div class="dialogs_row" id="im_dialog">
    <div class="dialogs_del_wrap">
        <div class="dialogs_del"></div>
    </div>
    <table class="dialogs_row_t">
        <tr>
            <td class="dialogs_photo">
                <?php echo ActiveHtml::link(
                    ($dialog->leader->profile->photo)
                        ? ActiveHtml::showUploadImage($dialog->leader->profile->photo, 'c')
                        : '<img src="/images/camera_a.gif" />', '/id'. $dialog->leader_id) ?>
            </td>
            <td class="dialogs_info">
                <div class="dialogs_user">
                    <?php echo ActiveHtml::link($dialog->leader->getDisplayName(), '/id'. $dialog->leader->id) ?>
                </div>
                <?php if ($dialog->leader->isOnline()): ?>
                <div class="dialogs_online">Online</div>
                <?php endif; ?>
                <div class="dialogs_date">
                    <?php if($dialog->lastMessage) echo ActiveHtml::date($dialog->lastMessage->creation_date, true, true) ?>
                </div>
            </td>
            <td class="dialogs_msg_contents">
                <div class="dialogs_msg_body clearfix">
                <?php if($dialog->lastMessage): ?>
                    <?php if ($dialog->lastMessage->author_id != $dialog->leader_id): ?>
                    <?php echo ($dialog->lastMessage->author->profile->photo)
                            ? ActiveHtml::showUploadImage($dialog->lastMessage->author->profile->photo, 'c', array('class' => 'left dialogs_inline_author'))
                            : '<img src="/images/camera_a.gif" class="left dialogs_inline_author" />' ?>
                    <?php endif; ?>
                    <div class="dialogs_msg_text wrapped left">
                        <?php echo (mb_strlen($dialog->lastMessage->message, 'utf-8') < 90)
                            ? nl2br($dialog->lastMessage->message)
                            : nl2br(mb_substr($dialog->lastMessage->message, 0, 90, 'utf-8') .'..') ?>
                    </div>
                <?php endif; ?>
                </div>
            </td>
        </tr>
    </table>
</div>
<?php endforeach; ?>