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
    if (Yii::app()->mutex->lock('email-notify-im', 600)) {
      /** @var $conn CDbConnection */
      $conn = Yii::app()->db;
      $command = $conn->createCommand("
SELECT o.email, r.owner_id, COUNT(m.message_id) AS msg_num, m.message, p.photo, COUNT(u.id) AS auth_num,
  GROUP_CONCAT(DISTINCT CONCAT_WS(' ', u.id, u.login, p.firstname, p.lastname)) AS authors
  FROM `profile_requests` r
    INNER JOIN `dialog_messages` m ON m.message_id = r.req_link_id
    INNER JOIN `users` u ON u.id = m.author_id
    INNER JOIN `profiles` p ON p.user_id = u.id
    INNER JOIN `users` o ON o.id = r.owner_id
  WHERE r.req_type = ". ProfileRequest::TYPE_PM ."
  GROUP BY r.owner_id");
      $reader = $command->query();

      while (($row = $reader->read()) !== false) {

      }

      Yii::app()->mutex->unlock();
    }
    else return 1;
  }
}