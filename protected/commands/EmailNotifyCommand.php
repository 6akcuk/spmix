<?php
/**
 * Created by JetBrains PhpStorm.
 * User: denis
 * Date: 12.04.13
 * Time: 9:53
 * To change this template use File | Settings | File Templates.
 */

class EmailNotifyCommand extends CConsoleCommand {
  public function actionIm() {
    if (true) {
      /** @var $conn CDbConnection */
      $conn = Yii::app()->db;
      $command = $conn->createCommand("
SELECT o.email, op.firstname, r.owner_id, COUNT(m.message_id) AS msg_num,
  m.creation_date, m.message, m.message_id, p.photo, p.gender, COUNT(u.id) AS auth_num,
  GROUP_CONCAT(DISTINCT CONCAT_WS(';', u.id, u.login, p.firstname)) AS authors
  FROM `profile_requests` r
    INNER JOIN `dialog_messages` m ON m.message_id = r.req_link_id
    INNER JOIN `users` u ON u.id = m.author_id
    INNER JOIN `profiles` p ON p.user_id = u.id
    INNER JOIN `users` o ON o.id = r.owner_id
    INNER JOIN `profiles` op ON op.user_id = o.id
  WHERE r.req_type = ". ProfileRequest::TYPE_PM ." AND r.owner_id = 25
  GROUP BY r.owner_id");
      $reader = $command->query();

      Yii::import('application.vendors.*');
      require_once 'Mail/Mail.php';

      $mail = Mail::getInstance();
      $mail->setSender(array(Yii::app()->params['noreplymail'], Yii::app()->params['noreplyname']));
      $mail->IsMail();

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

        $mail->sendMail(Yii::app()->params['noreplymail'], Yii::app()->params['noreplyname'], '6akcuk@gmail.com', $title, $html, true, null, null, null);
        $mail->ClearAddresses();
      }

      return 0;
    }
    else return 1;
  }
}