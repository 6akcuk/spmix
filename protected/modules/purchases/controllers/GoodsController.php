<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Sum
 * Date: 10.12.12
 * Time: 1:45
 * To change this template use File | Settings | File Templates.
 */

class GoodsController extends Controller {
    public function filters() {
        return array(
            array(
                'ext.AjaxFilter.AjaxFilter'
            ),
            array(
                'ext.RBACFilter.RBACFilter'
            ),
            array(
                'ext.DevelopFilter'
            ),
        );
    }

    public function actionOrder($purchase_id, $good_id) {
        if(isset($_POST['Order']))
        {
            /** @var $good Good */
            $good = Good::model()->with('purchase', 'grid')->findByPk($good_id);

            $order = new Order(($good->is_range) ? 'create_range' : 'create');

            if (
                in_array(
                    $good->purchase->state,
                    array(
                        Purchase::STATE_CALL_STUDY,
                        Purchase::STATE_ORDER_COLLECTION,
                        Purchase::STATE_REORDER
                    )
                )
            )
            {
                $order->attributes = $_POST['Order'];
                $order->purchase_id = $purchase_id;
                $order->good_id = $good_id;
                $order->color = ($order->grid_id) ? $_POST['color'][$order->grid_id] : '';
                $order->customer_id = Yii::app()->user->getId();
                $order->price = $good->price;
                $price = floatval($good->price) * ($good->purchase->org_tax / 100 + 1);

                if ($order->oic) {
                    $oic = PurchaseOic::model()->findByPk($order->oic);
                    $price += floatval($oic->price);

                    $order->oic = $oic->price .' - '. $oic->description;
                }

                $order->total_price = $price * intval($order->amount);
                $result = array();

                if($order->validate() && $order->save()) {
                    $result['success'] = true;
                    $result['msg'] = Yii::t('purchase', 'Заказ добавлен в список покупок');
                    $result['url'] = '/orders';
                }
                else {
                    foreach ($order->getErrors() as $attr => $error) {
                        $result[ActiveHtml::activeId($order, $attr)] = $error;
                    }
                }
            }
            else $result[''] = '';

            echo json_encode($result);
            exit;
        }
    }

    public function actionPurchase($purchase_id, $offset = 0) {
        $purchase = Purchase::model()->findByPk($purchase_id);

        if (Yii::app()->user->checkAccess(RBACFilter::getHierarchy() .'Super') ||
            Yii::app()->user->checkAccess(RBACFilter::getHierarchy() .'Own', array('purchase' => $purchase)))
        {
            $c = (isset($_REQUEST['c'])) ? $_REQUEST['c'] : array();
            if (!isset($c['limit'])) $c['limit'] = 30;

            $criteria = new CDbCriteria();
            $criteria->limit = $c['limit'];
            $criteria->offset = $offset;
            $criteria->addCondition('t.purchase_id = :purchase_id');
            $criteria->params[':purchase_id'] = $purchase_id;

            if (isset($c['id'])) {
                $criteria->addSearchCondition('t.good_id', $c['id']);
            }
            if (isset($c['artikul'])) {
                $criteria->addSearchCondition('t.artikul', $c['artikul']);
            }
            if (isset($c['name'])) {
                $criteria->addSearchCondition('t.name', $c['name']);
            }
            if (isset($c['price'])) {
                $criteria->addSearchCondition('t.price', $c['price']);
            }

            $goods = Good::model()->findAll($criteria);

            $this->wideScreen = true;
            if (Yii::app()->request->isAjaxRequest) {
                $this->pageHtml = $this->renderPartial(
                    'purchase',
                    array(
                        'purchase' => $purchase,
                        'goods' => $goods,
                        'c' => $c,
                    ),
                    true);
            }
            else
                $this->render(
                    'purchase',
                    array(
                        'purchase' => $purchase,
                        'goods' => $goods,
                        'c' => $c,
                    )
                );
        }
        else
            throw new CHttpException(403, 'В доступе отказано');
    }

    public function actionShow($purchase_id, $good_id) {
        $good = Good::model()->with('image', 'grid', 'purchase', 'oic', 'orders', 'orders.customer', 'ordersNum')->findByPk($good_id);
        $orderc = new Order('create');

        if (Yii::app()->request->isAjaxRequest) {
            $this->pageHtml = $this->renderPartial('show', array('good' => $good, 'orderc' => $orderc), true);
        }
        else $this->render('show', array('good' => $good, 'orderc' => $orderc));
    }

