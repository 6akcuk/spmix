<?php /** @var $purchase Purchase */ ?>
<div id="purchases">
<?php foreach ($purchases as $purchase): ?>
    <div class="purchase clearfix">
        <div class="left photo">
            <?php echo ActiveHtml::showUploadImage($purchase->image) ?>
        </div>
        <div class="left info">
            <?php echo ActiveHtml::link($purchase->name, '/purchase'. $purchase->purchase_id) ?>
            <span>Организатор <?php echo ActiveHtml::link($purchase->author->login, '/id'. $purchase->author_id) ?></span>
            <span>Город: <?php echo $purchase->city->name ?></span>
            <span>Дата стопа: <?php echo ActiveHtml::date($purchase->stop_date, false) ?></span>
            <span>Статус: <?php echo Yii::t('purchase', $purchase->state) ?></span>
        </div>
    </div>
<?php endforeach; ?>
</div>