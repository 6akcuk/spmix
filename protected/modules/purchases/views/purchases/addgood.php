<div class="create">
    <?php
/** @var $model Purchase */

Yii::app()->getClientScript()->registerCssFile('/css/purchases.css');
Yii::app()->getClientScript()->registerScriptFile('/js/purchase.js');

$this->pageTitle = Yii::app()->name .' - Добавление нового товара';
?>

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
        <div class="row">
            <?php echo ActiveHtml::inputPlaceholder('Good[sizes][]', '', array('id' => '', 'placeholder' => 'Размер')) ?>
            <a class="iconify_plus_a" onclick="sfar.add(this)"></a>
            <a class="iconify_x_a" onclick="sfar.del(this)" style="display:none"></a>
        </div>
        <div class="row">
            <?php echo ActiveHtml::inputPlaceholder('Good[colors][]', '', array('id' => '', 'placeholder' => 'Цвет')) ?>
            <a class="iconify_plus_a" onclick="sfar.add(this)"></a>
            <a class="iconify_x_a" onclick="sfar.del(this)" style="display:none"></a>
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
