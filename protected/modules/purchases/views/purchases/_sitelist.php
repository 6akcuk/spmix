<?php /** @var $site SiteList */ ?>
<?php $page = ($offset + Yii::app()->controller->module->sitesPerPage) / Yii::app()->controller->module->sitesPerPage ?>
<?php $added = false; ?>
<?php if($sites): ?>
<?php foreach ($sites as $site): ?>
<div class="clearfix">
  <div class="right">
    <?php echo ActiveHtml::link($site->author->getDisplayName(), '/id'. $site->author_id) ?>
  </div>
  <div class="left"><?php echo $site->site ?></div>
  <div class="left" style="padding-left: 20px">
    <?php echo $site->shortstory ?>
  </div>
</div>
<?php endforeach; ?>
<?php endif; ?>