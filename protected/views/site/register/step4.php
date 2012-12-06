<?php Yii::app()->getClientScript()->registerScriptFile('/js/register.js'); ?>

<h1>Регистрация на сайте</h1>

<div id="stepcolumns" class="clearfix">
    <div class="left">
        <ul>
            <li>
                <?php echo ActiveHtml::link('Родной город', '/register/step1') ?>
            </li>
            <li>
                <?php echo ActiveHtml::link('Личные данные', '/register/step2') ?>
            </li>
            <li>
                <?php echo ActiveHtml::link('Данные для входа', '/register/step3') ?>
            </li>
            <li>
                <?php echo ActiveHtml::link('Завершение регистрации', '/register/step4', array('class' => 'selected')) ?>
            </li>
        </ul>
    </div>
    <div class="right">
        <h3>Завершение регистрации</h3>
        <p>
            Чтобы завершить регистрацию, укажите свой мобильный телефон, на который придет код подтверждения
        </p>
        <?php /** @var $form ActiveForm */
        $form = $this->beginWidget('ext.ActiveHtml.ActiveForm', array(
            'id' => 'regform',
            'action' => $this->createUrl('/register'),
        )); ?>
        <input type="hidden" name="step" value="4" />
        <div class="row">
            <?php echo $form->inputPlaceholder($model, 'phone', array('onblur' => 'register.sendCode(false)')) ?>
            <a id="sendCodeLink" onclick="register.sendCode(true)" title="Повторно отправить код подтверждения" class="tt iconify_refresh_a" style="display:none"></a>
        </div>
        <div class="row">
            <?php echo $form->inputPlaceholder($model, 'confirm') ?>
        </div>
        <?php $this->endWidget(); ?>
        <div class="buttons clearfix">
            <div class="right">
                <a class="btn light_green" onclick="register.next()">
                    Завершить
                </a>
            </div>
        </div>
    </div>
</div>