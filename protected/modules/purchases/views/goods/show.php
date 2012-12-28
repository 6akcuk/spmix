<?php
/**
 * @var $good Good
 * @var $form ActiveForm
 * @var $oic PurchaseOic
 * @var $grid GoodGrid
 * @var $order Order
 * @var $range GoodRange
 */

Yii::app()->getClientScript()->registerCssFile('/css/purchases.css');
Yii::app()->getClientScript()->registerScriptFile('/js/purchase.js');
Yii::app()->getClientScript()->registerCssFile('/css/photoview.css');
Yii::app()->getClientScript()->registerScriptFile('/js/photoview.js');

$this->pageTitle = Yii::app()->name .' - '. $good->purchase->name .' - '. $good->name;

$dd_sizes = array();
$dd_colors = array();
$dd_oic = array();

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
    <?php echo $good->name ?>
    <?php if (Yii::app()->user->checkAccess('purchases.goods.edit') &&
              (Yii::app()->user->checkAccess('purchases.goods.editSuper') ||
               Yii::app()->user->checkAccess('purchases.goods.editOwn', array('purchase' => $good->purchase)))): ?>
    <?php echo ActiveHtml::link('Редактировать', '/good'. $good->purchase_id .'_'. $good->good_id .'/edit', array('class' => 'button right')) ?>
    <?php endif; ?>
</h1>
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
        <?php if ($good->grid): ?>
        <div class="row">
            <?php echo $form->dropdown($order, 'grid_id', $dd_sizes) ?>
        </div>
        <div class="row">
        <?php foreach ($dd_colors as $grid_id => $colors): ?>
            <div id="dd<?php echo $grid_id ?>" rel="colors" class="row" style="display:none">
                <?php echo ActiveHtml::dropdown('color['. $grid_id .']', 'Цвет', '', $colors) ?>
            </div>
        <?php endforeach; ?>
        </div>
        <?php endif; ?>
        <div class="row">
            <?php echo $form->inputPlaceholder($order, 'amount') ?>
        </div>
        <div class="row">
            <?php echo $form->smartTextarea($order, 'client_comment', array('maxheight' => 250)) ?>
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
            <div class="left labeled"><?php echo ActiveHtml::price($good->price, $good->currency) ?></div>
        </div>
        <div class="clearfix">
            <div class="left label">Орг. сбор:</div>
            <div class="left labeled"><?php echo $good->purchase->org_tax .'% - '. ActiveHtml::price($good->price * $good->purchase->org_tax / 100, $good->currency) ?></div>
        </div>
        <div class="clearfix">
            <div class="left label">Итог. цена:</div>
            <div class="left labeled"><?php echo ActiveHtml::price(floatval($good->price) * ($good->purchase->org_tax / 100 + 1), $good->currency) ?></div>
        </div>
        <div class="row">
            <?php if (in_array($good->purchase->state, array(Purchase::STATE_CALL_STUDY, Purchase::STATE_ORDER_COLLECTION, Purchase::STATE_REORDER))): ?>
            <a class="button" onclick="return Purchase.order()">Заказать</a>
            <?php else: ?>
            <div class="error">
                Заказ товаров приостановлен
            </div>
            <?php endif; ?>
        </div>
        <?php $this->endWidget(); ?>
    </div>
</div>
<div id="tabs" data-link="#tabs_content" class="tabs">
    <a target="div.purchase_fullstory" class="selected">Описание</a>
    <?php if ($good->is_range): ?><a target="div.purchase_range">Заполнение рядов</a><?php endif; ?>
    <a target="div.purchase_customers">Список заказов</a>
</div>
<div id="tabs_content">
    <div class="purchase_fullstory">
        <?php echo nl2br($good->description) ?>
    </div>
    <div class="purchase_range" style="display:none">
        <table class="data">
        <thead>
        <tr>
            <td>Строки/Столбцы</td>
            <?php foreach ($good->grid as $grid): ?>
            <td><?php echo $grid->size ?></td>
            <?php endforeach; ?>
            <td>Собран</td>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($good->ranges as $row => $range): ?>
        <tr>
            <td>Ряд <?php echo ($row+1) ?></td>
            <?php ?>
            <td><?php echo ($range->filled) ? 'Да' : 'Нет' ?></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
        </table>
    </div>
    <div class="purchase_customers" style="display:none">
        <table class="data">
        <thead>
        <tr>
            <td>Номер</td><td>Дата заказа</td><td>Пользователь</td><td>Размер</td><td>Цвет</td><td>Цена</td>
            <td>Кол</td><td>Итого (с орг.сбором)</td>
        </tr>
        </thead>
        <tbody>
        <?php $sum = 0.00; ?>
        <?php if ($good->orders): ?>
        <?php foreach ($good->orders as $order): ?>
        <tr>
            <td><?php echo $order->order_id ?></td><td><?php echo ActiveHtml::date($order->creation_date, false, true) ?></td>
            <td><?php echo ($order->anonymous) ? 'анонимно' : ActiveHtml::link($order->customer->login, '/id'. $order->customer_id) ?></td>
            <td><?php echo ($order->grid) ? $order->grid->size : '' ?></td>
            <td><?php echo $order->color ?></td><td><?php echo ActiveHtml::price($order->price); $sum += $order->price ?></td>
            <td><?php echo $order->amount ?></td><td><?php echo ActiveHtml::price($order->total_price) ?></td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
        </table>

        Количество заказов: <b><?php echo $good->ordersNum ?></b><br/>
        Всего на сумму (без орг.сбора): <b><?php echo ActiveHtml::price($sum) ?></b>
    </div>
</div>
<script type="text/javascript">
$().ready(function() {
    $('#Order_grid_id').change(switchSize);
});
function switchSize() {
    $('[rel="colors"]').hide();
    $('#dd'+ $(this).val()).show();
}
</script>