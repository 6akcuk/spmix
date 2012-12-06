<?php
/**
 * Created by JetBrains PhpStorm.
 * User: denis
 * Date: 08.11.12
 * Time: 14:32
 * To change this template use File | Settings | File Templates.
 */

class RBACFilter extends CFilter {
    protected function preFilter($filterChain) {
        $ctrl = Yii::app()->controller;
        $action = $ctrl->action;
        $module = $ctrl->module;

        $access = Yii::app()->user->checkAccess($module->name .'.'. $ctrl->id .'.'. $action->id);

        if (!$access) {
            throw new CHttpException(403, 'В доступе отказано');
        }
        return $access;
    }
}