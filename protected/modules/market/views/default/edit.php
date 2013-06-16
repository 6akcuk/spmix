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
  <?php echo ActiveHtml::link('Редактирование товара', '/market?act=edit&id='. $good->good_id, array('class' => 'selected')) ?>
</div>
<div class="market_add_wrap clearfix">
  <?php
  /** @var $form ActiveForm */
  $form = $this->beginWidget('ext.ActiveHtml.ActiveForm', array(
    'id' => 'market_add_form',
    'action' => $this->createUrl('/market?act=edit&id='. $good->good_id),
    'htmlOptions' => array(
      'onsubmit' => 'return Market.edit()'
    )
  )); ?>
  <div class="fl_l market_add_image_col">
    <?php echo $form->upload($good, 'image', 'Добавить фотографию', array('data-image' => 'e')) ?>
  </div>
  <div class="fl_l market_add_wide_col">
    <div class="market_add_header">Название:</div>
    <?php echo $form->textField($good, 'name', array('class' => 'market_add_wide_input')) ?>
    <div class="market_add_header">Категории:</div>
    <div id="market_wdd" class="wdd clearfix" style="width: 562px" onclick="WideDropdown.show('market_wdd', event)">
      <div class="wdd_lwrap" style="width: 400px">
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
      <?php foreach($goodCategories as $cat): ?>
        WideDropdown.select('market_wdd', null, '<?php echo $cat->category_id ?>');
      <?php endforeach; ?>
      </script>
    </div>
  </div>
  <div class="clear"></div>
  <div class="fl_l market_add_left_col">
    <div class="market_add_header">Размер:</div>
    <?php echo $form->textField($good, 'size', array('class' => 'market_add_input')) ?>
    <div class="market_add_header">Цвет:</div>
    <?php echo $form->textField($good, 'color', array('class' => 'market_add_input')) ?>
    <div class="market_add_header">Барахолка:</div>
    <div class="market_add_check">
      <?php echo $form->checkBox($good, 'is_used') ?>
      <?php echo $form->label($good, 'is_used') ?>
    </div>
  </div>
  <div class="fl_l market_add_right_col">
    <div class="market_add_header">Цена:</div>
    <?php echo $form->textField($good, 'price', array('class' => 'market_add_input')) ?>
    <div class="market_add_header">Доставка:</div>
    <?php echo $form->textField($good, 'delivery', array('class' => 'market_add_input')) ?>
    <div class="market_add_header">Контактный телефон:</div>
    <?php echo $form->textField($good, 'phone', array('class' => 'market_add_input')) ?>
  </div>
  <div class="clear"></div>
  <div class="market_add_header">Описание:</div>
  <?php echo $form->smartTextarea($good, 'description', array('class' => 'market_add_descr', 'rm_placeholder' => true)) ?>

  <div class="clearfix market_add_buttons">
    <div class="fl_l button_submit">
      <button id="market_add" onclick="return Market.edit()">Сохранить</button>
    </div>
    <div id="market_add_progress" class="fl_l progress post_progress"></div>
  </div>
  <?php $this->endWidget(); ?>
</div>
<script type="text/javascript">
  if (!A.wddOnSelect) A.wddOnSelect = {};
</script>