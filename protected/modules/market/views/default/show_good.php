<?php
/**
 * @var $good MarketGood
 * @var $form ActiveForm
 */

Yii::app()->getClientScript()->registerCssFile('/css/market.css');
Yii::app()->getClientScript()->registerScriptFile('/js/market.js');

Yii::app()->getClientScript()->registerCssFile('/css/photoview.css');
Yii::app()->getClientScript()->registerScriptFile('/js/photoview.js');
Yii::app()->getClientScript()->registerScriptFile('/js/jquery.cookie.js', null, 'after jquery-');

$this->pageTitle = Yii::app()->name .' - '. $good->name;
?>
<div class="breadcrumbs">
  <?php echo ActiveHtml::link($good->purchase->name, '/purchase'. $good->purchase_id) ?> &raquo;
  <?php echo $good->name ?>
</div>
<h1>
  <?php echo $good->name ?>
  <?php if (Yii::app()->user->checkAccess('purchases.goods.edit') &&
    (Yii::app()->user->checkAccess('purchases.goods.editSuper') ||
      Yii::app()->user->checkAccess('purchases.goods.editOwn', array('purchase' => $good->purchase)))): ?>
    <?php echo ActiveHtml::link('Редактировать', '/good'. $good->purchase_id .'_'. $good->good_id .'/edit', array('class' => 'button right')) ?>
  <?php endif; ?>
</h1>
<div class="purchase_table clearfix">
  <div class="left good_photo">
    <?php if($good->image): ?>
      <a onclick="Purchase.showImages(<?php echo $good->good_id ?>)">
        <?php echo ActiveHtml::showUploadImage($good->image->image, 'd'); ?>
        <?php $images_num = $good->countImages() ?>
        <div class="subtitle">
          <div class="text">Просмотреть <?php echo Yii::t('app', 'фотографию|все {n} фотографии|все {n} фотографий', $images_num) ?></div>
        </div>
      </a>
    <?php else: ?>
      <span>Фотография отсутствует</span>
    <?php endif; ?>
  </div>
  <div class="left td good_td">
    <?php $form = $this->beginWidget('ext.ActiveHtml.ActiveForm', array(
      'id' => 'orderform',
      'action' => $this->createUrl('/good'. $good->purchase_id .'_'. $good->good_id .'/order'),
    )); ?>
    <div class="clearfix price">
      <?php echo ActiveHtml::price($good->getEndPrice(), $good->currency) ?>
    </div>
    <?php if ($good->sizes): ?>
      <div class="clearfix row">
        <?php echo $form->dropdown($orderc, 'size', $dd_sizes) ?>
      </div>
      <div class="row small">размеры: <?php echo implode('; ', $row_sizes) ?></div>
    <?php endif; ?>
    <?php if ($good->colors): ?>
      <div class="clearfix row">
        <?php echo $form->dropdown($orderc, 'color', $dd_colors) ?>
      </div>
      <div class="row small">цвета: <?php echo implode('; ', $dd_colors) ?></div>
    <?php endif; ?>
    <div class="row">
      <?php echo $form->inputPlaceholder($orderc, 'amount') ?>
    </div>
    <div class="row">
      <?php echo $form->smartTextarea($orderc, 'client_comment', array('maxheight' => 250)) ?>
    </div>
    <div class="row">
      <?php echo $form->checkBox($orderc, 'anonymous') ?>
      <?php echo $form->label($orderc, 'anonymous') ?>
    </div>
    <div class="clearfix row">
      <?php if (!$oic): ?>
        <?php echo $form->dropdown($orderc, 'oic', $dd_oic) ?>
      <?php else: ?>
        <div id="oic_text">
          Место выдачи: <?php echo $oic->oic_name ?>
          <!--<span class="icon-remove" rel="tooltip" title="Удалить место" onclick="removeSavedOic()"></span> -->
        </div>
        <div id="oic" class="clearfix" style="display:none">
          <?php echo $form->dropdown($orderc, 'oic', $dd_oic) ?>
        </div>
        <script>
          function removeSavedOic() {
            $('#oic_text').remove();
            $('#oic').show();
          }
        </script>
      <?php endif; ?>
    </div>
    <div class="row">
      <?php if (in_array($good->purchase->state, array(Purchase::STATE_CALL_STUDY, Purchase::STATE_ORDER_COLLECTION, Purchase::STATE_REORDER))): ?>
        <a class="button" onclick="return Purchase.order()">Заказать</a>
      <?php else: ?>
        <div class="op_error">
          Заказ товаров приостановлен
        </div>
      <?php endif; ?>
    </div>
    <?php $this->endWidget(); ?>
    <?php if ($good->url): ?>
      <div class="row">
        <?php echo ActiveHtml::link('Информация о товаре на сайте поставщика', $good->url, array('target' => '_blank')) ?>
      </div>
    <?php endif; ?>
    <div class="row purchase_links" style="margin-left: -4px">
      <a id="subscribe<?php echo $good->good_id ?>" onclick="Purchase.subscribeGood(<?php echo $good->good_id ?>)"><span class="icon-check"></span> <?php echo ($subscription) ? "Отписаться от новостей" : "Подписаться на новости" ?></a>
      <a onclick="Purchase.shareGoodToFriends(<?php echo $good->good_id ?>)"><span class="icon-comment"></span> Рассказать друзьям</a>
    </div>
  </div>
</div>
<div data-link="#tabs_content" class="tabs">
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
        <?php foreach ($struct as $col): ?>
          <td>
            <?php echo (isset($col['size'])) ? 'Размер ('. $col['size'] .')' : '' ?>
            <?php echo (isset($col['color'])) ? 'Цвет ('. $col['color'] .')' : '' ?>
          </td>
        <?php endforeach; ?>
        <td>Собран</td>
      </tr>
      </thead>
      <tbody>
      <?php if ($ranges): ?>
        <?php foreach ($ranges as $range => $range_data): ?>
          <?php $filled = 0; ?>
          <tr>
            <td>Ряд <?php echo $range ?> <?php echo $range_data['tag'] ?></td>
            <?php foreach ($range_data['items'] as $item): ?>
              <td><?php if ($item) { echo ($item->anonymous) ? 'анонимно' : ActiveHtml::link($item->customer->login, '/id'. $item->customer_id); $filled++; } ?></td>
            <?php endforeach; ?>
            <td><?php echo ($filled == sizeof($range_data['items'])) ? 'Да' : 'Нет'; ?></td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
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
            <td><?php echo $order->size ?></td>
            <td><?php echo $order->color ?></td><td><?php echo ActiveHtml::price($order->price); $sum += ($order->price * $order->amount) ?></td>
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
<div style="margin-top: 10px">
  <?php $this->widget('Comments', array('hoop' => $good->purchase, 'hoop_id' => $good->good_id, 'hoop_type' => 'good', 'reply' => $reply)) ?>
</div>