<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Sum
 * Date: 25.11.12
 * Time: 21:42
 * To change this template use File | Settings | File Templates.
 */

class PurchasesController extends Controller {
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

    public function init() {
        parent::init();

        if (isset($_GET['action']))
            $this->defaultAction = $_GET['action'];
    }



    public function actionAcquire($offset = 0) {
        if (isset($_POST['confirm'])) {
            /** @var $purchase Purchase */
            $purchase = Purchase::model()->resetScope()->with('mod_request')->findByPk($_POST['id']);

            if ($purchase) {
                if ($_POST['confirm'] == 1) {
                    $mod_request_id = $purchase->mod_request_id;

                    $purchase->mod_confirmation = 1;
                    $purchase->mod_request_id = null;
                    $purchase->save(true, array('mod_confirmation', 'mod_request_id'));

                    $purchase->mod_request->status = PurchaseModRequest::STATUS_CLOSED;
                    $purchase->mod_request->moderator_id= Yii::app()->user->getId();
                    $purchase->mod_request->save(true, array('status', 'moderator_id'));

                    $_SESSION['purchase.acquire.'. intval($_POST['id'])] = $hash = substr(md5('hh'. time() . $_POST['id']), 0, 8);

                    echo json_encode(array('html' => 'Закупка одобрена. <a onclick="return Purchase.cancelAcquire('. $_POST['id'] .', '. $mod_request_id .', \''. $hash .'\')">Отменить</a>'));
                    exit;
                }
                elseif ($_POST['confirm'] == -1) {
                    $mod_request_id = $purchase->mod_request_id;

                    $purchase->mod_request->status = PurchaseModRequest::STATUS_CLOSED;
                    $purchase->mod_request->moderator_id= Yii::app()->user->getId();
                    $purchase->mod_request->message = $_POST['message'];
                    $purchase->mod_request->save(true, array('status', 'moderator_id', 'message'));

                    $_SESSION['purchase.acquire.'. intval($_POST['id'])] = $hash = substr(md5('hh'. time() . $_POST['id']), 0, 8);

                    Yii::import('application.modules.im.models.*');
                    DialogMessage::send(array($purchase->author_id), $_POST['message'], '', array(
                        array(
                            'type' => 'purchase_edit',
                            'name' => $purchase->name,
                            'purchase_id' => $purchase->purchase_id,
                        )
                    ));

                    echo json_encode(array('html' => 'Замечание отправлено. <a onclick="return Purchase.cancelAcquire('. $_POST['id'] .', '. $mod_request_id .', \''. $hash .'\')">Отменить</a>'));
                    exit;
                }
                elseif ($_POST['confirm'] == 0 && $_SESSION['purchase.acquire.'. intval($_POST['id'])] == $_POST['hash']) {
                    unset($_SESSION['purchase.acquire.'. intval($_POST['id'])]);

                    $purchase->mod_confirmation = 0;
                    $purchase->mod_request_id = $_POST['request_id'];
                    $purchase->save(true, array('mod_confirmation', 'mod_request_id'));

                    /** @var $mod_request PurchaseModRequest */
                    $mod_request = PurchaseModRequest::model()->findByPk($_POST['request_id']);
                    $mod_request->status = PurchaseModRequest::STATUS_NEW;
                    $mod_request->message = '';
                    $mod_request->save(true, array('status', 'message'));

                    exit;
                }
            }
        }

        if (isset($_POST['offset'])) $offset = $_POST['offset'];

        $criteria = new CDbCriteria();
        $criteria->limit = Yii::app()->controller->module->purchasesPerPage;
        $criteria->offset = $offset;
        $criteria->order = 'mod_request.request_date DESC';

        $criteria->addCondition('t.purchase_delete IS NULL');
        $criteria->addCondition('t.mod_request_id IS NOT NULL AND t.mod_confirmation = 0');
        $criteria->addCondition('mod_request.status = '. PurchaseModRequest::STATUS_NEW);
        if (!Yii::app()->user->checkAccess('purchases.purchases.acquireSuper')) {
            $criteria->addCondition('t.city_id = :id');
            $criteria->params[':id'] = Yii::app()->user->model->profile->city_id;
        }

        $purchases = Purchase::model()->resetScope()->with('mod_request')->findAll($criteria);

        $criteria->limit = 0;
        $purchasesNum = Purchase::model()->resetScope()->with('mod_request')->count($criteria);

        if (Yii::app()->request->isAjaxRequest) {
            if (isset($_POST['pages'])) {
                $this->pageHtml = $this->renderPartial('_acquire', array(
                    'purchases' => $purchases,
                    'offset' => $offset,
                ), true);
            }
            else $this->pageHtml = $this->renderPartial('acquire', array(
                'purchases' => $purchases,
                'offset' => $offset,
                'offsets' => $purchasesNum,
            ), true);
        }
        else $this->render('acquire', array('purchases' => $purchases, 'offset' => $offset, 'offsets' => $purchasesNum,));
    }

