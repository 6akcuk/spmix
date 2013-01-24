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
        //if(isset($_POST['Order']))
        //{
            /** @var $good Good */
            $good = Good::model()->with(array(
              'purchase',
              /*'ranges' => array(
                'joinType' => 'LEFT JOIN',
                'condition' => 'ranges.filled = 0',
              ),
              'ranges.cols' => array(
                'joinType' => 'LEFT JOIN',
              ),*/
            ))->findByPk($good_id);
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
                /*$order->attributes = $_POST['Order'];
                $order->purchase_id = $purchase_id;
                $order->good_id = $good_id;
                $order->customer_id = Yii::app()->user->getId();
                $order->price = $good->price;*/
                $price = $good->getEndPrice();

                if ($order->oic) {
                    $oic = PurchaseOic::model()->findByPk($order->oic);
                    $price += floatval($oic->price);

                    $order->oic = $oic->price .' - '. $oic->description;
                }

                $order->total_price = $price * intval($order->amount);
                $result = array();

                // сохраняем заказ в таблице Заказов
                //if($order->validate() && $order->save()) {
                  // если товар имеет ряды, необходимо встать в один из рядов, либо создать новый
                  // если имеются незаполненные ряды
                  if ($good->ranges) {
                    // если ряды имеют одну строку
                    if (!stristr($good->range, '[rows]')) {
                      preg_match("'\\[cols\\](.*?)\\[\/cols\\]'si", $good->range, $cols_string);
                      preg_match_all("'\\[col\\](.*?)\\[\/col\\]'si", $cols_string[1], $cols_arr);


                    }
                  }

              $cols = array();
              preg_match("'\\[cols\\](.*?)\\[\/cols\\]'si", $good->range, $cols_string);
              preg_match_all("'\\[col\\](.*?)\\[\/col\\]'si", $cols_string[1], $cols_arr);

              $ranges = array_fill(0, sizeof($good->ranges), array_fill(0, sizeof($cols_arr[1]), null));
              $range_idx = 0;

              /** @var $range GoodRange */
              foreach ($good->ranges as $idx => $range) {
                $range_id[$range->range_id] = $idx;
              }

              /** @var $range_col RangeCol */
              foreach ($good->ranges->range_cols as $range_col) {
                foreach ($cols_arr[1] as $col_idx => $col_data) {
                  preg_match("'\\[size\\](.*?)\\[\/size\\]'si", $col_data, $size);
                  preg_match("'\\[color\\](.*?)\\[\/color\\]'si", $col_data, $color);

                  $helper = array();
                  if (isset($size[1])) $helper['size'] = $size[1];
                  if (isset($color[1])) $helper['color'] = $color[1];

                  $cols[$col_idx] = $helper;
                  $range_idx = $range_id[$range_col->range_id];

                  if ($ranges[$range_idx][$col_idx] === null && $size[1] == $range_col->size && $color[1] == $range_col->color) {
                    $ranges[$range_idx][$col_idx] = $range_col;
                  }
                }
              }

              $added = false;

              foreach ($ranges as $range_idx => $range_cols) {
                foreach ($range_cols as $col_idx => $range_col) {
                  if ($order->size == $cols[$col_idx]['size'] && $order->color == $cols[$col_idx]['color'] && $range_col == null) {


                    $ranges[$range_idx][$col_idx] = array();
                  }
                }
              }

              var_dump($cols);
              exit;


              $result['success'] = true;
                  $result['msg'] = Yii::t('purchase', 'Заказ добавлен в список покупок');
                  $result['url'] = '/orders';
                //}
                //else {
                //    foreach ($order->getErrors() as $attr => $error) {
                //        $result[ActiveHtml::activeId($order, $attr)] = $error;
                //    }
                //}
            }
            else $result[''] = '';

            echo json_encode($result);
            exit;
        //}
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
        $good = Good::model()->with('image', 'sizes', 'colors', 'purchase', 'oic', 'orders', 'orders.customer', 'ordersNum')->findByPk($good_id);
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