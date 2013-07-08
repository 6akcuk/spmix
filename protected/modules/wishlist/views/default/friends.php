<?php

Yii::app()->getClientScript()->registerCssFile('/css/pagination.css');

Yii::app()->getClientScript()->registerCssFile('/css/wishlist.css');
Yii::app()->getClientScript()->registerScriptFile('/js/wishlist.js');

$this->pageTitle = Yii::app()->name .' - Хотелки';

?>
<div class="wishlist_intro clearfix">
  <div class="left wishlist_images">
    <img src="/images/wishes/wish_<?php echo rand(2, 5) ?>.jpg" width="320" />
  </div>
  <div class="left wishlist_text">
    В этом разделе Вы можете поделиться своими желаниями и возможностями.<br/>
    Напишите тут то, что Вы хотите, возможно, кто-то сможет помочь Вам осуществить ваши желания. Или Вы можете
    порадовать кого-то, исполнив его желание.<br/>
    Правила:<br/>
    1. Ваши желания и возможности должны быть потенциально выполнимыми другими пользователями.<br/>
    2. Запрещаются сообщения, содержащие в себе ненормативную лексику, призывы к агрессии, насилию и иным оскорблениям.<br/>
    3. Запрещаются сообщения, содержащие в описании прямые ссылки и рекламу на сторонние ресурсы.<br/>
    4. Запрещаются сообщения, напечатанные с использованием заглавных букв.<br/>
  </div>
</div>
<div class="wishlist_summary clearfix">
  <div class="minitabs right">
    <?php echo ActiveHtml::link('Все', '/wishlist') ?>
    <?php echo ActiveHtml::link('Мои', '/wishlist?act=my', array('class' => 'tt', 'title' => 'Отобразить только ваши пожелания')) ?>
    <?php echo ActiveHtml::link('По городу', '/wishlist?act=city', array('class' => 'tt', 'title' => 'Отобразить желания пользователей по выбранному городу')) ?>
    <?php echo ActiveHtml::link('Друзья', '/wishlist?act=friends', array('class' => 'selected tt', 'title' => 'Отобразить желания Ваших друзей')) ?>
  </div>
  <div class="right post_progress"><img src="/images/upload.gif" /></div>
</div>
<div class="wishlist_wrap clearfix">
  <div class="wishlist_want_wrap left">
    <div class="wishlist_header_wrap clearfix">
      <div class="left wishlist_header">
        Я хочу
      </div>
      <div class="right wishlist_add" onclick="Wishlist.add(1)"><span class="icon-plus icon-white"></span></div>
    </div>
    <div id="wishlist_wants">
    <?php if ($wants): ?>
      <?php $this->renderPartial('_wishes', array('wishes' => $wants)) ?>
    <?php else: ?>
      <h2 class="empty" style="margin-top: 10px">Здесь будут отображаться пожелания Ваших друзей</h2>
    <?php endif; ?>
    </div>
    <? if (0 + Yii::app()->getModule('wishlist')->wishesPerPage < $wantsNum && $wantsNum > Yii::app()->getModule('wishlist')->wishesPerPage): ?><a class="pg_more" onclick="Wishlist.showMore(this, <?php echo Yii::app()->getModule('wishlist')->wishesPerPage ?>, 1, 'friends')">Показать еще</a><? endif; ?>
  </div>
  <div class="wishlist_can_wrap left">
    <div class="wishlist_header_wrap clearfix">
      <div class="left wishlist_header">
        Я могу
      </div>
      <div class="right wishlist_add" onclick="Wishlist.add(2)"><span class="icon-plus icon-white"></span></div>
    </div>
    <div id="wishlist_cans">
    <?php if ($cans): ?>
      <?php $this->renderPartial('_wishes', array('wishes' => $cans)) ?>
    <?php else: ?>
      <h2 class="empty" style="margin-top: 10px">Здесь будут отображаться возможности Ваших друзей</h2>
    <?php endif; ?>
    </div>
    <? if (0 + Yii::app()->getModule('wishlist')->wishesPerPage < $cansNum && $cansNum > Yii::app()->getModule('wishlist')->wishesPerPage): ?><a class="pg_more" onclick="Wishlist.showMore(this, <?php echo Yii::app()->getModule('wishlist')->wishesPerPage ?>, 2, 'friends')">Показать еще</a><? endif; ?>
  </div>
</div>