<?php
/**
 * @var $good Good
 * @var $purchase Purchase
 */

Yii::app()->getClientScript()->registerCssFile('/css/purchases.css');
Yii::app()->getClientScript()->registerScriptFile('/js/purchase.js');

$this->pageTitle = Yii::app()->name .' - Товары к закупке #'. $purchase->purchase_id;
?>

<h1>
    Товары к закупке #<?php echo $purchase->purchase_id ?> "<?php echo $purchase->name ?>"
    <div class="right">
        <?php echo ActiveHtml::link('Добавить товар', '/purchase'. $purchase->purchase_id .'/addgood', array('class' => 'button')) ?>
    </div>
</h1>

<div class="clearfix">
    <div class="left sortlimit">
        Выводить по:
        <?php echo ActiveHtml::link('10', '/goods'. $purchase->purchase_id .'?c[limit]=10', ($c['limit'] == 10) ? array('class' => 'selected') : array()) ?>
        <?php echo ActiveHtml::link('30', '/goods'. $purchase->purchase_id .'?c[limit]=30', ($c['limit'] == 30) ? array('class' => 'selected') : array()) ?>
        <?php echo ActiveHtml::link('50', '/goods'. $purchase->purchase_id .'?c[limit]=50', ($c['limit'] == 50) ? array('class' => 'selected') : array()) ?>
        <?php echo ActiveHtml::link('100', '/goods'. $purchase->purchase_id .'?c[limit]=100', ($c['limit'] == 100) ? array('class' => 'selected') : array()) ?>
    </div>
    <div class="right">

    </div>
</div>

        <table style="margin-top: 10px">
            <thead>
            <tr>
                <td>
    <div rel="filters" class="filter_order_id">
        <?php echo ActiveHtml::inputPlaceholder(
        'c[id]',
        (isset($c['id'])) ? $c['id'] : '',
        array('placeholder' => 'ID')
    ); ?>
    </div>
        </td><td>


        </td>
        <td>
            <div rel="filters" class="filter_order_artikul">
                <?php echo ActiveHtml::inputPlaceholder(
                'c[artikul]',
                (isset($c['artikul'])) ? $c['artikul'] : '',
                array('placeholder' => 'Артикул')
            ); ?>
            </div>
        </td>
        <td>
            <div rel="filters" class="left">
                <?php echo ActiveHtml::inputPlaceholder(
                'c[name]',
                (isset($c['name'])) ? $c['name'] : '',
                array('placeholder' => 'Название')
            ); ?>
            </div>
        </td>
                <td>
                    <div rel="filters" class="filter_order_cena">
                        <?php echo ActiveHtml::inputPlaceholder(
                        'c[price]',
                        (isset($c['price'])) ? $c['price'] : '',
                        array('placeholder' => 'Цена')
                    ); ?>
                    </div></td>
                <td></td>
                <td></td>
                <td></td>
</tr>
    <tr>
        <td>ID</td><td>Изображение</td><td>Артикул</td><td>Название</td><td>Цена</td><td>Ряды</td>
        <td>Кол-во заказов</td><td>Сумма заказов</td>
    </tr>
    </thead>
    <tbody id="goods">
    <?php foreach ($goods as $good): ?>
    <tr>
        <td><?php echo $good->good_id ?></td>
        <td><?php echo ActiveHtml::showUploadImage(($good->image) ? $good->image->image : '', 'b') ?></td>
        <td><?php echo $good->artikul ?></td>
        <td><?php echo ActiveHtml::link($good->name, '/good'. $good->purchase_id .'_'. $good->good_id) ?></td>
        <td><?php echo ActiveHtml::price($good->price) ?></td>
        <td></td>
        <td><?php echo Yii::t('purchase', '{n} заказ|{n} заказа|{n} заказов', $good->ordersNum) ?></td>
        <td><?php echo ActiveHtml::price($good->ordersSum) ?></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>