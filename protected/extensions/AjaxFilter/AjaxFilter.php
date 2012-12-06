<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Sum
 * Date: 05.10.12
 * Time: 15:53
 * To change this template use File | Settings | File Templates.
 */

class AjaxFilter extends CFilter {
    protected function postFilter($filterChain) {
        if (Yii::app()->request->isAjaxRequest) {
            echo json_encode(
                array(
                    'html' => Yii::app()->controller->pageHtml,
                    'title' => Yii::app()->controller->pageTitle,
                    'static' => Yii::app()->getClientScript()->renderAjax(),
                )
            );

            Yii::app()->end();
        }
    }
}