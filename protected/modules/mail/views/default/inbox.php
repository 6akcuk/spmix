<?php
/** @var $people User */

Yii::app()->getClientScript()->registerCssFile('/css/profile.css');
Yii::app()->getClientScript()->registerCssFile('/css/im.css');

Yii::app()->getClientScript()->registerCssFile('/css/pagination.css');
Yii::app()->getClientScript()->registerScriptFile('/js/pagination.js');

Yii::app()->getClientScript()->registerScriptFile('/js/mail.js');

$this->pageTitle = Yii::app()->name .' - Полученные сообщения';
$delta = Yii::app()->controller->module->messagesPerPage;
?>
<div class="tabs">
  <?php echo ActiveHtml::link('Полученные', '/mail?act=inbox', array('class' => 'selected')) ?>
  <?php echo ActiveHtml::link('Отправленные', '/mail?act=outbox') ?>
  <div class="right">
    <?php echo ActiveHtml::link('Написать сообщение', '/mail?act=write') ?>
  </div>
</div>
<div class="im_bar clearfix">
  <div class="left" style="padding-top: 6px">
    Выделить:
    <a onclick="mail.selectAll()">все</a>,
    <a onclick="mail.selectReaded()">прочитанные</a>,
    <a onclick="mail.selectNew()">новые</a>
  </div>
  <div id="mail_actions" style="display: none" class="right">
    <a class="button" onclick="mail.deleteSelected()">Удалить</a>
    <a class="button" onclick="mail.markAsReaded()">Отметить как прочитанные</a>
    <a class="button" onclick="mail.markAsNew()">Отметить как новые</a>
  </div>
  <div id="mail_search" rel="filters" class="right">
    <?php echo ActiveHtml::inputPlaceholder('c[msg]', (isset($c['msg'])) ? $c['msg'] : '', array('placeholder' => 'Поиск сообщений')) ?>
  </div>
  <div style="display: none">
    <?php $this->widget('Paginator', array(
      'url' => '/mail?act=inbox',
      'offset' => $offset,
      'offsets' => $offsets,
      'delta' => $delta,
      'nopages' => true,
    )); ?>
  </div>
</div>
<div class="summary_wrap">
  <div class="summary">
    <span id="mail_summary"><?php echo Yii::t('user', 'Вы получили {n} сообщение|Вы получили {n} сообщения|Вы получили {n} сообщений', $offsets) ?></span>
    <span class="divide">|</span>
    <span><?php echo ActiveHtml::link('Показать в виде диалогов', array('/im')) ?></span>
  </div>
</div>
<table id="messages" rel="pagination">
<?php if ($messages): ?>
  <?php $this->renderPartial('_message', array('messages' => $messages, 'offset' => $offset)) ?>
<?php else: ?>
<tr>
  <td><h2 class="empty">Здесь будут отображаться Ваши личные сообщения</h2></td>
</tr>
<?php endif; ?>
</table>
<? if ($offset + $delta < $offsets && $offsets > $delta): ?><a id="pg_more" class="pg_more" onclick="Paginator.showMore()">Еще сообщения</a><? endif; ?>
<script type="text/javascript">mail._reset()</script>