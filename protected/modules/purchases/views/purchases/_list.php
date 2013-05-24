<?php /** @var $purchase Purchase */ ?>
<?php $page = ($offset + Yii::app()->controller->module->purchasesPerPage) / Yii::app()->controller->module->purchasesPerPage ?>
<?php $added = false; ?>
<?php foreach ($purchases as $purchase): ?>
    <div<?php if(!$added) { echo ' rel="page-'. $page .'"'; $added = true; } ?> class="purchase clearfix">
        <div class="left photo">
            <?php echo ActiveHtml::showUploadImage($purchase->image) ?>
        </div>
        <div class="left info">
          <div class="purchase_labeled name"><?php echo ActiveHtml::link($purchase->name, '/purchase'. $purchase->purchase_id, array('nav' => array('useCache' => true))) ?></div>
          <div class="purchase_labeled">Организатор: <?php echo ActiveHtml::link($purchase->author->login, '/id'. $purchase->author_id) ?></div>
          <div class="purchase_labeled">Город: <?php echo $purchase->city->name ?></div>
          <div class="purchase_labeled">Дата стопа: <?php echo ActiveHtml::date($purchase->stop_date, false) ?></div>
          <div class="purchase_labeled">Статус: <?php echo Yii::t('purchase', $purchase->state) ?></div>
          <div class="purchase_labeled">Кол-во заказов: <?php echo $purchase->ordersNum ?></div>
          <?php if ($purchase->mod_confirmation == 0): ?><div class="input_error">Закупка не одобрена</div><?php endif; ?>
        </div>
      <div class="right"><?php echo $purchase->getMinimalPercentage() ?> %</div>
    </div>
<?php endforeach; ?>
