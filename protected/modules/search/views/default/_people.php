<?php /** @var $people User */ ?>
<?php $page = ($offset + Yii::app()->controller->module->peoplesPerPage) / Yii::app()->controller->module->peoplesPerPage ?>
<?php $added = false; ?>
<?php foreach ($peoples as $people): ?>
<div<?php if(!$added) { echo ' rel="page-'. $page .'"'; $added = true; } ?> class="people clearfix">
    <div class="left photo">
        <?php echo ($people->profile->photo) ? ActiveHtml::showUploadImage($people->profile->photo) : '<img src="/images/camera_a.gif" />' ?>
    </div>
    <div class="left info">
        <div class="labeled name">
            <?php echo ActiveHtml::link(
            (Yii::app()->user->checkAccess('global.fullnameView'))
                ? $people->profile->firstname .' '. $people->profile->lastname .' ('. $people->login .')'
                : $people->login, '/id'. $people->id) ?>
        </div>
        <div class="labeled"><?php echo $people->profile->city->name ?></div>
        <?php if ($people->role->itemname != 'Пользователь'): ?>
        <div class="labeled"><?php echo $people->role->itemname ?></div>
        <?php endif; ?>
        <?php if ($people->isOnline()): ?>
        <div class="online">Online</div>
        <?php endif; ?>
    </div>
    <div class="left menu">

    </div>
</div>
<?php endforeach; ?>