    public function actionIndex($offset = 0) {
        $cookies = Yii::app()->getRequest()->getCookies();
        $c = (isset($_REQUEST['c'])) ? $_REQUEST['c'] : array();
        if (isset($_POST['offset'])) $offset = $_POST['offset'];

        $criteria = new CDbCriteria();
        $criteria->limit = Yii::app()->controller->module->purchasesPerPage;
        $criteria->offset = $offset;
        $criteria->order = 'create_date DESC';

        if ($cookies['cur_city']) {
            $criteria->addCondition('t.city_id = :city_id');
            $criteria->params[':city_id'] = $cookies['cur_city']->value;
        }
        elseif (!Yii::app()->user->getIsGuest()) {
            $criteria->addCondition('t.city_id = :city_id');
            $criteria->params[':city_id'] = Yii::app()->user->model->profile->city_id;
        }

        if (!isset($c['state'])) {
            $criteria->params[':state'] = Purchase::STATE_ORDER_COLLECTION;
            $criteria->addCondition('state = :state');
        }
        else {
            if ($c['state'] == 'Progress') $criteria->addInCondition('state', array(Purchase::STATE_STOP, Purchase::STATE_REORDER, Purchase::STATE_PAY, Purchase::STATE_CARGO_FORWARD, Purchase::STATE_DISTRIBUTION));
            else {
                $criteria->params[':state'] = $c['state'];
                $criteria->addCondition('state = :state');
            }
        }

        if (isset($c['category_id'])) {
            $criteria->params[':category_id'] = $c['category_id'];
            $criteria->addCondition('category_id = :category_id');
        }

      if (!Yii::app()->user->checkAccess('purchases.purchases.acquire'))
        $criteria->addCondition("(state IN ('Draft', 'Call Study') OR (state NOT IN ('Draft', 'Call Study') AND mod_confirmation = 1))");

        $purchases = Purchase::model()->with('city', 'author', 'ordersNum', 'ordersSum')->findAll($criteria);

        $criteria->limit = 0;
        $purchasesNum = Purchase::model()->count($criteria);

        if (Yii::app()->request->isAjaxRequest) {
            if (isset($_POST['pages'])) {
                $this->pageHtml = $this->renderPartial('_list', array(
                    'purchases' => $purchases,
                    'offset' => $offset,
                ), true);
            }
            else $this->pageHtml = $this->renderPartial('index', array(
                'purchases' => $purchases,
                'c' => $c,
                'offset' => $offset,
                'offsets' => $purchasesNum,
            ), true);
        }
        else $this->render('index', array('purchases' => $purchases, 'c' => $c, 'offset' => $offset, 'offsets' => $purchasesNum,));
    }

