<?php /** @var $site SiteList */ ?>
<?php $page = ($offset + Yii::app()->controller->module->sitesPerPage) / Yii::app()->controller->module->sitesPerPage ?>
<?php $added = false; ?>
<?php if($sites): ?>
<?php foreach ($sites as $idx => $site): ?>
<tr id="site<?php echo $site->id ?>"<?php if(!$added) { echo ' rel="page-'. $page .'"'; $added = true; } ?> onclick="editSite(<?php echo $site->id ?>)">
  <td><?php echo $offset + $idx + 1 ?>.</td>
  <td id="site<?php echo $site->id ?>_name"><?php echo $site->site ?></td>
  <td id="site<?php echo $site->id ?>_shortstory">
    <?php echo $site->shortstory ?>
  </td>
  <td id="site<?php echo $site->id ?>_orgname"><?php echo $site->org_name ?></td>
  <td id="site<?php echo $site->id ?>_orgid">
    <?php echo $site->org_id ?>
  </td>
  <td id="site<?php echo $site->id ?>_datetime"><?php echo ActiveHtml::date($site->datetime) ?></td>
  <td>
    <?php echo ActiveHtml::link($site->author->getDisplayName(), '/id'. $site->author_id) ?>
  </td>
</tr>
<?php endforeach; ?>
<?php endif; ?>