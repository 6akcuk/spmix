<?php
/**
 * @var $purchase Purchase
 * @var $good Good
 */

Yii::app()->getClientScript()->registerCssFile('/css/purchases.css');
Yii::app()->getClientScript()->registerScriptFile('/js/purchase.js');

$this->pageTitle = Yii::app()->name .' - '. $purchase->name;
?>

<h1><?php echo $purchase->name ?></h1>
<div class="purchase_table clearfix">
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
            <div class="left labeled"><?php echo $purchase->supplier_url ?></div>
        </div>
        <div class="clearfix">
            <div class="left label">Организатор:</div>
            <div class="left labeled"><?php echo ActiveHtml::link($purchase->author->lastname .' '. $purchase->author->firstname, '/id'. $purchase->author->user_id) ?></div>
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
            <div class="left labeled"><?php echo $purchase->author->positive_rep ?> | <?php echo $purchase->author->negative_rep ?></div>
        </div>
    </div>
</div>
<div class="purchase_fullstory">
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
<div class="purchase_goods">
    <div class="clearfix">
        <h4 class="left">Товары в данной закупке</h4>
        <?php echo ActiveHtml::link('Добавить товар', '/purchase'. $purchase->purchase_id .'/addgood', array('class' => 'right button')) ?>
    </div>
    <div class="list">
    <?php if (sizeof($goods) > 0): ?>
    <?php foreach ($goods as $good): ?>
        <div class="left good">
            <h4>
                <?php echo ActiveHtml::link($good->name, '/good'. $good->purchase_id .'_'. $good->good_id) ?>
                <?php echo ActiveHtml::link('', '/good'. $purchase->purchase_id .'_'. $good->good_id.'/edit', array('class' => 'right iconify_gear_a tt', 'title' => 'Редактировать товар')) ?>
            </h4>
            <?php if ($good->image): ?><div><?php echo ActiveHtml::showUploadImage($good->image->image) ?></div><?php endif; ?>
            <div>Стоимость <?php echo ActiveHtml::price($good->price, $good->currency) ?></div>
            <div><a class="button">Заказать</a></div>
        </div>
    <?php endforeach; ?>
    <?php endif; ?>
    </div>
</div>