    public function actionMy($offset = 0) {
        $c = (isset($_REQUEST['c'])) ? $_REQUEST['c'] : array();
        if (!isset($c['limit'])) $c['limit'] = 30;

        $criteria = new CDbCriteria();
        $criteria->limit = $c['limit'];
        $criteria->offset = $offset;
        $criteria->addCondition('author_id = :author_id');
        $criteria->params[':author_id'] = Yii::app()->user->getId();
        $criteria->order = 'create_date DESC';

        if (isset($c['id'])) {
            //$criteria->params[':id'] = $c['id'];
            $criteria->addSearchCondition('t.purchase_id', $c['id']);
        }

        if (isset($c['create_date'])) {
            $criteria->params[':create_date'] = $c['create_date'];
            $next_date = new DateTime($c['create_date']);
            $next_date->add(new DateInterval("P1D"));
            $criteria->params[':next_date'] = $next_date->format('Y-m-d');
            $criteria->addCondition('create_date >= :create_date AND create_date < :next_date');
        }

        if (isset($c['name'])) {
            $criteria->addSearchCondition('t.name', $c['name']);
        }

        if (isset($c['state'])) {
            $criteria->params[':state'] = $c['state'];
            $criteria->addCondition('state = :state');
        }

        if (isset($c['completed'])) {
            $criteria->params[':completed'] = Purchase::STATE_COMPLETED;
            $criteria->addCondition('state != :completed');
        }

        if (isset($c['category_id'])) {
            $criteria->params[':category_id'] = $c['category_id'];
            $criteria->addCondition('category_id = :category_id');
        }

      if (isset($c['stop_date'])) {
        $criteria->compare('stop_date', $c['stop_date']);
      }

        $criteria->addCondition('purchase_delete IS NULL');

      $purchases = Purchase::model()->resetScope()->with('city', 'ordersNum', 'ordersSum', 'goodsNum')->findAll($criteria);
      $purchasesNum = Purchase::model()->resetScope()->count($criteria);

      $this->wideScreen = true;
      if (Yii::app()->request->isAjaxRequest) {
        if (isset($_POST['pages'])) {
          $this->pageHtml = $this->renderPartial('_listtable', array(
            'purchases' => $purchases,
            'offset' => $offset,
          ), true);
        }
        else $this->pageHtml = $this->renderPartial('my', array(
          'purchases' => $purchases,
          'c' => $c,
          'offset' => $offset,
          'offsets' => $purchasesNum,
        ), true);
      }
      else $this->render('my', array(
        'purchases' => $purchases,
        'c' => $c,
        'offset' => $offset,
        'offsets' => $purchasesNum,
      ));
    }

    public function actionShow($id, $offset = 0) {
      $purchase = Purchase::model()->with('city', 'author', 'category', 'ordersNum', 'ordersSum')->findByPk($id);
      if (isset($_POST['offset'])) $offset = $_POST['offset'];

      if (!$purchase)
          throw new CHttpException(500, 'Закупка не обнаружена');

      $criteria = new CDbCriteria();
      $criteria->limit = Yii::app()->controller->module->goodsPerPage;
      $criteria->offset = $offset;
      $criteria->addCondition('purchase_id = :purchase_id');
      $criteria->params[':purchase_id'] = $id;

      if (!in_array($purchase->state, array(Purchase::STATE_PAY, Purchase::STATE_CARGO_FORWARD, Purchase::STATE_DISTRIBUTION))) {
        $goods = Good::model()->quick()->with('image')->findAll($criteria);

        $criteria->limit = 0;
        $goodsNum = Good::model()->quick()->count($criteria);
      }
      else {
        $goods = null;
        $goodsNum = 0;
      }

      if (Yii::app()->request->isAjaxRequest) {
          if (isset($_POST['pages'])) {
              $this->pageHtml = $this->renderPartial('_goodlist', array(
                  'goods' => $goods,
                  'offset' => $offset,
              ), true);
          }
          else $this->pageHtml = $this->renderPartial('show', array('purchase' => $purchase, 'goods' => $goods, 'offset' => $offset, 'offsets' => $goodsNum), true);
      }
      else $this->render('show', array('purchase' => $purchase, 'goods' => $goods, 'offset' => $offset, 'offsets' => $goodsNum));
    }

    public function actionCreate() {
        $model = new Purchase('create');

        if(isset($_POST['Purchase']))
        {
            $model->attributes=$_POST['Purchase'];
          if (!$_POST['Purchase']['stop_date']) $model->stop_date = null;
            $model->city_id = Yii::app()->user->model->profile->city_id;
            $model->author_id = Yii::app()->user->getId();
            $result = array();

            if($model->validate() && $model->save()) {
                $result['success'] = true;
                $result['url'] = '/purchases/edit/'. $model->purchase_id;
            }
            else {
                foreach ($model->getErrors() as $attr => $error) {
                    $result[ActiveHtml::activeId($model, $attr)] = $error;
                }
            }

            echo json_encode($result);
            exit;
        }
        
        if (Yii::app()->request->isAjaxRequest) {
            $this->pageHtml = $this->renderPartial('create', array('model' => $model), true);
        }
        else $this->render('create', array('model' => $model));
    }

