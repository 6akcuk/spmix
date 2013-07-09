<?php
/** @var Purchase $purchase */
?>
<a href="/purchase<?php echo $purchase->purchase_id ?>" onclick="return nav.go(this, event)" class="ads_ad_box">
  <div class="ads_ad_title_box"><?php echo $purchase->name ?></div>
  <div class="ads_ad_photo_box">
    <img src="<?php echo ActiveHtml::getImageUrl($purchase->image, 'e') ?>" class="ads_ad_photo" />
  </div>
  <div class="ads_ad_description">
    <?php echo nl2br($purchase->shortstory) ?>
  </div>
</a>