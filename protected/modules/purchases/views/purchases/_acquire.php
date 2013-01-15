<?php /** @var $purchase Purchase */ ?>
<?php $page = ($offset + Yii::app()->controller->module->purchasesPerPage) / Yii::app()->controller->module->purchasesPerPage ?>
<?php $added = false; ?>
<?php foreach ($purchases as $purchase): ?>
<div id="purchase<?php echo $purchase->purchase_id ?>"<?php if(!$added) { echo ' rel="page-'. $page .'"'; $added = true; } ?> class="purchase clearfix">
    <div class="clearfix">
        <div class="purchase_name">
            <span class="list_name"><?php echo ActiveHtml::link($purchase->name, '/purchase'. $purchase->purchase_id, array('target' => '_blank')) ?></span>
        </div>
        <div class="left photo">
            <?php echo ActiveHtml::showUploadImage($purchase->image) ?>
        </div>
        <div class="left info">
            <span>Организатор: <?php echo ActiveHtml::link($purchase->author->getDisplayName(), '/id'. $purchase->author_id) ?></span>
            <span>Категория: <?php echo $purchase->category->name ?></span>
            <span>Город: <?php echo $purchase->city->name ?></span>
            <span>Дата стопа: <?php echo ActiveHtml::date($purchase->stop_date, false) ?></span>
            <span>Статус: <?php echo Yii::t('purchase', $purchase->state) ?></span>
            <span>
                Кол-во заказов: <?php echo $purchase->ordersNum ?>
                <div class="right"><?php echo $purchase->getMinimalPercentage() ?> %</div>
            </span>
        </div>
    </div>
    <div>
        <a class="button" onclick="return Purchase.acquire(<?php echo $purchase->purchase_id ?>)">Одобрить</a>
        <a id="purchase<?php echo $purchase->purchase_id ?>_warning" class="button" onclick="return Purchase.sendWarning(<?php echo $purchase->purchase_id ?>)">Есть замечания</a>
        <?php echo ActiveHtml::link('Редактировать', '/purchase'. $purchase->purchase_id .'/edit', array('class' => 'button')) ?>
    </div>
</div>
<?php endforeach; ?>