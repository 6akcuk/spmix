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

<h1>
    Заказ #<?php echo $order->order_id ?>, <?php echo ActiveHtml::date($order->creation_date, true) ?>
</h1>
<?php $form = $this->beginWidget('ext.ActiveHtml.ActiveForm', array(
    'id' => 'orderform',
    'action' => $this->createUrl('/order'. $order->order_id),
)); ?>
<div class="order_table clearfix">
  <div class="left photo">
  <?php if($order->good->image): ?>
    <?php echo ActiveHtml::showUploadImage($order->good->image->image, 'b'); ?>
  <?php else: ?>
    <span>Фотография отсутствует</span>
  <?php endif; ?>
  </div>
  <div class="left info">
    <div class="order_row clearfix">
      <div class="order_row_labeled">Закупка</div>
      <div class="order_row_label">
        <?php echo ActiveHtml::link($order->purchase->name, '/purchase'. $order->purchase_id) ?>
      </div>
    </div>
    <div class="order_row clearfix">
      <div class="order_row_labeled">Название</div>
      <div class="order_row_label">
        <?php echo ActiveHtml::link($order->good->name, '/good'. $order->purchase_id .'_'. $order->good_id) ?>
      </div>
    </div>
    <div class="order_row clearfix">
      <div class="order_row_labeled">Артикул</div>
      <div class="order_row_label">
        <?php echo $order->good->artikul ?>
      </div>
    </div>
    <?php if ($good->sizes): ?>
    <div class="order_row clearfix">
      <div class="order_row_labeled">Размер</div>
      <div class="order_row_label">
        <?php if ($order->customer_id == Yii::app()->user->getId() && $order->canEdit()): ?>
        <?php echo $form->dropdown($order, 'size', $dd_sizes) ?>
        <?php else: ?>
        <?php echo $order->size ?>
        <?php endif; ?>
      </div>
    </div>
    <?php endif; ?>
    <?php if ($good->colors): ?>
    <div class="order_row clearfix">
      <div class="order_row_labeled">Цвет</div>
      <div class="order_row_label">
        <?php if ($order->customer_id == Yii::app()->user->getId() && $order->canEdit()): ?>
        <?php echo $form->dropdown($order, 'color', $dd_colors) ?>
        <?php else: ?>
        <?php echo $order->color ?>
        <?php endif; ?>
      </div>
    </div>
    <?php endif; ?>
    <div class="order_row clearfix">
      <div class="order_row_labeled">Цена</div>
      <div class="order_row_label">
        <?php echo ActiveHtml::price($order->price) ?>
      </div>
    </div>
    <div class="order_row clearfix">
      <div class="order_row_labeled">Орг. сбор</div>
      <div class="order_row_label">
        <?php echo $order->org_tax ?> %
      </div>
    </div>
    <div class="order_row clearfix">
      <div class="order_row_labeled">Цена + орг. сбор</div>
      <div class="order_row_label">
        <?php echo ActiveHtml::price($order->good->getEndCustomPrice($order->org_tax, $order->price)) ?>
      </div>
    </div>
    <div class="order_row clearfix">
      <div class="order_row_labeled">Количество</div>
      <div class="order_row_label">
        <?php if ($order->customer_id == Yii::app()->user->getId() && $order->canEdit()): ?>
        <?php echo $form->textField($order, 'amount') ?>
        <?php else: ?>
        <?php echo $order->color ?>
        <?php endif; ?>
      </div>
    </div>
    <div class="order_row clearfix">
      <div class="order_row_labeled">Комментарии для организатора</div>
      <div class="order_row_label">
        <?php if ($order->customer_id == Yii::app()->user->getId() && $order->canEdit()): ?>
        <?php echo $form->textArea($order, 'client_comment') ?>
        <?php else: ?>
        <?php echo $order->client_comment ?>
        <?php endif; ?>
      </div>
    </div>
    <div class="order_row clearfix">
      <div class="order_row_labeled">Анонимно</div>
      <div class="order_row_label">
        <?php if ($order->customer_id == Yii::app()->user->getId() && $order->canEdit()): ?>
        <?php echo $form->checkBox($order, 'anonymous') ?>
        <?php else: ?>
        <?php echo ($order->anonymous == 1) ? 'Да' : 'Нет' ?>
        <?php endif; ?>
      </div>
    </div>
    <div class="order_row clearfix">
      <div class="order_row_labeled">Место выдачи</div>
      <div class="order_row_label">
        <?php echo $order->oic->oic_name .' '. $order->oic->oic_price ?>
      </div>
    </div>
  </div>
