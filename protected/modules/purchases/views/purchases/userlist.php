<?php
Yii::app()->getClientScript()->registerCssFile('/css/purchases.css');

$this->pageTitle = Yii::app()->name .' - Закупки '. ActiveHtml::lex(2, $user->profile->firstname);
$delta = Yii::app()->controller->module->purchasesPerPage;
?>
<div class="breadcrumbs">
  <?php echo ActiveHtml::link($user->getDisplayName(), '/id'. $id) ?> &raquo;
  Закупки <?php echo ActiveHtml::lex(2, $user->profile->firstname) ?>
</div>

<div class="tabs">
  <?php echo ActiveHtml::link('Активные закупки', '/purchases'. $id, array('class' => ($section == 'active') ? 'selected' : '')) ?>
  <?php echo ActiveHtml::link('Завершенные закупки', '/purchases'. $id .'?section=finished', array('class' => ($section == 'finished') ? 'selected' : '')) ?>
</div>

<div class="clearfix">
  <div class="right">
    <?php $this->widget('Paginator', array(
      'url' => '/purchases'. $id,
      'offset' => $offset,
      'offsets' => $offsets,
      'delta' => $delta,
    )); ?>
  </div>
</div>
<div id="purchases" rel="pagination">
  <?php $this->renderPartial('_list', array('purchases' => $purchases, 'offset' => $offset)) ?>
</div>
<? if ($offset + $delta < $offsets && $offsets > $delta): ?><a id="pg_more" class="pg_more" onclick="Paginator.showMore()">Еще закупки</a><? endif; ?>