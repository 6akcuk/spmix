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
        $friends = $userinfo->profile->getFriends();
        $friendsNum = $userinfo->profile->countFriends();

        if (Yii::app()->request->isAjaxRequest) {
            $this->pageHtml = $this->renderPartial('index', array('userinfo' => $userinfo, 'friends' => $friends, 'friendsNum' => $friendsNum), true);
        }
        else $this->render('index', array('userinfo' => $userinfo, 'friends' => $friends, 'friendsNum' => $friendsNum));
    }

    public function actionEdit() {
        /** @var $userinfo User */
        $userinfo = User::model()->with('profile')->findByPk(Yii::app()->user->getId());

        if (isset($_POST['Profile'])) {
            $userinfo->profile->setScenario('edit');
            $userinfo->profile->attributes = $_POST['Profile'];

            if ($userinfo->profile->save()) {
                ProfilePaydetail::model()->deleteAll('user_id = :user_id', array(':user_id' => Yii::app()->user->getId()));

                foreach ($_POST['ProfilePaydetail']['paysystem_name'] as $idx => $oiv) {
                    $pay = $_POST['ProfilePaydetail'];

                    $model = new ProfilePaydetail();
                    $model->user_id = Yii::app()->user->getId();
                    $model->paysystem_name = $pay['paysystem_name'][$idx];
                    $model->paysystem_details = $pay['paysystem_details'][$idx];
                    $model->save();
                }

                $result['success'] = true;
            }
            else {
                foreach ($userinfo->profile->getErrors() as $attr => $error) {
                    $result[ActiveHtml::activeId($userinfo->profile, $attr)] = $error;
                }
            }

            echo json_encode($result);
            exit;
        }

        if (Yii::app()->request->isAjaxRequest) {
            $this->pageHtml = $this->renderPartial('edit', array('userinfo' => $userinfo), true);
        }
        else $this->render('edit', array('userinfo' => $userinfo));
    }
}