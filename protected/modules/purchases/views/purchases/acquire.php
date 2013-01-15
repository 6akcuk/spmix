<?php
/** @var $people User */
Yii::app()->getClientScript()->registerCssFile('/css/purchases.css');
Yii::app()->getClientScript()->registerScriptFile('/js/purchase.js');

Yii::app()->getClientScript()->registerCssFile('/css/pagination.css');
Yii::app()->getClientScript()->registerScriptFile('/js/pagination.js');

$this->pageTitle = Yii::app()->name .' - Одобрение закупок';
$delta = Yii::app()->controller->module->purchasesPerPage;
?>
<div class="tabs">
    <?php echo ActiveHtml::link('Новые', '/purchases?action=acquire', array('class' => 'selected')) ?>
</div>
<div class="summary clearfix">
    <?php echo Yii::t('user', '{n} закупка|{n} закупки|{n} закупок', $offsets) ?>
    <div class="right">
    <?php $this->widget('Paginator', array(
        'url' => '/purchases?action=acquire',
        'offset' => $offset,
        'offsets' => $offsets,
        'delta' => $delta,
    )); ?>
    </div>
</div>
<div id="purchases" rel="pagination">
    <?php $this->renderPartial('_acquire', array('purchases' => $purchases, 'offset' => $offset)) ?>
    <? if ($offset + $delta < $offsets && $offsets > $delta): ?><a id="pg_more" class="pg_more" onclick="Paginator.showMore()">Еще закупки</a><? endif; ?>
</div>