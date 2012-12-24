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
    <div>
        <div class="left dropdown_down">
            <?php echo ActiveHtml::dropdown('c[state]', 'Статус', Purchase::STATE_ORDER_COLLECTION, Purchase::getStateSearchArray()) ?>
        </div>
        <div class="left dropdown_down">
            <?php echo ActiveHtml::dropdown('c[category_id]', 'Категория', '', PurchaseCategory::getDataArray()) ?>
        </div>
        <?php $this->renderPartial('_list', array('purchases' => $purchases)) ?>
    </div>

</div>