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
        );
    }

    public function actionOrder($purchase_id, $good_id) {
        if(isset($_POST['Order']))
        {
            /** @var $good Good */
            $good = Good::model()->with(array(
              'purchase', 'sizes',
            ))->findByPk($good_id);
            $order = new Order('create');

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
                $order->customer_id = Yii::app()->user->getId();
                $order->org_tax = $good->purchase->org_tax;
                $order->price = $good->price;

                foreach ($good->sizes as $size) {
                  if ($size->size == $order->size && $size->adv_price > 0) {
                    $order->price = $size->adv_price;
                    break;
                  }
                }

                $price = $good->getEndPrice($order->price);

                $order_oic = $order->oic;
                if ($order->oic) {
                  $oic = PurchaseOic::model()->findByPk($order->oic);
                  $price += floatval($oic->price);

                  $order->oic = $oic->price .' - '. $oic->description;
                }

                $order->total_price = $price * intval($order->amount);
                $result = array();

                // сохраняем заказ в таблице Заказов
                if($order->validate() && $order->save()) {
                  $cookies = Yii::app()->getRequest()->getCookies();
                  if (!isset($cookies['purchase'. $good->purchase_id .'_oic'])) {
                    $ck = new CHttpCookie('purchase'. $good->purchase_id .'_oic', $order_oic);
                    $ck->expire = time() + 2592000;

                    $cookies->add('purchase'. $good->purchase_id .'_oic', $ck);
                  }

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
      else
        throw new CHttpException(500, 'Неверный запрос');
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
      /** @var $good Good */
      $good = Good::model()->with('image', 'sizes', 'colors', 'purchase', 'oic', 'orders', 'orders.customer', 'ordersNum')->findByPk($good_id);
      $orderc = new Order('create');

      $ranges = $good->buildRanges();
      $struct = $good->getRangeStructure();

      if (Yii::app()->request->isAjaxRequest) {
          $this->pageHtml = $this->renderPartial('show', array('good' => $good, 'orderc' => $orderc, 'struct' => $struct, 'ranges' => $ranges), true);
      }
      else $this->render('show', array('good' => $good, 'orderc' => $orderc, 'struct' => $struct, 'ranges' => $ranges));
    }

    public function actionEdit($purchase_id, $good_id) {
        $purchase = Purchase::model()->findByPk($purchase_id);

        if (Yii::app()->user->checkAccess(RBACFilter::getHierarchy() .'Super') ||
            Yii::app()->user->checkAccess(RBACFilter::getHierarchy() .'Own', array('purchase' => $purchase)))
        {
            $good = Good::model()->with('images', 'sizes', 'colors')->findByPk($good_id);

            if (isset($_POST['Good'])) {
              $prev_sizes = $good->sizes;

              $good->attributes=$_POST['Good'];

              if($good->validate() && $good->save()) {
                $prev_sizes = $good->sizes;
                $prev_colors = $good->colors;

                $sizes = explode(";", trim($_POST['sizes']));
                $colors = explode(";", trim($_POST['colors']));

                /* Редактирование размеров */
                if ($prev_sizes) {
                  foreach ($prev_sizes as $gsize) {
                    if (!$sizes) {
                      $gsize->delete();
                      continue;
                    }

                    $found = false;
                    foreach ($sizes as $idx => $size) {
                      if (preg_match("/\[([0-9\.]{1,})\]/i", $size, $price)) {
                        $price = trim($price[1]);
                        $size = trim(preg_replace("/\[[0-9\.]{1,}\]$/i", "", $size));
                      }
                      else $price = 0;

                      if ($size == $gsize->size && $price == $gsize->adv_price) {
                        $found = true;
                        array_splice($sizes, $idx, 1);
                      }
                      elseif ($size == $gsize->size && $price != $gsize->adv_price) {
                        $found = true;
                        $gsize->adv_price = $price;
                        $gsize->save(true, array('adv_price'));
                      }
                    }

                    if (!$found) $gsize->delete();
                  }
                }

                if ($sizes && sizeof($sizes)) {
                  foreach ($sizes as $size) {
                    if (preg_match("/\[([0-9\.]{1,})\]/i", $size, $price)) {
                      $price = trim($price[1]);
                      $size = trim(preg_replace("/\[[0-9\.]{1,}\]$/i", "", $size));
                    }
                    else $price = 0;

                    $gs = new GoodSize();
                    $gs->good_id = $good->good_id;
                    $gs->size = $size;
                    $gs->adv_price = $price;
                    $gs->save();
                  }
                }

                /* Редактирование цветов */
                if ($prev_colors) {
                  foreach ($prev_colors as $gcolor) {
                    if (!$colors) {
                      $gcolor->delete();
                      continue;
                    }

                    $found = false;
                    foreach ($colors as $idx => $color) {
                      if ($color == $gcolor->color) {
                        $found = true;
                        array_splice($colors, $idx, 1);
                      }
                    }

                    if (!$found) $gcolor->delete();
                  }
                }

                if ($colors && sizeof($colors)) {
                  foreach ($colors as $color) {
                    $gc = new GoodColor();
                    $gc->good_id = $good->good_id;
                    $gc->color = $color;
                    $gc->save();
                  }
                }

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

          $configs = PurchaseGoodConfig::model()->findAll('purchase_id = :id', array(':id' => $purchase->purchase_id));

          if (Yii::app()->request->isAjaxRequest) {
              $this->pageHtml = $this->renderPartial('edit', array('purchase' => $purchase, 'good' => $good, 'configs' => $configs), true);
          }
          else $this->render('edit', array('purchase' => $purchase, 'good' => $good, 'configs' => $configs));
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