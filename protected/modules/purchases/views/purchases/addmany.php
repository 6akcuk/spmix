<?php
/** @var Purchase $purchase */

Yii::app()->getClientScript()->registerCssFile('/css/purchases.css');
Yii::app()->getClientScript()->registerScriptFile('/js/purchase.js');

Yii::app()->getClientScript()->registerCssFile('/css/pagination.css');
Yii::app()->getClientScript()->registerScriptFile('/js/pagination.js');

Yii::app()->getClientScript()->registerScriptFile('/js/swfupload.js');

$this->pageTitle = Yii::app()->name .' - Добавление нескольких товаров';
$delta = Yii::app()->getModule('purchases')->goodsPerPage;
?>
<div class="breadcrumbs">
  <?php echo ActiveHtml::link($purchase->name, '/purchase'. $purchase->purchase_id) ?> &raquo;
  Добавить несколько товаров
</div>
<div class="photos_upload_area_wrap">
  <div id="photos_add_bar" rel="scrollfix" data-scroll="top">
    <div class="photos_add_bar_shadow"></div>
    <div class="swfupload_wrap">
      <div id="swfupload_button"></div>
      <div class="upload_field">
        Добавить фотографии
      </div>
    </div>
    <div id="photos_add_bar_progress">
      <div id="photos_add_p_line">
        <div id="photos_add_p_inner"></div>
      </div>
      <div id="photos_add_p_text"></div>
    </div>
  </div>
</div>
<script type="text/javascript">
  var swfu, settings = {
    flash_url : "http://<?php echo Yii::app()->params['domain'] ?>/assets/swfupload.swf",
    upload_url: A.uploadMap.action,
    post_params: {"ext": "purchase", "purchase_id": <?php echo $id ?>},
    file_size_limit : "20 MB",
    file_types : "*.jpg;*.gif",
    file_types_description : "Images",
    file_upload_limit : 20,
    file_queue_limit : 0,

    // Button settings
    button_placeholder_id: "swfupload_button",
    button_width: "750",
    button_height: "57",
    button_window_mode: SWFUpload.WINDOW_MODE.TRANSPARENT,
    button_cursor: SWFUpload.CURSOR.HAND
  };

  swfu = new SWFUpload(settings);
</script>