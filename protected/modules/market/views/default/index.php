<?php
/* @var $this DefaultController
 * @var PurchaseCategory $category
 */

Yii::app()->getClientScript()->registerCssFile('/css/market.css');

Yii::app()->getClientScript()->registerCssFile('/css/pagination.css');
Yii::app()->getClientScript()->registerScriptFile('/js/pagination.js');

Yii::app()->getClientScript()->registerScriptFile('/js/market.js');

$this->pageTitle = Yii::app()->name .' - Пристрой';

$delta = Yii::app()->getModule('market')->goodsPerPage;
?>
<div class="tabs">
  <?php echo ActiveHtml::link('Пристрой организаторов', '/market?org=1', array('class' => ($org == 1 && $used == 0) ? 'selected' : '')) ?>
  <?php echo ActiveHtml::link('Пристрой участников', '/market?org=0', array('class' => ($org == 0 && $used == 0) ? 'selected' : '')) ?>
  <?php echo ActiveHtml::link('Барахолка', '/market?used=1', array('class' => ($used == 1) ? 'selected' : '')) ?>
  <div class="right">
    <?php echo ActiveHtml::link('Добавить товар', '/market?act=add') ?>
  </div>
</div>
<div rel="filters" class="market_search">
  <?php echo ActiveHtml::inputPlaceholder('c[q]', (isset($c['q'])) ? $c['q'] : '', array('placeholder' => 'Поиск по ключевым словам')) ?>
</div>
<div class="market_search_categories clearfix">
<?php foreach ($categories as $category): ?>
  <a onclick="return nav.go(this, event)" class="left<?php echo (isset($c['category_id']) && $c['category_id'] == $category->category_id) ? ' selected' : '' ?>" href="/market?org=<?php echo $org ?>&used=<?php echo $used ?>&c[q]=<?php echo (isset($c['q'])) ? $c['q'] : '' ?>&c[category_id]=<?php echo $category->category_id ?>"><?php echo $category->name ?></a>
<?php endforeach; ?>
</div>
<div class="summary_wrap">
  <div class="summary">
    <?php echo Yii::t('user', '{n} товар|{n} товара|{n} товаров', $offsets) ?>
  </div>
  <div class="right">
    <?php $this->widget('Paginator', array(
      'offset' => $offset,
      'offsets' => $offsets,
      'delta' => $delta,
    )); ?>
  </div>
</div>
<div id="market_goods" rel="pagination">
  <?php if ($goods): ?>
    <?php $this->renderPartial('_goods', array('goods' => $goods, 'offset' => $offset)) ?>
  <?php else: ?>
  <h2 class="empty">Здесь будут отображаться товары, которые выставили пользователи</h2>
  <?php endif; ?>
</div>
<? if ($offset + $delta < $offsets && $offsets > $delta): ?><a id="pg_more" class="pg_more" onclick="Paginator.showMore()">Еще товары</a><? endif; ?>