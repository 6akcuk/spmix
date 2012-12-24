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

    public $pageHtml = '';
    public $wideScreen = false;

    public function init() {
        if (Yii::app()->controller->id == "site" && Yii::app()->user->getIsGuest()) {
            $this->layout = '//layouts/intro';
        }
        else {
            $this->layout = '//layouts/main';
        }
    }
}