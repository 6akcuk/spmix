<?php
/**
 * @var $order Order
 * @var $purchase Purchase
 */

Yii::app()->getClientScript()->registerCssFile('/css/purchases.css');
Yii::app()->getClientScript()->registerScriptFile('/js/purchase.js');
Yii::app()->getClientScript()->registerScriptFile('/js/orders.js');

Yii::app()->getClientScript()->registerCssFile('/css/pagination.css');
Yii::app()->getClientScript()->registerScriptFile('/js/pagination.js');

$this->pageTitle = Yii::app()->name .' - Заказы к закупке #'. $purchase->purchase_id;
$delta = $c['limit'];
?>
<div id="_box_hidden_status" style="display:none">
  <div class="row">
    <?php echo ActiveHtml::dropdown('new_state', 'Новый статус', '', Purchase::getStateDataArray()) ?>
  </div>
  <div class="row">
    <?php echo ActiveHtml::smartTextarea('org_comment', '', array('placeholder' => 'Комментарий организатора')) ?>
  </div>
</div>

<div class="my_orders">
<h1>
    Заказы к закупке #<?php echo $purchase->purchase_id ?> "<?php echo $purchase->name ?>"
</h1>

<div class="orders_controlbar">
  <a class="button" onclick="Order.massChangeStatus()">Изменение статуса заказов</a>
  <a class="button" onclick="Order.massSendMessage()">Отправка ЛС</a>
  <a class="button" onclick="Order.massSendSMS()">Отправка СМС</a>
  <?php echo CHtml::link('Сохранить в Excel', '/orders'. $purchase->purchase_id .'/excel', array('class' => 'button')) ?>
</div>

<div class="clearfix">
    <div class="left sortlimit">
        Выводить по:
        <?php echo ActiveHtml::link('10', '/orders'. $purchase->purchase_id .'?c[limit]=10', ($c['limit'] == 10) ? array('class' => 'selected') : array()) ?>
        <?php echo ActiveHtml::link('30', '/orders'. $purchase->purchase_id .'?c[limit]=30', ($c['limit'] == 30) ? array('class' => 'selected') : array()) ?>
        <?php echo ActiveHtml::link('50', '/orders'. $purchase->purchase_id .'?c[limit]=50', ($c['limit'] == 50) ? array('class' => 'selected') : array()) ?>
        <?php echo ActiveHtml::link('100', '/orders'. $purchase->purchase_id .'?c[limit]=100', ($c['limit'] == 100) ? array('class' => 'selected') : array()) ?>
    </div>
    <div class="right">
      <div class="right">
        <?php $this->widget('Paginator', array(
        'url' => '/orders'. $purchase->purchase_id,
        'offset' => $offset,
        'offsets' => $offsets,
        'delta' => $delta,
      )); ?>
      </div>
    </div>
</div>
<div class="clearfix filters">
    <table>
        <thead>
        <tr>
            <td>
                Фильтр по городу :
            </td>
            <td>
                <div rel="filters" class="filter_order_gorod">
                <?php echo ActiveHtml::dropdown(
                'c[city_id]',
                'Город',
                (isset($c['city_id'])) ? $c['city_id'] : '',
                City::getDataArray()
            ); ?>
            </div>
            </td>
        </tr>
        <tr>
            <td>
                Фильтр по статусу:
            </td>
            <td>
                <div rel="filters" class="filter_order_status">
                    <?php echo ActiveHtml::dropdown(
                    'c[status]',
                    'Статус',
                    (isset($c['status'])) ? $c['status'] : '',
                    Order::getStatusDataArray()
                ); ?>
                </div>
            </td>
        </tr>
        </thead>
    </table>
</div>
    <table class="ordertable">
        <thead>
        <tr>
    <td>
        </td>
            <td>

    <div rel="filters" class="left filter_order_id">
        <?php echo ActiveHtml::inputPlaceholder(
        'c[id]',
        (isset($c['id'])) ? $c['id'] : '',
        array('placeholder' => 'ID')
    ); ?>
    </div>
    </td>
    <td>
    <div rel="filters" class="left filter_order_date">
        <?php echo ActiveHtml::inputCalendar(
        'c[creation_date]',
        (isset($c['creation_date'])) ? $c['creation_date'] : '',
        'Дата заказа'
    ); ?>
    </div>
    </td>
    <td>
    <div rel="filters" class="left filter_order_tovar">
        <?php echo ActiveHtml::inputPlaceholder(
        'c[good]',
        (isset($c['good'])) ? $c['good'] : '',
        array('placeholder' => 'Товар')
    ); ?>
    </div>
    </td>
    <td>
    <div rel="filters" class="left filter_order_artikul">
        <?php echo ActiveHtml::inputPlaceholder(
        'c[artikul]',
        (isset($c['artikul'])) ? $c['artikul'] : '',
        array('placeholder' => 'Артикул')
    ); ?>
    </div>
    </td>
    <td>
        <div rel="filters" class="left filter_order_cvet">
            <?php echo ActiveHtml::inputPlaceholder(
            'c[color]',
            (isset($c['color'])) ? $c['color'] : '',
            array('placeholder' => 'Цвет')
        ); ?>
        </div>
    </td>
    <td>
        <div rel="filters" class="left filter_order_razmer">
            <?php echo ActiveHtml::inputPlaceholder(
            'c[size]',
            (isset($c['size'])) ? $c['size'] : '',
            array('placeholder' => 'Размер')
        ); ?>
        </div>
    </td>
    <td>
    <div rel="filters" class="left filter_order_zakaz">
        <?php echo ActiveHtml::inputPlaceholder(
        'c[name]',
        (isset($c['name'])) ? $c['name'] : '',
        array('placeholder' => 'Заказчик')
    ); ?>
    </div>
    </td>
    <td>
    </td>
            <td></td>
    <td>
    </td>
            <td></td>
            <td>
            </td>
    </tr>
    <tr>
        <td>
            <input type="checkbox" />
        </td>
        <td>#</td><td>Дата заказа</td><td>Товар</td><td>Артикул</td><td>Цвет</td><td>Размер</td><td>Заказчик</td>
        <td>Город</td><td>Реп.</td><td>Статус</td><td>Кол</td><td>Стоимость</td>
    </tr>
    </thead>
    <tbody id="orders">
    <?php $this->renderPartial('_order', array('orders' => $orders, 'offset' => $offset, 'c' => $c)) ?>
    </tbody>
</table>
</div>