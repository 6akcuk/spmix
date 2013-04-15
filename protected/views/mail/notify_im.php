<!DOCTYPE html>
<html lang="en">
<head>
  <title></title>
  <meta charset="UTF-8">
  <style type="text/css">
    a {cursor: pointer;text-decoration: none}
    a:hover {text-decoration: none}
  </style>
</head>
<body style="margin:0px;padding:0px;font-family:tahoma,arial;font-size:12px">
  <table border="0" cellpadding="0" cellspacing="0" width="100%" bgcolor="#f5f5f5">
    <tr>
      <td width="5%">

      </td>
      <td width="90%" style="padding:15px 0px;">
        <table border="0" width="100%" cellpadding="0" cellspacing="0" bgcolor="#ffffff">
          <tr>
            <td>
              <table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#557132">
                <tr>
                  <td>
                    <a href="http://<?php echo Yii::app()->params['domain'] ?>" style="display:inline-block;color:#ffffff;font-weight:bold;font-size: 20px;padding:10px">SPMIX</a>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr>
            <td>
              <div style="border: 1px solid #70973f; border-top: 0px; font-size: 12px; padding: 18px 18px 13px 18px">
                <h1 style="margin: 2px 0px 15px 0px; border-bottom: 1px solid #70973f; color: #508033; padding: 0px 0px 4px; font-size: 100%">У Вас 1 новое сообщение</h1>
                <p style="max-width:650px;margin:0px;padding:0px;font-size:12px">
                  Здравствуйте, <a href="http://<?php echo Yii::app()->params['domain'] ?>/id" style="color:#005b01"><b>Кто-то</b></a>.
                  Вы получили:
                </p>
                <br>
                <p style="max-width:650px;margin:0px;padding:0px;font-size:12px">
                  <a href="http://<?php echo Yii::app()->params['domain'] ?>/mail?filter=new" style="color:#005b01">
                    <b>1</b> новое сообщение
                  </a>
                  от
                  <a href="http://<?php echo Yii::app()->params['domain'] ?>/id" style="color:#005b01">
                    Кого-то
                  </a>
                </p>
                <table border="0" cellpadding="0" cellspacing="0" style="padding:10px 0px">
                  <tr>
                    <td>
                      <a href="http://<?php echo Yii::app()->params['domain'] ?>/mail?filter=new" style="background: #447229; color: #ffffff; font-weight: bold; display: inline-block; padding: 7px 10px;">
                        Прочитать
                      </a>
                    </td>
                  </tr>
                </table>
                <table cellpadding="0" cellspacing="0" border="0">
                  <tr>
                    <td valign="top" style="padding-right: 10px">
                      <a href="http://<?php echo Yii::app()->params['domain'] ?>/id" target="_blank">
                        <img src="" alt="" style="display:block" border="0" />
                      </a>
                    </td>
                    <td>
                      <table border="0" cellpadding="0" cellspacing="0">
                        <tr>
                          <td width="70" style="color:#808080;padding-right:10px;" valign="top">От кого:</td>
                          <td style="padding-bottom: 6px">
                            <a href="http://<?php echo Yii::app()->params['domain'] ?>/id" target="_blank" style="color:#005b01;text-decoration:none">Тест</a>
                          </td>
                        </tr>
                        <tr>
                          <td style="color:#808080;padding-right:10px;" valign="top">Сообщение:</td>
                          <td style="padding-bottom: 6px">
                            Message
                          </td>
                        </tr>
                        <tr>
                          <td>
                            <div style="width:80px">&nbsp;</div>
                          </td>
                          <td>
                            <a href="http://<?php echo Yii::app()->params['domain'] ?>/mail?act=show&id=" target="_blank" style="color:#808080;text-decoration:none">15 апреля 2013 в 0:46</a>
                          </td>
                        </tr>
                        <tr>
                          <td></td>
                          <td style="padding-top: 14px">
                            <a href="http://<?php echo Yii::app()->params['domain'] ?>/mail?act=show&id=" style="background: #447229; color: #ffffff; font-weight: bold; display: inline-block; padding: 7px 10px;">
                              Ответить кому-то
                            </a>
                          </td>
                        </tr>
                      </table>
                    </td>
                  </tr>
                </table>
              </div>
            </td>
          </tr>
          <tr>
            <td bgcolor="#f5f5f5" align="center" style="padding: 13px 0px 0px 0px; font-size: 12px;">
              <a href="http://<?php echo Yii::app()->params['domain'] ?>/id" style="color:#005b01;font-weight:bold">Кто-то</a>,
              Вы можете отменить уведомления на E-Mail в
              <a href="http://<?php echo Yii::app()->params['domain'] ?>/notify" style="color:#005b01">
                Настройках оповещений
              </a>
            </td>
          </tr>
        </table>
      </td>
      <td width="5%">

      </td>
    </tr>
  </table>
</body>
</html>