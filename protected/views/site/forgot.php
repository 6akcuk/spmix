<?php

?>
<h1>Восстановление доступа к сайту</h1>
<p>Доступ к своему аккаунту можно восстановить с помощью сотового телефона или адреса E-Mail</p>
<div class="row clearfix">
  <div class="left"><?php echo ActiveHtml::inputPlaceholder('email', '', array('placeholder' => 'Аккаунт (E-Mail адрес)', 'style' => 'width: 375px')) ?></div>
  <a class="button left" rel="tooltip" title="Отправить код восстановления на телефон" onclick="restore('cellular')" style="margin-left: 10px">Телефон</a>
  <a class="button left" rel="tooltip" title="Отправить код восстановления на E-Mail" onclick="restore('email')" style="margin-left: 10px">E-Mail</a>
</div>
<div id="code_row" class="row" style="display: none; margin-top: 10px">
  <?php echo ActiveHtml::inputPlaceholder('code', '', array('placeholder' => 'Код восстановления доступа')) ?>
  <a class="button" onclick="proceed()">Продолжить</a>
</div>
<div style="margin-top: 10px">&nbsp;</div>

<script>
function restore(type) {
  var $email = $('#email');
  $('.input_error').remove();

  if (!$.trim($email.val())) {
    inputError($email, 'Укажите E-Mail, на который зарегистрирован потерянный аккаунт');
    return;
  }

  $('#code_row').slideDown();
  ajax.post('/site/forgot', {email: $email.val(), type: type}, function(r) {
    boxPopup(r.msg);
  }, function(xhr) {});
}

function proceed() {
  var $code = $('#code'),
      $email = $('#email');;
  $('.input_error').remove();

  if (!$.trim($email.val())) {
    inputError($email, 'Укажите E-Mail, на который зарегистрирован потерянный аккаунт');
    return;
  }
  if (!$.trim($code.val())) {
    inputError($code, 'Укажите код восстановления для продолжения');
    return;
  }

  location.href = '/site/forgot?email='+ $.trim($email.val()) +'&code='+ $.trim($code.val());
}
</script>