<?php
/**
 * Created by JetBrains PhpStorm.
 * User: denis
 * Date: 26.12.12
 * Time: 16:28
 * To change this template use File | Settings | File Templates.
 */
Yii::import('zii.widgets.CPortlet');

class NewPurchases extends CPortlet {
    protected function renderContent() {
        $cookies = Yii::app()->getRequest()->getCookies();

        $criteria = new CDbCriteria();
        $criteria->limit = 4;
        $criteria->order = 'create_date DESC';

        if ($cookies['cur_city']) {
            $criteria->params[':city_id'] = $cookies['cur_city']->value;
            $criteria->addCondition('city_id = :city_id');
        }

        Yii::import('application.modules.purchases.models.*');
        $purchases = Purchase::model()->with('author', 'city', 'external')->findAll($criteria);
        $this->render('newpurchases', array('purchases' => $purchases));
    }
}