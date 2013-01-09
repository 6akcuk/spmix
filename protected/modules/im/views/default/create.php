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
        <div class="wdd clearfix">
            <div class="wdd_lwrap" style="width: 420px">
                <div class="wdd_list">
                    <div class="wddi_over" id="wddi2">
                        <div class="wddi_data">
                            <b class="left wddi_thumb"><img class="wddi_img" src="http://cs1.spmix.ru/v1000/176/LiogOQv0iwf.jpg" /></b>
                            <div class="wddi_text">fluegel Евгений</div>
                            <div class="wddi_sub">Стерлитамак</div>
                        </div>
                    </div>
                    <div class="wddi" id="wddi5">
                        <div class="wddi_data">
                            <b class="left wddi_thumb"><img class="wddi_img" src="http://cs1.spmix.ru/v1000/170/DeAq_2ez4UD.jpg" /></b>
                            <div class="wddi_text">honeytata Алла</div>
                            <div class="wddi_sub">Стерлитамак</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="right wdd_arrow"></div>
            <div class="wdd_bubbles">
                <div id="wddb2" class="summary_tab_sel left">
                    <div class="summary_tab2">
                        <table>
                            <tr>
                                <td>
                                    <div class="summary_tab3">
                                        <nobr>fluegel Евгений</nobr>
                                    </div>
                                </td>
                                <td>
                                    <div class="summary_tab_x"></div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="wdd_add left">
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
            <input type="text" class="left wdd_text" value="" />
        </div>
    </div>
    <div class="row">
        <h5>Сообщение</h5>
        <?php echo ActiveHtml::smartTextarea('Im[message]', '') ?>
    </div>
<?php $this->endWidget(); ?>
</div>