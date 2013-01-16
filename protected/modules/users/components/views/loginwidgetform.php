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
<div class="fl_l login_text">
<div class="row">
    <?php echo $form->label($model, 'email') ?>
    <?php echo $form->emailField($model, 'email', array('autocomplete' => 'on')); ?>
    <?php echo $form->error($model, 'email') ?>
</div>
<div class="row">
    <?php echo $form->label($model, 'password') ?>
    <?php echo $form->passwordField($model, 'password'); ?>
    <?php echo $form->error($model, 'password') ?>
</div>
    </div>
    <div class="fl_l login_button">
        <div class="row">
            <?php echo ActiveHtml::submitButton('Войти', array('class' => 'button')); ?>
        </div>
    </div>
        <div class="clear"></div>
<?php $this->endWidget(); ?>