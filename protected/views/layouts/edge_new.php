<?php
Yii::app()->getClientScript()->registerScriptFile('/js/jquery-1.8.2.min.js');
Yii::app()->getClientScript()->registerScriptFile('/js/main.js');
Yii::app()->getClientScript()->registerCssFile('/css/main.css', '', 'before purchases.css');
Yii::app()->getClientScript()->registerCssFile('/css/elements.css', '', 'before main.css');
Yii::app()->getClientScript()->registerCssFile('/css/icons.css');
Yii::app()->getClientScript()->registerCssFile('/css/edge.css');

$app=Yii::app();
$request = $app->getRequest();
/** @var $cookies CCookieCollection */
$cookies = $request->getCookies();
?>
<!DOCTYPE html>
<html lang="en">
<head>
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
  <title><?php echo CHtml::encode($this->pageTitle); ?></title>
  <meta charset="utf-8">
  <script type="text/javascript">
    Upload.assign({server_id: 1, action: 'http://cs1.spmix.ru/upload.php'});
  </script>
  <script type="text/javascript" src="http://code.jquery.com/ui/1.10.1/jquery-ui.js"></script>
</head>
<body>
<div id="utils">
  <div class="filectrl"><iframe id="iframe_200" name="iframe_200"></iframe></div>
</div>
<div id="global_progress_bg"></div>
<div id="global_progress" class="box_popup fixed">
  <div class="loader"></div>
  <div class="back"></div>
</div>

<div id="box_popup" class="box_popup box_popup_dark fixed">
  <div class="text"></div>
  <div class="back"></div>
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
    <div class="top">
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
  </div>
  <div id="page_layout" class="clearfix">
    <div id="sidebar" class="fl_l smallcolumn">
      <?php
      if (Yii::app()->user->getIsGuest()) {
        $this->widget('application.modules.users.components.LoginWidget2');
      }
      ?>
    </div>
    <div id="content" class="fl_l largecolumn wrap">
      <?php echo $content ?>
    </div>
  </div>
</div>

</body>
</body>
</html>