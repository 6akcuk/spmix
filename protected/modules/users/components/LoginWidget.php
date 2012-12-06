<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Sum
 * Date: 26.09.12
 * Time: 15:55
 * To change this template use File | Settings | File Templates.
 */

class LoginWidget extends CWidget {
    public function run() {
        $model = new LoginForm();
        $this->render('loginwidgetform', array('model' => $model));
    }
}