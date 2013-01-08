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
            }

            $this->layout = '//layouts/main';
        }
    }
}