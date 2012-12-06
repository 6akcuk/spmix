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
        <div id="global_progress_bg"></div>
        <div id="global_progress" class="fixed">
            <img src="<?php echo Yii::app()->request->baseUrl; ?>/images/2.gif" alt="" />
        </div>
        <div id="top">
            <div class="wrap">
                <div id="top_intro" class="clearfix">
                    <div class="left">
                        <h1>SPMix</h1>
                    </div>
                    <div class="right">
                        <?php
                            if (Yii::app()->user->getIsGuest()) {
                                $this->widget('application.modules.users.components.LoginWidget');
                            }
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <div id="features">
            <div class="wrap">
                <div class="features_top">
                    <h1>Что дает этот сайт?</h1>
                    <ul class="grid clearfix">
                        <li class="first">
                            <img src="" alt="" />
                            <h2>В разработке</h2>
                            <p>Данная функция в разработке</p>
                        </li>
                        <li>
                            <img src="" alt="" />
                            <h2>В разработке</h2>
                            <p>Данная функция в разработке</p>
                        </li>
                        <li>
                            <img src="" alt="" />
                            <h2>В разработке</h2>
                            <p>Данная функция в разработке</p>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div id="meetings">
            <div class="wrap">
                <h1>Все еще не с нами? Присоединяйтесь!</h1>
                <div class="center">
                    <a href="/register" class="btn green">Зарегистрироваться</a>
                </div>
            </div>
        </div>
        <div id="footer">
            <div class="wrap">
                <div class="footers_foot">
                    <div id="legal">
                        <p>
                            Сделано с <a onclick="test_rw()" class="iconify love">k</a> и на базе <a href="http://yiiframework.com">Yii Framework</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>