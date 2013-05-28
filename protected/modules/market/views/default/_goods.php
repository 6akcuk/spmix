<?php /** @var $good MarketGood */ ?>
<?php $page = ($offset + Yii::app()->getModule('market')->goodsPerPage) / Yii::app()->getModule('market')->goodsPerPage ?>
<?php $added = false; ?>
<?php foreach ($goods as $good): ?>
<div<?php if(!$added) { echo ' rel="page-'. $page .'"'; $added = true; } ?> class="fl_l market_good">
  <?php echo ActiveHtml::link('<nobr>'. $good->name .'</nobr>', '/market'. $good->author_id .'_'. $good->good_id, array('class' => 'market_good_name')) ?>
  <div class="market_good_cont clearfix">
    <div class="fl_l market_good_photo_wrap">
      <a class="market_good_bigph">
        <span class="market_good_bigph_label">Увеличить</span>
      </a>
      <a class="img" href="/market<?php echo $good->author_id ?>_<?php echo $good->good_id ?>" onclick="return nav.go(this, event)">
        <?php echo ActiveHtml::showUploadImage($good->image, 'b', array('class' => 'market_good_img')) ?>
      </a>
    </div>
    <div class="fl_l market_good_info">
      <div class="market_good_price"><?php echo ActiveHtml::price($good->showPrice()) ?></div>
      <div class="market_good_author"><?php echo ActiveHtml::link('<span class="icon-user"></span> '. $good->author->login, '/id'. $good->author_id) ?></div>
      <div class="fl_l market_good_label">Размер:</div>
      <div class="fl_l market_good_labeled"><nobr><?php echo $good->size ?></nobr></div>
      <div class="clear"></div>
      <div class="fl_l market_good_label">Цвет:</div>
      <div class="fl_l market_good_labeled"><nobr><?php echo $good->color ?></nobr></div>
      <div class="clear"></div>
    </div>
  </div>
</div>
<?php endforeach; ?>
