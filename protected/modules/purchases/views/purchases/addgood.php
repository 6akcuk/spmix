<?php
/** @var $model Purchase */

Yii::app()->getClientScript()->registerCssFile('/css/purchases.css');
Yii::app()->getClientScript()->registerScriptFile('/js/purchase.js');

$this->pageTitle = Yii::app()->name .' - Добавление нового товара';
?>
<div class="breadcrumbs">
    <?php echo ActiveHtml::link($purchase->name, '/purchase'. $purchase->purchase_id) ?> &raquo;
    Добавить новый товар
</div>
<div class="create">
<h1>Добавить новый товар</h1>

<?php
/** @var $form ActiveForm */

$form = $this->beginWidget('ext.ActiveHtml.ActiveForm', array(
    'id' => 'addgoodform',
    'action' => $this->createUrl('/purchase'. $id .'/addgood'),
)); ?>
<div class="purchase_columns clearfix">
    <div class="left purchase_column">
        <div class="row">
            <?php echo $form->inputPlaceholder($model, 'name') ?>
        </div>
        <div class="row">
            <?php echo $form->inputPlaceholder($model, 'artikul') ?>
        </div>
        <div class="row">
            <?php echo $form->inputPlaceholder($model, 'price') ?>
        </div>
        <div class="row">
            <?php echo $form->inputPlaceholder($model, 'url') ?>
        </div>
        <div class="row">
            <?php echo ActiveHtml::upload('image', '', 'Прикрепить фотографию', array('data-image' => 'a')) ?>
        </div>
    </div>
    <div class="left purchase_column">
        <h3>Размерная сетка</h3>
        <div>
            <?php echo $form->checkBox($model, 'is_range') ?>
            <?php echo $form->label($model, 'is_range') ?>
        </div>
        <div id="block0" rel="sbar" class="row">
            <div class="row">
                <?php echo ActiveHtml::inputPlaceholder('size[0]', '', array('id' => '', 'placeholder' => 'Размер')) ?>
                <a class="iconify_x_a" onclick="sbar.del(this)" style="display:none"></a>
            </div>
            <div rel="range" class="row" style="display:none">
                <?php echo ActiveHtml::inputPlaceholder('allowed[0]', '', array('id' => '', 'placeholder' => 'Количество на ряд')) ?>
            </div>
            <div sbar="sub" class="row" style="margin-left: 20px">
                <?php echo ActiveHtml::inputPlaceholder('color[0][]', '', array('id' => '', 'placeholder' => 'Цвет')) ?>
                <a class="iconify_plus_a" onclick="sfar.add(this)"></a>
                <a class="iconify_x_a" onclick="sfar.del(this)" style="display:none"></a>
            </div>
        </div>
        <div class="row">
            <a onclick="sbar.add(this)" class="button"><span class="iconify_plus_a"></span> Добавить еще размер</a>
        </div>
    </div>
</div>
<div class="row">
    <?php echo $form->smartTextarea($model, 'description', array('style' => 'width: 520px')) ?>
</div>
<div class="row">
    <?php echo ActiveHtml::submitButton('Добавить товар', array('class' => 'btn light_blue', 'onclick' => 'return Purchase.addgood()')); ?>
</div>
<?php $this->endWidget(); ?>
</div>
<script type="text/javascript">
$().ready(function() {
    $('#Good_is_range').click(function() {
        ($(this).attr('checked')) ? $('[rel="range"]').show() : $('[rel="range"]').hide();
    });
});
</script>