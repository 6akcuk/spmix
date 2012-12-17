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
        $criteria->order = 'purchase_id';

        $orders = array();
        $p_ids = array();
        $_orders = Order::model()->with('good')->findAll($criteria);
        /** @var $order Order */
        foreach ($_orders as $order) {
            if (!isset($orders[$order->purchase_id])) {
                $orders[$order->purchase_id] = array();
                $p_ids[] = $order->purchase_id;
            }
            $orders[$order->purchase_id][] = $order;
        }

        $pur_criteria = new CDbCriteria();
        $pur_criteria->addInCondition('purchase_id', $p_ids);
        $purchases = Purchase::model()->findAll($pur_criteria);

        if (Yii::app()->request->isAjaxRequest) {
            $this->pageHtml = $this->renderPartial('index', array('orders' => $orders, 'purchases' => $purchases), true);
        }
        else $this->render('index', array('orders' => $orders, 'purchases' => $purchases));
    }
}