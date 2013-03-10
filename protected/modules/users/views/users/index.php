<?php
$this->pageTitle=Yii::app()->name . ' - Пользователи';

Yii::app()->getClientScript()->registerCssFile('/css/users.css');
Yii::app()->getClientScript()->registerScriptFile('/js/usermgr.js');

Yii::app()->getClientScript()->registerCssFile('/css/pagination.css');
Yii::app()->getClientScript()->registerScriptFile('/js/pagination.js');

foreach ($roles as $role) {
    $rolesJs[] = "'". $role->name ."'";
}

$delta = Yii::app()->controller->module->usersPerPage;
?>

<div class="tabs">
  <?php echo ActiveHtml::link('Пользователи', $this->createUrl('users/index'), array('class' => 'selected')) ?>
  <?php echo ActiveHtml::link('Роли', $this->createUrl('roles/index')) ?>
</div>
<div class="users_filters clearfix">
  <div rel="filters" class="left">
    <?php echo ActiveHtml::inputPlaceholder('c[name]', (isset($c['name'])) ? $c['name'] : '', array('placeholder' => 'Имя пользователя')) ?>
  </div>
  <div rel="filters" class="left">
    <?php echo ActiveHtml::dropdown('c[city_id]', 'Город', (isset($c['city_id'])) ? $c['city_id'] : '', City::getDataArray()) ?>
  </div>
  <div rel="filters" class="left">
    <?php echo ActiveHtml::dropdown('c[role]', 'Роль', (isset($c['role'])) ? $c['role'] : '', RbacItem::getSearchRoleArray()) ?>
  </div>
  <div class="right">
    <?php $this->widget('Paginator', array(
    'url' => '/users',
    'offset' => $offset,
    'offsets' => $offsets,
    'delta' => $delta,
  )); ?>
  </div>
</div>

<table class="users_table" style="width: 100%">
  <thead>
    <tr>
      <th>#</th>
      <th>E-Mail</th>
      <th>Имя пользователя</th>
      <th>Роль</th>
      <th></th>
    </tr>
  </thead>
  <tbody rel="pagination">
  <?php echo $this->renderPartial('_userlist', array('users' => $users, 'offset' => $offset)) ?>
  </tbody>
</table>
<? if ($offset + $delta < $offsets && $offsets > $delta): ?><a id="pg_more" class="pg_more" onclick="Paginator.showMore()">Еще пользователи</a><? endif; ?>

<script type="text/javascript">
var rolesList = [<?php echo implode(',', $rolesJs) ?>];
</script>