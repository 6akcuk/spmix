<?php
/**
 * Created by JetBrains PhpStorm.
 * User: denis
 * Date: 28.02.13
 * Time: 12:56
 * To change this template use File | Settings | File Templates.
 */

class CommentController extends Controller {
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


}