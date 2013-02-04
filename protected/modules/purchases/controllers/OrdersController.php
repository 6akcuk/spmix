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
          array(
            'ext.DevelopFilter',
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
            $stat[$order->purchase_id] = array('num' => 0, 'sum' => 0.00, 'credit' => 0.00);
            $p_ids[] = $order->purchase_id;
          }
          $orders[$order->purchase_id][] = $order;
          $stat[$order->purchase_id]['num'] += $order->amount;
          $stat[$order->purchase_id]['sum'] += floatval($order->total_price);
          $stat[$order->purchase_id]['credit'] += floatval($order->total_price - $order->payed);
        }

        $pur_criteria = new CDbCriteria();
        $pur_criteria->addInCondition('purchase_id', $p_ids);
        $_purchases = Purchase::model()->findAll($pur_criteria);
        foreach ($_purchases as $p) {
            $purchases[$p->purchase_id] = $p;
        }

        $awaitingNum = Order::model()->count('customer_id = :customer_id AND status = :status', array(':customer_id' => Yii::app()->user->getId(), ':status' => Order::STATUS_AWAITING));

        if (Yii::app()->request->isAjaxRequest) {
            $this->pageHtml = $this->renderPartial(
                'index',
                array(
                    'orders' => $orders,
                    'purchases' => $purchases,
                    'stat' => $stat,
                    'awaitingNum' => $awaitingNum,
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
        $_orders = Order::model()->with('good', 'payment')->findAll($criteria);
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
        $pur_criteria->addInCondition('purchase_id', $p_ids);
        $_purchases = Purchase::model()->findAll($pur_criteria);
        foreach ($_purchases as $p) {
          $purchases[$p->purchase_id] = $p;
        }

        $awaitingNum = Order::model()->count('customer_id = :customer_id AND status = :status', array(':customer_id' => Yii::app()->user->getId(), ':status' => Order::STATUS_AWAITING));

        if (Yii::app()->request->isAjaxRequest) {
            $this->pageHtml = $this->renderPartial(
                'awaiting',
                array(
                    'orders' => $orders,
                    'purchases' => $purchases,
                    'stat' => $stat,
                    'awaitingNum' => $awaitingNum,
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

            $orders = Order::model()->with('good', 'customer', 'payment')->findAll($criteria);

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
                    $order->payed, $order->oic,
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
                $criteria->addSearchCondition('grid.size', $c['size']);
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

            $orders = Order::model()->with('good', 'customer', 'payment')->findAll($criteria);

          $criteria->limit = 0;
          $ordersNum = Order::model()->with('good', 'customer', 'payment')->count($criteria);

            $this->wideScreen = true;
            if (Yii::app()->request->isAjaxRequest) {
              if (isset($_POST['pages'])) {
                $this->pageHtml = $this->renderPartial('_acquire', array(
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
        $order = Order::model()->with('good', 'good.sizes', 'good.colors', 'purchase', 'purchase.oic')->findByPk($order_id);

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

                    if ($order->oic) {
                      preg_match("/([0-9\.]{1,})/ui", $order->oic, $oic_price);
                      $price += floatval($oic_price[1]);
                        /*$oic = PurchaseOic::model()->findByPk($order->oic);
                        if ($oic) {
                            $price += floatval($oic->price);

                            //$order->oic = $oic->price .' - '. $oic->description;
                        }*/
                    }

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
    $order = Order::model()->with('good', 'good.sizes', 'good.colors', 'purchase', 'purchase.oic')->findByPk($id);

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

          if ($order->oic) {
            preg_match("/([0-9\.]{1,})/ui", $order->oic, $oic_price);
            $price += floatval($oic_price[1]);
            /*$oic = PurchaseOic::model()->findByPk($order->oic);
            if ($oic) {
                $price += floatval($oic->price);

                //$order->oic = $oic->price .' - '. $oic->description;
            }*/
          }

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
      elseif ($order->status != Order::STATUS_PAID) {
        throw new CHttpException(500, 'Заказ еще не оплачен');
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

      echo json_encode(array('success' => true, 'msg' => 'Все заказы были обновлены', 'status' => array_search($_POST['status'], $statuses)));
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

    public function actionCreatePayment($id) {
        $order = Order::model()->findByPk($id);

        if (Yii::app()->user->checkAccess(RBACFilter::getHierarchy() .'Own', array('order' => $order))) {
            if (isset($_POST['OrderPayment'])) {
                $pay = new OrderPayment('create');
                $pay->attributes = $_POST['OrderPayment'];
                $pay->order_id = $id;

                if ($pay->save()) {
                    $result['success'] = true;
                    $result['msg'] = Yii::t('app', 'Изменения сохранены');
                    $result['url'] = '/orders/awaiting';
                }
                else {
                    foreach ($pay->getErrors() as $attr => $error) {
                        $result[ActiveHtml::activeId($pay, $attr)] = $error;
                    }
                }

                echo json_encode($result);
                exit;
            }

            $awaitingNum = Order::model()->count('customer_id = :customer_id AND status = :status', array(':customer_id' => Yii::app()->user->getId(), ':status' => Order::STATUS_AWAITING));
            $org_details = ProfilePaydetail::model()->findAll('user_id = :user_id', array(':user_id' => $order->purchase->author_id));

            if (Yii::app()->request->isAjaxRequest) {
                $this->pageHtml = $this->renderPartial('createPayment', array(
                    'order' => $order,
                    'awaitingNum' => $awaitingNum,
                    'paydetails' => $org_details,
                ), true);
            }
            else $this->render('createPayment', array(
                'order' => $order,
                'awaitingNum' => $awaitingNum,
                'paydetails' => $org_details,
            ));
        }
        else
            throw new CHttpException(403, 'В доступе отказано');
    }

    public function actionPayments() {
        $criteria = new CDbCriteria();
        $criteria->addCondition('payer_id = :payer_id');
        $criteria->params[':payer_id'] = Yii::app()->user->getId();
        $criteria->order = 'datetime DESC';

        $payments = OrderPayment::model()->with('order', 'paydetails')->findAll($criteria);
        $awaitingNum = Order::model()->count('customer_id = :customer_id AND status = :status', array(':customer_id' => Yii::app()->user->getId(), ':status' => Order::STATUS_AWAITING));

        if (Yii::app()->request->isAjaxRequest) {
            $this->pageHtml = $this->renderPartial('payments', array(
                'payments' => $payments,
                'awaitingNum' => $awaitingNum,
            ), true);
        }
        else $this->render('payments', array(
            'payments' => $payments,
            'awaitingNum' => $awaitingNum,
        ));
    }

    public function actionPayment($payment_id) {
        /** @var $payment OrderPayment */
        $payment = OrderPayment::model()->with('order', 'order.purchase')->findByPk($payment_id);

        if (Yii::app()->user->checkAccess(RBACFilter::getHierarchy() .'Super') ||
            //Yii::app()->user->checkAccess(RBACFilter::getHierarchy() .'Own', array('order' => $payment->order)) ||
            Yii::app()->user->checkAccess(RBACFilter::getHierarchy() .'Org', array('purchase' => $payment->order->purchase))) {
            if (isset($_POST['OrderPayment'])) {
                $cache['status'] = $payment->status;
                $payment->attributes = $_POST['OrderPayment'];

                if ($cache['status'] == OrderPayment::STATUS_AWAITING &&
                    $payment->status == OrderPayment::STATUS_PERFORMED) {
                    $payment->order->status = Order::STATUS_PAID;
                    $payment->order->save(true, array('status'));
                }

                if ($payment->save()) {
                    $result['success'] = true;
                    $result['msg'] = 'Статус платежа и заказа успешно изменены';
                    $result['url'] = '/orders'. $payment->order->purchase_id;
                }
                else {
                    foreach ($payment->getErrors() as $attr => $error) {
                        $result[ActiveHtml::activeId($payment, $attr)] = $error;
                    }
                }

                echo json_encode($result);
                exit;
            }

            if (Yii::app()->request->isAjaxRequest) {
                $this->pageHtml = $this->renderPartial('payment', array('payment' => $payment), true);
            }
            else $this->render('payment', array('payment' => $payment));
        }
        else
            throw new CHttpException(403, 'В доступе отказано');
    }
}