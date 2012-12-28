<div class="create">
<?php
/**
 * @var $purchase Purchase
 * @var $good Good
 * @var $image GoodImages
 * @var $grid GoodGrid
 */

Yii::app()->getClientScript()->registerCssFile('/css/purchases.css');
Yii::app()->getClientScript()->registerScriptFile('/js/purchase.js');

$this->pageTitle = Yii::app()->name .' - Редактирование товара';
?>
<h1>Редактировать товар</h1>

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
        <h3>Размерная сетка</h3>
        <div>
            <?php echo $form->checkBox($good, 'is_range') ?>
            <?php echo $form->label($good, 'is_range') ?>
        </div>
        <?php if ($good->grid): ?>
        <?php foreach ($good->grid as $idx => $grid): ?>
        <?php $colors = json_decode($grid->colors, true); ?>
        <div id="block<?php echo $idx ?>" class="row">
            <div class="row">
                <?php echo ActiveHtml::inputPlaceholder('size['. $idx .']', $grid->size, array('id' => '', 'placeholder' => 'Размер')) ?>
                <a class="iconify_x_a" onclick="sbar.del(this)"<?php if($idx == 0): ?> style="display:none"<?php endif; ?>></a>
            </div>
            <?php foreach($colors as $cidx => $color): ?>
            <div sbar="sub" class="row" style="margin-left: 20px">
                <?php echo ActiveHtml::inputPlaceholder('color['. $idx .'][]', $color, array('id' => '', 'placeholder' => 'Цвет')) ?>
                <a class="iconify_plus_a" onclick="sfar.add(this)"></a>
                <a class="iconify_x_a" onclick="sfar.del(this)"<?php if($cidx == 0): ?> style="display:none"<?php endif; ?>></a>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endforeach; ?>
        <?php else: ?>
        <div id="block0" class="row">
            <div class="row">
                <?php echo ActiveHtml::inputPlaceholder('size[0]', '', array('id' => '', 'placeholder' => 'Размер')) ?>
                <a class="iconify_x_a" onclick="sbar.del(this)" style="display:none"></a>
            </div>
            <div sbar="sub" class="row" style="margin-left: 20px">
                <?php echo ActiveHtml::inputPlaceholder('color[0][]', '', array('id' => '', 'placeholder' => 'Цвет')) ?>
                <a class="iconify_plus_a" onclick="sfar.add(this)"></a>
                <a class="iconify_x_a" onclick="sfar.del(this)" style="display:none"></a>
            </div>
        </div>
        <?php endif; ?>
        <div class="row">
            <a onclick="sbar.add(this)" class="button"><span class="iconify_plus_a"></span> Добавить еще размер</a>
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