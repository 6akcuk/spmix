<?php

Yii::app()->getClientScript()->registerCssFile('/css/profile.css');
Yii::app()->getClientScript()->registerCssFile('/css/im.css');

Yii::app()->getClientScript()->registerCssFile('/css/pagination.css');
Yii::app()->getClientScript()->registerScriptFile('/js/pagination.js');

Yii::app()->getClientScript()->registerScriptFile('/js/mail.js');

Yii::app()->getClientScript()->registerCssFile('/css/photoview.css');
Yii::app()->getClientScript()->registerScriptFile('/js/photoview.js');
Yii::app()->getClientScript()->registerScriptFile('/js/jquery.cookie.js', null, 'after jquery-');

$this->pageTitle = Yii::app()->name .' - Новое сообщение';

$friendsJS = array();
foreach ($friends as $friend) {
  $friendsJS[] = "'". $friend->friend->id ."': {img: '". (($friend->friend->profile->photo) ? ActiveHtml::getImageUrl($friend->friend->profile->photo, 'a') : 'http://spmix.ru/images/camera_a.gif') ."', text: '". $friend->friend->getDisplayName() ."', sub: '". $friend->friend->profile->city->name ."'}";
}

$friendsJS[] = "'". Yii::app()->user->getId() ."': {private: true, img: '". ((Yii::app()->user->model->profile->photo) ? ActiveHtml::getImageUrl(Yii::app()->user->model->profile->photo, 'a') : 'http://spmix.ru/images/camera_a.gif') ."', text: '". Yii::app()->user->model->getDisplayName() ."', sub: '". Yii::app()->user->model->profile->city->name ."'}";

?>
<div class="tabs">
  <?php echo ActiveHtml::link('Полученные', '/mail?act=inbox') ?>
  <?php echo ActiveHtml::link('Отправленные', '/mail?act=outbox') ?>
  <?php echo ActiveHtml::link('Новое сообщение', '/mail?act=write', array('class' => 'selected')) ?>
</div>
<div class="mail_message">
  <div class="mail_envelope_wrap">
    <div class="mail_envelope" id="mail_envelope">
      <form id="mail_form" action="/mail?act=write">
      <table cellpadding="0" cellspacing="0">
        <tbody>
        <tr>
          <td class="mail_envelope_photo_cell">
            <div class="mail_envelope_photo">
              <img src="/images/camera_a.gif" width="100" />
            </div>
          </td>
          <td>
            <h4 class="mail_write_header">Получатель</h4>
            <div id="im_wdd" class="wdd clearfix" onclick="WideDropdown.show('im_wdd', event)">
              <div class="wdd_lwrap" style="width: 420px">
                <div class="wdd_list"></div>
              </div>
              <div class="right wdd_arrow"></div>
              <div class="wdd_bubbles"></div>
              <div class="wdd_add left" style="display:none">
                <div class="wdd_add2">
                  <table>
                    <tr>
                      <td>
                        <div class="wdd_add3">
                          <nobr>Добавить</nobr>
                        </div>
                      </td>
                      <td>
                        <div class="wdd_add_plus"></div>
                      </td>
                    </tr>
                  </table>
                </div>
              </div>
              <?php echo ActiveHtml::inputPlaceholder(
                '',
                '',
                array(
                  'class' => 'left wdd_text',
                  'placeholder' => 'Введите имя друга',
                  'onfocus' => "WideDropdown.setFocused('im_wdd', event)",
                  'onblur' => "WideDropdown.setUnfocused('im_wdd', event)",
                )
              ) ?>
              <script type="text/javascript">
                WideDropdown.addList('im_wdd', {<?php echo implode(', ', $friendsJS) ?>});
              </script>
            </div>
            <div id="im_theme" class="row" style="display:none">
              <h4 class="mail_write_header">Тема</h4>
              <?php echo ActiveHtml::inputPlaceholder('Mail[title]', '') ?>
            </div>
          </td>
        </tr>
        </tbody>
      </table>
      <div class="mail_envelope_form">
        <?php echo ActiveHtml::smartTextarea('mail_message', '', array(
          'style' => 'overflow:hidden;resize:none',
          'minheight' => 98,
          'maxheight' => 300,
          'onkeypress' => 'onCtrlEnter(event, mail.write)',
        )) ?>
      </div>
      <div id="mail_attaches" class="mail_post_attaches clearfix"></div>
      <div class="mail_envelope_post clearfix">
        <div class="left">
          <a class="button" onclick="mail.write()">Отправить</a>
        </div>
        <div id="mail_progress" class="left mail_post_progress">
          <img src="/images/upload.gif" />
        </div>
        <div class="right">
          <?php echo ActiveHtml::upload('photo', '', 'Прикрепить фотографию', array('onchange' => 'mail.attachPhoto({id})')) ?>
        </div>
      </div>
      </form>
    </div>
  </div>
  <div class="mail_envelope_shadow"></div>
</div>
<script type="text/javascript">
  if (!A.wddOnSelect) A.wddOnSelect = {};
  A.wddOnSelect['im_wdd'] = function() {
    if (WideDropdown.countBubbleSize('im_wdd') > 1) $('#im_theme').show();
  };
  if (!A.wddOnDeselect) A.wddOnDeselect = {};
  A.wddOnDeselect['im_wdd'] = function() {
    if (WideDropdown.countBubbleSize('im_wdd') <= 1) $('#im_theme').hide();
  };
</script>