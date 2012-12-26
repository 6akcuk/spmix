<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Sum
 * Date: 04.10.12
 * Time: 19:08
 * To change this template use File | Settings | File Templates.
 */

class UsersController extends Controller {
    public function filters() {
        return array(
            array(
                'ext.AjaxFilter.AjaxFilter'
            ),
            /*array(
                'ext.RBACFilter.RBACFilter',
            ),*/
        );
    }

    public function actionIndex($offset = 0) {
        $criteria = new CDbCriteria();
        $criteria->limit = 100;
        $criteria->offset = $offset;

        if (isset($_POST['q']) && !empty($_POST['q'])) {
            $criteria->compare('name', $_POST['q'], true);
        }

        $users = User::model()->findAll($criteria);
        $roles = RbacItem::model()->findAll('type = :type', array(':type' => RbacItem::TYPE_ROLE));

        if (Yii::app()->request->isAjaxRequest) {
            $this->pageHtml =  $this->renderPartial('index', array('users' => $users, 'roles' => $roles), true);
        }
        else $this->render('index', array('users' => $users, 'roles' => $roles));
    }

    public function actionAssignRole() {
        $user_id = intval($_POST['user_id']);
        $role = $_POST['role'];

        /** @var $authMgr IAuthManager */
        $authMgr = Yii::app()->getAuthManager();
        $user = User::model()->findByPk($user_id);

        $authMgr->revoke($user->role->itemname, $user_id);
        $authMgr->assign($role, $user_id);

        echo json_encode(array('msg' => 'Изменения сохранены'));
        exit;
    }
}