    public function actionEdit($purchase_id, $good_id) {
        $purchase = Purchase::model()->findByPk($purchase_id);

        if (Yii::app()->user->checkAccess(RBACFilter::getHierarchy() .'Super') ||
            Yii::app()->user->checkAccess(RBACFilter::getHierarchy() .'Own', array('purchase' => $purchase)))
        {
            $good = Good::model()->with('grid', 'images')->findByPk($good_id);

            if (isset($_POST['Good'])) {
                $good->attributes=$_POST['Good'];

                if (isset($_POST['grid'])) {
                    foreach ($_POST['grid'] as $idx => $id) {
                        if (!isset($_POST['size'][$idx])) {
                            GoodGrid::model()->deleteByPk($id);
                            continue;
                        }

                        $size = $_POST['size'][$idx];
                        $colors = $_POST['color'][$idx];

                        $grid = GoodGrid::model()->findByPk($id);
                        $grid->colors = json_encode($colors);
                        $grid->allowed = $_POST['allowed'][$idx];
                        $grid->save();

                        unset($_POST['size'][$idx]);
                        unset($_POST['color'][$idx]);
                        unset($_POST['allowed'][$idx]);
                    }
                }
                // были добавлены новые размеры
                if (sizeof($_POST['size'])) {
                    foreach ($_POST['size'] as $idx => $size) {
                        $grid = GoodGrid::model()->find('good_id = :good_id AND size = :size', array(':good_id' => $good_id, ':size' => $size));
                        if (!$grid) {
                            $grid = new GoodGrid('create');
                            $grid->purchase_id = $purchase_id;
                            $grid->good_id = $good_id;
                            $grid->size = $size;
                            $grid->allowed = $_POST['allowed'][$idx];
                            $grid->colors = json_encode($_POST['color'][$idx]);
                            $grid->save();
                        }
                    }
                }

                if($good->validate() && $good->save()) {
                    $result['success'] = true;
                    $result['url'] = '/good'. $purchase_id .'_'. $good_id .'/edit';
                }
                else {
                    foreach ($good->getErrors() as $attr => $error) {
                        $result[ActiveHtml::activeId($good, $attr)] = $error;
                    }
                }

                echo json_encode($result);
                exit;
            }

            if (Yii::app()->request->isAjaxRequest) {
                $this->pageHtml = $this->renderPartial('edit', array('purchase' => $purchase, 'good' => $good), true);
            }
            else $this->render('edit', array('purchase' => $purchase, 'good' => $good));
        }
        else
            throw new CHttpException(403, 'В доступе отказано');
    }

    public function actionDelete($purchase_id, $good_id) {
        $purchase = Purchase::model()->findByPk($purchase_id);

        if (Yii::app()->user->checkAccess(RBACFilter::getHierarchy() .'Super') ||
            Yii::app()->user->checkAccess(RBACFilter::getHierarchy() .'Own', array('purchase' => $purchase)))
        {
            $good = Good::model()->findByPk($good_id);

            $good->good_delete = new CDbExpression('NOW()');
            $good->save(true, array('good_delete'));

            echo json_encode(array('html' => 'Товар удален. <a onclick="Purchase.restoregood('. $purchase_id .', '. $good_id .')">Восстановить</a>'));
            exit;
        }
        else
            throw new CHttpException(403, 'В доступе отказано');
    }

    public function actionRestore($purchase_id, $good_id) {
        $purchase = Purchase::model()->findByPk($purchase_id);

        if (Yii::app()->user->checkAccess(RBACFilter::getHierarchy() .'Super') ||
            Yii::app()->user->checkAccess(RBACFilter::getHierarchy() .'Own', array('purchase' => $purchase)))
        {
            $good = Good::model()->resetScope()->findByPk($good_id);

            $good->good_delete = NULL;
            if ($good->save(true, array('good_delete')))
                echo json_encode(array('success' => true));
            else
                echo json_encode(array('success' => false, 'html' => ''));
            exit;
        }
        else
            throw new CHttpException(403, 'В доступе отказано');
    }

    public function actionAddImage() {
        $purchase = Purchase::model()->findByPk($_POST['purchase_id']);
        if (Yii::app()->user->checkAccess(RBACFilter::getHierarchy() .'Super') ||
            Yii::app()->user->checkAccess(RBACFilter::getHierarchy() .'Own', array('purchase' => $purchase)))
        {
            $model = new GoodImages();
            $model->good_id = $_POST['good_id'];
            $model->image = $_POST['image'];

            if($model->validate() && $model->save()) {
                $result['success'] = true;
                $result['id'] = $model->image_id;
            }
            else {
                foreach ($model->getErrors() as $attr => $error) {
                    $result[ActiveHtml::activeId($model, $attr)] = $error;
                }
            }

            echo json_encode($result);
            exit;
        }
        else
            throw new CHttpException(403, 'В доступе отказано');
    }

    public function actionRemoveImage() {
        $purchase = Purchase::model()->findByPk($_POST['purchase_id']);
        if (Yii::app()->user->checkAccess(RBACFilter::getHierarchy() .'Super') ||
            Yii::app()->user->checkAccess(RBACFilter::getHierarchy() .'Own', array('purchase' => $purchase)))
        {
            $model = GoodImages::model()->findByPk($_POST['image_id']);
            $model->delete();

            echo json_encode(array());
            exit;
        }
        else
            throw new CHttpException(403, 'В доступе отказано');
    }

    public function actionGetImages() {
        $images = GoodImages::model()->findAll('good_id = :good_id', array(':good_id' => $_POST['good_id']));
        $array = array('items' => array());

        /** @var GoodImages $image */
        foreach ($images as $image) {
            $array['items'][] = json_decode($image->image);
        }

        $array['count'] = sizeof($array['items']);

        echo json_encode($array);
        exit;
    }
}