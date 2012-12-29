<?php
/**
 * @var $order Order
 * @var $form ActiveForm
 */

Yii::app()->getClientScript()->registerCssFile('/css/purchases.css');
Yii::app()->getClientScript()->registerScriptFile('/js/purchase.js');
Yii::app()->getClientScript()->registerCssFile('/css/photoview.css');
Yii::app()->getClientScript()->registerScriptFile('/js/photoview.js');

$this->pageTitle = Yii::app()->name .' - Заказ #'. $order->order_id .' - '. $order->good->name;

$dd_sizes = array();
$dd_colors = array();
$dd_oic = array();

$good = $order->good;

if ($good->grid) {
    foreach ($good->grid as $grid) {
        $colors = json_decode($grid->colors, true);
        $dd_sizes[$grid->size] = $grid->grid_id;
        foreach ($colors as $color) {
            $dd_colors[$grid->grid_id][$color] = $color;
        }
    }
}

if ($good->oic) {
    foreach ($good->oic as $oic) {
        $dd_oic[$oic->description .' '. ActiveHtml::price($oic->price)] = $oic->pk;
    }
}

?>

<h1>
    Заказ #<?php echo $order->order_id ?> <?php echo $order->good->name ?>
</h1>
<?php $form = $this->beginWidget('ext.ActiveHtml.ActiveForm', array(
    'id' => 'orderform',
    'action' => $this->createUrl('/order'. $order->order_id),
)); ?>
<div class="purchase_table clearfix">
    <div class="left photo">
        <?php if($order->good->image): ?>
        <a onclick="Purchase.showImages(<?php echo $order->good->good_id ?>)">
            <?php echo ActiveHtml::showUploadImage($order->good->image->image, 'a'); ?>
            <?php $images_num = $order->good->countImages() ?>
            <div class="subtitle">
                <div class="text">Просмотреть <?php echo Yii::t('app', 'фотографию|все {n} фотографии|все {n} фотографий', $images_num) ?></div>
            </div>
        </a>
        <?php endif; ?>
    </div>
    <div class="left td">
        <?php if ($good->grid): ?>
        <div class="row">
            <?php echo $form->dropdown($order, 'grid_id', $dd_sizes) ?>
        </div>
        <div class="row">
            <?php foreach ($dd_colors as $grid_id => $colors): ?>
            <div id="dd<?php echo $grid_id ?>" rel="colors" class="row" style="display:<?php echo ($order->grid_id == $grid_id) ? 'block' : 'none' ; ?>">
                <?php echo ActiveHtml::dropdown('color['. $grid_id .']', 'Цвет', $order->color, $colors) ?>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        <div class="row">
            <?php echo $form->inputPlaceholder($order, 'amount') ?>
        </div>
        <div class="row clearfix">
        <?php if ($order->customer_id == Yii::app()->user->getId()): ?>
            <?php echo $form->smartTextarea($order, 'client_comment', array('maxheight' => 250)) ?>
        <?php else: ?>
            <div class="left label">Комментарий для организатора:</div>
            <div class="left labeled"><?php echo nl2br($order->client_comment) ?></div>
        <?php endif; ?>
        </div>
        <div class="row">
            <?php echo $form->checkBox($order, 'anonymous') ?>
            <?php echo $form->label($order, 'anonymous') ?>
        </div>
        <?php if($good->oic): ?>
        <div class="row">
            Вы можете выбрать Центр Выдачи Заказов, если хотите самостоятельно забрать свой заказ <br/>
            <?php echo $form->dropdown($order, 'oic', $dd_oic) ?>
        </div>
        <?php endif; ?>
        <div class="clearfix">
            <div class="left label">Цена:</div>
            <div class="left labeled"><?php echo ActiveHtml::price($order->good->price, $order->good->currency) ?></div>
        </div>
        <div class="clearfix">
            <div class="left label">Орг. сбор:</div>
            <div class="left labeled"><?php echo $order->good->purchase->org_tax .'% - '. ActiveHtml::price($order->good->price * $order->good->purchase->org_tax / 100, $order->good->currency) ?></div>
        </div>
        <div class="clearfix">
            <div class="left label">Итог. цена:</div>
            <div class="left labeled"><?php echo ActiveHtml::price(floatval($order->good->price) * ($order->good->purchase->org_tax / 100 + 1), $order->good->currency) ?></div>
        </div>
        <div class="clearfix">
            <div class="left label">Статус закупки:</div>
            <div class="left labeled"><?php echo Yii::t('purchase', $order->purchase->state) ?></div>
        </div>
        <div class="clearfix">
        <?php if ($order->purchase->author_id == Yii::app()->user->getId()): ?>
            <?php echo $form->dropdown($order, 'status', Order::getStatusDataArray()) ?>
        <?php else: ?>
            <div class="left label">Статус заказа:</div>
            <div class="left labeled"><?php echo Yii::t('purchase', $order->status) ?></div>
        <?php endif; ?>
        </div>
        <div class="clearfix">
        <?php if ($order->purchase->author_id == Yii::app()->user->getId()): ?>
            <?php echo $form->smartTextarea($order, 'org_comment', array('maxheight' => 250)) ?>
        <?php else: ?>
            <div class="left label">Комментарий организатора:</div>
            <div class="left labeled"><?php echo nl2br($order->org_comment) ?></div>
        <?php endif; ?>
        </div>
    </div>
    <div class="row clearfix">
    <?php
        if (Yii::app()->user->checkAccess(RBACFilter::getHierarchy() .'Super') ||
            Yii::app()->user->checkAccess(RBACFilter::getHierarchy() .'Org', array('purchase' => $order->purchase)) ||
            in_array($order->purchase->state, array(Purchase::STATE_DRAFT, Purchase::STATE_CALL_STUDY)) ||
            (
                $order->purchase->state == Purchase::STATE_ORDER_COLLECTION &&
                in_array($order->status, array(Order::STATUS_PROCEEDING, Order::STATUS_REFUSED, Order::STATUS_ACCEPTED))
            ) ||
            (
                $order->purchase->state == Purchase::STATE_REORDER &&
                in_array($order->status, array(Order::STATUS_PROCEEDING, Order::STATUS_REFUSED))
            )
        ):
    ?>
        <a class="button" onclick="return FormMgr.submit('#orderform')">Сохранить изменения</a>
    <?php endif; ?>
    <?php
        if (in_array($order->purchase->state, array(Purchase::STATE_DRAFT, Purchase::STATE_CALL_STUDY)) ||
            (
                $order->purchase->state == Purchase::STATE_ORDER_COLLECTION &&
                    in_array($order->status, array(Order::STATUS_PROCEEDING, Order::STATUS_REFUSED, Order::STATUS_ACCEPTED))
            ) ||
            (
                in_array($order->status, array(Order::STATUS_PROCEEDING, Order::STATUS_REFUSED))
            )
        ):
    ?>
        <a class="button">Удалить</a>
    <?php endif; ?>
    </div>
</div>
<?php $this->endWidget(); ?>
<div class="purchase_fullstory">
    <?php echo nl2br($order->good->description) ?>
</div>