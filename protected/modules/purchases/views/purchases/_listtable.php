<?php /** @var $purchase Purchase */ ?>
<div id="purchases">
    <?php foreach ($purchases as $purchase): ?>
    <div class="purchase clearfix">
        <div class="left info">
            <?php echo ActiveHtml::link($purchase->name, '/purchase'. $purchase->purchase_id) ?>
            <span>Создана: <?php echo ActiveHtml::date($purchase->create_date) ?></span>
            <span>Дата стопа: <?php echo ActiveHtml::date($purchase->stop_date, false) ?></span>
            <span>Статус: <?php echo Yii::t('purchase', $purchase->state) ?></span>
            <span>Заказы: <?php echo $purchase->ordersNum ?></span>
        </div>
    </div>
    <?php endforeach; ?>
</div>