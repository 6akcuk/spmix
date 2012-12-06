<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Sum
 * Date: 16.10.12
 * Time: 20:41
 * To change this template use File | Settings | File Templates.
 */

class RolesController extends Controller {
    public function filters() {
        return array(
            array(
                'ext.AjaxFilter.AjaxFilter'
            ),
            /*array(
                'ext.RBACFilter.RBACFilter'
            )*/
        );
    }

    public function actionIndex($offset = 0) {
        $criteria = new CDbCriteria();
        $criteria->limit = 20;
        $criteria->offset = $offset;
        $criteria->condition = 'type = :type';
        $criteria->params = array(':type' => RbacItem::TYPE_ROLE);

        $roles = RbacItem::model()->findAll($criteria);

        if (Yii::app()->request->isAjaxRequest) {
            $this->pageHtml = $this->renderPartial('index', array('roles' => $roles), true);
        }
        else $this->render('index', array('roles' => $roles));
    }

    public function actionCreateRole() {
        $model = new RoleForm();

        // collect user input data
        if(isset($_POST['RoleForm']))
        {
            $model->attributes=$_POST['RoleForm'];
            $result = array();

            if($model->validate()) {
                /** @var $authManager IAuthManager */
                $authManager = Yii::app()->getAuthManager();
                $item = $authManager->getAuthItem($model->name);

                if (!$item) {
                    $authManager->createAuthItem($model->name, RbacItem::TYPE_ROLE, $model->description);
                    $result['success'] = true;
                    $result['message'] = 'Роль <b>'. $model->name .'</b> успешно добавлена';
                }
                else {
                    $result[ActiveHtml::activeId($model, 'name')] = 'Роль с таким именем уже существует';
                }
            }
            else {
                foreach ($model->getErrors() as $attr => $error) {
                    $result[ActiveHtml::activeId($model, $attr)] = $error;
                }
            }

            echo json_encode($result);
            exit;
        }

        if (Yii::app()->request->isAjaxRequest) {
            $this->pageHtml = $this->renderPartial('createRole', array('model' => $model), true);
        }
        else $this->render('createRole', array('model' => $model));
    }

    public function actionOperations($offset = 0) {
        $criteria = new CDbCriteria();
        $criteria->limit = 20;
        $criteria->offset = $offset;
        $criteria->condition = 'type = :type';
        $criteria->params = array(':type' => RbacItem::TYPE_OPERATION);

        $operations = RbacItem::model()->findAll($criteria);

        if (Yii::app()->request->isAjaxRequest) {
            $this->pageHtml = $this->renderPartial('operations', array('operations' => $operations), true);
        }
        else $this->render('operations', array('operations' => $operations));
    }

    public function actionCreateOperation() {
        $model = new OperationForm();

        // collect user input data
        if(isset($_POST['OperationForm']))
        {
            $model->attributes=$_POST['OperationForm'];
            $result = array();

            if($model->validate()) {
                /** @var $authManager IAuthManager */
                $authManager = Yii::app()->getAuthManager();
                $item = $authManager->getAuthItem($model->name);

                if (!$item) {
                    $authManager->createAuthItem($model->name, RbacItem::TYPE_OPERATION, $model->description);
                    $result['success'] = true;
                    $result['message'] = 'Операция <b>'. $model->name .'</b> успешно добавлена';
                }
                else {
                    $result[ActiveHtml::activeId($model, 'name')] = 'Операция с таким кодом уже существует';
                }
            }
            else {
                foreach ($model->getErrors() as $attr => $error) {
                    $result[ActiveHtml::activeId($model, $attr)] = $error;
                }
            }

            echo json_encode($result);
            exit;
        }

        if (Yii::app()->request->isAjaxRequest) {
            $this->pageHtml = $this->renderPartial('createOperation', array('model' => $model), true);
        }
        else $this->render('createOperation', array('model' => $model));
    }

    public function actionLink() {
        $operations = RbacItem::model()->findAll('type = :type', array(':type' => RbacItem::TYPE_OPERATION), array('order' => 'name'));
        $roles = RbacItem::model()->findAll('type = :type', array(':type' => RbacItem::TYPE_ROLE));

        foreach ($roles as $role) {
            $roleChilds[$role->name] = RbacItemChild::model()->findAll('parent = :parent', array(':parent' => $role->name));
        }

        if (Yii::app()->request->isAjaxRequest) {
            $this->pageHtml = $this->renderPartial('link', array(
                'operations' => $operations,
                'roles' => $roles,
                'roleChilds' => $roleChilds,
            ), true);
        }
        else $this->render('link', array(
            'operations' => $operations,
            'roles' => $roles,
            'roleChilds' => $roleChilds,
        ));
    }

    public function actionSyncRoleItems() {
        /** @var $child RbacItemChild */
        $role = $_POST['role'];
        $items = (isset($_POST['items'])) ? $_POST['items'] : array();
        $childs = array();
        $roleChilds = RbacItemChild::model()->findAll('parent = :parent', array(':parent' => $role));
        $changed = false;

        /** @var $auth IAuthManager */
        $auth = Yii::app()->getAuthManager();

        foreach ($roleChilds as $child) {
            if (!in_array($child->child, $items)) {
                $auth->removeItemChild($role, $child->child);
                $changed = true;
            }
            $childs[] = $child->child;
        }
        foreach ($items as $item) {
            if (!in_array($item, $childs) && $item) {
                $auth->addItemChild($role, $item);
                $changed = true;
            }
        }

        if ($changed) echo json_encode(array('msg' => 'Изменения сохранены'));
        else echo json_encode(array());
        exit;
    }
}