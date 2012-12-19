<?php /** @var $purchase Purchase */ ?>
<div id="purchases">
    <?php foreach ($purchases as $purchase): ?>
    <?php
        $sum_perc = ceil((floatval($purchase->min_sum) > 0) ? ($purchase->ordersSum / $purchase->min_sum) * 100 : 0);
        $num_perc = ceil((floatval($purchase->min_num) > 0) ? ($purchase->ordersNum / $purchase->min_num) * 100 : 0);
    ?>
    <div class="purchase clearfix">
        <div class="left info">
            <?php echo ActiveHtml::link($purchase->name, '/purchase'. $purchase->purchase_id) ?>
            <span>Создана: <?php echo ActiveHtml::date($purchase->create_date) ?></span>
            <span>Дата стопа: <?php echo ActiveHtml::date($purchase->stop_date, false) ?></span>
            <span>Статус: <?php echo Yii::t('purchase', $purchase->state) ?></span>
            <span>Заказы: <?php echo $purchase->ordersNum ?></span>
            <span>Сумма заказов: <?php echo ActiveHtml::price($purchase->ordersSum) ?></span>
            <?php if ($num_perc > 0 || $sum_perc > 0): ?><span>% от минималки: <?php echo ($num_perc > $sum_perc) ? $num_perc : $sum_perc ?>%</span><?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>