<?php /** @var Good $good */ ?>
<?php $page = ($offset + Yii::app()->getModule('purchases')->goodsPerPage) / Yii::app()->getModule('purchases')->goodsPerPage ?>
<?php $added = false; ?>
<?php foreach ($goods as $good): ?>
<div id="good<?php echo $good->good_id ?>"<?php if(!$added) { echo ' rel="page-'. $page .'"'; $added = true; } ?> class="good_many clearfix">
  <div class="photo">
    <?php echo ActiveHtml::showUploadImage(($good->image) ? $good->image->image : '') ?>
  </div>
  <div class="info">
  <form id="good<?php echo $good->good_id ?>_form" action="/purchase<?php ?>" method="post">
    <div class="good_many_row clearfix">
      <div class="left good_many_label">Название:</div>
      <div class="left"><?php echo ActiveHtml::textField('Good[name]', $good->name) ?></div>
    </div>
    <div class="good_many_row clearfix">
      <div class="left good_many_label">Цена:</div>
      <div class="left"><?php echo ActiveHtml::textField('Good[price]', $good->price) ?></div>
    </div>
    <div class="good_many_row clearfix">
      <div class="left good_many_label">Доставка:</div>
      <div class="left"><?php echo ActiveHtml::textField('Good[delivery]', $good->delivery) ?></div>
    </div>
    <div class="good_many_buttons clearfix">
      <div class="button_submit left">
        <button onclick="return Purchase.addPurchaseGood(<?php echo $purchase->purchase_id ?>, <?php echo $good->good_id ?>)">Добавить</button>
      </div>
      <a class="button_cancel left" onclick="Purchase.hidePurchaseGood(<?php echo $good->good_id ?>)">Скрыть</a>
      <div id="good<?php echo $good->good_id ?>_progress" class="left progress"></div>
    </div>
  </form>
  </div>
</div>
<?php endforeach; ?>
