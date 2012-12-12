<?php
/**
 * @var $good Good
 */

Yii::app()->getClientScript()->registerCssFile('/css/purchases.css');
Yii::app()->getClientScript()->registerScriptFile('/js/purchase.js');
Yii::app()->getClientScript()->registerCssFile('/css/photoview.css');
Yii::app()->getClientScript()->registerScriptFile('/js/photoview.js');

$this->pageTitle = Yii::app()->name .' - '. $good->purchase->name .' - '. $good->name;
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
        <div class="clearfix">
            <div class="left label">Артикул:</div>
            <div class="left labeled"><?php echo $good->artikul ?></div>
        </div>
        <div class="clearfix">
            <div class="left label">Цена:</div>
            <div class="left labeled"><?php echo ActiveHtml::price($good->price, $good->currency) ?></div>
        </div>
        <div class="clearfix">
            <div class="left label">URL:</div>
            <div class="left labeled"><?php echo $good->url ?></div>
        </div>
        <div class="clearfix">
            <div class="left label">Размеры:</div>
            <div class="left labeled"><?php echo $good->sizes ?></div>
        </div>
        <div class="clearfix">
            <div class="left label">Цвета:</div>
            <div class="left labeled"><?php echo $good->colors ?></div>
        </div>
        <div class="clearfix">
            <a class="button">Заказать</a>
        </div>
    </div>
</div>
<div class="purchase_fullstory">
    <?php echo nl2br($good->description) ?>
</div>