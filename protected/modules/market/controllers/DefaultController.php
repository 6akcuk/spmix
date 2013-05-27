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
    $categories = PurchaseCategory::model()->findAll();

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
}