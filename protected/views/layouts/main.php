<?php
Yii::app()->getClientScript()->registerScriptFile('/js/jquery-1.8.2.min.js');
Yii::app()->getClientScript()->registerScriptFile('/js/main.js');
Yii::app()->getClientScript()->registerCssFile('/css/main.css');
Yii::app()->getClientScript()->registerCssFile('/css/elements.css');
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

<div id="header">
    <div class="wrap clearfix">
        <a href="/" class="logo">SPMIX</a>
        <div class="left gsearch">
            <input type="text" id="gsearch" name="q" value="" />
            <a class="iconify_search_a"></a>
        </div>
        <div class="right">
            <?php if (!Yii::app()->user->getIsGuest()): ?>
            <ul class="hmenu clearfix">
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
<div id="body">
    <div class="wrap">
        <div class="maincolumns clearfix">
            <div class="smallcolumn">
                <?php echo $this->renderPartial('//layouts/leftmenu') ?>
            </div>
            <div id="content" class="largecolumn">
                <?php echo $content; ?>
            </div>
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
<script type="text/javascript">
Upload.assign({server_id: 1, action: 'http://cs1.spmix.ru/upload.php'});
</script>
</body>
</html>