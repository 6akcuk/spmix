<?php
/** @var $purchase Purchase */

Yii::app()->getClientScript()->registerCssFile('/css/purchases.css');
Yii::app()->getClientScript()->registerScriptFile('/js/purchase.js');

$this->pageTitle = Yii::app()->name .' - Быстрый заказ';

$dd_oic = array();
if (is_array($purchase->oic)) {
  foreach ($purchase->oic as $purchase_oic) {
    $dd_oic[$purchase_oic->description .' '. ActiveHtml::price($purchase_oic->price)] = $purchase_oic->pk;
  }
}

?>
<div class="breadcrumbs">
    <?php echo ActiveHtml::link($purchase->name, '/purchase'. $purchase->purchase_id) ?> &raquo;
    Быстрый заказ
</div>
<div class="create">
<h1>Быстрый заказ</h1>

<?php
/** @var $form ActiveForm */

$form = $this->beginWidget('ext.ActiveHtml.ActiveForm', array(
    'id' => 'quickform',
    'action' => $this->createUrl('/purchase'. $purchase->purchase_id .'/quick'),
)); ?>
<div class="row">
    <?php echo $form->inputPlaceholder($good, 'name') ?>
</div>
<div class="row">
    <?php echo $form->inputPlaceholder($good, 'artikul') ?>
</div>
<div class="row">
    <?php echo $form->inputPlaceholder($good, 'price') ?>
</div>
<div class="row">
    <?php echo ActiveHtml::inputPlaceholder('size', '', array('placeholder' => 'Размер')) ?>
</div>
<div class="row">
    <?php echo ActiveHtml::inputPlaceholder('color', '', array('placeholder' => 'Цвет')) ?>
</div>
<div class="row">
    <?php echo $form->inputPlaceholder($order, 'amount') ?>
</div>
<div class="row">
    <?php echo $form->smartTextarea($order, 'client_comment') ?>
</div>
<?php if ($purchase->oic): ?>
<div class="clearfix row">
  <?php if (!$oic): ?>
  <?php echo $form->dropdown($orderc, 'oic', $dd_oic) ?>
  <?php else: ?>
  <div id="oic_text">
    Место выдачи: <?php echo $oic->oic_name ?>
    <!--<span class="icon-remove" rel="tooltip" title="Удалить место" onclick="removeSavedOic()"></span> -->
  </div>
  <div id="oic" class="clearfix" style="display:none">
    <?php echo $form->dropdown($order, 'oic', $dd_oic) ?>
  </div>
  <script>
    function removeSavedOic() {
      $('#oic_text').remove();
      $('#oic').show();
    }
  </script>
  <?php endif; ?>
</div>
<?php endif; ?>
<div class="row">
    <?php echo ActiveHtml::submitButton('Заказать товар', array('class' => 'btn light_blue', 'onclick' => 'return FormMgr.submit(\'#quickform\')')); ?>
</div>
<?php $this->endWidget(); ?>
    </div>