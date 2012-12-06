<?php
$this->pageTitle=Yii::app()->name . ' - Роли';
?>

<div id="cols" class="clearfix">
    <div class="col_large">
        <div id="tabs">
            <?php echo ActiveHtml::link('Пользователи', $this->createUrl('users/index')) ?>
            <?php echo ActiveHtml::link('Роли', $this->createUrl('roles/index'), array('class' => 'selected')) ?>
        </div>
        <div id="searchbar">
            <div class="search">
                <span class="iconify iconify_search_b"></span>
                <input type="text" name="q" data-url="<?php echo $this->createUrl('roles/search') ?>" value="" />
                <div class="progress"></div>
            </div>
        </div>
        <div id="roles_list">
            <table class="data">
                <thead>
                <tr>
                    <th>Имя</th>
                    <th>Описание</th>
                </tr>
                </thead>
                <?php foreach ($roles as $role): ?>
                <tr>
                    <td><?php echo $role->name ?></td>
                    <td><?php echo $role->description ?></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="col_small">
        <div class="block">
            <?php echo ActiveHtml::link('+ Добавить новую роль', $this->createUrl('roles/createRole'), array('class' => 'button button_full')) ?>
        </div>
        <?php $this->renderPartial('_menu', array('selected' => 'Роли')) ?>
    </div>
</div>