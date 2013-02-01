<?php /** @var $purchase Purchase */ ?>
<?php $page = ($offset + Yii::app()->controller->module->purchasesPerPage) / Yii::app()->controller->module->purchasesPerPage ?>
<?php $added = false; ?>
<?php foreach ($purchases as $purchase): ?>
<div id="purchase<?php echo $purchase->purchase_id ?>"<?php if(!$added) { echo ' rel="page-'. $page .'"'; $added = true; } ?> class="purchase clearfix">
    <div class="clearfix">
        <div class="left photo">
            <?php echo ActiveHtml::showUploadImage($purchase->image) ?>
        </div>
        <div class="left info">
          <div class="purchase_labeled name"><?php echo ActiveHtml::link($purchase->name, '/purchase'. $purchase->purchase_id, array('target' => '_blank')) ?></div>
          <div class="purchase_labeled">Организатор: <?php echo ActiveHtml::link($purchase->author->getDisplayName(), '/id'. $purchase->author_id) ?></div>
          <div class="purchase_labeled">Категория: <?php echo $purchase->category->name ?></div>
          <div class="purchase_labeled">Город: <?php echo $purchase->city->name ?></div>
          <div class="purchase_labeled">Дата стопа: <?php echo ActiveHtml::date($purchase->stop_date, false) ?></div>
          <div class="purchase_labeled">Статус: <?php echo Yii::t('purchase', $purchase->state) ?></div>
          <div class="purchase_labeled">Кол-во заказов: <?php echo $purchase->ordersNum ?></div>
        </div>
        <div class="right"><?php echo $purchase->getMinimalPercentage() ?> %</div>
    </div>
    <div>
        <a class="button" onclick="return Purchase.acquire(<?php echo $purchase->purchase_id ?>)">Одобрить</a>
        <a id="purchase<?php echo $purchase->purchase_id ?>_warning" class="button" onclick="return Purchase.sendWarning(<?php echo $purchase->purchase_id ?>)">Есть замечания</a>
        <?php echo ActiveHtml::link('Редактировать', '/purchase'. $purchase->purchase_id .'/edit', array('class' => 'button')) ?>
    </div>
</div>
<?php endforeach; ?>