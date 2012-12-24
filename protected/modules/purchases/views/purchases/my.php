<?php
/** @var $model Purchase */

Yii::app()->getClientScript()->registerCssFile('/css/purchases.css');
Yii::app()->getClientScript()->registerScriptFile('/js/purchase.js');

$this->pageTitle = Yii::app()->name .' - Мои закупки';

foreach (Purchase::getStateDataArray() as $state) {
    $statesJs[] = "'". Yii::t('purchase', $state) ."'";
}
?>

<h1>
    Мои закупки
    <?php echo ActiveHtml::link('Все закупки', '/purchases', array('class' => 'right')) ?>
</h1>

<div class="clearfix">
    <div class="left">
        Выводить по:
            <a onclick="">10</a>
    </div>
</div>
<div class="search">
    <span class="iconify iconify_search_b"></span>
    <input type="text" name="q" data-url="" value="" />
    <div class="progress"></div>
</div>
<table>
    <thead>
    <tr>
        <td>ID</td><td>Создана</td><td>Категория</td><td>Название</td><td>Статус</td><td>Стоп заказов</td>
        <td>Кол-во товаров</td><td>Заказы</td><td>% от мин</td>
    </tr>
    </thead>
    <tbody id="purchases">
    <?php $this->renderPartial('_listtable', array('purchases' => $purchases)) ?>
    </tbody>
</table>

<script type="text/javascript">
var statesList = [<?php echo implode(',', $statesJs) ?>];
function changeState(el) {
    olcm.show(el, statesList, function(el, item) {
        var id = parseInt($(el).attr('data-id'));
        ajax.post('/purchase'+ id +'/updateState', {state: item}, function(r) {
            if (r.msg) msi.show(r.msg);
        });
    });
}
</script>