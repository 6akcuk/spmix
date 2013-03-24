<?php
/**
 * @var $order Order
 * @var $form ActiveForm
 * @var $history OrderHistory
 */

Yii::app()->getClientScript()->registerCssFile('/css/orders.css');
Yii::app()->getClientScript()->registerCssFile('/css/purchases.css');
Yii::app()->getClientScript()->registerScriptFile('/js/purchase.js');
Yii::app()->getClientScript()->registerCssFile('/css/photoview.css');
Yii::app()->getClientScript()->registerScriptFile('/js/photoview.js');

$this->pageTitle = Yii::app()->name .' - Заказ #'. $order->order_id .' - '. $order->good->name;

$dd_sizes = array();
$dd_colors = array();
$dd_oic = array();

$good = $order->good;

if ($good->sizes) {
  foreach ($good->sizes as $size) {
    $dd_sizes[$size->size . (($size->adv_price > 0) ? ' ['. ActiveHtml::price($good->getEndPrice($size->adv_price, $order->delivery)) .']' : '')] = $size->size;
  }
}

if ($good->colors) {
  foreach ($good->colors as $color) {
    $dd_colors[$color->color] = $color->color;
  }
}

if ($good->oic) {
  foreach ($good->oic as $oic) {
    $dd_oic[$oic->description .' '. ActiveHtml::price($oic->price)] = $oic->pk;
  }
}

?>

<h1>
  Заказ #<?php echo $order->order_id ?> от <?php echo ActiveHtml::lex(2, $order->customer->getDisplayName()) ?>
</h1>
<?php $form = $this->beginWidget('ext.ActiveHtml.ActiveForm', array(
  'id' => 'orderform',
  'action' => $this->createUrl('/order-'. $order->order_id),
)); ?>

<div class="order_org_info">
  Название: <?php echo ActiveHtml::link($order->good->name, '/good'. $order->purchase_id .'_'. $order->good_id) ?>
</div>
<div class="order_org_info">
  Телефон заказчика: <?php echo $order->customer->profile->phone ?>
</div>
<div class="order_org_info">
  Место выдачи: <?php echo $order->oic->oic_name .' '. $order->oic->oic_price ?>
</div>

<table class="order_org">
  <tr>
    <td>Статус заказа</td>
    <td>
      <?php echo $form->dropdown($order, 'status', Order::getStatusDataArray()) ?>
    </td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td>Размер</div>
    <td>
      <?php echo $order->size ?>
    </td>
    <td>Цвет</td>
    <td>
      <?php echo $order->color ?>
    </td>
  </tr>
  <tr>
    <td>Артикул</td>
    <td>
      <?php echo $order->good->artikul ?>
    </td>
    <td>Количество</td>
    <td>
      <?php echo $order->amount ?>
    </td>
  </tr>
  <tr>
    <td>Цена</td>
    <td>
      <?php echo $form->textField($order, 'price') ?>
    </td>
    <td>Орг. сбор</td>
    <td>
      <?php echo $form->textField($order, 'org_tax') ?>
    </td>
  </tr>
  <tr>
    <td class="label">Стоимость доставки</td>
    <td>
      <?php echo $form->textField($order, 'delivery') ?>
    </td>
    <td>Оплачено</td>
    <td>
      <?php echo $form->textField($order, 'payed') ?>
    </td>
  </tr>
  <tr>
    <td class="label">Цена + орг. сбор</td>
    <td colspan="3">
      <?php echo ActiveHtml::price($order->good->getEndCustomPrice($order->org_tax, $order->price, $order->delivery)) ?>
    </td>
  </tr>
  <tr>
    <td>Комментарии для организатора</td>
    <td colspan="3">
      <?php echo $order->client_comment ?>
    </td>
  </tr>
  <tr>
    <td>Комментарии организатора</td>
    <td colspan="3">
      <?php echo $form->smartTextarea($order, 'org_comment') ?>
    </td>
  </tr>
</table>
<?php $this->endWidget(); ?>

<div class="order_buttons clearfix">
  <a class="button" onclick="FormMgr.submit('#orderform', 'left')">Сохранить</a>
</div>
<a onclick="$(this).next().toggle()">История заказа</a>
<div class="order_history" style="display: none">
  <?php if ($order->history): ?>
  <?php foreach ($order->history as $history): ?>
    <div><?php echo $history->author->getDisplayName() .' '. ActiveHtml::date($history->datetime, true, true) .' '. Yii::t('purchase', $history->msg, json_decode($history->params, true)) ?></div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>