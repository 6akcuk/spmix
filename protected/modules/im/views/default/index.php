<?php
/** @var $people User */

Yii::app()->getClientScript()->registerCssFile('/css/profile.css');
Yii::app()->getClientScript()->registerCssFile('/css/im.css');

Yii::app()->getClientScript()->registerCssFile('/css/pagination.css');
Yii::app()->getClientScript()->registerScriptFile('/js/pagination.js');

Yii::app()->getClientScript()->registerScriptFile('/js/profile.js');

$this->pageTitle = Yii::app()->name .' - Диалоги';
$delta = Yii::app()->controller->module->dialogsPerPage;
?>
<div class="tabs">
    <?php echo ActiveHtml::link('Диалоги', '/im', array('class' => 'selected')) ?>
</div>
<div class="im_bar clearfix">
    <div rel="filters" class="left">
        <?php echo ActiveHtml::inputPlaceholder('c[name]', (isset($c['name'])) ? $c['name'] : '', array('placeholder' => 'Поиск по диалогам')) ?>
    </div>
    <div class="right">
        <?php echo ActiveHtml::link('Написать сообщение', '/im?sel=-1', array('class' => 'button')) ?>
    </div>
    <div style="display: none">
        <?php $this->widget('Paginator', array(
        'url' => '/friends?section=requests',
        'offset' => $offset,
        'offsets' => $offsets,
        'delta' => $delta,
        'nopages' => true,
    )); ?>
    </div>
</div>
<div class="summary">
    <?php echo Yii::t('user', '{n} диалог|{n} диалога|{n} диалогов', $offsets) ?>
</div>