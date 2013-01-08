<?php
/** @var $people User */

Yii::app()->getClientScript()->registerCssFile('/css/profile.css');
Yii::app()->getClientScript()->registerCssFile('/css/search.css');

Yii::app()->getClientScript()->registerCssFile('/css/pagination.css');
Yii::app()->getClientScript()->registerScriptFile('/js/pagination.js');

Yii::app()->getClientScript()->registerScriptFile('/js/profile.js');

$this->pageTitle = Yii::app()->name .' - Все подписчики';
$delta = Yii::app()->controller->module->friendsPerPage;
?>
<div class="tabs">
    <?php echo ActiveHtml::link('Все друзья', '/friends') ?>
    <?php echo ActiveHtml::link('Друзья онлайн', '/friends?section=online') ?>
    <?php echo ActiveHtml::link('Заявки в друзья'. (($this->pageCounters['friends'] > 0) ? ' <b>+'. $this->pageCounters['friends'] .'</b>' : ''), '/friends?&section=requests', array('class' => 'selected')) ?>
</div>
<div class="gsearch clearfix">
    <div class="minitabs clearfix">
        <?php echo ActiveHtml::link('Все подписчики', '/friends?section=allRequests') ?>
        <?php echo ActiveHtml::link('Исходящие заявки', '/friends?section=outRequests', array('class' => 'selected')) ?>
        <?php if ($this->pageCounters['friends'] > 0): ?><?php echo ActiveHtml::link('Текущие заявки', '/friends?section=requests') ?><?php endif; ?>
    </div>
    <div style="display: none">
        <?php $this->widget('Paginator', array(
        'url' => '/friends?section=outRequests',
        'offset' => $offset,
        'offsets' => $offsets,
        'delta' => $delta,
        'nopages' => true,
    )); ?>
    </div>
</div>
<div class="summary">
    Вы подали заявку и подписались на <?php echo Yii::t('user', '{n} человека|{n} человек|{n} человек', $offsets) ?>
</div>
<div rel="pagination" class="searchresults">
    <?php echo $this->renderPartial('_request', array('peoples' => $peoples, 'offset' => $offset)) ?>
</div>