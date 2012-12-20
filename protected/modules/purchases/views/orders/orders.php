<?php
/**
 * @var $order Order
 */

Yii::app()->getClientScript()->registerCssFile('/css/purchases.css');
Yii::app()->getClientScript()->registerScriptFile('/js/purchase.js');

$this->pageTitle = Yii::app()->name .' - Заказы';
?>

<h1>Заказы</h1>
<table>
    <thead>
    <tr>
        <td>
            <input type="checkbox" />
        </td>
        <td>#</td><td>Дата заказа</td><td>Товар</td><td>Цвет</td><td>Размер</td><td>Заказчик</td>
        <td>Город</td><td>Репутация</td><td>Статус</td><td>Кол-во</td><td>Стоимость</td>
    </tr>
    </thead>
    <tbody id="orders">
    <?php foreach ($orders as $order): ?>
    <tr>
        <td><?php echo ActiveHtml::checkBox('select['. $order->order_id .']') ?></td>
        <td>
            <?php echo ActiveHtml::link('Зак№'. $order->order_id, '/order'. $order->order_id) ?><br/>
            <?php if ($order->payment): ?>
            <?php echo ActiveHtml::link('Платеж №'. $order->payment->payment_id .' от '. ActiveHtml::date($order->payment->datetime, false, true), '/payment'. $order->payment->payment_id) ?>
            <?php echo Yii::t('purchase', $order->payment->status) ?>
            <?php endif; ?>
        </td>
        <td><?php echo ActiveHtml::date($order->creation_date) ?></td>
        <td><?php echo ActiveHtml::link($order->good->name, '/good'. $order->purchase_id .'_'. $order->good_id) ?></td>
        <td><?php echo $order->color ?></td>
        <td><?php echo $order->size ?></td>
        <td><?php echo $order->customer->login .' '. $order->customer->profile->firstname .' '. $order->customer->profile->lastname ?></td>
        <td><?php echo $order->customer->profile->city->name ?></td>
        <td><?php echo $order->customer->profile->positive_rep .' | '. $order->customer->profile->negative_rep ?></td>
        <td><?php echo Yii::t('purchase', $order->status) ?></td>
        <td><?php echo $order->amount ?></td>
        <td><?php echo ActiveHtml::price($order->total_price) ?></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>