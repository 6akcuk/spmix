<?php /** @var $purchase Purchase */ ?>
<?php foreach ($purchases as $purchase): ?>
<?php
    $sum_perc = ceil((floatval($purchase->min_sum) > 0) ? ($purchase->ordersSum / $purchase->min_sum) * 100 : 0);
    $num_perc = ceil((floatval($purchase->min_num) > 0) ? ($purchase->ordersNum / $purchase->min_num) * 100 : 0);
?>
<tr>
    <td><?php echo $purchase->purchase_id ?></td>
    <td><?php echo ActiveHtml::date($purchase->create_date, false, true) ?></td>
    <td><?php echo $purchase->category->name ?></td>
    <td>
        <?php echo ActiveHtml::link($purchase->name, '/purchase'. $purchase->purchase_id) ?>
        <div><small><?php echo ActiveHtml::link('редактировать', '/purchase'. $purchase->purchase_id .'/edit') ?></small></div>
    </td>
    <td style="position: relative">
        <a data-id="<?php echo $purchase->purchase_id ?>" onclick="changeState(this)">
            <?php echo Yii::t('purchase', $purchase->state) ?>
        </a>
    </td>
    <td><?php echo ActiveHtml::date($purchase->stop_date, false, true) ?></td>
    <td><?php echo ActiveHtml::link(Yii::t('purchase', '{n} товар|{n} товара|{n} товаров', $purchase->goodsNum), '/goods'. $purchase->purchase_id) ?></td>
    <td>
        <?php echo ActiveHtml::link(Yii::t('purchase', '{n} заказ|{n} заказа|{n} заказов', $purchase->ordersNum), '/orders'. $purchase->purchase_id) ?>
        <br/>
        <?php echo ActiveHtml::price($purchase->ordersSum) ?>
    </td>
    <td><?php echo $purchase->getMinimalPercentage() ?>%</td>
</tr>
<?php endforeach; ?>