    public function actionUpdateState($id) {
      /** @var $model Purchase */
      $model = Purchase::model()->findByPk($id);

      if (Yii::app()->user->checkAccess(RBACFilter::getHierarchy() .'Super') ||
          Yii::app()->user->checkAccess(RBACFilter::getHierarchy() .'Own', array('purchase' => $model)))
      {
        $states = Purchase::getStateDataArray();
        $model->state = $states[$_POST['state']];

        if ($model->mod_confirmation == 0 &&
          !in_array($model->state, array(Purchase::STATE_DRAFT, Purchase::STATE_CALL_STUDY)))
          throw new CHttpException(500, 'Закупка не согласована');

        if ($model->save()) {
            $result['success'] = true;
            $result['msg'] = 'Изменения сохранены';
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

    public function actionEdit($id) {
        /** @var $model Purchase */
        $model = Purchase::model()->resetScope()->with('mod_request')->findByPk($id);

        if (Yii::app()->user->checkAccess(RBACFilter::getHierarchy() .'Super') ||
            Yii::app()->user->checkAccess(RBACFilter::getHierarchy() .'Own', array('purchase' => $model)))
        {
            $scenario = array();
            $scenario[] = 'edit';
            $scenario[] = (!Yii::app()->user->checkAccess(RBACFilter::getHierarchy() .'Super')) ? 'own' : 'super';
            if ($scenario[1] == 'own') {
                $scenario[] = ($model->mod_confirmation) ? 'confirmed' : 'notconfirmed';
            }
            else {
                $scenario[] = (Yii::app()->user->checkAccess('purchases.purchases.acquireSuper')) ? 'admin' : 'moderator';
            }

            $scenario = implode('_', $scenario);
            $model->setScenario($scenario);

            if(isset($_POST['Purchase']))
            {
                $history = array(
                    'stop_date' => 'Изменена дата стопа с {from} на {to}',
                    'state' => 'Изменен статус закупки с {from} на {to}',
                    'min_sum' => 'Изменена мин. сумма заказа с {from} на {to}',
                    'min_num' => 'Изменено мин. кол-во заказов с {from} на {to}',
                    'org_tax' => 'Изменен % наценки организатора с {from} на {to}',
                );
                foreach ($history as $h => $m) {
                    $cache[$h] = $model->$h;
                }

                $model->attributes=$_POST['Purchase'];
                if (!$_POST['Purchase']['stop_date']) $model->stop_date = null;

                $result = array();

                if($model->validate() && $model->save()) {
                    foreach ($history as $h => $m) {
                        if ($cache[$h] != $model->$h) {
                            $ph = new PurchaseHistory();
                            $ph->purchase_id = $id;
                            $ph->author_id = Yii::app()->user->getId();
                            $ph->msg = $m;

                            $from = $cache[$h];
                            $to = $model->$h;

                            switch ($h) {
                                case 'stop_date':
                                    $from = ActiveHtml::date($from, false, true);
                                    $to = ActiveHtml::date($to, false, true);
                                    break;
                                case 'state':
                                    $from = Yii::t('purchase', $from);
                                    $to = Yii::t('purchase', $to);
                                    break;
                                case 'min_sum':
                                    $from = ActiveHtml::price($from);
                                    $to = ActiveHtml::price($to);
                                    break;
                            }

                            $ph->params = json_encode(array('{from}' => $from, '{to}' => $to));
                            $ph->save();
                        }
                    }

                    // отправить на согласование
                    if ($_POST['mod_request'] == 1) {
                        if (!$model->mod_request || $model->mod_request->moderator_id > 0) {
                            $modrequest = new PurchaseModRequest();
                            $modrequest->purchase_id = $model->purchase_id;
                            if ($modrequest->save()) {
                                $model->mod_request_id = $modrequest->mod_request_id;
                                $model->save(true, array('mod_request_id'));
                            }
                            else {
                                foreach ($modrequest->getErrors() as $error) {
                                    $result[] = $error;
                                }
                                echo json_encode($result);
                                exit;
                            }
                        }
                        else {
                            echo json_encode(array('Вы уже отправили заявку'));
                            exit;
                        }
                    }

                    $result['success'] = true;
                    $result['url'] = '/purchase'. $model->purchase_id;
                }
                else {
                    foreach ($model->getErrors() as $attr => $error) {
                        $result[ActiveHtml::activeId($model, $attr)] = $error;
                    }
                }

                echo json_encode($result);
                exit;
            }

            if (Yii::app()->request->isAjaxRequest) {
                $this->pageHtml = $this->renderPartial('edit', array('model' => $model), true);
            }
            else $this->render('edit', array('model' => $model));
        }
        else
            throw new CHttpException(403, 'В доступе отказано');
    }

    public function actionDelete($id) {
        $purchase = Purchase::model()->findByPk($id);

        if (Yii::app()->user->checkAccess(RBACFilter::getHierarchy() .'Super') ||
            Yii::app()->user->checkAccess(RBACFilter::getHierarchy() .'Own', array('purchase' => $purchase)))
        {
            //TODO: Необходимо также сразу удалять товары
            $purchase->purchase_delete = new CDbExpression('NOW()');
            $purchase->save(true, array('purchase_delete'));

            if (Yii::app()->request->isAjaxRequest) {
                $this->pageHtml = $this->renderPartial('delete', array('purchase' => $purchase), true);
            }
            else $this->render('delete', array('purchase' => $purchase));
        }
        else
            throw new CHttpException(403, 'В доступе отказано');
    }

    public function actionRestore($id) {
        $purchase = Purchase::model()->resetScope()->findByPk($id);

        if (Yii::app()->user->checkAccess(RBACFilter::getHierarchy() .'Super') ||
            Yii::app()->user->checkAccess(RBACFilter::getHierarchy() .'Own', array('purchase' => $purchase)))
        {
            $purchase->purchase_delete = NULL;
            $purchase->save(true, array('purchase_delete'));

            echo json_encode(array('url' => '/purchase'. $purchase->purchase_id));
            exit;
        }
        else
            throw new CHttpException(403, 'В доступе отказано');
    }

    public function actionOic($id) {
        $purchase = Purchase::model()->with('oic')->findByPk($id);

        if (Yii::app()->user->checkAccess(RBACFilter::getHierarchy() .'Super') ||
            Yii::app()->user->checkAccess(RBACFilter::getHierarchy() .'Own', array('purchase' => $purchase)))
        {
            if (isset($_POST['PurchaseOic'])) {
                PurchaseOic::model()->deleteAll('purchase_id = :purchase_id', array(':purchase_id' => $id));
                $common_success = false;
                $have_error = false;

                foreach ($_POST['PurchaseOic']['price'] as $idx => $oiv) {
                    $oic = $_POST['PurchaseOic'];

                    $model = new PurchaseOic();
                    $model->purchase_id = $id;
                    $model->price = $oic['price'][$idx];
                    $model->description = $oic['description'][$idx];
                    if ($model->save()) {
                        $common_success = true;
                        $result = array(
                            'success' => true,
                            'url' => '/purchase'. $id .'/oic',
                            'msg' => Yii::t('app', 'Изменения сохранены'),
                        );
                    }
                    else {
                        $have_error = true;
                        foreach ($model->getErrors() as $attr => $error) {
                            $result[ActiveHtml::activeId($model, $attr)] = $error;
                        }
                    }
                }

                echo json_encode(array(
                    'success' => true,
                    'url' => '/purchase'. $id .'/oic',
                    'msg' => Yii::t('app', 'Изменения сохранены')
                ));
                exit;
            }

            if (Yii::app()->request->isAjaxRequest) {
                $this->pageHtml = $this->renderPartial('oic', array('purchase' => $purchase), true);
            }
            else $this->render('oic', array('purchase' => $purchase));
        }
        else
            throw new CHttpException(403, 'В доступе отказано');
    }

    public function actionUpdateFullstory() {
        $id = intval($_POST['id']);

        $purchase = Purchase::model()->findByPk($id);
        if (Yii::app()->user->checkAccess(RBACFilter::getHierarchy() .'Super') ||
            Yii::app()->user->checkAccess(RBACFilter::getHierarchy() .'Own', array('purchase' => $purchase)))
        {
            $ext = PurchaseExternal::model()->findByPk($id);

            if (isset($_POST['text'])) {
                if (!$ext) {
                    $ext = new PurchaseExternal();
                    $ext->purchase_id = $id;
                    $ext->fullstory = $_POST['text'];
                    $ext->save();
                }
                else {
                    $ext->fullstory = trim($_POST['text']);
                    $ext->save(true, array('fullstory'));
                }

                $arr = array('text' => nl2br($ext->fullstory), 'msg' => Yii::t('app', 'Изменения сохранены'));
            }
            else $arr = array('text' => ($ext) ? $ext->fullstory : '');

            echo json_encode($arr);
            exit;
        }
        else
            throw new CHttpException(403, 'В доступе отказано');
    }

    public function actionQuick($id) {
      $purchase = Purchase::model()->findByPk($id);
      $good = new Good('quick');
      $order = new Order('quick');

      $oic = OrderOic::model()->find('purchase_id = :pid AND customer_id = :cid', array(':pid' => $id, ':cid' => Yii::app()->user->getId()));

      if (isset($_POST['Good'])) {
        $good->attributes = $_POST['Good'];
        $good->purchase_id = $id;
        $good->currency = 'RUR';
        $good->is_quick = 1;

        if (!$oic && intval($_POST['Order']['oic']) == 0)
          throw new CHttpException(500, 'Вы не указали место выдачи товара');

        if ($good->validate(null, false)) {
          $order->attributes = $_POST['Order'];
          $order->purchase_id = $id;
          $order->customer_id = Yii::app()->user->getId();
          $order->price = $good->price;
          $order->org_tax = $good->purchase->org_tax;
          $price = floatval($good->price) * ($good->purchase->org_tax / 100 + 1);

          if (!$oic) {
            $purchase_oic = PurchaseOic::model()->findByPk($_POST['Order']['oic']);
            if (!$purchase_oic) {
              throw new CHttpException(500, 'Место выдачи не найдено в закупке');
            }
            else {
              $oic = new OrderOic();
              $oic->purchase_id = $id;
              $oic->customer_id = Yii::app()->user->getId();
              $oic->oic_name = $purchase_oic->description;
              $oic->oic_price = $purchase_oic->price;
              $oic->save();
            }
          }

          $order->total_price = $price * intval($order->amount);

          if($order->validate()) {
            $good->save();

            $order->good_id = $good->good_id;
            $order->save();

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
        else {
          foreach ($good->getErrors() as $attr => $error) {
            $result[ActiveHtml::activeId($good, $attr)] = $error;
          }
        }

        echo json_encode($result);
        exit;
      }

      if (Yii::app()->request->isAjaxRequest) {
          $this->pageHtml = $this->renderPartial('quick', array(
            'purchase' => $purchase,
            'good' => $good,
            'order' => $order,
            'oic' => $oic,
          ), true);
      }
      else $this->render('quick', array(
        'purchase' => $purchase,
        'good' => $good,
        'order' => $order,
        'oic' => $oic,
      ));
    }

    public function actionAddGood($id) {
      /** @var $purchase Purchase */
        $purchase = Purchase::model()->findByPk($id);

        if (Yii::app()->user->checkAccess(RBACFilter::getHierarchy() .'Super') ||
            Yii::app()->user->checkAccess(RBACFilter::getHierarchy() .'Own', array('purchase' => $purchase)) ||
            Yii::app()->user->checkAccess(RBACFilter::getHierarchy() .'Accepted', array('purchase' => $purchase)))
        {
            $model = new Good('create');
            $model->purchase_id = $id;
            $model->currency = 'RUR';

            if(isset($_POST['Good']))
            {
                $model->attributes=$_POST['Good'];
                $result = array();

                if($model->validate() && $model->save()) {
                  $sizes = explode(";", trim($_POST['sizes']));
                  $colors = explode(";", trim($_POST['colors']));

                  if ($sizes) {
                    foreach ($sizes as $size) {
                      if (preg_match("/\[([0-9\.]{1,})\]/i", $size, $price)) {
                        $price = $price[1];
                        $size = preg_replace("/\[[0-9\.]{1,}\]$/i", "", $size);
                      }
                      else $price = 0;

                      $gs = new GoodSize();
                      $gs->good_id = $model->good_id;
                      $gs->size = $size;
                      $gs->adv_price = $price;
                      $gs->save();
                    }
                  }

                  if ($colors) {
                    foreach ($colors as $color) {
                      $gc = new GoodColor();
                      $gc->good_id = $model->good_id;
                      $gc->color = $color;
                      $gc->save();
                    }
                  }

                  if (trim($_POST['config'])) {
                    $config = new PurchaseGoodConfig();
                    $config->purchase_id = $id;
                    $config->name = trim($_POST['config']);
                    $config->config = json_encode(array(
                      'sizes' => trim($_POST['sizes']),
                      'colors' => trim($_POST['colors']),
                      'range' => $model->range,
                    ));
                    $config->save();
                  }

                  $image = new GoodImages();
                  $image->good_id = $model->good_id;
                  $image->image = $_POST['image'];
                  $image->save();

                  $result['success'] = true;
                  $result['url'] = ($_POST['direction'] == 0) ? '/purchase'. $purchase->purchase_id : '/purchase'. $purchase->purchase_id .'/addgood';
                }
                else {
                    foreach ($model->getErrors() as $attr => $error) {
                        $result[ActiveHtml::activeId($model, $attr)] = $error;
                    }
                }

                echo json_encode($result);
                exit;
            }

          $configs = PurchaseGoodConfig::model()->findAll('purchase_id = :id', array(':id' => $purchase->purchase_id));

          if (Yii::app()->request->isAjaxRequest) {
              $this->pageHtml = $this->renderPartial('addgood', array('id' => $id, 'purchase' => $purchase, 'model' => $model, 'configs' => $configs), true);
          }
          else $this->render('addgood', array('id' => $id, 'purchase' => $purchase, 'model' => $model, 'configs' => $configs));
        }
        else
            throw new CHttpException(403, 'В доступе отказано');
    }

  public function actionSiteList($offset = 0) {
    $model = new SiteList();

    if (isset($_POST['SiteList']) || isset($_POST['id'])) {
      $result = array();

      if (isset($_POST['id']))
        $model = SiteList::model()->findByPk($_POST['id']);

      if (isset($_POST['delete'])) {
        $model->delete();
        $result['success'] = true;
        $result['msg'] = 'Сайт успешно удален';
      }
      else {
        $model->attributes = $_POST['SiteList'];
        if (Yii::app()->user->checkAccess('purchases.purchases.siteListMyCity'))
          $model->city_id = Yii::app()->user->model->profile->city_id;
        elseif (!$_POST['SiteList']['city_id'])
          $model->city_id = Yii::app()->user->model->profile->city_id;

        $model->author_id = Yii::app()->user->getId();

        if ($model->save()) {
          $result['success'] = true;
          $result['date'] = ActiveHtml::date($model->datetime);
          $result['msg'] = 'Сайт успешно добавлен';
        }
        else {
          foreach ($model->getErrors() as $attr => $error) {
            $result[ActiveHtml::activeId($model, $attr)] = $error;
          }
        }
      }

      echo json_encode($result);
      exit;
    }

    $c = (isset($_REQUEST['c'])) ? $_REQUEST['c'] : array();

    $criteria = new CDbCriteria();
    $criteria->offset = $offset;
    $criteria->limit = Yii::app()->getModule('purchases')->sitesPerPage;
    $criteria->order = 'site ASC';

    if (isset($c['site'])) {
      $criteria->addSearchCondition('site', $c['site']);
    }

    if (isset($c['city_id'])) {
      $criteria->compare('city_id', $c['city_id']);
    }

    if (Yii::app()->user->checkAccess('purchases.purchases.siteListMyCity')) {
      $criteria->addCondition('city_id = :id');
      $criteria->params[':id'] = Yii::app()->user->model->profile->city_id;
    }

    $sites = SiteList::model()->findAll($criteria);

    $criteria->limit = 0;
    $sitesNum = SiteList::model()->count($criteria);

    $this->wideScreen = true;

    if (Yii::app()->request->isAjaxRequest) {
      if (isset($_POST['pages'])) {
        $this->pageHtml = $this->renderPartial('_sitelist', array('sites' => $sites, 'offset' => $offset), true);
      }
      else $this->pageHtml = $this->renderPartial('sitelist', array('model' => $model, 'sites' => $sites, 'offset' => $offset, 'offsets' => $sitesNum, 'c' => $c), true);
    }
    else $this->render('sitelist', array('model' => $model, 'sites' => $sites, 'offset' => $offset, 'offsets' => $sitesNum, 'c' => $c));
  }
}