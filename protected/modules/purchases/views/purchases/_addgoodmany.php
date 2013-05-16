<?php /** @var PurchaseUploadMany $photo */ ?>
<?php foreach ($photos as $photo): ?>
  <div id="photo<?php echo $photo->pk ?>" class="good_many clearfix">
    <div class="photo">
      <?php echo ActiveHtml::showUploadImage($photo->photo) ?>
    </div>
    <div class="info">
      <form id="photo<?php echo $photo->pk ?>_form" method="post">
        <div class="good_many_row clearfix">
          <div class="left good_many_label">Название:</div>
          <div class="left"><?php echo ActiveHtml::textField('Good[name]', '') ?></div>
        </div>
        <div class="good_many_row clearfix">
          <div class="left good_many_label">Артикул:</div>
          <div class="left"><?php echo ActiveHtml::textField('Good[artikul]', '') ?></div>
        </div>
        <div class="good_many_row clearfix">
          <div class="left good_many_label">Цена:</div>
          <div class="left"><?php echo ActiveHtml::textField('Good[price]', '') ?></div>
        </div>
        <div class="good_many_row clearfix">
          <div class="left good_many_label">Доставка:</div>
          <div class="left"><?php echo ActiveHtml::textField('Good[delivery]', '') ?></div>
        </div>
        <div class="good_many_buttons clearfix">
          <div class="button_submit left">
            <button onclick="return PurchasePhotos.addGood(<?php echo $photo->purchase_id ?>, <?php echo $photo->pk ?>)">Добавить</button>
          </div>
          <a class="button_cancel left" onclick="PurchasePhotos.deleteGood(<?php echo $photo->purchase_id ?>, <?php echo $photo->pk ?>)">Удалить</a>
          <div id="photo<?php echo $photo->pk ?>_progress" class="left progress"></div>
        </div>
      </form>
    </div>
  </div>
<?php endforeach; ?>
