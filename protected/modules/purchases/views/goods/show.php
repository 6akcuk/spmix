<?php
/**
 * @var $good Good
 * @var $form ActiveForm
 */

Yii::app()->getClientScript()->registerCssFile('/css/purchases.css');
Yii::app()->getClientScript()->registerScriptFile('/js/purchase.js');
Yii::app()->getClientScript()->registerCssFile('/css/photoview.css');
Yii::app()->getClientScript()->registerScriptFile('/js/photoview.js');

$this->pageTitle = Yii::app()->name .' - '. $good->purchase->name .' - '. $good->name;

$sizes = json_decode($good->sizes, true);
$colors = json_decode($good->colors, true);
$dd_sizes = array();
$dd_colors = array();

if (is_array($sizes)) {
    foreach ($sizes as $size) {
        $dd_sizes[$size] = $size;
    }
}
if (is_array($colors)) {
    foreach ($colors as $color) {
        $dd_colors[$color] = $color;
    }
}

?>

<h1><?php echo $good->name ?></h1>
<div class="purchase_table clearfix">
    <div class="left photo">
    <?php if($good->image): ?>
    <a onclick="Purchase.showImages(<?php echo $good->good_id ?>)">
        <?php echo ActiveHtml::showUploadImage($good->image->image, 'a'); ?>
        <?php $images_num = $good->countImages() ?>
        <div class="subtitle">
            <div class="text">Просмотреть <?php echo Yii::t('app', 'фотографию|все {n} фотографии|все {n} фотографий', $images_num) ?></div>
        </div>
    </a>
    <?php endif; ?>
    </div>
    <div class="left td">
        <?php $form = $this->beginWidget('ext.ActiveHtml.ActiveForm', array(
            'id' => 'orderform',
            'action' => $this->createUrl('/good'. $good->purchase_id .'_'. $good->good_id .'/order'),
        )); ?>
        <div class="clearfix">
            <div class="left label">Цена:</div>
            <div class="left labeled"><?php echo ActiveHtml::price($good->price, $good->currency) ?></div>
        </div>
        <div class="clearfix">
            <div class="left label">Итог. цена:</div>
            <div class="left labeled"><?php echo ActiveHtml::price(floatval($good->price) * ($good->purchase->org_tax / 100 + 1), $good->currency) ?></div>
        </div>
        <div class="row">
            <?php echo $form->dropdown($order, 'size', $dd_sizes) ?>
        </div>
        <div class="row">
            <?php echo $form->dropdown($order, 'color', $dd_colors) ?>
        </div>
        <div class="row">
            <?php echo $form->inputPlaceholder($order, 'amount') ?>
        </div>
        <div class="row">
            <?php echo $form->smartTextarea($order, 'client_comment', array('maxheight' => 250)) ?>
        </div>

        <div class="row">
            <a class="button">Заказать</a>
        </div>
        <?php $this->endWidget(); ?>
    </div>
</div>
<div class="purchase_fullstory">
    <?php echo nl2br($good->description) ?>
</div>