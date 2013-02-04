<?php /** @var $user User */ ?>
<?php $page = ($offset + Yii::app()->controller->module->usersPerPage) / Yii::app()->controller->module->usersPerPage ?>
<?php $added = false; ?>
<?php foreach ($users as $user): ?>
<tr<?php if(!$added) { echo ' rel="page-'. $page .'"'; $added = true; } ?>>
  <td><?php echo $user->id ?></td>
  <td><?php echo $user->email ?></td>
  <td><?php echo $user->getDisplayName() ?></td>
  <td style="position: relative">
    <a data-id="<?php echo $user->id ?>" onclick="UserMgr.assignRole(this)">
        <?php echo ($user->role) ? $user->role->itemname : "Назначить роль" ?>
    </a>
  </td>
  <td>
    <?php echo ActiveHtml::link('Перейти на страницу', '/id'. $user->id) ?>
  </td>
</tr>
<?php endforeach; ?>