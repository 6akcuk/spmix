<?php
$app=Yii::app();
$request = $app->getRequest();
/** @var $cookies CCookieCollection */
$cookies = $request->getCookies();

Yii::app()->getClientScript()->registerScriptFile('/js/jquery-1.8.2.min.js');
Yii::app()->getClientScript()->registerScriptFile('/js/main.js');
Yii::app()->getClientScript()->registerScriptFile('/js/jquery.nivo.slider.pack.js');
Yii::app()->getClientScript()->registerCssFile('/css/main.css', '', 'before purchases.css');
Yii::app()->getClientScript()->registerCssFile('/css/elements.css', '', 'before main.css');
Yii::app()->getClientScript()->registerCssFile('/css/icons.css');

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title><?php echo CHtml::encode($this->pageTitle); ?></title>
        <link rel="stylesheet" href="<?php  echo Yii::app()->request->baseUrl; ?>/css/nivo.css" type="text/css" media="screen" />
        <link rel="stylesheet" href="<?php  echo Yii::app()->request->baseUrl; ?>/css/default.css" type="text/css" media="screen" />
        <script type="text/javascript">
            $(window).load(function() {
                $('#slider').nivoSlider();
            });
        </script>
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
    </head>
    <body>
    <div class="wrapper">
        <div class='top'>
            <div class="fl_l logo">
                <a href="/" class="logo_a"></a>
            </div>
            <div class="fl_l login">
              <div class="clearfix">
              <?php echo ActiveHtml::dropdown('cur_city', 'Город', ($cookies['cur_city']) ? $cookies['cur_city']->value : '', City::getDataArray()) ?>
              </div>
              <?php
              if (Yii::app()->user->getIsGuest()) {
                  $this->widget('application.modules.users.components.LoginWidget');
              }
              ?>
              <div class="login_reg">
                <a href="/site/forgot" class="btn green">Восстановить пароль</a>
              </div>
              <div class="login_reg">
                <a href="/register" class="btn green">Зарегистрироваться</a>
              </div>
            </div>
            <div class="clear"></div>
        </div>
    </div>
    <div class="slide">
        <div class="wrapper">
            <div class="slider-boxed"><div class="slider-wrapper theme-default">
                <div class="ribbon"></div>
                <div id="slider" class="nivoSlider">
                    <img src="/images/1.jpg" alt=""  />
                    <img src="/images/3.jpg" alt=""  />
                </div>
            </div></div></div>
        </div>
    </div>
    <div class="main">
        <div class="wrapper">
            <div class="main_a">

                <ul class="clearfix">
                    <li>
                        <?php echo ActiveHtml::link('Закупки', '/purchases') ?>
                    </li>
                    <li>
                        <?php echo ActiveHtml::link('Пристрой', '/annexe') ?>
                    </li>
                    <li>
                        <?php echo ActiveHtml::link('Хотелки', '/wishlist') ?>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="wrapper">
      <?php echo $content ?>
        <div class="articles">
        Наверное практически все знают что оптовые цены на товары ниже, и часто значительно ниже чем розничные. Соблазн купить товар по оптовой цене велик, но сделать это практически невозможно так как такие замечательные цены действуют только при покупке товара большой партией, выкупе сразу нескольких рамеров одежды или покупке товаров на достаточно большую сумму.

        И раньше бывали случаи что несколько человек объединялись и вместе вскладчину покупали вещи на оптовых складах но это было редко, так как найти несколько человек которым нужны похожие товары достаточно тяжело. С развитием интернета эта возможность перестала быть редким явлением, с одной стороны многие фирмы выкладывают свой ассортимент в интернете, а с другой стороны можно легко найти множество «соучастников» для вашей покупки.

        Подобные закупки в оптовых фирмах группами частных лиц получили название совместных покупок или просто СП

        На нашем сайте эта идея получила продолжение в виде специальной системы для осуществления подобного вида закупок. На сайте проходит несколько сот закупок по очень широкому ассортименту товаров, от детской одежды до автомобильных запчастей и акссесуаров. Несколько тысяч человек каждый день заходят на сайт и ищут выгодные предложения среди предлагаемого ассортимента.


        Функции организатора:
        выкладывают ассортимент товара,
        принимают и обрабатывают заявки,
        собирают деньги от участников
        взаимодейтвуют с поставщиком товаров
        раздают товар участникам закупки

        Как правило наценка на оптовую цену со стороны организатора составляет 5 – 17%. Эта наценка называется орг-процент Эти деньги идут на покрытие расходов организатора покупки, на транпортировку, организацию раздачи товара и другие расходы.

        Посетители сайта принимающие участие в закупках называются участники. Фактически участнику не нужно знать про тонкости работы с поставщиками, с отгрузками товара, особеностями заказа – эти задачи берет не себя организатор. Главные обязанности участника вовремя оплатить свою заявку и забрать свой заказ.
            </div>
    </div>
    <?php $this->widget('application.modules.purchases.components.NewPurchases') ?>
</body>
</html>