<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Sum
 * Date: 26.09.12
 * Time: 16:18
 * To change this template use File | Settings | File Templates.
 */
/* @var $form ActiveForm */
Yii::app()->getClientScript()->registerScriptFile('/js/user.js');

$form = $this->beginWidget('ext.ActiveHtml.ActiveForm', array(
  'id' => 'login-form',
  'action' => '/login',
  'enableClientValidation' => false,
  'htmlOptions' => array('autocomplete' => 'on'),
)); ?>
<div class="label"><?php echo $form->label($model, 'email') ?></div>
<div class="labeled"><?php echo $form->emailField($model, 'email', array('autocomplete' => 'on')); ?></div>
<?php echo $form->error($model, 'email') ?>
<div class="label"><?php echo $form->label($model, 'password') ?></div>
<div class="labeled"><?php echo $form->passwordField($model, 'password'); ?></div>
<?php echo $form->error($model, 'password') ?>
<div class="login_row button_submit button_wide">
  <button onclick="$('#login-form').submit()">Войти</button>
</div>
<div class="login_row">
  <a href="/site/forgot">Забыли пароль?</a>
</div>
<?php $this->endWidget(); ?>