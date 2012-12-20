<?php
/** @var $model Purchase */

Yii::app()->getClientScript()->registerCssFile('/css/purchases.css');
Yii::app()->getClientScript()->registerScriptFile('/js/purchase.js');

$this->pageTitle = Yii::app()->name .' - Мои закупки';
?>

<h1>
    Мои закупки
    <?php echo ActiveHtml::link('Все закупки', '/purchases', array('class' => 'right')) ?>
</h1>

<div class="search">
    <span class="iconify iconify_search_b"></span>
    <input type="text" name="q" data-url="" value="" />
    <div class="progress"></div>
</div>
<table>
    <thead>
    <tr>
        <td>
            <input type="checkbox" />
        </td>
        <td>#</td><td>Дата создания</td><td>Категория</td><td>Название</td><td>Статус</td><td>Кол-во товаров</td>
        <td>Кол-во заказов</td><td></td>
    </tr>
    </thead>
    <tbody id="purchases">
    <?php $this->renderPartial('_listtable', array('purchases' => $purchases)) ?>
    </tbody>
</table>