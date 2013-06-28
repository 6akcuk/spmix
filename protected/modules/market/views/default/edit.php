<?php
/* @var $this DefaultController
 * @var $category PurchaseCategory
 */

Yii::app()->getClientScript()->registerCssFile('/css/market.css');
Yii::app()->getClientScript()->registerScriptFile('/js/market.js');

$this->pageTitle = Yii::app()->name .' - Пристрой';

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
    <div class="clearfix" style="width: 562px">
      <?php foreach ($categories as $category): ?>
        <?php
        $found = 0;
        foreach ($goodCategories as $cat) {
          if ($cat->category_id == $category->category_id) {
            $found = 1;
            break;
          }
        }
        ?>
        <div class="fl_l" style="padding: 5px 10px 5px 0px">
          <?php echo ActiveHtml::checkBox('category_id['. $category->category_id .']', $found) ?>
          <?php echo ActiveHtml::label($category->name, 'category_id_'. $category->category_id) ?>
        </div>
      <?php endforeach; ?>
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