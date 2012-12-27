<div class="my">
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
    <div class="left sortlimit">
        Выводить по:
        <?php echo ActiveHtml::link('10', '/purchases/my?c[limit]=10', ($c['limit'] == 10) ? array('class' => 'selected') : array()) ?>
        <?php echo ActiveHtml::link('30', '/purchases/my?c[limit]=30', ($c['limit'] == 30) ? array('class' => 'selected') : array()) ?>
        <?php echo ActiveHtml::link('50', '/purchases/my?c[limit]=50', ($c['limit'] == 50) ? array('class' => 'selected') : array()) ?>
        <?php echo ActiveHtml::link('100', '/purchases/my?c[limit]=100', ($c['limit'] == 100) ? array('class' => 'selected') : array()) ?>
    </div>
    <div class="left">
        <input<?php if(isset($c['completed'])) echo ' checked="true"' ?> id="dont_show" type="checkbox" onchange="return nav.go('/purchases/my?c[completed]=0', event, {revoke: !this.checked})" />
        <label for="dont_show">не отображать завершенные закупки</label>
    </div>
    <div class="right">
    <? /*$this->widget('Paginator', array(
        'offsets' => $model->imagesCountRN,
        'offset' => $offset,
        'delta' => 18,
        'url' => array(
            '/photosessions/associate',
            'id' => $model->id,
            'offset' => $offset,
        )
    ));*/ ?>
    </div>
</div>

<div class="clearfix">
    <table class="bezborder" style="margin-top: 4px">
        <thead>
        <tr>
            <td>Категория:</td>
            <td>   <div rel="filters" class="filters_my_category">
                <?php echo ActiveHtml::dropdown(
                'c[category_id]',
                'Категория',
                (isset($c['category_id'])) ? $c['category_id'] : '',
                PurchaseCategory::getDataArray()
            ); ?>
            </div></td>
        </tr>
        <tr>
            <td>Статус:</td>
            <td>
                <div rel="filters" class="filters_my_status">
                <?php echo ActiveHtml::dropdown(
                'c[state]',
                'Статус',
                (isset($c['state'])) ? $c['state'] : '',
                Purchase::getStateDataArray()
            ); ?>
            </div></td>
        </tr>
        </thead>

    <table class="mytable">
        <thead>
        <tr>
        <td>
    <div rel="filters" class="filters_my_id">
    <?php echo ActiveHtml::inputPlaceholder(
        'c[id]',
        (isset($c['id'])) ? $c['id'] : '',
        array('placeholder' => 'ID')
    ); ?>
    </div>
            <td>
    <div rel="filters" class="filters_my_date">
    <?php echo ActiveHtml::inputCalendar(
        'c[create_date]',
        (isset($c['create_date'])) ? $c['create_date'] : '',
        'Создана'
    ); ?>
    </div>
            </td>
        <td>
            <div rel="filters" class="filters_my_name">
                <?php echo ActiveHtml::inputPlaceholder(
                'c[name]',
                (isset($c['name'])) ? $c['name'] : '',
                array('placeholder' => 'Название')
            ); ?>
            </div>
            </td>
        <td>

            </td>
        <td>
       </td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
</tr>
</div>
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
    </div>