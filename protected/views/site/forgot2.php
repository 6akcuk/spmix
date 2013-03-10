<?php

?>
<h1>Восстановление доступа к сайту</h1>
<p>Введите новый пароль к своему аккаунту</p>
<form id="chpwdform" action="/site/forgot?email=<?php echo $email ?>&code=<?php echo $code ?>">
<div class="row">
  <?php echo ActiveHtml::inputPlaceholder('new_password', '', array('placeholder' => 'Новый пароль')) ?>
</div>
<div class="row" style="margin-top: 10px">
  <?php echo ActiveHtml::inputPlaceholder('new_password_rpt', '', array('placeholder' => 'Повторите пароль')) ?>
</div>
<div class="row" style="margin-top: 10px">
  <a class="button" onclick="changePwd()">Сменить пароль</a>
</div>
</form>
<div style="margin-top: 10px">&nbsp;</div>
<script>
  function changePwd() {
    FormMgr.submit('#chpwdform', 'right', function(r) {
      if (r.success) location.href = '/';
    });
  }
</script>