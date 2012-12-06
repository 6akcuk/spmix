<?php
/** @var $model Purchase */

Yii::app()->getClientScript()->registerCssFile('/css/purchases.css');
Yii::app()->getClientScript()->registerScriptFile('/js/purchase.js');

$this->pageTitle = Yii::app()->name .' - Закупки';
?>

<h1>
    Закупки
    <?php echo ActiveHtml::link('Мои закупки', '/purchases/my', array('class' => 'right')) ?>
</h1>

<div class="ls_columns clearfix">
    <div class="left column">
        <div class="search">
            <span class="iconify iconify_search_b"></span>
            <input type="text" name="q" data-url="" value="" />
            <div class="progress"></div>
        </div>
        <?php $this->renderPartial('_list', array('purchases' => $purchases)) ?>
    </div>
    <div class="left column options">
        <div>
            <?php echo ActiveHtml::dropdown('c[state]', 'Статус', Purchase::STATE_ORDER_COLLECTION, Purchase::getStateSearchArray()) ?>
        </div>
        <div>
            <?php echo ActiveHtml::dropdown('c[category_id]', 'Категория', '', PurchaseCategory::getDataArray()) ?>
        </div>
    </div>
</div>