<?php
$this->pageTitle=Yii::app()->name . ' - Пользователи';

Yii::app()->getClientScript()->registerScriptFile('/js/usermgr.js');

foreach ($roles as $role) {
    $rolesJs[] = "'". $role->name ."'";
}
?>

<div id="cols" class="clearfix">
    <div class="col_large">
        <div id="tabs">
            <?php echo ActiveHtml::link('Пользователи', $this->createUrl('users/index'), array('class' => 'selected')) ?>
            <?php echo ActiveHtml::link('Роли', $this->createUrl('roles/index')) ?>
        </div>
        <div id="searchbar">
            <div class="search">
                <span class="iconify iconify_search_b"></span>
                <input type="text" name="q" data-url="<?php echo $this->createUrl('users/search') ?>" value="" />
                <div class="progress"></div>
            </div>
        </div>
        <div id="users_list">
            <table class="data">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Логин</th>
                        <th>Роль</th>
                    </tr>
                </thead>
                <tbody>
                <?php echo $this->renderPartial('_userlist', array('users' => $users)) ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="col_small">
        <div class="block">
            <?php echo ActiveHtml::link('+ Добавить нового пользователя', $this->createUrl('users/create'), array('class' => 'button button_full')) ?>
        </div>
        <section class="block">
            <h4 class="green">Меню</h4>
            <ul>
                <li>
                    <?php echo ActiveHtml::link('Добавить нового пользователя', $this->createUrl('users/create')) ?>
                </li>
            </ul>
        </section>
    </div>
</div>

<script type="text/javascript">
var rolesList = [<?php echo implode(',', $rolesJs) ?>];
</script>