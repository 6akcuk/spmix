<div class="create">
<?php
/**
 * @var $purchase Purchase
 * @var $good Good
 * @var $image GoodImages
 */

Yii::app()->getClientScript()->registerCssFile('/css/purchases.css');
Yii::app()->getClientScript()->registerScriptFile('/js/purchase.js');

$this->pageTitle = Yii::app()->name .' - Редактирование товара';

$sizes = json_decode($good->sizes, true);
$colors = json_decode($good->colors, true);
?>
<div class="clearfix">
 <h1 class="left">Редактировать товар</h1> <h1 class="left" style="margin-left: 300px">Настройка рядов</h1>
</div>
<?php
/** @var $form ActiveForm */

$form = $this->beginWidget('ext.ActiveHtml.ActiveForm', array(
    'id' => 'purchaseform',
    'action' => $this->createUrl('/good'. $purchase->purchase_id .'_'. $good->good_id .'/edit'),
)); ?>
<div class="purchase_columns clearfix">
    <div class="left purchase_column">
        <div class="row">
            <?php echo $form->inputPlaceholder($good, 'name') ?>
        </div>
        <div class="row">
            <?php echo $form->inputPlaceholder($good, 'artikul') ?>
        </div>
        <div class="row">
            <?php echo $form->inputPlaceholder($good, 'price') ?>
        </div>
        <div class="row">
            <?php echo $form->inputPlaceholder($good, 'url') ?>
        </div>
    </div>
    <div class="left purchase_column">
        <?php if(is_array($sizes)): ?>
        <?php foreach($sizes as $size): ?>
        <?php if ($size): ?>
        <div class="row">
            <?php echo ActiveHtml::inputPlaceholder('Good[sizes][]', $size, array('id' => '', 'placeholder' => 'Размер')) ?>
            <a class="iconify_plus_a" onclick="sfar.add(this)"></a>
            <a class="iconify_x_a" onclick="sfar.del(this)"></a>
        </div>
        <?php endif; ?>
        <?php endforeach; ?>
        <?php endif; ?>
        <div class="row">
            <?php echo ActiveHtml::inputPlaceholder('Good[sizes][]', '', array('id' => '', 'placeholder' => 'Размер')) ?>
            <a class="iconify_plus_a" onclick="sfar.add(this)"></a>
            <a class="iconify_x_a" onclick="sfar.del(this)" style="display:none"></a>
        </div>
        <?php if(is_array($colors)): ?>
        <?php foreach($colors as $color): ?>
        <?php if ($color): ?>
        <div class="row">
            <?php echo ActiveHtml::inputPlaceholder('Good[colors][]', $color, array('id' => '', 'placeholder' => 'Цвет')) ?>
            <a class="iconify_plus_a" onclick="sfar.add(this)"></a>
            <a class="iconify_x_a" onclick="sfar.del(this)"></a>
        </div>
        <?php endif; ?>
        <?php endforeach; ?>
        <?php endif; ?>
        <div class="row">
            <?php echo ActiveHtml::inputPlaceholder('Good[colors][]', '', array('id' => '', 'placeholder' => 'Цвет')) ?>
            <a class="iconify_plus_a" onclick="sfar.add(this)"></a>
            <a class="iconify_x_a" onclick="sfar.del(this)" style="display:none"></a>
        </div>
    </div>
</div>
<div class="row">
    <?php echo $form->smartTextarea($good, 'description', array('style' => 'width: 520px')) ?>
</div>
<div class="row">
    <?php echo ActiveHtml::submitButton('Сохранить изменения', array('class' => 'btn light_blue', 'onclick' => 'return Purchase.editgood()')); ?>
</div>
<?php $this->endWidget(); ?>

<h1>Галерея изображений товара</h1>
<?php echo ActiveHtml::upload('photo', '', 'Выберите изображение', array('onchange' => 'Purchase.uploadGoodImage('. $good->purchase_id .', '. $good->good_id .', {id})')) ?>
<div id="images_list" class="images clearfix">
<?php foreach ($good->images as $image): ?>
    <div class="left good_image">
        <?php echo ActiveHtml::showUploadImage($image->image, 'b') ?>
        <a class="tt iconify_x_a"
           title="Удалить изображение"
           onclick="Purchase.removeImage.call(this, <?php echo $good->purchase_id ?>, <?php echo $good->good_id ?>, <?php echo $image->image_id ?>);"></a>
    </div>
<?php endforeach; ?>
</div>
    </div>