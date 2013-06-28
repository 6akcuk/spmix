<?php
/**
 * @var $good MarketGood
 * @var $form ActiveForm
 */

Yii::app()->getClientScript()->registerCssFile('/css/market.css');
Yii::app()->getClientScript()->registerScriptFile('/js/market.js');

Yii::app()->getClientScript()->registerCssFile('/css/photoview.css');
Yii::app()->getClientScript()->registerScriptFile('/js/photoview.js');

Yii::app()->getClientScript()->registerCssFile('/css/profile.css');
Yii::app()->getClientScript()->registerCssFile('/css/im.css');

Yii::app()->getClientScript()->registerScriptFile('/js/im.js');

$this->pageTitle = Yii::app()->name .' - '. $good->name;
?>
<div class="market_good_header_wrap clearfix">
  <div class="fl_l market_good_header"><?php echo $good->name ?></div>
  <div class="right">
  <?php echo ActiveHtml::qVKMenu('Редактировать', array(
    ActiveHtml::link('Редактировать', '/market?act=edit&id='. $good->good_id),
    '<a onclick="Market.deleteGood('. $good->good_id .')">Удалить</a>',
  ), array('class' => 'right market_act_menu')) ?>
  </div>
</div>
<div class="market_good_table clearfix">
  <div class="left mgs_photo_wrap">
    <?php if($good->image): ?>
      <a>
        <?php echo ActiveHtml::showUploadImage($good->image, 'a'); ?>
      </a>
    <?php else: ?>
      <span>Фотография отсутствует</span>
    <?php endif; ?>
  </div>
  <div class="left mgs_cont">
    <div class="price">
      <?php echo ActiveHtml::price($good->showPrice(), $good->currency) ?>
    </div>

    <div class="fl_l market_good_label">Дата:</div>
    <div class="fl_l market_good_labeled"><?php echo ActiveHtml::date($good->add_date, false) ?></div>
    <div class="clear"></div>

    <div class="fl_l market_good_label">Размер:</div>
    <div class="fl_l market_good_labeled"><?php echo $good->size ?></div>
    <div class="clear"></div>

    <div class="fl_l market_good_label">Цвет:</div>
    <div class="fl_l market_good_labeled"><?php echo $good->color ?></div>
    <div class="clear"></div>

    <div class="fl_l market_good_label">Описание:</div>
    <div class="fl_l market_good_labeled"><?php echo $good->description ?></div>
    <div class="clear"></div>

    <div class="fl_l market_good_label">Продает:</div>
    <div class="fl_l market_good_labeled"><?php echo ActiveHtml::link($good->author->getDisplayName(), '/id'. $good->author_id) ?></div>
    <div class="clear"></div>

    <div class="fl_l market_good_label">Телефон:</div>
    <div class="fl_l market_good_labeled">
      <span id="mgc_phone">+7 (XXX) XXX-XX-XX</span>
      <a onclick="$('#mgc_phone').html('<?php echo ActiveHtml::phone($good->phone) ?>'); $(this).hide()">Показать номер</a>
    </div>
    <div class="clear"></div>

    <div class="clearfix mgs_buttons">
      <div class="button_submit button_icon">
        <button onclick="Market.buy(<?php echo $good->author_id ?>, '<?php echo urlencode("Здравствуйте. Я бы ". (Yii::t('app', '0#хотел|1#хотела', Yii::app()->user->model->profile->genderToInt())) ." купить товар ". $good->name ." в Вашем пристрое") ?>')">
          <span class="icon-white icon-shopping-cart"></span>
          Купить
        </button>
      </div>
      <div class="button_submit button_icon">
        <button id="subscribe<?php echo $good->good_id ?>" onclick="Market.subscribe(<?php echo $good->good_id ?>)">
          <span class="icon-white icon-check"></span> <?php echo ($subscription) ? "Отписаться от новостей" : "Подписаться на новости" ?>
        </button>
      </div>
      <div class="button_submit button_icon">
        <button onclick="Market.shareToFriends(<?php echo $good->good_id ?>)">
          <span class="icon-white icon-comment"></span> Рассказать друзьям
        </button>
      </div>
    </div>
  </div>
</div>
<div style="margin-top: 10px">
  <?php $this->widget('Comments', array('hoop' => $good, 'hoop_id' => $good->good_id, 'hoop_type' => 'marketgood', 'reply' => $reply)) ?>
</div>