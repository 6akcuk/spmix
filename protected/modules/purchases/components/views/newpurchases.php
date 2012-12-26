<?php
/** @var $purchase Purchase */
?>
<div class="clearfix">
    <h1>Новые закупки</h1>
    <div class="clearfix">
    <?php foreach ($purchases as $purchase): ?>
    <div class="left">
        <div>
            <?php echo $purchase->city->name ?>,
            <?php echo $purchase->author->login ?>,
            <?php echo ActiveHtml::date($purchase->create_date, false, true) ?>
        </div>
        <div><a href="/purchase<?php echo $purchase->purchase_id ?>"><?php echo $purchase->name ?></a></div>
        <div>
            <?php echo ActiveHtml::showUploadImage($purchase->image) ?>
            <?php echo ($purchase->external) ? nl2br(mb_substr($purchase->external->fullstory, 0, 50, 'utf-8')) : '' ?>
        </div>
    </div>
    <?php endforeach; ?>
    </div>
</div>