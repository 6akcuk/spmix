<?php
/**
 * @var $purchase Purchase
 * @var $good Good
 */

Yii::app()->getClientScript()->registerCssFile('/css/purchases.css');
Yii::app()->getClientScript()->registerScriptFile('/js/purchase.js');

$this->pageTitle = Yii::app()->name .' - Мои покупки';
?>

<h1>Мои покупки</h1>

<div id="tabs">
    <?php echo ActiveHtml::link('Заказы', '/shopping', array('class' => 'selected')) ?>
    <?php echo ActiveHtml::link('Платежи', '/shopping/payments') ?>
</div>
