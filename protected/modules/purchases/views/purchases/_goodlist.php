<?php /** @var $good Good */ ?>
<?php $page = ($offset + Yii::app()->controller->module->goodsPerPage) / Yii::app()->controller->module->goodsPerPage ?>
<?php $added = false; ?>
<?php if (sizeof($goods) > 0): ?>
<?php foreach ($goods as $good): ?>
<div id="good<?php echo $good->purchase_id ?>_<?php echo $good->good_id ?>"<?php if(!$added) { echo ' rel="page-'. $page .'"'; $added = true; } ?> class="good_row_cont">
    <div class="good_row_inner_cont">
        <a class="good_row_relative" onmouseover="Purchase.overGood(this, event)" onmouseout="Purchase.outGood(this, event)" href="/good<?php echo $good->purchase_id ?>_<?php echo $good->good_id ?>" onclick="if(A.glCancelClick) return (A.glCancelClick = false); return nav.go(this, null)">
        <?php if ($good->is_range && $good->range): ?>
            <div class="good_row_ranges_wrap <?php if ($good->ordersNum > 0): ?>good_row_ranges_wrap_orders<?php endif; ?>">
                <div class="good_row_ranges_bg"></div>
                <div class="good_row_ranges">
                  Ряды
                  <?php if ($good->ordersNum > 0): ?>
                  <br>
                  Заказано: <?php echo $good->ordersNum ?> шт.
                  <?php endif; ?>
                </div>
            </div>
        <?php else: ?>
          <?php if ($good->ordersNum > 0): ?>
          <div class="good_row_orders_wrap">
            <div class="good_row_orders_bg"></div>
            <div class="good_row_orders">
              Заказано: <?php echo $good->ordersNum ?> шт.
            </div>
          </div>
          <?php endif; ?>
        <?php endif; ?>
            <div class="good_row_info_line_wrap">
                <div class="good_row_info_line"></div>
                <div class="good_raw_info_name"><?php echo $good->name ?></div>
                <div class="good_row_price"><?php echo ActiveHtml::price($good->getEndPrice(), $good->currency) ?></div>
            </div>
        <?php if (Yii::app()->user->checkAccess(RBACFilter::getHierarchy()) ||
                  Yii::app()->user->checkAccess(RBACFilter::getHierarchy() .'Super') ||
                  Yii::app()->user->checkAccess(RBACFilter::getHierarchy() .'Own', array('purchase' => $purchase))): ?>
            <div class="good_row_controls" style="display: none">
                <div class="good_row_controls_bg"></div>
                <div class="good_row_icons">
                    <div class="left good_row_icon">
                        <div class="icon-pencil icon-white" onclick="return Purchase.goEditGood(this, '<?php echo $good->purchase_id ?>_<?php echo $good->good_id ?>', event)" onmouseover="Purchase.overIcon(this)" onmouseout="Purchase.outIcon(this)" ></div>
                    </div>
                    <div class="left good_row_icon">
                        <div class="icon-remove icon-white" onclick="return Purchase.deletegood(this, '<?php echo $good->purchase_id ?>_<?php echo $good->good_id ?>', event)" onmouseover="Purchase.overIcon(this)" onmouseout="Purchase.outIcon(this)" ></div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
            <div class="good_image_div"<?php if ($good->image): ?> style="background-image: url('<?php echo ActiveHtml::getImageUrl($good->image->image, 'd') ?>')"<?php endif; ?>></div>
        </a>
    </div>
</div>
<?php endforeach; ?>
<?php endif; ?>