<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Sum
 * Date: 07.01.13
 * Time: 11:26
 * To change this template use File | Settings | File Templates.
 */

class DevelopFilter extends CFilter {
    protected function preFilter($filterChain) {
        if (Yii::app()->user->getId() != 1) {
            throw new CHttpException(403, 'В разработке');
            return false;
        }
        return true;
    }
}