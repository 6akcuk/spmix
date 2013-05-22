<?php
Yii::app()->getClientScript()->registerScriptFile('/js/registernew.js');
Yii::app()->getClientScript()->registerCssFile('/css/register.css');
?>
<div id="register_content">
  <div class="tabs">
    <?php echo ActiveHtml::link('Личные данные', '/registernew/step1') ?>
    <?php echo ActiveHtml::link('Данные для входа', '/registernew/step2', array('class' => 'selected')) ?>
    <?php echo ActiveHtml::link('Соглашение', '/registernew/step3') ?>
    <?php echo ActiveHtml::link('Завершение регистрации', '/registernew/step4') ?>
  </div>

  <div class="reg_header_wrap">
    <h1>Шаг 2. Укажите данные для входа</h1>
  </div>
  <div class="reg_text_wrap">
    <p>
      С помощью этих данных вы сможете входить на данный сайт.
    </p>
  </div>
  <div class="reg_input_wrap">
    <?php /** @var $form ActiveForm */
    $form = $this->beginWidget('ext.ActiveHtml.ActiveForm', array(
      'id' => 'regform',
      'action' => $this->createUrl('/register'),
    )); ?>
    <input type="hidden" name="step" value="2" />
    <div class="reg_input_row clearfix">
      <div class="reg_input_label left">Логин<span class="required">*</span>:</div>
      <div class="reg_input_labeled left"><?php echo $form->textField($model, 'login') ?></div>
    </div>
    <div class="reg_input_row clearfix">
      <div class="reg_input_label left">E-Mail<span class="required">*</span>:</div>
      <div class="reg_input_labeled left"><?php echo $form->textField($model, 'email') ?></div>
    </div>
    <div class="reg_input_row clearfix">
      <div class="reg_input_label left">Пароль<span class="required">*</span>:</div>
      <div class="reg_input_labeled left"><?php echo $form->textField($model, 'password') ?></div>
    </div>
  </div>
  <div class="reg_next_wrap">
    <?php $this->endWidget(); ?>
    <div class="button_submit reg_next_button">
      <button onclick="register.next()"><span class="with_arr">Далее</span></button>
    </div>
  </div>
</div>