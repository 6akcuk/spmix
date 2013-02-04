<div class="order_box_header">
  Реквизиты для оплаты заказов
  <a onclick="curBox().hide()" class="order_box_close right">Закрыть</a>
</div>
<div class="order_box_cont">
  <?php foreach ($orders as $order): ?>
  <div class="order_org_name">
    Организатор <?php echo ActiveHtml::link($order['author']->getDisplayName(), '/id'. $order['author']->id, array('_target' => 'blank')) ?>
  </div>
  <div class="order_list">
    Заказы <?php foreach ($order['items'] as $item): ?>№<?php echo $item->order_id ?> <?php endforeach; ?>
  </div>
  <div class="order_org_paydetails">
    <?php foreach ($order['author']->profile->paydetails as $detail): ?>
    <?php echo $detail->paysystem_name ?> <?php echo $detail->paysystem_details ?><br>
    <?php endforeach; ?>
  </div>
  <?php endforeach; ?>
</div>
<div class="order_box_buttons"></div>