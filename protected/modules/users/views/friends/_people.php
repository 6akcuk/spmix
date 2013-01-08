<?php /** @var $people ProfileRelationship */ ?>
<?php $page = ($offset + Yii::app()->controller->module->friendsPerPage) / Yii::app()->controller->module->friendsPerPage ?>
<?php $added = false; ?>
<?php foreach ($peoples as $people): ?>
<div<?php if(!$added) { echo ' rel="page-'. $page .'"'; $added = true; } ?> id="people<?php echo $people->friend->id ?>" class="people clearfix">
    <div class="left photo">
        <?php echo ($people->friend->profile->photo) ? ActiveHtml::showUploadImage($people->friend->profile->photo) : '<img src="/images/camera_a.gif" />' ?>
    </div>
    <div class="left info">
        <div class="labeled name">
            <?php echo ActiveHtml::link(
            (Yii::app()->user->checkAccess('global.fullnameView'))
                ? $people->friend->profile->firstname .' '. $people->friend->profile->lastname .' ('. $people->friend->login .')'
                : $people->friend->login, '/id'. $people->friend->id) ?>
        </div>
        <div class="labeled"><?php echo $people->friend->profile->city->name ?></div>
        <?php if ($people->friend->role->itemname != 'Пользователь'): ?>
        <div class="labeled"><?php echo $people->friend->role->itemname ?></div>
        <?php endif; ?>
        <?php if ($people->friend->isOnline()): ?>
        <div class="online">Online</div>
        <?php endif; ?>
    </div>
    <div class="right menu">
        <?php echo ActiveHtml::link('Написать сообщение', '') ?>
        <?php echo ActiveHtml::link('Просмотреть друзей', '/friends?id='. $people->friend->id) ?>
        <?php if ($user->id == Yii::app()->user->getId()): ?>
        <a onclick="return Profile.deleteFriend(this, <?php echo $people->friend->id ?>)">Убрать из друзей</a>
        <?php endif; ?>
    </div>
</div>
<?php endforeach; ?>
