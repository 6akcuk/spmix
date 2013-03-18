<?php

Yii::app()->getClientScript()->registerCssFile('/css/profile.css');

$this->pageTitle = Yii::app()->name .' - Общие настройки';
?>

<div class="tabs">
  <?php echo ActiveHtml::link('Общие', '/settings', array('class' => 'selected')) ?>
  <?php echo ActiveHtml::link('Оповещения', '/notify') ?>
</div>

<div class="profile-settings">
  <div class="settings">
    <h2>Изменить пароль</h2>
    <?php $changepwdform = $this->beginWidget('ext.ActiveHtml.ActiveForm', array(
      'id' => 'changepwdform',
      'action' => $this->createUrl('/settings'),
    )); ?>
    <div class="row">
      <?php echo $changepwdform->inputPlaceholder($changepwdmdl, 'old_password') ?>
    </div>
    <div class="row">
      <?php echo $changepwdform->inputPlaceholder($changepwdmdl, 'new_password') ?>
    </div>
    <div class="row">
      <?php echo $changepwdform->inputPlaceholder($changepwdmdl, 'rpt_password') ?>
    </div>
    <div class="row">
      <a class="button" onclick="Profile.changePassword()">Изменить пароль</a>
    </div>
    <?php $this->endWidget(); ?>
  </div>
</div>