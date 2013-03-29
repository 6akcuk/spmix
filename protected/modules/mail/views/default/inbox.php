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
  <?php echo ActiveHtml::link('Отправленные', '/mail?section=outbox') ?>
  <div class="right">
    <?php echo ActiveHtml::link('Написать сообщение', '/mail?act=write') ?>
  </div>
</div>
<div class="im_bar clearfix">
  <div class="left" style="padding-top: 6px">
    Выделить:
    <a>все</a>,
    <a>прочитанные</a>,
    <a>новые</a>
  </div>
  <div rel="filters" class="right">
    <?php echo ActiveHtml::inputPlaceholder('c[msg]', (isset($c['msg'])) ? $c['msg'] : '', array('placeholder' => 'Поиск сообщений')) ?>
  </div>
</div>
<div class="summary">
  <?php echo Yii::t('user', 'Вы получили {n} сообщение|Вы получили {n} сообщения|Вы получили {n} сообщений', $offsets) ?>
  |
  <?php echo ActiveHtml::link('Показать в виде диалогов', array('/im')) ?>
</div>
<div id="messages" rel="pagination">
<?php if ($messages): ?>
  <?php $this->renderPartial('_message', array('messages' => $messages, 'offset' => $offset)) ?>
<?php else: ?>
  <h2 class="empty">Здесь будут отображаться Ваши личные сообщения</h2>
<?php endif; ?>
</div>