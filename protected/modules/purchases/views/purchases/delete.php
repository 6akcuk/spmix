<?php
/**
 * @var $purchase Purchase
 */

Yii::app()->getClientScript()->registerCssFile('/css/purchases.css');
Yii::app()->getClientScript()->registerScriptFile('/js/purchase.js');

$this->pageTitle = Yii::app()->name .' - '. $purchase->name;
?>
<div class="big_informer">
    Закупка удалена. <a onclick="Purchase.restore(<?php echo $purchase->purchase_id ?>)">Восстановить</a>
</div>