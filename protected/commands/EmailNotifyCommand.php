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
    if (Yii::app()->mutex->lock('email-notify-im', 300)) {
      $timestamp = file_get_contents(Yii::app()->getRuntimePath() . '/email_notify_im.time');
      if (!$timestamp) $timestamp = time();



      Yii::app()->mutex->unlock();
    }
    else return 1;
  }
}