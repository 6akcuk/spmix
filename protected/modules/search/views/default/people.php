<?php
/** @var $people User */

Yii::app()->getClientScript()->registerCssFile('/css/profile.css');
Yii::app()->getClientScript()->registerCssFile('/css/search.css');

Yii::app()->getClientScript()->registerCssFile('/css/pagination.css');
Yii::app()->getClientScript()->registerScriptFile('/js/pagination.js');

$this->pageTitle = Yii::app()->name .' - Люди';
$delta = Yii::app()->controller->module->peoplesPerPage;
?>
<div class="gsearch clearfix">
    <div rel="filters" class="left">
        <?php echo ActiveHtml::inputPlaceholder('c[name]', (isset($c['name'])) ? $c['name'] : '', array('placeholder' => 'Поиск по имени')) ?>
    </div>
    <div class="left">
        <a class="button" onclick="return nav.go(this, event, null)">Поиск</a>
    </div>
    <div style="display: none">
    <?php $this->widget('Paginator', array(
        'url' => '/search?c[section]=people',
        'offset' => $offset,
        'offsets' => $offsets,
        'delta' => $delta,
        'nopages' => true,
    )); ?>
    </div>
</div>
<table class="gsearch_table">
<tr>
    <td rel="pagination" class="searchresults">
    <?php echo $this->renderPartial('_people', array('peoples' => $peoples, 'offset' => $offset)) ?>
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