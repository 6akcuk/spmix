<?php
/** @var $model Purchase */

Yii::app()->getClientScript()->registerCssFile('/css/purchases.css');
Yii::app()->getClientScript()->registerScriptFile('/js/purchase.js');

$this->pageTitle = Yii::app()->name .' - Мои закупки';
?>

<h1>
    Мои закупки
    <?php echo ActiveHtml::link('Все закупки', '/purchases', array('class' => 'right')) ?>
</h1>

<div class="ls_columns clearfix">
    <div class="left column">
        <div class="search">
            <span class="iconify iconify_search_b"></span>
            <input type="text" name="q" data-url="" value="" />
            <div class="progress"></div>
        </div>
        <?php $this->renderPartial('_listtable', array('purchases' => $purchases)) ?>
    </div>
    <div class="left column options">
        <div>
            <?php echo ActiveHtml::dropdown('c[state]', 'Статус', null, Purchase::getStateDataArray()) ?>
        </div>
        <div>
            <?php echo ActiveHtml::dropdown('c[category_id]', 'Категория', '', PurchaseCategory::getDataArray()) ?>
        </div>
    </div>
</div>