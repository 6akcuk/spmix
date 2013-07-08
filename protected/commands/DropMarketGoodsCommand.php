<?php

class DropMarketGoodsCommand extends CConsoleCommand {
  public function actionDrop() {
    if (Yii::app()->mutex->lock('drop-market-goods', 5)) {
      set_time_limit(50);
      /** @var $conn CDbConnection */
      $conn = Yii::app()->db;

      $command1 = $conn->createCommand("
        DELETE FROM `market_good_categories` WHERE good_id IN
        (
          SELECT good_id FROM `market_goods` WHERE add_date < NOW() - INTERVAL 1 MONTH
        )
      ");
      $command1->query();

      $command2 = $conn->createCommand("
        DELETE FROM `market_goods` WHERE add_date < NOW() - INTERVAL 1 MONTH");
      $command2->query();

      Yii::app()->mutex->unlock();
      return 0;
    }
    else return 1;
  }
}