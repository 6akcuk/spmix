<?php
/**
 * @var $purchase Purchase
 * @var $good Good
 */
Yii::app()->getClientScript()->registerCssFile('/css/purchases.css');
Yii::app()->getClientScript()->registerScriptFile('/js/purchase.js');

Yii::app()->getClientScript()->registerCssFile('/css/pagination.css');
Yii::app()->getClientScript()->registerScriptFile('/js/pagination.js');

$this->pageTitle = Yii::app()->name .' - '. $purchase->name;
$delta = Yii::app()->controller->module->goodsPerPage;
?>

<h1><?php echo $purchase->name ?></h1>
<div class="purchase_table clearfix">
    <div class="clearfix">
        <div class="left photo">
            <?php echo ActiveHtml::showUploadImage($purchase->image, 'a') ?>
        </div>
        <div class="left td">
            <div class="clearfix">
                <div class="left label">Город:</div>
                <div class="left labeled"><?php echo $purchase->city->name ?></div>
            </div>
            <div class="clearfix">
                <div class="left label">Категория:</div>
                <div class="left labeled"><?php echo $purchase->category->name ?></div>
            </div>
            <div class="clearfix">
                <div class="left label">Создана:</div>
                <div class="left labeled"><?php echo ActiveHtml::date($purchase->create_date, true, true) ?></div>
            </div>
            <div class="clearfix">
                <div class="left label">Тип:</div>
                <div class="left labeled"><?php echo Yii::t('purchase', $purchase->status) ?></div>
            </div>
            <div class="clearfix">
                <div class="left label">Поставщик:</div>
                <div class="left labeled"><?php echo (!Yii::app()->user->checkAccess('global.supplierView') && $purchase->hide_supplier) ? 'скрыт' : $purchase->supplier_url ?></div>
            </div>
            <div class="clearfix">
                <div class="left label">Организатор:</div>
                <div class="left labeled"><?php echo ActiveHtml::link((Yii::app()->user->checkAccess('global.fullnameView')) ? $purchase->author->profile->firstname .' '. $purchase->author->profile->lastname : $purchase->author->login, '/id'. $purchase->author_id) ?></div>
            </div>
        </div>
        <div class="left td">
            <div class="clearfix">
                <div class="left label">Статус:</div>
                <div class="left labeled"><?php echo Yii::t('purchase', $purchase->state) ?></div>
            </div>
            <div class="clearfix">
                <div class="left label">Орг. сбор:</div>
                <div class="left labeled"><?php echo $purchase->org_tax ?>%</div>
            </div>
            <div class="clearfix">
                <div class="left label">Дата стопа:</div>
                <div class="left labeled"><?php echo ActiveHtml::date($purchase->stop_date, false) ?></div>
            </div>
            <div class="clearfix">
                <div class="left label">Минималка:</div>
                <div class="left labeled"><?php echo ActiveHtml::price($purchase->min_sum) ?> (<?php echo $purchase->min_num ?> шт.)</div>
            </div>
            <div class="clearfix">
                <div class="left label">Прайс:</div>
                <div class="left labeled"><?php echo $purchase->price_url ?></div>
            </div>
            <div class="clearfix">
                <div class="left label">Репутация:</div>
                <div class="left labeled"><?php echo $purchase->author->profile->positive_rep ?> | <?php echo $purchase->author->profile->negative_rep ?></div>
            </div>
        </div>
    </div>
    <div class="left">
        <?php if ($purchase->getMinimalPercentage() > 0): ?>
        <div class="bar">
            <div class="barline" style="width: <?php echo $purchase->getMinimalPercentage() ?>%"></div>
            <span><?php echo $purchase->getMinimalPercentage() ?>%</span>
        </div>
        <?php endif; ?>
    </div>
    <div class="right">
        <?php echo ActiveHtml::link('Быстрый заказ', '/purchase'. $purchase->purchase_id .'/quick', array('class' => 'button')) ?>
    <?php if (Yii::app()->user->checkAccess('purchases.purchases.edit') &&
              (Yii::app()->user->checkAccess('purchases.purchases.editSuper') ||
               Yii::app()->user->checkAccess('purchases.purchases.editOwn', array('purchase' => $purchase)))): ?>
        <?php echo ActiveHtml::link('Список заказов', '/orders'. $purchase->purchase_id, array('class' => 'button')) ?>
        <?php echo ActiveHtml::link('ЦВЗ', '/purchase'. $purchase->purchase_id .'/oic', array('class' => 'button')) ?>
        <?php echo ActiveHtml::link('Редактировать', '/purchase'. $purchase->purchase_id .'/edit', array('class' => 'button')) ?>
        <?php //echo ActiveHtml::link('Удалить', '/purchase'. $purchase->purchase_id .'/delete', array('class' => 'button')) ?>
    <?php endif; ?>
    </div>
