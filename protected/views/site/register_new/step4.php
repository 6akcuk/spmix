<?php
Yii::app()->getClientScript()->registerScriptFile('/js/registernew.js');
Yii::app()->getClientScript()->registerCssFile('/css/register.css');
?>
<div id="register_content">
  <div class="tabs">
    <?php echo ActiveHtml::link('Личные данные', '/registernew/step1') ?>
    <?php echo ActiveHtml::link('Данные для входа', '/registernew/step2') ?>
    <?php echo ActiveHtml::link('Соглашение', '/registernew/step3') ?>
    <?php echo ActiveHtml::link('Завершение регистрации', '/registernew/step4', array('class' => 'selected')) ?>
  </div>

  <div class="reg_header_wrap">
    <h1>Шаг 4. Завершение регистрации</h1>
  </div>
  <div class="reg_text_wrap">
    <p>
      На указанный вами мобильный телефон, было отправлено SMS-сообщение с кодом подтверждения
    </p>
  </div>
  <div class="reg_input_wrap">
    <?php /** @var $form ActiveForm */
    $form = $this->beginWidget('ext.ActiveHtml.ActiveForm', array(
      'id' => 'regform',
      'action' => $this->createUrl('/register'),
    )); ?>
    <input type="hidden" name="step" value="4" />
    <div class="reg_input_row clearfix">
      <div class="reg_input_label left">Код сессии:</div>
      <div class="reg_input_labeled left"></div>
    </div>
    <div class="reg_input_row clearfix">
      <div class="reg_input_label left">Код подтверждения<span class="required">*</span>:</div>
      <div class="reg_input_labeled left"><?php echo $form->textField($model, 'confirm') ?></div>
    </div>
    <div class="reg_input_row reg_input_link clearfix">
      <a onclick="register.sendCode(true)">Получить код повторно</a>
    </div>
  </div>
  <div class="reg_next_wrap">
    <?php $this->endWidget(); ?>
    <div class="button_submit">
      <button onclick="register.next()">Завершить регистрацию</button>
    </div>
  </div>
</div>