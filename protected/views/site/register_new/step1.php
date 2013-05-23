<?php
Yii::app()->getClientScript()->registerScriptFile('/js/registernew.js');
Yii::app()->getClientScript()->registerCssFile('/css/register.css');
?>
<?php
foreach ($cities as $city) {
    $cityList[$city->name] = $city->id;
}
?>
<div id="register_content">
  <div class="tabs">
    <?php echo ActiveHtml::link('Личные данные', '/registernew/step1', array('class' => 'selected')) ?>
    <?php echo ActiveHtml::link('Данные для входа', '/registernew/step2') ?>
    <?php echo ActiveHtml::link('Соглашение', '/registernew/step3') ?>
    <?php echo ActiveHtml::link('Завершение регистрации', '/registernew/step4') ?>
  </div>

  <div class="reg_header_wrap">
    <h1>Шаг 1. Укажите свои личные данные</h1>
  </div>
  <div class="reg_text_wrap">
    <p>
      Нам потребуется информация о Вашем реальном местоположении для корректировки информации
      по вашему региону.
    </p>
  </div>
  <div class="reg_input_wrap">
    <?php /** @var $form ActiveForm */
    $form = $this->beginWidget('ext.ActiveHtml.ActiveForm', array(
    'id' => 'regform',
    'action' => $this->createUrl('/registernew'),
    )); ?>
    <input type="hidden" name="step" value="1" />
    <div class="reg_input_row clearfix">
      <div class="reg_input_label left">Родной город<span class="required">*</span>:</div>
      <div class="reg_input_labeled left"><?php echo $form->dropdown($model, 'city', $cityList) ?></div>
    </div>
    <div class="reg_input_row clearfix">
      <div class="reg_input_label left">Пол<span class="required">*</span>:</div>
      <div class="reg_input_labeled left">
        <?php echo $form->dropdown($model, 'gender', array('Мужской' => 'Male', 'Женский' => 'Female')) ?>
      </div>
    </div>
    <div class="reg_input_row clearfix">
      <div class="reg_input_label left">Фамилия<span class="required">*</span>:</div>
      <div class="reg_input_labeled left"><?php echo $form->textField($model, 'lastname') ?></div>
    </div>
    <div class="reg_input_row clearfix">
      <div class="reg_input_label left">Имя<span class="required">*</span>:</div>
      <div class="reg_input_labeled left"><?php echo $form->textField($model, 'firstname') ?></div>
    </div>
    <div class="reg_input_row clearfix">
      <div class="reg_input_label left">Отчество<span class="required">*</span>:</div>
      <div class="reg_input_labeled left"><?php echo $form->textField($model, 'middlename') ?></div>
    </div>
    <div class="reg_input_row clearfix">
      <div class="reg_input_label left">Номер приглашения:</div>
      <div class="reg_input_labeled left"><?php echo $form->textField($model, 'invite_code') ?></div>
    </div>
  </div>
  <div class="reg_next_wrap">
    <?php $this->endWidget(); ?>
    <div class="button_submit reg_next_button">
      <button onclick="register.next()"><span class="with_arr">Далее</span></button>
    </div>
  </div>
</div>