</div>
<div data-link="#tabs_content" class="tabs">
    <a target="div.purchase_fullstory">Условие</a>
    <a target="div.purchase_customers">Статистика</a>
    <a target="div.purchase_history">История действий</a>
    <a target="div.purchase_goods" class="selected">Альбом</a>
</div>
<div id="tabs_content">
    <div class="purchase_fullstory" style="display: none">
        <?php if (Yii::app()->user->checkAccess('purchases.purchases.editSuper') ||
                  Yii::app()->user->checkAccess('purchases.purchases.editOwn', array('purchase' => $purchase))): ?>
        <a class="purchase_edit_story tt" onclick="$(this).editor('simple', '/purchases/updateFullstory', {id: <?php echo $purchase->purchase_id ?>}); return false" title="<?php echo ($purchase->external && $purchase->external->fullstory) ? "Редактировать описание" : "Добавить новое описание" ?>">
        <?php endif; ?>
            <?php echo ($purchase->external && $purchase->external->fullstory) ? nl2br($purchase->external->fullstory) : "Добавить описание" ?>
        <?php if (Yii::app()->user->checkAccess('purchases.purchases.editSuper') ||
        Yii::app()->user->checkAccess('purchases.purchases.editOwn', array('purchase' => $purchase))): ?>
        </a>
        <?php endif; ?>
    </div>
    <div class="purchase_customers" style="display: none">
        <div>Сделано всего заказов: <b><?php echo $purchase->ordersNum ?></b></div>
        <div>Сделано заказов на общую сумму: <b><?php echo ActiveHtml::price($purchase->ordersSum) ?></b></div>
    </div>
    <div class="purchase_history" style="display: none">
        <?php if ($purchase->history): ?>
        <?php foreach ($purchase->history as $history): ?>
        <div><?php echo ActiveHtml::date($history->datetime, true, true) .' '. Yii::t('purchase', $history->msg, json_decode($history->params, true)) ?></div>
        <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <div class="purchase_goods">
        <div class="clearfix">
            <h2 class="left"><?php echo Yii::t('purchase', '{n} товар|{n} товара|{n} товаров', $offsets) ?></h2>
            <a href="/purchase<?php echo $purchase->purchase_id ?>/addgood" class="left button add_good" rel="tooltip" title="Добавить товар"><span class="icon-plus icon-white"></span></a>
            <div class="right">
                <?php $this->widget('Paginator', array(
                'url' => '/purchase'. $purchase->purchase_id,
                'offset' => $offset,
                'offsets' => $offsets,
                'delta' => $delta,
            )); ?>
            </div>
        </div>
        <div class="list clearfix" rel="pagination">
            <?php $this->renderPartial('_goodlist', array('goods' => $goods, 'offset' => $offset)) ?>
        </div>
        <? if ($offset + $delta < $offsets && $offsets > $delta): ?><a id="pg_more" class="pg_more" onclick="Paginator.showMore()">Еще товары</a><? endif; ?>
    </div>
</div>