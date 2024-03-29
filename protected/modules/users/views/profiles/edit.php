<?php
/** @var $userinfo User */

Yii::app()->getClientScript()->registerCssFile('/css/profile.css');

$this->pageTitle = Yii::app()->name .' - Редактирование профиля';
?>

<h1>Редактирование профиля</h1>

<?php
/** @var $form ActiveForm */
$photo = json_decode($userinfo->profile->photo);

$form = $this->beginWidget('ext.ActiveHtml.ActiveForm', array(
    'id' => 'editform',
    'action' => $this->createUrl('/edit'),
)); ?>
<div class="clearfix">
    <div class="left">
        <div class="row">
            <?php echo $form->label($userinfo->profile, 'firstname') ?>
        </div>
        <div class="row">
            <?php echo $form->inputPlaceholder($userinfo->profile, 'firstname') ?>
        </div>
        <div class="row">
            <?php echo $form->label($userinfo->profile, 'lastname') ?>
        </div>
        <div class="row">
            <?php echo $form->inputPlaceholder($userinfo->profile, 'lastname') ?>
        </div>
        <div class="row">
            <?php echo $form->label($userinfo->profile, 'middlename') ?>
        </div>
        <div class="row">
            <?php echo $form->inputPlaceholder($userinfo->profile, 'middlename') ?>
        </div>
        <div class="row">
            <?php echo $form->label($userinfo->profile, 'photo') ?>
        </div>
        <div class="row">
            <?php echo $form->upload($userinfo->profile, 'photo', 'Загрузить фотографию', array('data-image' => 'a')) ?>
        </div>
        <div class="row">
            <?php echo $form->label($userinfo->profile, 'about') ?>
        </div>
        <div class="row">
            <?php echo $form->smartTextarea($userinfo->profile, 'about') ?>
        </div>
    </div>
    <div class="left">
        <div class="row">
            Платежные реквизиты
        </div>
        <?php if ($userinfo->profile->paydetails): ?>
        <?php /** @var $paydetail ProfilePaydetail */ ?>
        <?php foreach ($userinfo->profile->paydetails as $paydetail): ?>
            <div class="row">
                <?php echo ActiveHtml::inputPlaceholder('ProfilePaydetail[paysystem_name][]', $paydetail->paysystem_name, array('id' => '', 'placeholder' => 'Платежная система')) ?>
                <?php echo ActiveHtml::inputPlaceholder('ProfilePaydetail[paysystem_details][]', $paydetail->paysystem_details, array('id' => '', 'placeholder' => 'Реквизиты')) ?>
                <a class="iconify_plus_a" onclick="sfar.add(this)"></a>
                <a class="iconify_x_a" onclick="sfar.del(this)"></a>
            </div>
        <?php endforeach; ?>
        <?php endif; ?>
        <div class="row">
            <?php echo ActiveHtml::inputPlaceholder('ProfilePaydetail[paysystem_name][]', '', array('id' => '', 'placeholder' => 'Платежная система')) ?>
            <?php echo ActiveHtml::inputPlaceholder('ProfilePaydetail[paysystem_details][]', '', array('id' => '', 'placeholder' => 'Реквизиты')) ?>
            <a class="iconify_plus_a" onclick="sfar.add(this)"></a>
            <a class="iconify_x_a" onclick="sfar.del(this)" style="display:none"></a>
        </div>
    </div>
</div>
<div class="row">
    <?php echo ActiveHtml::submitButton('Сохранить изменения', array('class' => 'button', 'onclick' => 'saveProfileInfo(); return false')); ?>
</div>
<script>
function saveProfileInfo() {
  FormMgr.submit('#editform', 'left', function(r) {
    boxPopup('Изменения успешно сохранены');
  });
}
</script>
<?php $this->endWidget(); ?>