<?php
/** @var $purchase Purchase */
?>
<div class="main">
    <div class="wrapper">
        <h1>Новые закупки</h1>
    </div>
</div>
<div class="wrapper mainorders clearfix">
<?php if ($purchases): ?>
  <?php foreach ($purchases as $purchase): ?>
  <div class="left mainblock">
    <div class="mainsmall">
      <?php echo $purchase->city->name ?>,
      <?php echo $purchase->author->login ?>,
      <?php echo ActiveHtml::date($purchase->create_date, false, true) ?>
    </div>
    <div><a href="/purchase<?php echo $purchase->purchase_id ?>"><?php echo $purchase->name ?></a></div>
    <div>
      <table>
        <thead>
          <tr>
            <td>
              <?php echo ($purchase->image) ? ActiveHtml::showUploadImage($purchase->image) : 'Аватар отсутствует' ?>
            </td>
            <td>
              <?php echo ($purchase->external) ? nl2br(mb_substr($purchase->external->fullstory, 0, 50, 'utf-8')) : '' ?>
            </td>
          </tr>
        </thead>
      </table>
    </div>
  </div>
  <?php endforeach; ?>
<?php else: ?>
  <h2 class="empty">Закупки не найдены</h2>
<?php endif; ?>
</div>