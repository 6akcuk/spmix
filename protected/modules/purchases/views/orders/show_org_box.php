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
    $dd_sizes[$size->size . (($size->adv_price > 0) ? ' ['. ActiveHtml::price($size->adv_price) .']' : '')] = $size->size;
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

<div class="order_box_header">
  Заказ #<?php echo $order->order_id ?> от <?php echo ActiveHtml::lex(2, $order->customer->getDisplayName()) ?>
  <a onclick="curBox().hide()" class="order_box_close right">Закрыть</a>
</div>
<div class="order_box_cont">
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
    Место выдачи: <?php echo $order->oic ?>
  </div>

  <table class="order_org">
    <tr>
      <td class="label">Статус заказа</td>
      <td>
        <?php echo $form->dropdown($order, 'status', Order::getStatusDataArray()) ?>
      </td>
      <td class="label"></td>
      <td></td>
    </tr>
    <tr>
      <td class="label">Размер</div>
      <td>
        <?php echo $order->size ?>
      </td>
      <td class="label">Цвет</td>
      <td>
        <?php echo $order->color ?>
      </td>
    </tr>
    <tr>
      <td class="label">Артикул</td>
      <td>
        <?php echo $order->good->artikul ?>
      </td>
      <td class="label">Количество</td>
      <td>
        <?php echo $order->amount ?>
      </td>
    </tr>
    <tr>
      <td class="label">Цена</td>
      <td>
        <?php echo $form->textField($order, 'price') ?>
      </td>
      <td class="label">Орг. сбор</td>
      <td>
        <?php echo $form->textField($order, 'org_tax') ?>
      </td>
    </tr>
    <tr>
      <td class="label">Цена + орг. сбор</td>
      <td>
        <?php echo ActiveHtml::price($order->good->getEndCustomPrice($order->org_tax, $order->price)) ?>
      </td>
      <td class="label">Оплачено</td>
      <td>
        <?php echo $form->textField($order, 'payed') ?>
      </td>
    </tr>
    <tr>
      <td class="label">Комментарии для организатора</td>
      <td colspan="3">
        <?php echo $order->client_comment ?>
      </td>
    </tr>
    <tr>
      <td class="label">Комментарии организатора</td>
      <td colspan="3">
        <?php echo $form->smartTextarea($order, 'org_comment') ?>
      </td>
    </tr>
  </table>
  <?php $this->endWidget(); ?>
</div>

<div class="order_box_buttons">
  <a class="button" onclick="saveOrder()">Сохранить</a>
</div>
<a onclick="$(this).next().toggle()" class="order_box_history_link">История заказа</a>
<div class="order_box_history" style="display: none">
  <?php if ($order->history): ?>
  <?php foreach ($order->history as $history): ?>
    <div><?php echo $history->author->getDisplayName() .' '. ActiveHtml::date($history->datetime, true, true) .' '. Yii::t('purchase', $history->msg, json_decode($history->params, true)) ?></div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>
<script>
function saveOrder() {
  //if (A.saveOrder) return;
  //A.saveOrder = true;

  FormMgr.submit('#orderform', 'left', function(r) {
    if (r.success) {
      curBox().hide();
      boxPopup('Изменения в заказе успешно сохранены');
      $('#order<?php echo $order->order_id ?>_status').html(r.status);
      $('#order<?php echo $order->order_id ?>_total_price').html(r.total_price);
    }
  });
}
</script>