<?php
/** @var $people User */

Yii::app()->getClientScript()->registerCssFile('/css/profile.css');
Yii::app()->getClientScript()->registerCssFile('/css/search.css');

Yii::app()->getClientScript()->registerCssFile('/css/pagination.css');
Yii::app()->getClientScript()->registerScriptFile('/js/pagination.js');

Yii::app()->getClientScript()->registerScriptFile('/js/profile.js');

$this->pageTitle = Yii::app()->name .' - Друзья';
$delta = Yii::app()->controller->module->friendsPerPage;
?>
<div class="tabs">
    <?php echo ActiveHtml::link('Все друзья', '/friends?id='. $user->id) ?>
    <?php echo ActiveHtml::link('Друзья онлайн', '/friends?id='. $user->id .'&section=online', array('class' => 'selected')) ?>
    <?php if ($user->id == Yii::app()->user->getId()): ?>
    <?php echo ActiveHtml::link('Заявки в друзья'. (($this->pageCounters['friends'] > 0) ? ' <b>+'. $this->pageCounters['friends'] .'</b>' : ''), '/friends?id='. $user->id .'&section=requests') ?>
    <?php endif; ?>
</div>
<div class="gsearch clearfix">
    <div rel="filters" class="left">
        <?php echo ActiveHtml::inputPlaceholder('c[name]', (isset($c['name'])) ? $c['name'] : '', array('placeholder' => 'Поиск друга по имени')) ?>
    </div>
    <div class="left">
        <a class="button" onclick="return nav.go(this, event, null)">Поиск</a>
    </div>
    <div style="display: none">
        <?php $this->widget('Paginator', array(
        'url' => '/friends?id='. $user->id .'&section=online',
        'offset' => $offset,
        'offsets' => $offsets,
        'delta' => $delta,
        'nopages' => true,
    )); ?>
    </div>
</div>
<div class="summary">
    У <?php if ($user->id == Yii::app()->user->getId()): ?>Вас<?php else: ?><?php echo $user->login ?><?php endif; ?> <?php echo Yii::t('user', '{n} друг|{n} друга|{n} друзей', $offsets) ?> онлайн
</div>
<table class="gsearch_table">
    <tr>
        <td rel="pagination" class="searchresults">
            <?php echo $this->renderPartial('_people', array('user' => $user, 'peoples' => $peoples, 'offset' => $offset)) ?>
        </td>
        <td class="filters">
            <div rel="filters">
                <?php echo ActiveHtml::dropdown('c[city_id]', 'Город', (isset($c['city_id'])) ? $c['city_id'] : '', City::getDataArray()) ?>
            </div>
            <div rel="filters">
                <?php echo ActiveHtml::dropdown('c[role]', 'Роль', (isset($c['role'])) ? $c['role'] : '', RbacItem::getSearchRoleArray()) ?>
            </div>
        </td>
    </tr>
</table>