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
    'enableClientValidation' => true,
)); ?>
<div class="row">
    <?php echo $form->inputPlaceholder($model, 'email'); ?>
</div>
<div class="row">
    <?php echo $form->inputPlaceholder($model, 'password'); ?>
</div>
<div class="row">
    <?php echo ActiveHtml::submitButton('Войти', array('class' => 'btn light_blue', 'onclick' => 'return user.login()')); ?>
</div>
<?php $this->endWidget(); ?>