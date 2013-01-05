<?php /** @var $purchase Purchase */ ?>
<?php $page = ($offset + Yii::app()->controller->module->purchasesPerPage) / Yii::app()->controller->module->purchasesPerPage ?>
<?php $added = false; ?>
<?php foreach ($purchases as $purchase): ?>
    <div<?php if(!$added) { echo ' rel="page-'. $page .'"'; $added = true; } ?> class="purchase clearfix">
        <div class="purchase_name">
            <span class="list_name"><?php echo ActiveHtml::link($purchase->name, '/purchase'. $purchase->purchase_id) ?></span>
        </div>
        <div class="left photo">
            <?php echo ActiveHtml::showUploadImage($purchase->image) ?>
        </div>
        <div class="left info">
            <span>Организатор: <?php echo ActiveHtml::link($purchase->author->login, '/id'. $purchase->author_id) ?></span>
            <span>Город: <?php echo $purchase->city->name ?></span>
            <span>Дата стопа: <?php echo ActiveHtml::date($purchase->stop_date, false) ?></span>
            <span>Статус: <?php echo Yii::t('purchase', $purchase->state) ?></span>
            <span>
                Кол-во заказов: <?php echo $purchase->ordersNum ?>
                <div class="right"><?php echo $purchase->getMinimalPercentage() ?> %</div>
            </span>
        </div>
    </div>
<?php endforeach; ?>
