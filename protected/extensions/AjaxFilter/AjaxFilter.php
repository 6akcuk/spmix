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
            $return = array(
              'html' => Yii::app()->controller->pageHtml,
              'title' => Yii::app()->controller->pageTitle,
              'static' => Yii::app()->getClientScript()->renderAjax(),
              'counters' => Yii::app()->controller->pageCounters,
              'ad_blocks' => Yii::app()->controller->adBlocks,
            );

            if (Yii::app()->controller->wideScreen)
                $return['widescreen'] = true;

            if (Yii::app()->user->getIsGuest())
                $return['guest'] = true;

          if (Yii::app()->controller->boxWidth > 0)
            $return['boxWidth'] = Yii::app()->controller->boxWidth;

            echo json_encode($return);
            Yii::app()->end();
        }
    }
}