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
    );
  }

  public function init() {
    parent::init();

    if (isset($_GET['act']))
      $this->defaultAction = $_GET['act'];
  }


}