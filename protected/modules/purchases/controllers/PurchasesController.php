<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Sum
 * Date: 25.11.12
 * Time: 21:42
 * To change this template use File | Settings | File Templates.
 */

class PurchasesController extends Controller {
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

    public function actionIndex($c = array()) {
        $criteria = new CDbCriteria();
        $criteria->limit = 20;
        $criteria->offset = (isset($c['offset'])) ? intval($c['offset']) : 0;
        $criteria->addCondition('t.city_id = :city_id');
        $criteria->params[':city_id'] = Yii::app()->user->model->profile->city_id;

        if (!isset($c['state'])) {
            $criteria->params[':state'] = Purchase::STATE_ORDER_COLLECTION;
            $criteria->addCondition('state = :state');
        }
        else {
            if ($c['state'] == 'Progress') $criteria->addInCondition('state', array(Purchase::STATE_STOP, Purchase::STATE_REORDER, Purchase::STATE_PAY, Purchase::STATE_CARGO_FORWARD, Purchase::STATE_DISTRIBUTION));
            else {
                $criteria->params[':state'] = $c['state'];
                $criteria->addCondition('state = :state');
            }
        }

        if (isset($c['category_id'])) {
            $criteria->params[':category_id'] = $c['category_id'];
            $criteria->addCondition('category_id = :category_id');
        }

        $purchases = Purchase::model()->with('city', 'author')->findAll($criteria);

        if (Yii::app()->request->isAjaxRequest) {
            $this->pageHtml = $this->renderPartial('index', array('purchases' => $purchases), true);
        }
        else $this->render('index', array('purchases' => $purchases));
    }

    public function actionMy() {
        $criteria = new CDbCriteria();
        $criteria->limit = 20;
        $criteria->offset = (isset($c['offset'])) ? intval($c['offset']) : 0;
        $criteria->addCondition('author_id = :author_id');
        $criteria->params[':author_id'] = Yii::app()->user->getId();

        if (isset($c['state'])) {
            $criteria->params[':state'] = $c['state'];
            $criteria->addCondition('state = :state');
        }

        if (isset($c['category_id'])) {
            $criteria->params[':category_id'] = $c['category_id'];
            $criteria->addCondition('category_id = :category_id');
        }

        $purchases = Purchase::model()->with('city')->findAll($criteria);

        if (Yii::app()->request->isAjaxRequest) {
            $this->pageHtml = $this->renderPartial('my', array('purchases' => $purchases), true);
        }
        else $this->render('my', array('purchases' => $purchases));
    }

    public function actionShow($id) {
        $purchase = Purchase::model()->with('city', 'author', 'category')->findByPk($id);

        if (Yii::app()->request->isAjaxRequest) {
            $this->pageHtml = $this->renderPartial('show', array('purchase' => $purchase), true);
        }
        else $this->render('show', array('purchase' => $purchase));
    }

    public function actionCreate() {
        $model = new Purchase('create');

        if(isset($_POST['Purchase']))
        {
            $model->attributes=$_POST['Purchase'];
            $model->author_id = Yii::app()->user->getId();
            $result = array();

            if($model->validate() && $model->save()) {
                $result['success'] = true;
                $result['url'] = '/purchases/edit/'. $model->purchase_id;
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
            $this->pageHtml = $this->renderPartial('create', array('model' => $model), true);
        }
        else $this->render('create', array('model' => $model));
    }

    public function actionEdit($id) {
        $model = Purchase::model()->findByPk($id);
        $model->setScenario('edit');

        if(isset($_POST['Purchase']))
        {
            $model->attributes=$_POST['Purchase'];
            $result = array();

            if($model->validate() && $model->save()) {
                $result['success'] = true;
                $result['url'] = '/purchase'. $model->purchase_id;
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
            $this->pageHtml = $this->renderPartial('edit', array('model' => $model), true);
        }
        else $this->render('edit', array('model' => $model));
    }

    public function actionUpdateFullstory() {
        $id = intval($_POST['id']);

        $purchase = Purchase::model()->findByPk($id);
        if (Yii::app()->user->checkAccess(RBACFilter::getHierarchy() .'Super') ||
            Yii::app()->user->checkAccess(RBACFilter::getHierarchy() .'Own', array('purchase' => $purchase)))
        {
            $ext = PurchaseExternal::model()->findByPk($id);

            if (isset($_POST['text'])) {
                if (!$ext) {
                    $ext = new PurchaseExternal();
                    $ext->purchase_id = $id;
                    $ext->fullstory = $_POST['text'];
                    $ext->save();
                }
                else {
                    $ext->fullstory = trim($_POST['text']);
                    $ext->save(true, array('fullstory'));
                }

                $arr = array('text' => nl2br($ext->fullstory), 'msg' => Yii::t('app', 'Изменения сохранены'));
            }
            else $arr = array('text' => ($ext) ? $ext->fullstory : '');

            echo json_encode($arr);
            exit;
        }
        else
            throw new CHttpException(403, 'В доступе отказано');
    }
}