<?php
/** @var $people User */

Yii::app()->getClientScript()->registerCssFile('/css/profile.css');
Yii::app()->getClientScript()->registerCssFile('/css/im.css');

Yii::app()->getClientScript()->registerScriptFile('/js/profile.js');

$this->pageTitle = Yii::app()->name .' - Новое сообщение';
?>
<div class="tabs">
    <?php echo ActiveHtml::link('Диалоги', '/im') ?>
    <?php echo ActiveHtml::link('Новое сообщение', '/im?sel=-1', array('class' => 'selected')) ?>
</div>
<div class="wrap2">
<?php $form = $this->beginWidget('ext.ActiveHtml.ActiveForm', array(
    'id' => 'imform',
    'action' => $this->createUrl('/im?sel=-1'),
)); ?>
    <div class="row">
        <h5>Получатель</h5>
        <div id="im_wdd" class="wdd clearfix" onclick="WideDropdown.show('im_wdd', event)">
            <div class="wdd_lwrap" style="width: 420px">
                <div class="wdd_list"></div>
            </div>
            <div class="right wdd_arrow"></div>
            <div class="wdd_bubbles"></div>
            <div class="wdd_add left" style="display:none">
                <div class="wdd_add2">
                    <table>
                        <tr>
                            <td>
                                <div class="wdd_add3">
                                    <nobr>Добавить</nobr>
                                </div>
                            </td>
                            <td>
                                <div class="wdd_add_plus"></div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <?php echo ActiveHtml::inputPlaceholder(
                '',
                '',
                array(
                    'class' => 'left wdd_text',
                    'placeholder' => 'Введите имя друга',
                    'onfocus' => "WideDropdown.setFocused('im_wdd', event)",
                    'onblur' => "WideDropdown.setUnfocused('im_wdd', event)",
                )
            ) ?>
            <script type="text/javascript">
            WideDropdown.addList('im_wdd', {'2': {img: 'http://cs1.spmix.ru/v1000/176/LiogOQv0iwf.jpg',
            text: 'fluegel', sub: 'Стерлитамак'}, '5': {img: 'http://cs1.spmix.ru/v1000/170/DeAq_2ez4UD.jpg',
            text: 'honeytata', sub: 'Стерлитамак'}});
            </script>
        </div>
    </div>
    <div id="im_theme" class="row" style="display:none">
        <h5>Тема</h5>
        <?php echo ActiveHtml::inputPlaceholder('Im[title]', '') ?>
    </div>
    <div class="row">
        <h5>Сообщение</h5>
        <?php echo ActiveHtml::smartTextarea('Im[message]', '') ?>
    </div>
    <div class="row">
        <?php echo ActiveHtml::submitButton('Отправить', array('class' => 'left button')) ?>
    </div>
<?php $this->endWidget(); ?>
</div>
<script type="text/javascript">
if (!A.wddOnSelect) A.wddOnSelect = {};
A.wddOnSelect['im_wdd'] = function() {
    if (WideDropdown.countBubbleSize('im_wdd') > 1) $('#im_theme').show();
};
if (!A.wddOnDeselect) A.wddOnDeselect = {};
A.wddOnDeselect['im_wdd'] = function() {
    if (WideDropdown.countBubbleSize('im_wdd') <= 1) $('#im_theme').hide();
};
</script>