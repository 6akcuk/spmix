<?php
/** @var Purchase $purchase */

Yii::app()->getClientScript()->registerCssFile('/css/purchases.css');
Yii::app()->getClientScript()->registerScriptFile('/js/purchase.js');

Yii::app()->getClientScript()->registerCssFile('/css/pagination.css');
Yii::app()->getClientScript()->registerScriptFile('/js/pagination.js');

Yii::app()->getClientScript()->registerScriptFile('/js/uploader.js');

$this->pageTitle = Yii::app()->name .' - Добавление нескольких товаров';
$delta = Yii::app()->getModule('purchases')->goodsPerPage;
?>
<div class="breadcrumbs">
  <?php echo ActiveHtml::link($purchase->name, '/purchase'. $purchase->purchase_id) ?> &raquo;
  Добавить несколько товаров
</div>

<div class="row">
  <embed id="uploader" width="740" height="63" type="application/x-shockwave-flash" swliveconnect="true" allowscriptaccess="always" wmode="transparent" src="/assets/WideUploader.swf?<?php echo rand(10000, 99999) ?>" />
</div>
<div id="upload-preview"></div>
<script>
  Uploader.setup({
    queue: '#upload-preview',
    urlVariables: 'ext=goodmany',
    url: A.uploadMap.action
  });
</script>