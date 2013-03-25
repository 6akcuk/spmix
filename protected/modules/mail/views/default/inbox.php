<?php
/** @var $people User */

Yii::app()->getClientScript()->registerCssFile('/css/profile.css');
Yii::app()->getClientScript()->registerCssFile('/css/im.css');

Yii::app()->getClientScript()->registerCssFile('/css/pagination.css');
Yii::app()->getClientScript()->registerScriptFile('/js/pagination.js');

Yii::app()->getClientScript()->registerScriptFile('/js/im.js');

$this->pageTitle = Yii::app()->name .' - Полученные сообщения';
$delta = Yii::app()->controller->module->messagesPerPage;
?>
  <div class="tabs">
    <?php echo ActiveHtml::link('Полученные', '/mail?section=inbox', array('class' => 'selected')) ?>
    <?php echo ActiveHtml::link('Отправленные', '/mail?section=outbox', array('class' => 'selected')) ?>
    <div class="right">
      <?php echo ActiveHtml::link('Написать сообщение', '/mail?act=write') ?>
    </div>
  </div>
  <div class="im_bar clearfix">
    <div rel="filters" class="right">
      <?php echo ActiveHtml::inputPlaceholder('c[name]', (isset($c['name'])) ? $c['name'] : '', array('placeholder' => 'Поиск по диалогам')) ?>
    </div>
    <div style="display: none">
      <?php $this->widget('Paginator', array(
        'url' => '/im',
        'offset' => $offset,
        'offsets' => $offsets,
        'delta' => $delta,
        'nopages' => true,
      )); ?>
    </div>
  </div>
  <div class="summary">
    <?php echo Yii::t('user', '{n} диалог|{n} диалога|{n} диалогов', $offsets) ?>
  </div>
  <div id="im_dialogs" rel="pagination">
    <div class="im_none" id="im_rows_none" style="display:<?php echo ($offsets) ? 'none' : 'block' ?>">Здесь будет выводиться список Ваших сообщений</div>
    <?php $this->renderPartial('_dialog', array('dialogs' => $dialogs, 'offset' => $offset)) ?>
  </div>
<? if ($offset + $delta < $offsets && $offsets > $delta): ?><a id="pg_more" class="pg_more" onclick="Paginator.showMore()">Еще диалоги</a><? endif; ?>