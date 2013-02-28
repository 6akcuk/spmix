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

    $criteria->addInCondition('t.status', array(
      Order::STATUS_ACCEPTED,
      Order::STATUS_PROCEEDING,
      Order::STATUS_RANGE_ACCEPTED,
      Order::STATUS_PAID,
    ));

    $purchases = array();
    $orders = array();
    $stat = array();
    $p_ids = array();
    $_orders = Order::model()->with('good')->findAll($criteria);
    /** @var $order Order */
    foreach ($_orders as $order) {
      if (!isset($orders[$order->purchase_id])) {
        $orders[$order->purchase_id] = array();
        $stat[$order->purchase_id] = array('num' => 0, 'sum' => 0.00, 'credit' => 0.00);
        $p_ids[] = $order->purchase_id;
      }
      $orders[$order->purchase_id][] = $order;
      $stat[$order->purchase_id]['num'] += $order->amount;
      $stat[$order->purchase_id]['sum'] += floatval($order->total_price);
      $stat[$order->purchase_id]['credit'] += floatval($order->total_price - $order->payed);
    }

    $pur_criteria = new CDbCriteria();
    $pur_criteria->addInCondition('t.purchase_id', $p_ids);
    $pur_criteria->compare('user_oic.customer_id', Yii::app()->user->getId());
    $_purchases = Purchase::model()->with('user_oic')->findAll($pur_criteria);
    foreach ($_purchases as $p) {
      $purchases[$p->purchase_id] = $p;
      $stat[$p->purchase_id]['sum'] += floatval($p->user_oic->oic_price);

      if ($p->user_oic->payed == 0)
        $stat[$p->purchase_id]['credit'] += floatval($p->user_oic->oic_price);
    }

    $awaitingNum = Order::model()->count('customer_id = :customer_id AND status = :status', array(':customer_id' => Yii::app()->user->getId(), ':status' => Order::STATUS_AWAITING));
    $deliveringNum = Order::model()->count('customer_id = :customer_id AND status = :status', array(':customer_id' => Yii::app()->user->getId(), ':status' => Order::STATUS_WAIT_FOR_DELIVER));

    if (Yii::app()->request->isAjaxRequest) {
      $this->pageHtml = $this->renderPartial(
        'index',
        array(
          'orders' => $orders,
          'purchases' => $purchases,
          'stat' => $stat,
          'awaitingNum' => $awaitingNum,
          'deliveringNum' => $deliveringNum,
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
          'deliveringNum' => $deliveringNum,
        )
      );
  }

  public function actionAwaiting() {
      $criteria = new CDbCriteria();
      $criteria->addCondition('customer_id = :customer_id');
      $criteria->addCondition('t.status = :status');
      $criteria->params[':customer_id'] = Yii::app()->user->getId();
      $criteria->params[':status'] = Order::STATUS_AWAITING;
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
          $stat[$order->purchase_id] = array('num' => 0, 'sum' => 0.00, 'credit' => 0.00);
          $p_ids[] = $order->purchase_id;
        }
        $orders[$order->purchase_id][] = $order;
        $stat[$order->purchase_id]['num'] += $order->amount;
        $stat[$order->purchase_id]['sum'] += floatval($order->total_price);
        $stat[$order->purchase_id]['credit'] += floatval($order->total_price - $order->payed);
      }

      $pur_criteria = new CDbCriteria();
      $pur_criteria->addInCondition('t.purchase_id', $p_ids);
      $pur_criteria->compare('user_oic.customer_id', Yii::app()->user->getId());
      $_purchases = Purchase::model()->with('user_oic')->findAll($pur_criteria);
      foreach ($_purchases as $p) {
        $purchases[$p->purchase_id] = $p;
        $stat[$p->purchase_id]['sum'] += floatval($p->user_oic->oic_price);

        if ($p->user_oic->payed == 0)
          $stat[$p->purchase_id]['credit'] += floatval($p->user_oic->oic_price);
      }

    $awaitingNum = Order::model()->count('customer_id = :customer_id AND status = :status', array(':customer_id' => Yii::app()->user->getId(), ':status' => Order::STATUS_AWAITING));
    $deliveringNum = Order::model()->count('customer_id = :customer_id AND status = :status', array(':customer_id' => Yii::app()->user->getId(), ':status' => Order::STATUS_WAIT_FOR_DELIVER));

    if (Yii::app()->request->isAjaxRequest) {
      $this->pageHtml = $this->renderPartial(
        'awaiting',
        array(
          'orders' => $orders,
          'purchases' => $purchases,
          'stat' => $stat,
          'awaitingNum' => $awaitingNum,
          'deliveringNum' => $deliveringNum,
        ),
        true);
    }
    else
      $this->render(
        'awaiting',
        array(
          'orders' => $orders,
          'purchases' => $purchases,
          'stat' => $stat,
          'awaitingNum' => $awaitingNum,
          'deliveringNum' => $deliveringNum,
        )
      );
  }

  public function actionDelivering() {
    $criteria = new CDbCriteria();
    $criteria->addCondition('customer_id = :customer_id');
    $criteria->addCondition('t.status = :status');
    $criteria->params[':customer_id'] = Yii::app()->user->getId();
    $criteria->params[':status'] = Order::STATUS_WAIT_FOR_DELIVER;
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
        $stat[$order->purchase_id] = array('num' => 0, 'sum' => 0.00, 'credit' => 0.00);
        $p_ids[] = $order->purchase_id;
      }
      $orders[$order->purchase_id][] = $order;
      $stat[$order->purchase_id]['num'] += $order->amount;
      $stat[$order->purchase_id]['sum'] += floatval($order->total_price);
      $stat[$order->purchase_id]['credit'] += floatval($order->total_price - $order->payed);
    }

    $pur_criteria = new CDbCriteria();
    $pur_criteria->addInCondition('t.purchase_id', $p_ids);
    $pur_criteria->compare('user_oic.customer_id', Yii::app()->user->getId());
    $_purchases = Purchase::model()->with('user_oic')->findAll($pur_criteria);
    foreach ($_purchases as $p) {
      $purchases[$p->purchase_id] = $p;
      $stat[$p->purchase_id]['sum'] += floatval($p->user_oic->oic_price);

      if ($p->user_oic->payed == 0)
        $stat[$p->purchase_id]['credit'] += floatval($p->user_oic->oic_price);
    }

    $awaitingNum = Order::model()->count('customer_id = :customer_id AND status = :status', array(':customer_id' => Yii::app()->user->getId(), ':status' => Order::STATUS_AWAITING));
    $deliveringNum = Order::model()->count('customer_id = :customer_id AND status = :status', array(':customer_id' => Yii::app()->user->getId(), ':status' => Order::STATUS_WAIT_FOR_DELIVER));

    if (Yii::app()->request->isAjaxRequest) {
      $this->pageHtml = $this->renderPartial(
        'index',
        array(
          'orders' => $orders,
          'purchases' => $purchases,
          'stat' => $stat,
          'awaitingNum' => $awaitingNum,
          'deliveringNum' => $deliveringNum,
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
          'deliveringNum' => $deliveringNum,
        )
      );
  }

  public function actionDelivered($offset = 0) {
    $criteria = new CDbCriteria();
    $criteria->addCondition('customer_id = :customer_id');
    $criteria->addCondition('t.status = :status');
    $criteria->params[':customer_id'] = Yii::app()->user->getId();
    $criteria->params[':status'] = Order::STATUS_DELIVERED;
    $criteria->order = 't.creation_date DESC';

    $criteria->limit = Yii::app()->getModule('purchases')->ordersPerPage;
    $criteria->offset = $offset;

    $purchases = array();
    $orders = array();
    $stat = array();
    $p_ids = array();
    $orders = Order::model()->with('good')->findAll($criteria);

    $criteria->limit = 0;
    $ordersNum = Order::model()->with('good')->count($criteria);

    $awaitingNum = Order::model()->count('customer_id = :customer_id AND status = :status', array(':customer_id' => Yii::app()->user->getId(), ':status' => Order::STATUS_AWAITING));
    $deliveringNum = Order::model()->count('customer_id = :customer_id AND status = :status', array(':customer_id' => Yii::app()->user->getId(), ':status' => Order::STATUS_WAIT_FOR_DELIVER));

    if (Yii::app()->request->isAjaxRequest) {
      if (isset($_POST['pages'])) {
        $this->pageHtml = $this->renderPartial(
          '_delivered',
          array(
            'orders' => $orders,
            'offset' => $offset,
          ),
          true);
      }
      else $this->pageHtml = $this->renderPartial(
        'delivered',
        array(
          'orders' => $orders,
          'awaitingNum' => $awaitingNum,
          'deliveringNum' => $deliveringNum,
          'offset' => $offset,
          'offsets' => $ordersNum,
        ),
        true);
    }
    else
      $this->render(
        'delivered',
        array(
          'orders' => $orders,
          'awaitingNum' => $awaitingNum,
          'deliveringNum' => $deliveringNum,
          'offset' => $offset,
          'offsets' => $ordersNum,
        )
      );
  }

    public function actionPurchase2Excel($purchase_id) {
        /** @var $purchase Purchase */
        $purchase = Purchase::model()->findByPk($purchase_id);

        if (Yii::app()->user->checkAccess(RBACFilter::getHierarchy() .'Super') ||
            Yii::app()->user->checkAccess(RBACFilter::getHierarchy() .'Own', array('purchase' => $purchase))) {

            $criteria = new CDbCriteria();
            $criteria->addCondition('t.purchase_id = :purchase_id');
            $criteria->params[':purchase_id'] = $purchase_id;

            $criteria->order = 'creation_date DESC';

            $orders = Order::model()->with('good', 'customer', array('oic' => array(
              'on' => 'oic.customer_id = t.customer_id AND oic.purchase_id = '. $purchase_id,
            )
            ))->findAll($criteria);

            header('Content-Disposition: attachment; filename="zakazi.xls"');
            header("Content-Type: application/vnd.ms-excel");
            header ("HTTP/1.1 200 OK");

            $excel = new Excel();
            $excel->line(array(
                'ID', 'Товар', 'Артикул', 'Размер', 'Цвет', 'Время заказа', 'Анонимно', 'Заказал',
                'Имя', 'Фамилия', 'Город', 'Цена', 'Орг.сбор', 'Цена + орг.сбор', 'Кол-во', 'Цена (итог)',
                'Цена + орг.сбор (итог)', 'Оплачено', 'Место выдачи', 'Комментарии организатора', 'Комментарии для организатора',
                'Телефон', 'Ряд', 'Ряд', 'Ссылка', 'Тел.из профиля', 'Статус заказа'
            ));

            /** @var $order Order */
            foreach ($orders as $order) {
                $excel->line(array(
                    $order->order_id, $order->good->name, $order->good->artikul, $order->size,
                    $order->color, ActiveHtml::date($order->creation_date, true, true),
                    (($order->anonymous) ? 'Да' : 'Нет'), $order->customer->login,
                    $order->customer->profile->firstname, $order->customer->profile->lastname,
                    $order->customer->profile->city->name, $order->good->price, $purchase->org_tax,
                    $purchase->getPriceWithTax($order->good->price), $order->amount, ($order->good->price * $order->amount),
                    $purchase->getPriceWithTax($order->good->price * $order->amount),
                    $order->payed, $order->oic->oic_name .' '. $order->oic->oic_price,
                    $order->org_comment, $order->client_comment, $order->customer->profile->phone, 0, '',
                    $order->good->url, $order->customer->profile->phone, Yii::t('purchase', $order->status)
                ));
            }

            echo $excel->close();
            exit;
        }
        else
            throw new CHttpException(403, 'В доступе отказано');
    }

    public function actionPurchase($purchase_id, $offset = 0) {
      $purchase = Purchase::model()->findByPk($purchase_id);

      if (Yii::app()->user->checkAccess(RBACFilter::getHierarchy() .'Super') ||
        Yii::app()->user->checkAccess(RBACFilter::getHierarchy() .'Own', array('purchase' => $purchase))) {
        $c = (isset($_REQUEST['c'])) ? $_REQUEST['c'] : array();
        if (!isset($c['limit'])) $c['limit'] = 30;

        $criteria = new CDbCriteria();
        $criteria->limit = $c['limit'];
        $criteria->offset = $offset;
        $criteria->addCondition('t.purchase_id = :purchase_id');
        $criteria->params[':purchase_id'] = $purchase_id;
        $criteria->order = 'creation_date DESC';

        if (isset($c['id'])) {
            $criteria->addSearchCondition('t.order_id', $c['id']);
        }

        if (isset($c['creation_date'])) {
            $criteria->params[':create_date'] = $c['creation_date'];
            $next_date = new DateTime($c['creation_date']);
            $next_date->add(new DateInterval("P1D"));
            $criteria->params[':next_date'] = $next_date->format('Y-m-d');
            $criteria->addCondition('creation_date >= :create_date AND creation_date < :next_date');
        }

        if (isset($c['good'])) {
            $criteria->addSearchCondition('good.name', $c['good']);
        }
        if (isset($c['artikul'])) {
            $criteria->addSearchCondition('good.artikul', $c['artikul']);
        }
        if (isset($c['size'])) {
            $criteria->addSearchCondition('t.size', $c['size']);
        }
        if (isset($c['color'])) {
            $criteria->addSearchCondition('t.color', $c['color']);
        }
        if (isset($c['name'])) {
            $criteria->addSearchCondition('profile.lastname', $c['name']);
            $criteria->addSearchCondition('profile.firstname', $c['name'], true, 'OR');
        }
        if (isset($c['city_id'])) {
            $criteria->params[':city_id'] = $c['city_id'];
            $criteria->addCondition('profile.city_id = :city_id');
        }
        if (isset($c['status'])) {
            $criteria->params[':status'] = $c['status'];
            $criteria->addCondition('t.status = :status');
        }

        $orders = Order::model()->with('good', 'customer')->findAll($criteria);

      $criteria->limit = 0;
      $ordersNum = Order::model()->with('good', 'customer')->count($criteria);

        $this->wideScreen = true;
        if (Yii::app()->request->isAjaxRequest) {
          if (isset($_POST['pages'])) {
            $this->pageHtml = $this->renderPartial('_order', array(
              'orders' => $orders,
              'c' => $c,
              'offset' => $offset,
            ), true);
          }
          else $this->pageHtml = $this->renderPartial(
                'orders',
                array(
                    'purchase' => $purchase,
                    'orders' => $orders,
                    'c' => $c,
                  'offset' => $offset,
                  'offsets' => $ordersNum,
                ),
                true);
        }
        else
          $this->render(
            'orders',
            array(
              'purchase' => $purchase,
              'orders' => $orders,
              'c' => $c,
              'offset' => $offset,
              'offsets' => $ordersNum,
            )
          );
      }
      else
          throw new CHttpException(403, 'В доступе отказано');
    }

    public function actionShow($order_id) {
      /** @var $order Order */
        $order = Order::model()->with('good', 'good.sizes', 'good.colors', 'purchase', 'oic')->findByPk($order_id);

        if (Yii::app()->user->checkAccess(RBACFilter::getHierarchy() .'Super') ||
            Yii::app()->user->checkAccess(RBACFilter::getHierarchy() .'Own', array('order' => $order)) ||
            Yii::app()->user->checkAccess(RBACFilter::getHierarchy() .'Org', array('purchase' => $order->purchase))) {

            if (isset($_POST['Order'])) {
                if (Yii::app()->user->checkAccess(RBACFilter::getHierarchy() .'Super') ||
                    Yii::app()->user->checkAccess(RBACFilter::getHierarchy() .'Org', array('purchase' => $order->purchase)) ||
                    $order->canEdit()
                    )
                {
                    $order->setScenario('edit_own');

                    if (Yii::app()->user->checkAccess(RBACFilter::getHierarchy() .'Super') ||
                        Yii::app()->user->checkAccess(RBACFilter::getHierarchy() .'Org', array('purchase' => $order->purchase))) {

                    }
                    else {
                        unset($_POST['Order']['status']);
                        unset($_POST['Order']['org_comment']);
                    }

                  $cache = array();
                    $history = array(
                      'size' => 'Изменен размер с {from} на {to}',
                      'color' => 'Изменен цвет с {from} на {to}',
                      'price' => 'Изменена цена с {from} на {to}',
                      'org_tax' => 'Изменен орг. сбор с {from} на {to}',
                      'amount' => 'Изменено количество товара с {from} на {to}',
                      'total_price' => 'Изменена итог. цена с {from} на {to}',
                      'anonymous' => 'Изменен статус анонимности с {from} на {to}',
                      'status' => 'Изменен статус заказа с {from} на {to}',
                    );
                    foreach ($history as $h => $m) {
                        $cache[$h] = $order->$h;
                    }
                    $order->attributes = $_POST['Order'];
                    $price = $order->good->getEndCustomPrice($order->org_tax, $order->price);

                    $order->total_price = $price * intval($order->amount);
                    $order->status = Order::STATUS_PROCEEDING;

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
                            case 'price':
                            case 'total_price':
                              $from = ActiveHtml::price($from);
                              $to = ActiveHtml::price($to);
                              break;
                            case 'anonymous':
                              $from = ($from == 0) ? 'Не установлен' : 'Установлен';
                              $to = ($to == 0) ? 'Не установлен' : 'Установлен';
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

  public function actionShowForOrg($id) {
    /** @var $order Order */
    $order = Order::model()->with('good', 'good.sizes', 'good.colors', 'purchase')->findByPk($id);

    if (Yii::app()->user->checkAccess(RBACFilter::getHierarchy() .'Super') ||
      Yii::app()->user->checkAccess(RBACFilter::getHierarchy() .'Own', array('purchase' => $order->purchase))) {

      if (isset($_POST['Order'])) {
        if (Yii::app()->user->checkAccess(RBACFilter::getHierarchy() .'Super') ||
          Yii::app()->user->checkAccess(RBACFilter::getHierarchy() .'Own', array('purchase' => $order->purchase)) ||
          $order->canEdit()
        )
        {
          $order->setScenario('edit_org');

          $cache = array();
          $history = array(
            'size' => 'Изменен размер с {from} на {to}',
            'color' => 'Изменен цвет с {from} на {to}',
            'price' => 'Изменена цена с {from} на {to}',
            'org_tax' => 'Изменен орг. сбор с {from} на {to}',
            'amount' => 'Изменено количество товара с {from} на {to}',
            'total_price' => 'Изменена итог. цена с {from} на {to}',
            'anonymous' => 'Изменен статус анонимности с {from} на {to}',
            'status' => 'Изменен статус заказа с {from} на {to}',
          );
          foreach ($history as $h => $m) {
            $cache[$h] = $order->$h;
          }
          $order->attributes = $_POST['Order'];
          $price = $order->good->getEndCustomPrice($order->org_tax, $order->price);

          $order->total_price = $price * intval($order->amount);

          if ($order->save()) {
            foreach ($history as $h => $m) {
              if ($cache[$h] != $order->$h) {
                $ph = new OrderHistory();
                $ph->order_id = $id;
                $ph->author_id = Yii::app()->user->getId();
                $ph->msg = $m;

                $from = $cache[$h];
                $to = $order->$h;

                switch ($h) {
                  case 'status':
                    $from = Yii::t('purchase', $from);
                    $to = Yii::t('purchase', $to);
                    break;
                  case 'price':
                  case 'total_price':
                    $from = ActiveHtml::price($from);
                    $to = ActiveHtml::price($to);
                    break;
                  case 'anonymous':
                    $from = ($from == 0) ? 'Не установлен' : 'Установлен';
                    $to = ($to == 0) ? 'Не установлен' : 'Установлен';
                    break;
                }

                $ph->params = json_encode(array('{from}' => $from, '{to}' => $to));
                $ph->save();
              }
            }

            $result['msg'] = 'Изменения сохранены';
            $result['success'] = true;
            $result['status'] = Yii::t('purchase', $order->status);
            $result['total_price'] = ActiveHtml::price($order->total_price);
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
        $this->pageHtml = $this->renderPartial((isset($_POST['box_request'])) ? 'show_org_box' : 'show_org', array('order' => $order), true);
        if (isset($_POST['box_request'])) $this->boxWidth = 660;
      }
      else $this->render('show_org', array('order' => $order));
    }
    else
      throw new CHttpException(403, 'В доступе отказано');
  }

  public function actionDeleteOrder() {
    /** @var $order Order */
    $order = Order::model()->with('purchase')->findByPk($_POST['id']);

    if (Yii::app()->user->checkAccess(RBACFilter::getHierarchy() .'Super') ||
      Yii::app()->user->checkAccess(RBACFilter::getHierarchy() .'Own', array('order' => $order)) ||
      Yii::app()->user->checkAccess(RBACFilter::getHierarchy() .'Org', array('purchase' => $order->purchase))) {

      if (!$order) {
        throw new CHttpException(500, 'Заказ не найден');
      }
      else {
        if (!$order->canDelete())
          throw new CHttpException(500, 'Удалить заказ невозможно');

        $order->delete();
        OrderHistory::model()->deleteAll('order_id = :id', array(':id' => $_POST['id']));

        echo json_encode(array('success' => true, 'msg' => 'Заказ успешно удален'));
        exit;
      }
    }
    else
      throw new CHttpException(403, 'В доступе отказано');
  }

  public function actionMarkOrderAsDelivered() {
    /** @var $order Order */
    $order = Order::model()->with('purchase')->findByPk($_POST['id']);

    if (Yii::app()->user->checkAccess(RBACFilter::getHierarchy() .'Super') ||
      Yii::app()->user->checkAccess(RBACFilter::getHierarchy() .'Own', array('order' => $order))) {

      if (!$order) {
        throw new CHttpException(500, 'Заказ не найден');
      }
      elseif ($order->status != Order::STATUS_WAIT_FOR_DELIVER) {
        throw new CHttpException(500, 'Заказ еще не в очереди выдачи');
      }
      else {
        $order->status = Order::STATUS_DELIVERED;
        $order->save(true, array('status'));

        echo json_encode(array('success' => true, 'msg' => 'Заказ успешно отмечен как полученный'));
        exit;
      }
    }
    else
      throw new CHttpException(403, 'В доступе отказано');
  }

  public function actionMassChangeStatus($id) {
    $purchase = Purchase::model()->findByPk($id);

    if (Yii::app()->user->checkAccess(RBACFilter::getHierarchy() .'Super') ||
      Yii::app()->user->checkAccess(RBACFilter::getHierarchy() .'Org', array('purchase' => $purchase))) {

      if (!is_array($_POST['ids']) || sizeof($_POST['ids']) == 0)
        throw new CHttpException(500, 'Не выбраны заказы для обновления статуса');

      if (!$_POST['status'] || !in_array($_POST['status'], Order::getStatusDataArray()))
        throw new CHttpException(500, 'Передано неверное значения для статуса заказа');

      $criteria = new CDbCriteria();
      $criteria->addInCondition('order_id', $_POST['ids']);

      $orders = Order::model()->findAll($criteria);
      foreach ($orders as $order) {
        $oh = new OrderHistory();
        $oh->author_id = Yii::app()->user->getId();
        $oh->order_id = $order->order_id;
        $oh->msg = 'Статус заказа изменен с {from} на {to}';
        $oh->params = json_encode(array('{from}' => Yii::t('purchase', $order->status), '{to}' => Yii::t('purchase', $_POST['status'])));
        $oh->save();
      }

      Order::model()->updateAll(array(
        'status' => $_POST['status'],
        'org_comment' => $_POST['org_comment'],
      ), $criteria);

      $statuses = Order::getStatusDataArray();

      echo json_encode(array('success' => true, 'msg' => 'Статусы заказов изменены', 'status' => array_search($_POST['status'], $statuses)));
      exit;
    }
    else
      throw new CHttpException(403, 'В доступе отказано');
  }

  public function actionMassSendMessage($id) {
    $purchase = Purchase::model()->findByPk($id);

    if (Yii::app()->user->checkAccess(RBACFilter::getHierarchy() .'Super') ||
      Yii::app()->user->checkAccess(RBACFilter::getHierarchy() .'Org', array('purchase' => $purchase))) {

      if (!is_array($_POST['ids']) || sizeof($_POST['ids']) == 0)
        throw new CHttpException(500, 'Не выбраны заказы');

      if (!$_POST['message'])
        throw new CHttpException(500, 'Введите сообщение для отправки');

      $criteria = new CDbCriteria();
      $criteria->addInCondition('order_id', $_POST['ids']);

      $cache = array();
      $msg_counter = 0;
      $orders = Order::model()->findAll($criteria);
      /** @var $order Order */
      foreach ($orders as $order) {
        if (!isset($cache[$order->customer_id])) {
          Yii::import('application.modules.im.models.*');
          $result = DialogMessage::send(array($order->customer_id), $_POST['message']);
          if ($result['success']) $msg_counter++;
          $cache[$order->customer_id] = true;
        }
      }

      echo json_encode(array('success' => true, 'msg' => 'Успешно отправлено сообщений: '. $msg_counter .' из '. sizeof($cache)));
      exit;
    }
    else
      throw new CHttpException(403, 'В доступе отказано');
  }

  public function actionGetPayDetails() {
    $criteria = new CDbCriteria();
    $criteria->compare('customer_id', Yii::app()->user->getId());
    $criteria->addInCondition('order_id', $_POST['ids']);
    $criteria->order = 't.order_id';

    $orders = Order::model()->with('purchase', 'purchase.author', 'purchase.author.profile')->findAll($criteria);

    if (!$orders)
      throw new CHttpException(500, 'Заказы не найдены');

    $grouped_orders = array();

    /** @var $order Order */
    foreach ($orders as $order) {
      if (!isset($grouped_orders[$order->purchase->author_id]))
        $grouped_orders[$order->purchase->author_id] = array('author' => $order->purchase->author, 'items' => array());
      $grouped_orders[$order->purchase->author_id]['items'][] = $order;
    }

    $result = array('success' => true);
    $result['html'] = $this->renderPartial('getpaydetails', array('orders' => $grouped_orders), true);

    echo json_encode($result);
    exit;
  }

  public function actionCreatePayment() {
    $criteria = new CDbCriteria();
    $criteria->compare('customer_id', Yii::app()->user->getId());
    $criteria->addInCondition('order_id', $_POST['ids']);
    $criteria->order = 't.order_id';

    $orders = Order::model()->with('purchase', 'purchase.author', 'purchase.author.profile')->findAll($criteria);

    if (!$orders)
      throw new CHttpException(500, 'Заказы не найдены');

    $grouped_orders = array();
    /** @var $order Order */
    foreach ($orders as $order) {
      if (!isset($grouped_orders[$order->purchase->author_id]))
        $grouped_orders[$order->purchase->author_id] = array('author' => $order->purchase->author, 'items' => array());
      $grouped_orders[$order->purchase->author_id]['items'][] = $order;
    }

    if (sizeof($grouped_orders) > 1)
      throw new CHttpException(500, 'Вы выбрали заказы от разных организаторов');

    $oic = OrderOic::model()->find('purchase_id = :pid AND customer_id = :cid', array(
      ':pid' => $orders[0]->purchase->purchase_id,
      ':cid' => Yii::app()->user->getId(),
    ));

    if (!isset($_POST['save'])) {
      echo json_encode(array(
        'html' => $this->renderPartial('createPayment', array('orders' => $orders, 'oic' => $oic), true),
      ));
      exit;
    }

    $sum = 0.00;
    foreach ($orders as $order) {
      $sum += $order->total_price - $order->payed;
    }

    if ($oic->oic_price > 0 && $oic->payed == 0) {
      $sum += $oic->oic_price;
    }

    $payment = new OrderPayment('create');
    $payment->payer_id = Yii::app()->user->getId();
    $payment->sum = $sum;
    $payment->description = $_POST['comment'];
    if (!$payment->save())
      throw new CHttpException(500, 'Ошибка при создании нового платежа');

    foreach ($orders as $order) {
      $link = new OrderPaymentLink();
      $link->payment_id = $payment->payment_id;
      $link->order_id = $order->order_id;
      $link->save();
    }

    echo json_encode(array('success' => true, 'msg' => 'Информация о платеже успешно сохранена'));
    exit;
  }

    public function actionPayments($offset = 0) {
      $criteria = new CDbCriteria();
      $criteria->compare('payer_id', Yii::app()->user->getId());
      $criteria->order = 'datetime DESC';
      $criteria->offset = $offset;
      $criteria->limit = Yii::app()->getModule('purchases')->paymentsPerPage;

      $payments = OrderPayment::model()->with('orders')->findAll($criteria);

      $criteria->limit = 0;
      $paymentsNum = OrderPayment::model()->count($criteria);

      $awaitingNum = Order::model()->count('customer_id = :customer_id AND status = :status', array(':customer_id' => Yii::app()->user->getId(), ':status' => Order::STATUS_AWAITING));
      $deliveringNum = Order::model()->count('customer_id = :customer_id AND status = :status', array(':customer_id' => Yii::app()->user->getId(), ':status' => Order::STATUS_WAIT_FOR_DELIVER));

      if (Yii::app()->request->isAjaxRequest) {
        $this->pageHtml = $this->renderPartial('payments', array(
          'payments' => $payments,
          'offsets' => $paymentsNum,
          'awaitingNum' => $awaitingNum,
          'deliveringNum' => $deliveringNum,
          'offset' => $offset,
        ), true);
      }
      else $this->render('payments', array(
        'payments' => $payments,
        'offsets' => $paymentsNum,
        'awaitingNum' => $awaitingNum,
        'deliveringNum' => $deliveringNum,
        'offset' => $offset,
      ));
    }

  public function actionOrgPayments($offset = 0) {
    $c = (isset($_REQUEST['c'])) ? $_REQUEST['c'] : array();

    $criteria = new CDbCriteria();
    //$criteria->compare('orders.order.purchase.author_id', Yii::app()->user->getId());
    //$criteria->order = 'payment.datetime DESC';
    $criteria->offset = $offset;
    $criteria->limit = Yii::app()->getModule('purchases')->paymentsPerPage;

//    $purchases = Purchase::model()->with('orders', 'orders.payment')->findAll($criteria);
    /*$payments = OrderPayment::model()->with('orders', 'orders.order', array(
      'orders.order.purchase' => array(
        'on' => 'purchase.purchase_id = order.purchase_id AND purchase.author_id = '. Yii::app()->user->getId(),
      )
    ))->findAll($criteria);*/
    $payments = OrderPayment::getAllPaymentsForOrg(Yii::app()->user->getId(), $offset, $c);
    $paymentsNum = OrderPayment::countAllPaymentsForOrg(Yii::app()->user->getId());

    $this->wideScreen = true;
    if (Yii::app()->request->isAjaxRequest) {
      if (isset($_POST['pages'])) {
        $this->pageHtml = $this->renderPartial('_orgpayments', array(
          'payments' => $payments,
          'offset' => $offset,
        ), true);
      }
      else $this->pageHtml = $this->renderPartial('orgpayments', array(
        'payments' => $payments,
        'offsets' => $paymentsNum,
        'offset' => $offset,
        'c' => $c,
      ), true);
    }
    else $this->render('orgpayments', array(
      'payments' => $payments,
      'offsets' => $paymentsNum,
      'offset' => $offset,
      'c' => $c,
    ));
  }

  public function actionOrgPayment($id) {
    /** @var $payment OrderPayment */
    $payment = OrderPayment::model()->with('payer', 'orders', 'orders.order', 'orders.order.good', 'orders.order.purchase')->findByPk($id);
    if (!$payment)
      throw new CHttpException(500, 'Платеж не найден');

    $purchase = $payment->orders[0]->order->purchase;

    $oic = OrderOic::model()->find('purchase_id = :pid AND customer_id = :cid', array(
      ':pid' => $purchase->purchase_id,
      ':cid' => $payment->payer_id,
    ));

    if (Yii::app()->user->checkAccess(RBACFilter::getHierarchy() .'Super') ||
        Yii::app()->user->checkAccess(RBACFilter::getHierarchy() .'Org', array('purchase' => $purchase))) {

      if (isset($_POST['paydirection'])) {
        if ($_POST['paydirection'] == 1) {
          if (isset($_POST['oic_payed']) && $_POST['oic_payed'] == 1) {
            $oic->payed = 1;
            $oic->save(true, array('payed'));
          }

          /** @var $order Order */
          foreach ($payment->orders as $_order) {
            $order = $_order->order;

            if (isset($_POST['Payed'][$order->order_id])) {
              $order->payed = $_POST['Payed'][$order->order_id];
            }
            if (isset($_POST['Status'][$order->order_id])) {
              $order->status = $_POST['Status'][$order->order_id];
            }

            $order->save(true, array('payed', 'status'));
          }

          $payment->status = OrderPayment::STATUS_PERFORMED;
          $payment->save(true, array('status'));

          echo json_encode(array('success' => true, 'msg' => 'Платеж отмечен как принятый'));
        }
        elseif ($_POST['paydirection'] == 0) {
          $payment->status = OrderPayment::STATUS_REFUSED;
          $payment->save(true, array('status'));

          echo json_encode(array('success' => true, 'msg' => 'Платеж отмечен как непринятый'));
        }
        else throw new CHttpException(403, 'Данные формы неверны');
        exit;
      }

      if (Yii::app()->request->isAjaxRequest) {
        $this->pageHtml = $this->renderPartial((isset($_POST['box_request'])) ? 'orgpayment_box' : 'orgpayment', array(
          'payment' => $payment,
          'oic' => $oic,
        ), true);
        $this->boxWidth = 660;
      }
      else $this->render('orgpayment', array(
        'payment' => $payment,
        'oic' => $oic,
      ));
    }
    else
      throw new CHttpException(403, 'В доступе отказано');
  }
}