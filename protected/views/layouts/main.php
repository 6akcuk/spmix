<?php
Yii::app()->getClientScript()->registerScriptFile('/js/jquery-1.8.2.min.js');
Yii::app()->getClientScript()->registerScriptFile('/js/main.js');
Yii::app()->getClientScript()->registerCssFile('/css/main.css', '', 'before purchases.css');
Yii::app()->getClientScript()->registerCssFile('/css/elements.css', '', 'before main.css');
Yii::app()->getClientScript()->registerCssFile('/css/icons.css');

$app=Yii::app();
$request = $app->getRequest();
/** @var $cookies CCookieCollection */
$cookies = $request->getCookies();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title><?php echo CHtml::encode($this->pageTitle); ?></title>
    <meta charset="utf-8">
    <script type="text/javascript">
        var A = {
            uuid: 0,
            user_id: <?php echo Yii::app()->user->getId() ?>,
            navPrefix: '/',
            host: location.host
        };
        // Cross-domain fix
        document.domain = A.host.match(/[a-zA-Z]+\.[a-zA-Z]+\.?$/)[0];

        var hshtest = (location.toString().match(/#(.*)/) || {})[1] || '';
        if (hshtest.length && hshtest.substr(0, 1) == A.navPrefix) {
            location.replace(location.protocol + '//' + location.host + '/' + hshtest.replace(/^(\/|!)/, ''));
        }
    </script>
</head>
<body>
<div id="utils"></div>
<div id="global_progress_bg"></div>
<div id="global_progress" class="fixed">
    <img src="<?php echo Yii::app()->request->baseUrl; ?>/images/2.gif" alt="" />
</div>

<div class="fixed pv_dark" id="layout_bg" style="display: none;"></div>
<div class="fixed" id="layout_wrap" style="display: none;">
    <div id="layout"></div>
</div>

<div class="fixed" id="box_bg" style="display: none;"></div>
<div class="fixed" id="box_wrap" style="display: none;">
    <div id="box_layout"></div>
</div>

<div class="fixed stl" id="stl_left" style="display:none">
    <div id="stl_bg">
        <nobr id="stl_text"><span class="iconify_up_a"></span> Наверх</nobr>
    </div>
</div>

<div class="wrapper">
    <div id="header">
        <div class='top'>
            <div class="fl_l logo">
                <a href="/" class="logo_a"></a>
            </div>
            <div class="fl_l login">
                <div class="clearfix">
                    <?php echo ActiveHtml::dropdown('cur_city', 'Город', ($cookies['cur_city']) ? $cookies['cur_city']->value : '', City::getDataArray()) ?>
                </div>
            </div>
            <div class="clear"></div>
        </div>
        <div class="main">
            <div class="wrapper">
                <div class="main_a">
                <?php if (!Yii::app()->user->getIsGuest()): ?>
                <ul class="clearfix">
                    <li>
                        <?php echo ActiveHtml::link('Будем знакомы', '/search?c[section]=people') ?>
                    </li>
                    <li>
                        <?php echo ActiveHtml::link('Закупки', '/purchases') ?>
                    </li>
                    <li>
                        <?php echo ActiveHtml::link('Пристрой', '/annexe') ?>
                    </li>
                    <li>
                        <?php echo ActiveHtml::link('Хотелки', '/wishlist') ?>
                    </li>
                    <li>
                        <a href="/logout">Выйти</a>
                    </li>
                </ul>
                <?php else: ?>
                <?php $this->widget('application.modules.users.components.LoginWidget'); ?>
                <?php endif; ?>
                    </div>
            </div>
        </div>
    </div>
    <div id="page_layout" class="wrap clearfix">
        <div id="sidebar" class="left smallcolumn"<?php if(Yii::app()->controller->wideScreen): ?> style="display:none"<?php endif; ?>>
            <?php echo $this->renderPartial('//layouts/leftmenu') ?>
        </div>
        <div id="content" class="<?php if(!Yii::app()->controller->wideScreen): ?>largecolumn<?php endif; ?> right">
            <?php echo $content; ?>
        </div>
    </div>
</div>
</body>
<script type="text/javascript">
Upload.assign({server_id: 1, action: 'http://cs1.spmix.ru/upload.php'});
</script>
</body>
</html>