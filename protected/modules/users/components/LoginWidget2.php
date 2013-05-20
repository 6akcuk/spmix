<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Sum
 * Date: 26.09.12
 * Time: 15:55
 * To change this template use File | Settings | File Templates.
 */

class LoginWidget2 extends CWidget {
  public function run() {
    $model = new LoginForm();

    if (isset($_SESSION['LoginForm.errors'])) {
      $model->addErrors($_SESSION['LoginForm.errors']);
      unset($_SESSION['LoginForm.errors']);
    }

    $this->render('loginwidgetform2', array('model' => $model));
  }
}