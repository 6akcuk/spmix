<?php

Yii::app()->getClientScript()->registerCssFile('/css/profile.css');
Yii::app()->getClientScript()->registerScriptFile('/js/profile.js');

$this->pageTitle = Yii::app()->name .' - Общие настройки';
?>

<div class="tabs">
  <?php echo ActiveHtml::link('Общие', '/settings', array('class' => 'selected')) ?>
  <?php echo ActiveHtml::link('Оповещения', '/notify') ?>
</div>

<div class="profile-settings">
  <?php if ($report): ?>
  <div class="settings">
    <div class="op_report"><?php echo $report ?></div>
  </div>
  <?php endif; ?>
  <?php if ($error): ?>
    <div class="settings">
      <div class="op_error"><?php echo $error ?></div>
    </div>
  <?php endif; ?>
  <div class="settings">
    <h2>Изменить пароль</h2>
    <?php
    /** @var $changepwdform ActiveForm */
    $changepwdform = $this->beginWidget('ext.ActiveHtml.ActiveForm', array(
      'id' => 'changepwdform',
      'action' => $this->createUrl('/settings'),
    )); ?>
    <input type="hidden" name="act" value="changepwd" />
    <div class="row">
      <?php echo $changepwdform->passwordPlaceholder($changepwdmdl, 'old_password') ?>
    </div>
    <div class="row">
      <?php echo $changepwdform->passwordPlaceholder($changepwdmdl, 'new_password') ?>
    </div>
    <div class="row">
      <?php echo $changepwdform->passwordPlaceholder($changepwdmdl, 'rpt_password') ?>
    </div>
    <div class="row">
      <a class="button" onclick="Profile.changePassword()">Изменить пароль</a>
    </div>
    <?php $this->endWidget(); ?>
  </div>
  <div class="settings">
    <h2>Адрес Вашей электронной почты</h2>
    <?php
    /** @var $changeemailform ActiveForm */
    $changeemailform = $this->beginWidget('ext.ActiveHtml.ActiveForm', array(
      'id' => 'changeemailform',
      'action' => $this->createUrl('/settings'),
    )); ?>
    <input type="hidden" name="act" value="changeemail" />
    <div class="row">
      Текущий адрес: <b><?php echo preg_replace("/(\w{1}).*([@]{1}.*)/ui", "$1***$2", Yii::app()->user->model->email) ?></b>
    </div>
    <div class="row">
      <?php echo $changeemailform->inputPlaceholder($changeemailmdl, 'new_mail') ?>
    </div>
    <div class="row">
      <a class="button" onclick="Profile.saveEmail()">Сохранить адрес</a>
    </div>
    <?php $this->endWidget(); ?>
  </div>
  <div class="settings">
    <h2>Номер Вашего телефона</h2>
    <div class="row">
      Текущий номер: <b><?php echo preg_replace("/(\d{6}).*/ui", "$1*****", Yii::app()->user->model->profile->phone) ?></b>
    </div>
    <div class="row" style="margin-top: 10px">
      <a class="button" onclick="Profile.changePhone()">Изменить номер телефона</a>
    </div>
  </div>
  <div class="settings">
    <h2>Безопасность Вашей страницы</h2>
    <div class="row">
      Последняя активность:
      <span rel="tooltip" title="IP последнего посещения: <?php echo long2ip($activity->ip) ?>">
        <?php echo ActiveHtml::timeback($activity->getTimestamp()) ?>
      </span>
    </div>
  </div>
</div>