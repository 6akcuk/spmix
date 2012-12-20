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

        $purchases = Purchase::model()->with('city', 'ordersNum', 'ordersSum', 'goodsNum')->findAll($criteria);

        if (Yii::app()->request->isAjaxRequest) {
            $this->pageHtml = $this->renderPartial('my', array('purchases' => $purchases), true);
        }
        else $this->render('my', array('purchases' => $purchases));
    }

    public function actionShow($id) {
        $purchase = Purchase::model()->with('city', 'author', 'category', 'ordersNum', 'ordersSum')->findByPk($id);
        $goods = Good::model()->with('image')->findAll('purchase_id = :purchase_id', array(':purchase_id' => $id));

        if (Yii::app()->request->isAjaxRequest) {
            $this->pageHtml = $this->renderPartial('show', array('purchase' => $purchase, 'goods' => $goods), true);
        }
        else $this->render('show', array('purchase' => $purchase, 'goods' => $goods));
    }

    public function actionCreate() {
        $model = new Purchase('create');

        if(isset($_POST['Purchase']))
        {
            $model->attributes=$_POST['Purchase'];
            $model->city_id = Yii::app()->user->model->profile->city_id;
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

        if (Yii::app()->user->checkAccess(RBACFilter::getHierarchy() .'Super') ||
            Yii::app()->user->checkAccess(RBACFilter::getHierarchy() .'Own', array('purchase' => $model)))
        {
            if(isset($_POST['Purchase']))
            {
                $history = array(
                    'stop_date' => 'Изменена дата стопа с {from} на {to}',
                    'state' => 'Изменен статус закупки с {from} на {to}',
                    'min_sum' => 'Изменена мин. сумма заказа с {from} на {to}',
                    'min_num' => 'Изменено мин. кол-во заказов с {from} на {to}',
                    'org_tax' => 'Изменен % наценки организатора с {from} на {to}',
                );
                foreach ($history as $h => $m) {
                    $cache[$h] = $model->$h;
                }

                $model->attributes=$_POST['Purchase'];
                $result = array();

                if($model->validate() && $model->save()) {
                    foreach ($history as $h => $m) {
                        if ($cache[$h] != $model->$h) {
                            $ph = new PurchaseHistory();
                            $ph->purchase_id = $id;
                            $ph->author_id = Yii::app()->user->getId();
                            $ph->msg = $m;

                            $from = $cache[$h];
                            $to = $model->$h;

                            switch ($h) {
                                case 'stop_date':
                                    $from = ActiveHtml::date($from, false, true);
                                    $to = ActiveHtml::date($to, false, true);
                                    break;
                                case 'state':
                                    $from = Yii::t('purchase', $from);
                                    $to = Yii::t('purchase', $to);
                                    break;
                                case 'min_sum':
                                    $from = ActiveHtml::price($from);
                                    $to = ActiveHtml::price($to);
                                    break;
                            }

                            $ph->params = json_encode(array('{from}' => $from, '{to}' => $to));
                            $ph->save();
                        }
                    }

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
        else
            throw new CHttpException(403, 'В доступе отказано');
    }

    public function actionDelete($id) {
        $purchase = Purchase::model()->findByPk($id);

        if (Yii::app()->user->checkAccess(RBACFilter::getHierarchy() .'Super') ||
            Yii::app()->user->checkAccess(RBACFilter::getHierarchy() .'Own', array('purchase' => $purchase)))
        {
            //TODO: Необходимо также сразу удалять товары
            $purchase->purchase_delete = new CDbExpression('NOW()');
            $purchase->save(true, array('purchase_delete'));

            if (Yii::app()->request->isAjaxRequest) {
                $this->pageHtml = $this->renderPartial('delete', array('purchase' => $purchase), true);
            }
            else $this->render('delete', array('purchase' => $purchase));
        }
        else
            throw new CHttpException(403, 'В доступе отказано');
    }

    public function actionRestore($id) {
        $purchase = Purchase::model()->resetScope()->findByPk($id);

        if (Yii::app()->user->checkAccess(RBACFilter::getHierarchy() .'Super') ||
            Yii::app()->user->checkAccess(RBACFilter::getHierarchy() .'Own', array('purchase' => $purchase)))
        {
            $purchase->purchase_delete = NULL;
            $purchase->save(true, array('purchase_delete'));

            echo json_encode(array('url' => '/purchase'. $purchase->purchase_id));
            exit;
        }
        else
            throw new CHttpException(403, 'В доступе отказано');
    }

    public function actionOic($id) {
        $purchase = Purchase::model()->with('oic')->findByPk($id);

        if (Yii::app()->user->checkAccess(RBACFilter::getHierarchy() .'Super') ||
            Yii::app()->user->checkAccess(RBACFilter::getHierarchy() .'Own', array('purchase' => $purchase)))
        {
            if (isset($_POST['PurchaseOic'])) {
                PurchaseOic::model()->deleteAll('purchase_id = :purchase_id', array(':purchase_id' => $id));
                $common_success = false;
                $have_error = false;

                foreach ($_POST['PurchaseOic']['price'] as $idx => $oiv) {
                    $oic = $_POST['PurchaseOic'];

                    $model = new PurchaseOic();
                    $model->purchase_id = $id;
                    $model->price = $oic['price'][$idx];
                    $model->description = $oic['description'][$idx];
                    if ($model->save()) {
                        $common_success = true;
                        $result = array(
                            'success' => true,
                            'url' => '/purchase'. $id .'/oic',
                            'msg' => Yii::t('app', 'Изменения сохранены'),
                        );
                    }
                    else {
                        $have_error = true;
                        foreach ($model->getErrors() as $attr => $error) {
                            $result[ActiveHtml::activeId($model, $attr)] = $error;
                        }
                    }
                }

                echo json_encode(array(
                    'success' => true,
                    'url' => '/purchase'. $id .'/oic',
                    'msg' => Yii::t('app', 'Изменения сохранены')
                ));
                exit;
            }

            if (Yii::app()->request->isAjaxRequest) {
                $this->pageHtml = $this->renderPartial('oic', array('purchase' => $purchase), true);
            }
            else $this->render('oic', array('purchase' => $purchase));
        }
        else
            throw new CHttpException(403, 'В доступе отказано');
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

    public function actionAddGood($id) {
        $purchase = Purchase::model()->findByPk($id);

        if (Yii::app()->user->checkAccess(RBACFilter::getHierarchy() .'Super') ||
            Yii::app()->user->checkAccess(RBACFilter::getHierarchy() .'Own', array('purchase' => $purchase)))
        {
            $model = new Good();
            $model->purchase_id = $id;
            $model->currency = 'RUR';

            if(isset($_POST['Good']))
            {
                $model->attributes=$_POST['Good'];
                $model->sizes = json_encode($_POST['Good']['sizes']);
                $model->colors = json_encode($_POST['Good']['colors']);
                $result = array();

                $psizes = json_decode($purchase->sizes, true);
                $sizes = $_POST['Good']['sizes'];

                $psizes = array_merge($psizes, $sizes);
                sort($psizes);

                if($model->validate() && $model->save()) {
                    $purchase->sizes = json_encode($psizes);
                    $purchase->save(true, array('sizes'));

                    $result['success'] = true;
                    $result['url'] = '/good'. $model->purchase_id .'_'. $model->good_id .'/edit';
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
                $this->pageHtml = $this->renderPartial('addgood', array('id' => $id, 'model' => $model), true);
            }
            else $this->render('addgood', array('id' => $id, 'model' => $model));
        }
        else
            throw new CHttpException(403, 'В доступе отказано');
    }
}