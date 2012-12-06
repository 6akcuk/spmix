<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Sum
 * Date: 23.11.12
 * Time: 22:39
 * To change this template use File | Settings | File Templates.
 */

class ProfilesController extends Controller {
    public function filters() {
        return array(
            array(
                'ext.AjaxFilter.AjaxFilter'
            ),
            array(
                'ext.RBACFilter.RBACFilter'
            ),
        );
    }

    public function actionIndex($id) {
        $userinfo = User::model()->with('profile')->findByPk($id);

        if (Yii::app()->request->isAjaxRequest) {
            $this->pageHtml = $this->renderPartial('index', array('userinfo' => $userinfo), true);
        }
        else $this->render('index', array('userinfo' => $userinfo));
    }
}