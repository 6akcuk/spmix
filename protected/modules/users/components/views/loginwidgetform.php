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
    'htmlOptions' => array('autocomplete' => 'on'),
)); ?>
<div class="fl_l login_text">
<div class="row">
    <?php echo $form->emailPlaceholder($model, 'email'); ?>
</div>
<div class="row">
    <?php echo $form->passwordPlaceholder($model, 'password'); ?>
</div>
    </div>
    <div class="fl_l login_button">
        <div class="row">
            <?php echo ActiveHtml::submitButton('Войти', array('class' => 'button', 'onclick' => 'return user.login()')); ?>
        </div>
    </div>
        <div class="clear"></div>
<?php $this->endWidget(); ?>