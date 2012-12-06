<?php /** @var $user User */ ?>
<?php foreach ($users as $user): ?>
<tr>
    <td><?php echo $user->id ?></td>
    <td><?php echo $user->email ?></td>
    <td style="position: relative">
        <a data-id="<?php echo $user->id ?>" onclick="UserMgr.assignRole(this)">
            <?php echo ($user->role) ? $user->role->itemname : "Назначить роль" ?>
        </a>
    </td>
</tr>
<?php endforeach; ?>