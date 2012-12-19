<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Sum
 * Date: 17.12.12
 * Time: 1:38
 * To change this template use File | Settings | File Templates.
 */

class OrdersController extends Controller {
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

    public function actionIndex() {
        $criteria = new CDbCriteria();
        $criteria->addCondition('customer_id = :customer_id');
        $criteria->params[':customer_id'] = Yii::app()->user->getId();
        $criteria->order = 't.purchase_id';

        $purchases = array();
        $orders = array();
        $stat = array();
        $p_ids = array();
        $_orders = Order::model()->with('good')->findAll($criteria);
        /** @var $order Order */
        foreach ($_orders as $order) {
            if (!isset($orders[$order->purchase_id])) {
                $orders[$order->purchase_id] = array();
                $stat[$order->purchase_id] = array('num' => 0, 'sum' => 0.00);
                $p_ids[] = $order->purchase_id;
            }
            $orders[$order->purchase_id][] = $order;
            $stat[$order->purchase_id]['num'] += $order->amount;
            $stat[$order->purchase_id]['sum'] += floatval($order->total_price);
        }

        $pur_criteria = new CDbCriteria();
        $pur_criteria->addInCondition('purchase_id', $p_ids);
        $_purchases = Purchase::model()->findAll($pur_criteria);
        foreach ($_purchases as $p) {
            $purchases[$p->purchase_id] = $p;
        }

        $awaitingNum = Order::model()->count('customer_id = :customer_id AND status = :status', array(':customer_id' => Yii::app()->user->getId(), ':status' => Order::STATUS_AWAITING));

        if (Yii::app()->request->isAjaxRequest) {
            $this->pageHtml = $this->renderPartial(
                'index',
                array(
                    'orders' => $orders,
                    'purchases' => $purchases,
                    'stat' => $stat,
                    'awaitingNum' => $awaitingNum,
                ),
                true);
        }
        else
            $this->render(
                'index',
                array(
                    'orders' => $orders,
                    'purchases' => $purchases,
                    'stat' => $stat,
                    'awaitingNum' => $awaitingNum,
                )
            );
    }

    public function actionAwaiting() {
        $criteria = new CDbCriteria();
        $criteria->addCondition('customer_id = :customer_id');
        $criteria->addCondition('status = :status');
        $criteria->params[':customer_id'] = Yii::app()->user->getId();
        $criteria->params[':status'] = Order::STATUS_AWAITING;
        $criteria->order = 't.purchase_id';

        $orders = Order::model()->with('good')->findAll($criteria);
        $awaitingNum = Order::model()->count('customer_id = :customer_id AND status = :status', array(':customer_id' => Yii::app()->user->getId(), ':status' => Order::STATUS_AWAITING));

        if (Yii::app()->request->isAjaxRequest) {
            $this->pageHtml = $this->renderPartial(
                'awaiting',
                array(
                    'orders' => $orders,
                    'awaitingNum' => $awaitingNum,
                ),
                true);
        }
        else
            $this->render(
                'awaiting',
                array(
                    'orders' => $orders,
                    'awaitingNum' => $awaitingNum,
                )
            );
    }

    public function actionShow($order_id) {
        $order = Order::model()->with('good', 'purchase')->findByPk($order_id);

        if (Yii::app()->user->checkAccess(RBACFilter::getHierarchy() .'Super') ||
            Yii::app()->user->checkAccess(RBACFilter::getHierarchy() .'Own', array('order' => $order)) ||
            Yii::app()->user->checkAccess(RBACFilter::getHierarchy() .'Org', array('purchase' => $order->purchase))) {

            if (isset($_POST['Order'])) {
                if (Yii::app()->user->checkAccess(RBACFilter::getHierarchy() .'Super') ||
                    Yii::app()->user->checkAccess(RBACFilter::getHierarchy() .'Org', array('purchase' => $order->purchase)) ||
                    in_array($order->purchase->state, array(Purchase::STATE_DRAFT, Purchase::STATE_CALL_STUDY)) ||
                    (
                        $order->purchase->state == Purchase::STATE_ORDER_COLLECTION &&
                            in_array($order->status, array(Order::STATUS_PROCEEDING, Order::STATUS_REFUSED, Order::STATUS_ACCEPTED))
                    ) ||
                    (
                        $order->purchase->state == Purchase::STATE_REORDER &&
                            in_array($order->status, array(Order::STATUS_PROCEEDING, Order::STATUS_REFUSED))
                    )
                    )
                {
                    $order->setScenario('edit');

                    if (Yii::app()->user->checkAccess(RBACFilter::getHierarchy() .'Super') ||
                        Yii::app()->user->checkAccess(RBACFilter::getHierarchy() .'Org', array('purchase' => $order->purchase))) {

                    }
                    else {
                        unset($_POST['Order']['status']);
                        unset($_POST['Order']['org_comment']);
                    }

                    $history = array(
                        'size' => 'Изменен размер с {from} на {to}',
                        'color' => 'Изменен цвет с {from} на {to}',
                        'amount' => 'Изменено количество товара с {from} на {to}',
                        'total_price' => 'Изменена итог. цена с {from} на {to}',
                        'status' => 'Изменен статус заказа с {from} на {to}',
                    );
                    foreach ($history as $h => $m) {
                        $cache[$h] = $order->$h;
                    }
                    $order->attributes = $_POST['Order'];
                    $price = floatval($order->good->price) * ($order->good->purchase->org_tax / 100 + 1);

                    if ($order->oic) {
                        $oic = PurchaseOic::model()->findByPk($order->oic);
                        $price += floatval($oic->price);

                        $order->oic = $oic->price .' - '. $oic->description;
                    }

                    $order->total_price = $price * intval($order->amount);

                    if ($order->save()) {
                        foreach ($history as $h => $m) {
                            if ($cache[$h] != $order->$h) {
                                $ph = new OrderHistory();
                                $ph->order_id = $order_id;
                                $ph->author_id = Yii::app()->user->getId();
                                $ph->msg = $m;

                                $from = $cache[$h];
                                $to = $order->$h;

                                switch ($h) {
                                    case 'status':
                                        $from = Yii::t('purchase', $from);
                                        $to = Yii::t('purchase', $to);
                                        break;
                                    case 'total_price':
                                        $from = ActiveHtml::price($from);
                                        $to = ActiveHtml::price($to);
                                        break;
                                }

                                $ph->params = json_encode(array('{from}' => $from, '{to}' => $to));
                                $ph->save();
                            }
                        }

                        $result['msg'] = 'Изменения сохранены';
                        $result['success'] = true;
                    }
                    else {
                        foreach ($order->getErrors() as $attr => $error) {
                            $result[ActiveHtml::activeId($order, $attr)] = $error;
                        }
                    }
                }
                else {
                    $result[''] = 'Сохранение изменений отклонено';
                }

                echo json_encode($result);
                exit;
            }

            if (Yii::app()->request->isAjaxRequest) {
                $this->pageHtml = $this->renderPartial('show', array('order' => $order), true);
            }
            else $this->render('show', array('order' => $order));
        }
        else
            throw new CHttpException(403, 'В доступе отказано');
    }

    public function actionCreatePayment($id) {
        $order = Order::model()->findByPk($id);

        if (Yii::app()->user->checkAccess(RBACFilter::getHierarchy() .'Own', array('order' => $order))) {

        }
        else
            throw new CHttpException(403, 'В доступе отказано');
    }
}