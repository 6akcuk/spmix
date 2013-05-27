<?php
/* @var $this DefaultController
 * @var $category PurchaseCategory
 */

Yii::app()->getClientScript()->registerCssFile('/css/market.css');
Yii::app()->getClientScript()->registerScriptFile('/js/market.js');

$this->pageTitle = Yii::app()->name .' - Пристрой';

$categoryJs = array();
foreach ($categories as $category) {
  $categoryJs[] = $category->category_id .": {text: '". $category->name ."'}";
}
?>
<div class="tabs">
  <?php echo ActiveHtml::link('Пристрой организаторов', '/market?org=1') ?>
  <?php echo ActiveHtml::link('Пристрой участников', '/market?org=0') ?>
  <?php echo ActiveHtml::link('Барахолка', '/market?used=1') ?>
  <?php echo ActiveHtml::link('Новый товар', '/market?act=add', array('class' => 'selected')) ?>
</div>
<div class="market_add_wrap clearfix">
  <?php
  /** @var $form ActiveForm */
  $form = $this->beginWidget('ext.ActiveHtml.ActiveForm', array(
  'id' => 'market_add_form',
  'action' => $this->createUrl('/market?act=add'),
  )); ?>
  <div class="fl_l market_add_image_col">
    <?php echo $form->upload($good, 'image', 'Добавить фотографию') ?>
  </div>
  <div class="fl_l market_add_wide_col">
    <div class="market_add_header">Название:</div>
    <?php echo $form->textField($good, 'name', array('class' => 'market_add_wide_input')) ?>
    <div class="market_add_header">Категории:</div>
    <div id="market_wdd" class="wdd clearfix" onclick="WideDropdown.show('market_wdd', event)">
      <div class="wdd_lwrap" style="width: 420px">
        <div class="wdd_list"></div>
      </div>
      <div class="right wdd_arrow"></div>
      <div class="wdd_bubbles"></div>
      <div class="wdd_add left" style="display:none">
        <div class="wdd_add2">
          <table>
            <tr>
              <td>
                <div class="wdd_add3">
                  <nobr>Добавить</nobr>
                </div>
              </td>
              <td>
                <div class="wdd_add_plus"></div>
              </td>
            </tr>
          </table>
        </div>
      </div>
      <?php echo ActiveHtml::inputPlaceholder(
        '',
        '',
        array(
          'class' => 'left wdd_text',
          'placeholder' => 'Выберите категорию',
          'onfocus' => "WideDropdown.setFocused('market_wdd', event)",
          'onblur' => "WideDropdown.setUnfocused('market_wdd', event)",
        )
      ) ?>
      <script type="text/javascript">
        WideDropdown.addList('market_wdd', {<?php echo implode(', ', $categoryJs) ?>});
      </script>
    </div>
  </div>
  <div class="clear"></div>
  <div class="fl_l market_add_left_col">
    <div class="market_add_header">Размер:</div>
    <?php echo $form->textField($good, 'size', array('class' => 'market_add_input')) ?>
  </div>
  <div class="fl_l market_add_right_col">

  </div>
  <?php $this->endWidget(); ?>
</div>
<script type="text/javascript">
if (!A.wddOnSelect) A.wddOnSelect = {};
</script>