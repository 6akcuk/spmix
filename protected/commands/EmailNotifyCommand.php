<?php
/**
 * Created by JetBrains PhpStorm.
 * User: denis
 * Date: 12.04.13
 * Time: 9:53
 * To change this template use File | Settings | File Templates.
 */

class EmailNotifyCommand extends CConsoleCommand {
  /**
   * Оповещения о личных сообщениях по почте
   *
   * @return int
   */
  public function actionIm() {
    if (Yii::app()->mutex->lock('email-notify-im', 55)) {
      /** @var $conn CDbConnection */
      $conn = Yii::app()->db;

      $cur_date = date("Y-m-d H:i", (time() - 1800));

      $command = $conn->createCommand("
SELECT o.email, op.firstname, r.owner_id, COUNT(m.message_id) AS msg_num,
  m.creation_date, m.message, m.message_id, p.photo, p.gender, COUNT(u.id) AS auth_num,
  GROUP_CONCAT(DISTINCT CONCAT_WS(';', u.id, u.login, p.firstname)) AS authors
  FROM `profile_requests` r
    INNER JOIN `profile_notifies` n ON n.user_id = r.owner_id
    INNER JOIN `dialog_messages` m ON m.message_id = r.req_link_id
    INNER JOIN `users` u ON u.id = m.author_id
    INNER JOIN `profiles` p ON p.user_id = u.id
    INNER JOIN `users` o ON o.id = r.owner_id
    INNER JOIN `profiles` op ON op.user_id = o.id
  WHERE r.req_type = ". ProfileRequest::TYPE_PM ." AND n.notify_im = 1 AND m.creation_date BETWEEN '". $cur_date .":00' AND '". $cur_date .":59'
  GROUP BY r.owner_id");
      $reader = $command->query();

      Yii::import('application.vendors.*');
      require_once 'Mail/Mail.php';

      $mail = Mail::getInstance();
      $mail->setSender(array(Yii::app()->params['noreplymail'], Yii::app()->params['noreplyname']));
      $mail->IsMail();

      $counter = 0;

      while (($row = $reader->read()) !== false) {
        $author = explode(";",  $row['authors']);
        $ticket = UserTicket::model()->find('user_id = :id', array(':id' => $row['owner_id']));
        if (!$ticket) $ticket = new UserTicket();
        $ticket->user_id = $row['owner_id'];
        $ticket->generateToken();
        $ticket->save();

        $html = $this->renderFile(Yii::app()->getBasePath() ."/views/mail/notify_im.php", array('row' => $row, 'ticket' => $ticket), true);
        $title = ($row['msg_num'] > 1)
          ? Yii::t('app', 'У Вас {n} непрочитанное сообщение|У Вас {n} непрочитанных сообщения|У Вас {n} непрочитанных сообщений', $row['msg_num'])
          : $author[2] .' '. $author[1] .' оставил'. (($row['gender'] == 'Female') ? 'а' : '') .' Вам личное сообщение';

        $mail->sendMail(Yii::app()->params['noreplymail'], Yii::app()->params['noreplyname'], $row['email'], $title, $html, true, null, null, null);
        $mail->ClearAddresses();

        $counter++;
      }

      // Logs
      if ($counter > 0) {
        $logfile = Yii::app()->getRuntimePath() .'/email_notify_im.log';
        if (file_exists($logfile)) $log = file_get_contents($logfile);
        else $log = "";

        $log = date("d.m.Y H:i:s") ." отправлено ". $counter ." писем\r\n". $log;
        file_put_contents($logfile, $log);
      }

      Yii::app()->mutex->unlock();
      return 0;
    }
    else return 1;
  }

  /**
   * Оповещения о новых закупках
   */
  public function actionPurchase() {
    if (Yii::app()->mutex->lock('email-notify-purchase', 1)) {
      /** @var $conn CDbConnection */
      $conn = Yii::app()->db;

      $cur_date = date("Y-m-d");
      $prev_date = date("Y-m-d", (time() - 86400));

      $command = $conn->createCommand("
SELECT u.email, p.user_id, p.firstname, COUNT(pc.purchase_id) AS pc_num,
  GROUP_CONCAT(DISTINCT CONCAT_WS('<photo>', pc.purchase_id, pc.image) SEPARATOR '<gphoto>') AS purchases_photo,
  GROUP_CONCAT(DISTINCT CONCAT_WS('<purchase>', pc.purchase_id, pc.name, SUBSTR(e.fullstory, 0, 200)) SEPARATOR '<gp>') AS purchases
FROM `users` u
  INNER JOIN `profile_notifies` n ON n.user_id = u.id
  INNER JOIN `profiles` p ON p.user_id = u.id
  INNER JOIN `purchases` pc ON pc.city_id = p.city_id
  LEFT JOIN `purchase_external` e ON e.purchase_id = pc.purchase_id
WHERE n.notify_purchases = 1 AND pc.state = '". Purchase::STATE_ORDER_COLLECTION ."'
  AND pc.mod_confirmation = 1 AND pc.confirm_date BETWEEN '". $prev_date ." 07:00:00' AND '". $cur_date ." 06:59:59'
GROUP BY u.email
");
      $reader = $command->query();

      Yii::import('application.vendors.*');
      require_once 'Mail/Mail.php';

      $mail = Mail::getInstance();
      $mail->setSender(array(Yii::app()->params['noreplymail'], Yii::app()->params['noreplyname']));
      $mail->IsMail();

      $counter = 0;

      while (($row = $reader->read()) !== false) {
        $ticket = UserTicket::model()->find('user_id = :id', array(':id' => $row['user_id']));
        if (!$ticket) $ticket = new UserTicket();
        $ticket->user_id = $row['user_id'];
        $ticket->generateToken();
        $ticket->save();

        $html = $this->renderFile(Yii::app()->getBasePath() ."/views/mail/notify_purchases.php", array('row' => $row, 'ticket' => $ticket), true);
        $title = Yii::t('app', 'В Вашем городе {n} новая закупка|В Вашем городе {n} новые закупки|В Вашем городе {n} новых закупок', $row['pc_num']);

        $mail->sendMail(Yii::app()->params['noreplymail'], Yii::app()->params['noreplyname'], $row['email'], $title, $html, true, null, null, null);
        $mail->ClearAddresses();

        $counter++;
      }

      // Logs
      if ($counter > 0) {
        $logfile = Yii::app()->getRuntimePath() .'/email_notify_purchase.log';
        if (file_exists($logfile)) $log = file_get_contents($logfile);
        else $log = "";

        $log = date("d.m.Y H:i:s") ." отправлено писем ". $counter ."\r\n". $log;
        file_put_contents($logfile, $log);
      }

      Yii::app()->mutex->unlock();
      return 0;
    }
    else return 1;
  }
}