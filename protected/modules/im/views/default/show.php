<?php
/**
 * @var $dialog Dialog
 * @var $message DialogMessage
 */

Yii::app()->getClientScript()->registerCssFile('/css/profile.css');
Yii::app()->getClientScript()->registerCssFile('/css/im.css');

Yii::app()->getClientScript()->registerCssFile('/css/pagination.css');
Yii::app()->getClientScript()->registerScriptFile('/js/pagination.js');

Yii::app()->getClientScript()->registerScriptFile('/js/im.js');

$this->pageTitle = Yii::app()->name .' - Просмотр диалога';
$delta = Yii::app()->controller->module->messagesPerPage;
?>
<div id="im_nav">
    <div class="tabs">
        <?php echo ActiveHtml::link('Диалоги', '/im') ?>
        <?php echo ActiveHtml::link('Просмотр диалога', '/im?sel='. $dialog->dialog_id, array('class' => 'selected')) ?>
    </div>
    <div class="im_bar clearfix">
        <div class="summary_tab2"><?php ?></div>
        <div style="display: none">
            <?php $this->widget('Paginator', array(
            'url' => '/im?sel='. $dialog->dialog_id,
            'offset' => $offset,
            'offsets' => $offsets,
            'delta' => $delta,
            'nopages' => true,
        )); ?>
        </div>
    </div>
</div>
<div id="im_content"></div>
<div id="im_controls">

</div>

<script type="text/javascript">
Im.setup(<?php echo $dialog->dialog_id ?>);
</script>