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

<div class="ls_columns">
    <div class="clearfix filters">
        <div class="left dropdown_down">
            <?php echo ActiveHtml::dropdown('c[state]', 'Статус', (isset($c['state'])) ? $c['state'] : Purchase::STATE_ORDER_COLLECTION, Purchase::getStateSearchArray()) ?>
        </div>
        <div class="left dropdown_down">
            <?php echo ActiveHtml::dropdown('c[category_id]', 'Категория', (isset($c['category_id'])) ? $c['category_id'] : '', PurchaseCategory::getDataArray()) ?>
        </div>

    </div>
    <?php $this->renderPartial('_list', array('purchases' => $purchases)) ?>
</div>