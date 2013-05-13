<div class="box_header">
  Рассылка СМС
  <a onclick="curBox().hide()" class="box_close right">Закрыть</a>
</div>
<div class="box_cont">
  <p>Разослать СМС <?php echo Yii::t('app', '<b id="users_num">{n}</b> пользователю|<b id="users_num">{n}</b> пользователям|<b id="users_num">{n}</b> пользователям', $phonesNum) ?></p>
  <textarea id="sms_message" onkeyup="Order.countSMS()"></textarea>
  <p>
    Знаков: <b id="letters_num">0</b>, СМС: <b id="sms_num">0</b>, Всего СМС: <b id="total_sms_num">0</b>
  </p>
</div>
<div class="box_buttons clearfix">
  <div class="left progress" id="sms_progress"></div>
  <div class="right">
    <a class="button" onclick="Order.sendSMS(<?php echo $purchase_id ?>, [<?php echo implode(', ', $ids) ?>])">Отправить</a>
  </div>
</div>