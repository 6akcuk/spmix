<?php
Yii::app()->getClientScript()->registerScriptFile('/js/registernew.js');
Yii::app()->getClientScript()->registerCssFile('/css/register.css');
?>
<div id="register_content">
  <div class="tabs">
    <?php echo ActiveHtml::link('Личные данные', '/registernew/step1') ?>
    <?php echo ActiveHtml::link('Данные для входа', '/registernew/step2') ?>
    <?php echo ActiveHtml::link('Соглашение', '/registernew/step3', array('class' => 'selected')) ?>
    <?php echo ActiveHtml::link('Завершение регистрации', '/registernew/step4') ?>
  </div>

  <div class="reg_header_wrap">
    <h1>Шаг 3. Укажите номер своего мобильного телефона</h1>
  </div>
  <div class="reg_text_wrap">
    <p>
      Настоящим соглашением подтверждается, что Абонент, персональные данные которого являются предметом
      соглашения, выражает свое согласие на получение сообщений информационного и рекламного содержания.
    </p>
    <p>
      Для отзыва согласия необходимо в настройках аккаунта отключить отправку смс сообщений или же по запросу в
      техническую поддержку.
    </p>
  </div>
  <div class="reg_input_wrap">
    <?php /** @var $form ActiveForm */
    $form = $this->beginWidget('ext.ActiveHtml.ActiveForm', array(
      'id' => 'regform',
      'action' => $this->createUrl('/registernew'),
    )); ?>
    <input type="hidden" name="step" value="3" />
    <div class="reg_input_row clearfix">
      <div class="reg_input_label left">Мобильный телефон<span class="required">*</span>:</div>
      <div class="reg_input_labeled left"><?php echo $form->textField($model, 'phone') ?></div>
    </div>
    <div class="reg_input_row reg_input_link clearfix">
      <input type="checkbox" id="RegisterNewForm_agreement" name="RegisterNewForm[agreement]" value="1"<?php if($model->agreement) echo " checked" ?>/>
      <?php echo $form->label($model, 'agreement') ?>
    </div>
  </div>
  <div class="reg_next_wrap">
    <?php $this->endWidget(); ?>
    <div class="button_submit reg_next_button">
      <button onclick="register.next()"><span class="with_arr">Далее</span></button>
    </div>
  </div>
</div>