<?php /** @var $good Good */ ?>
<?php $page = ($offset + Yii::app()->controller->module->purchasesPerPage) / Yii::app()->controller->module->purchasesPerPage ?>
<?php $added = false; ?>
<?php if (sizeof($goods) > 0): ?>
<?php foreach ($goods as $good): ?>
<div id="good<?php echo $good->good_id ?>" class="good_row_cont">
    <div class="good_row_inner_cont">
        <a class="good_row_relative" href="/good<?php echo $good->purchase_id ?>_<?php echo $good->good_id ?>" onclick="return nav.go(this, null)">
            <div class="good_row_info_line_wrap">
                <div class="good_row_info_line"></div>
                <div class="good_raw_info_name"><?php echo $good->name ?></div>
                <div class="good_row_price"><?php echo ActiveHtml::price($good->getEndPrice(), $good->currency) ?></div>
            </div>
            <div class="good_image_div"<?php if ($good->image): ?> style="background-image: url('<?php echo ActiveHtml::getImageUrl($good->image->image, 'a') ?>')"<?php endif; ?>></div>
        </a>
    </div>
</div>
    <!--
    <div<?php if(!$added) { echo ' rel="page-'. $page .'"'; $added = true; } ?> id="good<?php echo $good->purchase_id ?>_<?php echo $good->good_id ?>" class="left good">
        <h4>
            <?php echo ActiveHtml::link($good->name, '/good'. $good->purchase_id .'_'. $good->good_id) ?>
            <a class="right iconify_x_a tt" title="Удалить товар" onclick="Purchase.deletegood(this, <?php echo $good->purchase_id ?>, <?php echo $good->good_id ?>)"></a>
            <?php echo ActiveHtml::link('', '/good'. $good->purchase_id .'_'. $good->good_id.'/edit', array('class' => 'right iconify_gear_a tt', 'title' => 'Редактировать товар')) ?>
        </h4>
        <?php if ($good->image): ?><div><?php echo ActiveHtml::showUploadImage($good->image->image) ?></div><?php endif; ?>
        <?php if ($good->is_range): ?>Ряды<?php endif; ?>
        <div class="price"><?php echo ActiveHtml::price($good->price, $good->currency) ?></div>
    </div>-->
<?php endforeach; ?>
<?php endif; ?>