</div>
<?php $this->endWidget(); ?>
<div class="order_table clearfix">
  <div class="left photo">&nbsp;</div>
  <div class="left info">
    <div class="order_row clearfix">
      <div class="order_row_labeled">Организатор</div>
      <div class="order_row_label">
        <?php echo ActiveHtml::link($order->purchase->author->getDisplayName(), '/id'. $order->purchase->author_id) ?>
      </div>
    </div>
    <div class="order_row clearfix">
      <div class="order_row_labeled">Статус закупки</div>
      <div class="order_row_label">
        <b><?php echo Yii::t('purchase', $order->purchase->state) ?></b>
      </div>
    </div>
    <div class="order_row clearfix">
      <div class="order_row_labeled">Статус заказа</div>
      <div class="order_row_label">
        <b><?php echo Yii::t('purchase', $order->status) ?></b>
      </div>
    </div>
    <div class="order_row clearfix">
      <div class="order_row_labeled">Комментарии организатора</div>
      <div class="order_row_label">
        <b><?php echo nl2br($order->org_comment) ?></b>
      </div>
    </div>
    <div class="order_row clearfix">
      <div class="order_row_labeled">Оплачено</div>
      <div class="order_row_label">
        <b><?php echo ActiveHtml::price($order->payed) ?></b>
      </div>
    </div>
    <div class="order_row clearfix">
      <div class="order_row_labeled">Ваш долг</div>
      <div class="order_row_label">
        <b><?php echo ActiveHtml::price($order->total_price - $order->payed) ?></b>
      </div>
    </div>
  </div>
</div>
<div class="order_buttons clearfix">
  <?php
  if ($order->canEdit()):
    ?>
    <a class="button" onclick="return FormMgr.submit('#orderform')">Сохранить изменения</a>
    <?php endif; ?>
  <?php
  if ($order->canDelete()):
    ?>
    <a class="button" onclick="deleteOrder()">Удалить заказ</a>
    <?php endif; ?>
  <?php if ($order->status == Order::STATUS_WAIT_FOR_DELIVER): ?>
    <a class="button" onclick="markAsDelivered()">Отметить заказ как полученный</a>
  <?php endif; ?>
</div>
<div class="order_help">
  <p>- удалить или изменить заказ возможно только при определенных статусах заказа и закупки;</p>
  <p>- при внесении изменений в заказ, статус заказа будет изменен на "В обработке";</p>
  <p>- при удалении заказа, восстановить заказ будет невозможно;</p>
  <p>- заказ можно отметить как полученный только если заказ оплачен.</p>
</div>
<a onclick="$(this).next().toggle()">История заказа</a>
<div class="order_history" style="display: none">
  <?php if ($order->history): ?>
  <?php foreach ($order->history as $history): ?>
    <div><?php echo $history->author->getDisplayName() .' '. ActiveHtml::date($history->datetime, true, true) .' '. Yii::t('purchase', $history->msg, json_decode($history->params, true)) ?></div>
  <?php endforeach; ?>
  <?php endif; ?>
</div>
<script>
function deleteOrder() {
  showConfirmBox('Вы действительно хотите удалить заказ? Это действие необратимо.', 'Да, удалить', function() {
    if (A.deleteOrder) return;
    A.deleteOrder = true;

    ajax.post('/purchases/orders/deleteOrder', {id: <?php echo $order->order_id ?>}, function(r) {
      A.deleteOrder = false;
      if (r.success) {
        curBox().hide();
        boxPopup('Заказ успешно удален');
        nav.go('/orders', null);
      }
    }, function(r) {
      A.deleteOrder = false;
    });
  }, 'Нет, отменить', function() {
    curBox().hide();
  });
}
function markAsDelivered() {
  if (A.markingOrder) return;
  A.markingOrder = true;

  ajax.post('/purchases/orders/markOrderAsDelivered', {id: <?php echo $order->order_id ?>}, function(r) {
    A.markingOrder = false;
    if (r.success) {
      boxPopup('Заказ успешно отмечен как полученный');
    }
  }, function(r) {
    A.markingOrder = false;
  });
}
</script>