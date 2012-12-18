<ul>
    <li>
        <?php echo ActiveHtml::link('Моя страница', '/id'. Yii::app()->user->getId()) ?>
        <?php echo ActiveHtml::link('ред.', '/edit', array('class' => 'right')) ?>
    </li>
    <li>
        <?php echo ActiveHtml::link('Мои друзья', '/friends') ?>
    </li>
    <li>
        <?php echo ActiveHtml::link('Мои сообщения', '/im') ?>
    </li>
    <li>
        <?php echo ActiveHtml::link('Мои покупки', '/shopping') ?>
    </li>
    <li>
        <?php echo ActiveHtml::link('Мой пристрой', '/annexe') ?>
    </li>
    <li>
        <?php echo ActiveHtml::link('Мои закладки', '/favorites') ?>
    </li>
    <li>
        <?php echo ActiveHtml::link('Мои новости', '/feed') ?>
    </li>
    <li>
        <?php echo ActiveHtml::link('Мои настройки', '/settings') ?>
    </li>
</ul>
<?php if(Yii::app()->user->checkAccess('users.users.index')): ?>
<ul>
    <li>
        <?php echo ActiveHtml::link('Пользователи', '/users') ?>
    </li>
</ul>
<?php endif; ?>
<?php if(Yii::app()->user->checkAccess('purchases.purchases.create')): ?>
<ul class="orgmenu">
    <li>
        <?php echo ActiveHtml::link('Создать закупку', '/purchases/create') ?>
    </li>
    <li>
        <?php echo ActiveHtml::link('Мои закупки', '/purchases/my') ?>
    </li>

</ul>
<?php endif; ?>