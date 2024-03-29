<?php
/**
 * @var $good Good
 * @var $purchase Purchase
 */

Yii::app()->getClientScript()->registerCssFile('/css/purchases.css');
Yii::app()->getClientScript()->registerScriptFile('/js/purchase.js');

Yii::app()->getClientScript()->registerCssFile('/css/pagination.css');
Yii::app()->getClientScript()->registerScriptFile('/js/pagination.js');

$this->pageTitle = Yii::app()->name .' - Товары к закупке #'. $purchase->purchase_id;
?>
<div class="breadcrumbs">
  <?php echo ActiveHtml::link('Моя страница', '/id'. Yii::app()->user->getId()) ?> &raquo;
  <?php echo ActiveHtml::link('Мои закупки', '/purchases/my') ?> &raquo;
  Товары к закупке #<?php echo $purchase->purchase_id ?>
</div>

<h1>
  Товары к закупке #<?php echo $purchase->purchase_id ?> "<?php echo $purchase->name ?>"
  <div class="right">
    <div class="button_submit button_menu">
      <button rel="menu">Добавить товар</button>
      <div class="dd_menu dd_menu_act">
        <div class="dd_menu_body">
          <table>
            <tr>
              <td class="dd_menu_shad_l">
                <div></div>
              </td>
              <td>
                <div class="dd_menu_shad_t2"></div>
                <div class="dd_menu_shad_t"></div>
                <div class="dd_menu_rows">
                  <div class="dd_menu_rows2">
                    <?php echo ActiveHtml::link('Добавить один товар', '/purchase'. $purchase->purchase_id .'/addgood') ?>
                    <?php echo ActiveHtml::link('Добавить несколько товаров', '/purchase'. $purchase->purchase_id .'/addmany') ?>
                    <?php echo ActiveHtml::link('Добавить из другой закупки', '/purchase'. $purchase->purchase_id .'/addfromanother') ?>
                  </div>
                </div>
                <div class="dd_menu_shad_b"></div>
                <div class="dd_menu_shad_b2"></div>
              </td>
              <td class="dd_menu_shad_r">
                <div></div>
              </td>
            </tr>
          </table>
        </div>
      </div>
    </div>
  </div>
</h1>

<div class="goods_purchases">
<div class="clearfix">
  <div class="left sortlimit">
    Выводить по:
    <?php echo ActiveHtml::link('10', '/goods'. $purchase->purchase_id .'?c[limit]=10', ($c['limit'] == 10) ? array('class' => 'selected') : array()) ?>
    <?php echo ActiveHtml::link('30', '/goods'. $purchase->purchase_id .'?c[limit]=30', ($c['limit'] == 30) ? array('class' => 'selected') : array()) ?>
    <?php echo ActiveHtml::link('50', '/goods'. $purchase->purchase_id .'?c[limit]=50', ($c['limit'] == 50) ? array('class' => 'selected') : array()) ?>
    <?php echo ActiveHtml::link('100', '/goods'. $purchase->purchase_id .'?c[limit]=100', ($c['limit'] == 100) ? array('class' => 'selected') : array()) ?>
  </div>
  <div class="right">
    <?php $this->widget('Paginator', array(
      'url' => '/goods'. $purchase->purchase_id,
      'offset' => $offset,
      'offsets' => $offsets,
      'delta' => $c['limit'],
    )); ?>
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
    <tbody id="goods" rel="pagination">
    <?php $this->renderPartial('_goods', array('goods' => $goods, 'offset' => $offset, 'c' => $c)) ?>
    </tbody>
</table>
<? if ($offset + $c['limit'] < $offsets && $offsets > $c['limit']): ?><a id="pg_more" class="pg_more" onclick="Paginator.showMore()">Еще товары</a><? endif; ?>
    </div>