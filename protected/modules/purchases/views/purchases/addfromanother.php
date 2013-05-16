<?php
/** @var Purchase $purchase */

Yii::app()->getClientScript()->registerCssFile('/css/purchases.css');
Yii::app()->getClientScript()->registerScriptFile('/js/purchase.js');

Yii::app()->getClientScript()->registerCssFile('/css/pagination.css');
Yii::app()->getClientScript()->registerScriptFile('/js/pagination.js');

$this->pageTitle = Yii::app()->name .' - Добавление товара из другой закупки';
$delta = Yii::app()->getModule('purchases')->goodsPerPage;
?>
<div class="breadcrumbs">
  <?php echo ActiveHtml::link($purchase->name, '/purchase'. $purchase->purchase_id) ?> &raquo;
  <?php echo ActiveHtml::link('Список товаров закупки', '/goods'. $purchase->purchase_id) ?> &raquo;
  Добавить товары из другой закупки
</div>
<div>
  <?php echo ActiveHtml::inputPlaceholder('from_id', ($from_id) ?: '', array('placeholder' => 'ID закупки')) ?>
  &nbsp;
  <a class="button" onclick="Purchase.showGoodsFrom(<?php echo $purchase->purchase_id ?>)">Просмотреть</a>
</div>
<?php if($from): ?>
<div class="purchase_name">
  <?php echo $from->name ?>
</div>
<div class="purchase_aa_copy_wrap clearfix">
  <div class="purchase_aa_copy left">
  Скопировать
  <?php echo ActiveHtml::qVKMenu('все товары',
    '
    <a onclick="Purchase.setAACopy(0)">все товары</a>
    <a onclick="Purchase.setAACopy(1)">только видимые</a>
    ',
    array('id' => 'aa_copy_menu')
  ) ?>
  в данную закупку
  </div>
  <div id="purchase_aa_copy_btn" class="button_submit">
    <button onclick="Purchase.copyAA(<?php echo $purchase->purchase_id ?>, <?php echo $from_id ?>)">Скопировать</button>
  </div>
  <div id="purchase_aa_copy_progress" class="left progress"></div>
</div>
<div class="summary_wrap clearfix">
  <div class="right">
    <?php $this->widget('Paginator', array(
      'offset' => $offset,
      'offsets' => $goodsNum,
      'delta' => $delta,
    )); ?>
  </div>
  <div class="left summary">
    <?php echo Yii::t('app', '{n} товар|{n} товара|{n} товаров', $goodsNum) ?>
    <span class="divide">|</span>
    <?php echo ActiveHtml::qVKMenu(($all == 1) ? 'Показывать все товары' : 'Показывать только видимые',
      ActiveHtml::link('Показывать все товары', '/purchase'. $purchase->purchase_id .'/addfromanother?from_id='. $from_id .'&all=1') .
        ActiveHtml::link('Показывать только видимые', '/purchase'. $purchase->purchase_id .'/addfromanother?from_id='. $from_id .'&all=0')
    ) ?>
  </div>
</div>
<div rel="pagination">
  <?php $this->renderPartial('_addgoodfromanother', array('purchase' => $purchase, 'goods' => $goods, 'offset' => $offset)) ?>
  <? if ($offset + $delta < $goodsNum && $goodsNum > $delta): ?><a id="pg_more" class="pg_more" onclick="Paginator.showMore()">Еще товары</a><? endif; ?>
</div>
<?php endif; ?>