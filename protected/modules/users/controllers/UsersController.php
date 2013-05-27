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
            array(
                'ext.RBACFilter.RBACFilter',
            ),
        );
    }

    public function actionIndex($offset = 0) {
      $c = (isset($_REQUEST['c'])) ? $_REQUEST['c'] : array();

      $criteria = new CDbCriteria();
      $criteria->limit = Yii::app()->getModule('users')->usersPerPage;
      $criteria->offset = $offset;

      if (isset($c['name']) && $c['name']) {
        $criteria->addSearchCondition('profile.firstname', $c['name'], true, 'OR');
        $criteria->addSearchCondition('profile.lastname', $c['name'], true, 'OR');
        $criteria->addSearchCondition('t.login', $c['name'], true, 'OR');
      }

      if (isset($c['city_id']) && $c['city_id']) {
        $criteria->compare('profile.city_id', $c['city_id']);
      }

      if (isset($c['role']) && $c['role']) {
        $criteria->compare('role.itemname', $c['role']);
      }

      $users = User::model()->with('role', 'profile')->findAll($criteria);
      $usersNum = User::model()->with('role', 'profile')->count($criteria);
      $roles = RbacItem::model()->findAll('type = :type', array(':type' => RbacItem::TYPE_ROLE));

      if (Yii::app()->request->isAjaxRequest) {
        if (isset($_POST['pages'])) {
          $this->pageHtml = $this->renderPartial('_userlist', array('users' => $users, 'offset' => $offset), true);
        }
        else
          $this->pageHtml =  $this->renderPartial('index',
            array(
              'users' => $users,
              'roles' => $roles,
              'c' => $c,
              'offset' => $offset,
              'offsets' => $usersNum,
            ), true);
      }
      else $this->render('index', array(
        'users' => $users,
        'roles' => $roles,
        'c' => $c,
        'offset' => $offset,
        'offsets' => $usersNum,
      ));
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