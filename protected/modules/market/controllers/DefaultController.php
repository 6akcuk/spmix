<?php

class DefaultController extends Controller
{
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
      )
    );
  }

  public function init() {
    parent::init();

    if (isset($_GET['act']))
      $this->defaultAction = $_GET['act'];
  }

  public function actionIndex($org = 1, $used = 0, $offset = 0)
	{
    $criteria = new CDbCriteria();
    $criteria->compare('is_used', $used);
    $criteria->compare('is_org', $org);

    $goodsNum = MarketGood::model()->count($criteria);

    $criteria->limit = Yii::app()->getModule('market')->goodsPerPage;
    $criteria->offset = $offset;

    $goods = MarketGood::model()->with('author')->findAll($criteria);

    if (Yii::app()->request->isAjaxRequest) {
      if (isset($_POST['pages'])) {
        $this->pageHtml = $this->renderPartial('_goods', array(
          'goods' => $goods,
          'offset' => $offset,
        ), true);
      }
      else $this->pageHtml = $this->renderPartial('index', array(
        'org' => $org,
        'used' => $used,
        'goods' => $goods,
        'offset' => $offset,
        'offsets' => $goodsNum,
      ), true);
    }
    else $this->render('index', array(
      'org' => $org,
      'used' => $used,
      'goods' => $goods,
      'offset' => $offset,
      'offsets' => $goodsNum,
    ));
	}

  public function actionAdd() {
    $good = new MarketGood();
    $good->phone = Yii::app()->user->model->profile->phone;
    /** @var PurchaseCategory $category */
    $categories = PurchaseCategory::model()->findAll();

    if (isset($_POST['MarketGood'])) {
      $result = array();
      $good->attributes = $_POST['MarketGood'];
      $good->currency = 'RUR';
      $good->author_id = Yii::app()->user->getId();
      $good->is_org = (Yii::app()->user->checkAccess('purchases.purchases.create')) ? 1 : 0;

      if ($good->save()) {
        foreach ($_POST['market_wdd'] as $wdd) {
          foreach ($categories as $category) {
            if ($category->category_id == $wdd) {
              $c = new MarketGoodCategory();
              $c->good_id = $good->good_id;
              $c->category_id = $wdd;
              $c->save();
            }
          }
        }

        $result['success'] = true;
        $result['msg'] = 'Товар успешно добавлен';
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
        $this->pageHtml = $this->renderPartial('add', array(
          'good' => $good,
          'categories' => $categories,
        ), true);
      }
    else $this->render('add', array(
      'good' => $good,
      'categories' => $categories,
    ));
  }

  public function actionShowGood($author_id, $good_id, $reply = null) {
    $good = MarketGood::model()->findByPk($good_id);

    $subscription = Subscription::model()->find('user_id = :id AND sub_type = :type AND sub_link_id = :lid', array(
      ':id' => Yii::app()->user->getId(),
      ':type' => Subscription::TYPE_MARKET_GOOD,
      ':lid' => $good_id,
    ));

    if (Yii::app()->request->isAjaxRequest) {
      $this->pageHtml = $this->renderPartial('show_good', array(
        'good' => $good,
        'subscription' => $subscription,
        'reply' => $reply,
      ), true);
    }
    else $this->render('show_good', array(
      'good' => $good,
      'subscription' => $subscription,
      'reply' => $reply,
    ));
  }

  public function actionSubscribe($id) {
    $result = array();
    $good = Good::model()->findByPk($id);
    $subscription = Subscription::model()->find('user_id = :id AND sub_type = :type AND sub_link_id = :lid', array(
      ':id' => Yii::app()->user->getId(),
      ':type' => Subscription::TYPE_MARKET_GOOD,
      ':lid' => $id,
    ));
    if ($subscription) {
      $subscription->delete();
      $result['step'] = 1;
    }
    else {
      $subscription = new Subscription();
      $subscription->user_id = Yii::app()->user->getId();
      $subscription->sub_type = Subscription::TYPE_MARKET_GOOD;
      $subscription->sub_link_id = $id;
      $subscription->save();
      $result['step'] = 0;
    }

    echo json_encode($result);
    exit;
  }

  public function actionShareToFriends($id) {
    $result = array();
    $good = MarketGood::model()->findByPk($id);

    if (isset($_POST['msg'])) {
      $post = new ProfileWallPost();
      $post->wall_id = Yii::app()->user->getId();
      $post->author_id = Yii::app()->user->getId();
      $post->reference_type = ProfileWallPost::REF_TYPE_GOOD;
      $post->reference_id = $id;
      $post->post = $_POST['msg'];
      if ($post->save()) $result['success'] = true;
      else $result['success'] = false;
    }
    else $result['html'] = $this->renderPartial('sharetofriends_box', array('good' => $good), true);

    echo json_encode($result);
    exit;
  }
}