<?php
/** @var $userinfo User */
$title = Yii::app()->name;

Yii::app()->getClientScript()->registerCssFile('/css/profile.css');

if ($userinfo)
    $title .= ' - ' .
        ((Yii::app()->user->checkAccess('global.fullnameView'))
            ? $userinfo->profile->lastname .' '. $userinfo->profile->firstname .' '. $userinfo->profile->middlename
            : $userinfo->login .' '. $userinfo->profile->firstname);

$this->pageTitle = $title;
?>
<div class="profile-header clearfix">
    <span class="left username">
        <?php if (Yii::app()->user->checkAccess('global.fullnameView')): ?>
        <?php echo $userinfo->profile->firstname ?> <?php echo $userinfo->profile->lastname ?>
        <?php else: ?>
        <?php echo $userinfo->login ?> <?php echo $userinfo->profile->firstname ?>
        <?php endif; ?>
    </span>
    <?php if ($userinfo->role->itemname == 'Организатор'): ?><span class="left userspecial">(Организатор)</span><?php endif; ?>
    <?php if ($userinfo->role->itemname == 'Модератор'): ?><span class="left userspecial">(Модератор)</span><?php endif; ?>
    <?php if ($userinfo->role->itemname == 'Администратор'): ?><span class="left userspecial">(Администратор)</span><?php endif; ?>
    <span class="right useronline">
        <?php
        if ($userinfo->isOnline()) {
            echo "Online";
        }
        else {
            echo "был". (($userinfo->profile->gender == 'Female') ? "а" : "") ." в сети ". ActiveHtml::timeback($userinfo->lastvisit);
        }
        ?>
    </span>
</div>
<div class="profile-columns clearfix">
    <div class="left profile-left">
        <div class="profile-photo">
            <?php $photo = json_decode($userinfo->profile->photo, true); ?>
            <?php if (is_array($photo) && sizeof($photo)): ?>
            <?php echo ActiveHtml::showUploadImage($userinfo->profile->photo, 'a') ?>
            <?php else: ?>
            <img src="/images/camera_a.gif" width="250" alt="" />
            <?php endif; ?>
        </div>
        <div class="module">
            <a href="/friends?id=<?php echo $userinfo->id ?>" onclick="return nav.go(this, event, {noback: false})" class="module-header">
                <div class="header-top">
                    Друзья
                </div>
                <div class="header-bottom">
                    0 друзей
                </div>
            </a>
        </div>
    </div>
    <div class="left profile-right">
        <div class="profile-info">
            <div class="clearfix">
                <div class="label left">
                    Город:
                </div>
                <div class="labeled left">
                    <?php echo ActiveHtml::link($userinfo->profile->city->name, '/search?c[city_id]='. $userinfo->profile->city_id) ?>
                </div>
            </div>
            <div class="clearfix miniblock">
                <div class="label left">
                    Зарегистрирован<?php echo (($userinfo->profile->gender == 'Female') ? "а" : "") ?>:
                </div>
                <div class="labeled left">
                    <?php echo ActiveHtml::link(ActiveHtml::date($userinfo->regdate), '/search?c[regdate]='. $userinfo->regdate) ?>
                </div>
            </div>
            <div class="clearfix miniblock">
                <div class="label left">
                    Репутация:
                </div>
                <div class="labeled left">
                    <span class="profile-positive-rep">
                        <a class="iconify_plus_a"></a>
                        <?php echo $userinfo->profile->positive_rep ?>
                    </span>
                    <span class="profile-negative-rep">
                        <?php echo $userinfo->profile->negative_rep ?>
                        <a class="iconify_dash_a"></a>
                    </span>
                </div>
            </div>
        </div>
        <div class="module">
            <a class="module-header">
                <div class="header-top">
                    Пригласить друзей на сайт
                </div>
            </a>
            <div class="module-body">
                <small class="profile-invite-info">За каждого зарегистрированного пользователя вы получите +1 балл в репутацию.</small>
                <div class="profile-invite">
                    <div class="clearfix">
                        <span class="left label">Ваш номер для приглашений</span>
                        <span class="left labeled"><b><?php echo $userinfo->id ?></b></span>
                    </div>
                    <div class="clearfix">
                        <span class="left label">Ваша ссылка для приглашений</span>
                        <span class="left labeled"><a>http://spmix.ru/invite<?php echo $userinfo->id ?></a></span>
                    </div>
                </div>
                <p>
                    Вы можете пригласить своих коллег, друзей, знакомых на наш сайт сообщив им свой номер для
                    приглашений или отправив ссылку (ссылку можно разместит, например на вашей странице в
                    социальных сетях), также вы можете отправить им СМС сообщение. Отправка сообщения
                    бесплатное.
                </p>
                <form action="/inviteBySMS" method="post">

                </form>
            </div>
        </div>
        <div class="module">
            <a class="module-header">
                <div class="header-top">
                    О себе
                </div>
            </a>
            <div class="module-body">
                <?php echo nl2br($userinfo->profile->about) ?>
            </div>
        </div>
    </div>
</div>