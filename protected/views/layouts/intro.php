<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/main.css" />
        <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/elements.css" />
        <script type="text/javascript" src="<?php  echo Yii::app()->request->baseUrl; ?>/js/jquery-1.8.2.min.js"></script>
        <script type="text/javascript" src="<?php  echo Yii::app()->request->baseUrl; ?>/js/main.js"></script>
        <script type="text/javascript">
            var A = {
                uuid: 0,
                user_id: 0,
                navPrefix: '/',
                host: location.host
            };

            var hshtest = (location.toString().match(/#(.*)/) || {})[1] || '';
            if (hshtest.length && hshtest.substr(0, 1) == A.navPrefix) {
                location.replace(location.protocol + '//' + location.host + '/' + hshtest.replace(/^(\/|!)/, ''));
            }
        </script>
        <title><?php echo CHtml::encode($this->pageTitle); ?></title>
    </head>
    <body>
    <div class="wrapper">
        <div class='top'>
            <div class="fl_l logo">
                <a href="/" class="logo_a"></a>
            </div>
            <div class="fl_l login">
                <?php
                if (Yii::app()->user->getIsGuest()) {
                    $this->widget('application.modules.users.components.LoginWidget');
                }
                ?>
                <div class="login_reg">
                    <a href="/register" class="btn green">Зарегистрироваться</a>
                </div>
            </div>
            <div class="clear"></div>
        </div>

    </div>
</body>
</html>