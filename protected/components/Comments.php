<?php
/**
 * Created by JetBrains PhpStorm.
 * User: denis
 * Date: 28.02.13
 * Time: 9:57
 * To change this template use File | Settings | File Templates.
 */
Yii::import('zii.widgets.CPortlet');

class Comments extends CPortlet {
  public $hoop_id;
  public $hoop_type;

  protected function renderContent() {
    $this->render('comments');
  }
}