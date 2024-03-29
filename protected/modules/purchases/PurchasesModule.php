<?php

class PurchasesModule extends CWebModule
{
  public $purchasesPerPage = 20;
  public $goodsPerPage = 50;
  public $sitesPerPage = 50;
  public $paymentsPerPage = 30;
  public $ordersPerPage = 30;

	public function init()
	{
		// this method is called when the module is being created
		// you may place code here to customize the module or the application

		// import the module-level models and components
		$this->setImport(array(
			'purchases.models.*',
            'purchases.components.*',
		));
	}

	public function beforeControllerAction($controller, $action)
	{
		if(parent::beforeControllerAction($controller, $action))
		{
			// this method is called before any module controller action is performed
			// you may place customized code here
			return true;
		}
		else
			return false;
	}
}
