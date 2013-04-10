<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends CController
{
	/**
	 * @var string the default layout for the controller view. Defaults to '//layouts/column1',
	 * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
	 */
	public $layout='//layouts/main';
  public $breadcrumbs = array();
  public $menu = array();

  public $pageCounters = array('friends' => 0, 'pm' => 0);
  public $pageHtml = '';
  public $wideScreen = false;
  public $boxWidth = 0;

  public function init() {
    if (Yii::app()->controller->id == "site" && Yii::app()->user->getIsGuest()) {
      $this->layout = '//layouts/intro';
    }
    else {
      if (!Yii::app()->user->getIsGuest()) {
        $criteria = new CDbCriteria();
        $criteria->addCondition('owner_id = :id');
        $criteria->addCondition('viewed = 0');
        $criteria->addCondition('req_type = :type');
        $criteria->params[':id'] = Yii::app()->user->getId();

        $criteria->params[':type'] = ProfileRequest::TYPE_FRIEND;
        $this->pageCounters['friends'] = ProfileRequest::model()->count($criteria);

        $criteria->params[':type'] = ProfileRequest::TYPE_PM;
        $this->pageCounters['pm'] = ProfileRequest::model()->count($criteria);

        Yii::import('application.modules.purchases.models.*');
        $order_criteria = new CDbCriteria();
        $order_criteria->compare('customer_id', Yii::app()->user->getId());
        $order_criteria->addInCondition('status', array(Order::STATUS_AWAITING, Order::STATUS_WAIT_FOR_DELIVER));
        $this->pageCounters['orders'] = Order::model()->count($order_criteria);

        if (Yii::app()->user->checkAccess('purchases.purchases.acquire')) {
          $criteria = new CDbCriteria();
          $criteria->addCondition('purchase_delete IS NULL');
          $criteria->addCondition('t.mod_request_id IS NOT NULL AND t.mod_confirmation = 0 AND mod_request.status = '. PurchaseModRequest::STATUS_NEW);

          if (!Yii::app()->user->checkAccess('purchases.purchases.acquireSuper')) {
            $criteria->addCondition('city_id = :id');
            $criteria->params[':id'] = Yii::app()->user->model->profile->city_id;
          }

          $this->pageCounters['purchases'] = Purchase::model()->resetScope()->with('mod_request')->count($criteria);
        }

        if (Yii::app()->user->checkAccess('users.users.index')) {
          $this->pageCounters['users'] = User::model()->count();
        }
      }

      $this->layout = '//layouts/main';
    }
  }
}