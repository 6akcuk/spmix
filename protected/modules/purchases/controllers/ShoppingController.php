<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Sum
 * Date: 17.12.12
 * Time: 1:38
 * To change this template use File | Settings | File Templates.
 */

class ShoppingController extends Controller {
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

        if (Yii::app()->request->isAjaxRequest) {
            $this->pageHtml = $this->renderPartial('index', array('orders' => $orders, 'purchases' => $purchases, 'stat' => $stat), true);
        }
        else $this->render('index', array('orders' => $orders, 'purchases' => $purchases, 'stat' => $stat));
    }

    public function actionOrders() {
        $criteria = new CDbCriteria();
        $criteria->addCondition('author_id = :author_id');
        $criteria->params[':author_id'] = Yii::app()->user->getId();

        $purchases = Purchase::model()->with('orders', 'ordersNum', 'ordersSum')->findAll($criteria);

        if (Yii::app()->request->isAjaxRequest) {
            $this->pageHtml = $this->renderPartial('orders', array('purchases' => $purchases), true);
        }
        else $this->render('orders', array('purchases' => $purchases));
    }

    public function actionPurchaseOrders($id) {
        $purchase = Purchase::model()->findByPk($id);


    }
}