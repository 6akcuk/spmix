<?php

class DefaultController extends Controller
{
  public $defaultAction = 'inbox';

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

  public function actionInbox($offset = 0) {
    $c = (isset($_REQUEST['c'])) ? $_REQUEST['c'] : array();

    $messages = DialogMessage::getInboxMessages(Yii::app()->user->getId(), $offset, $c);
    $offsets = DialogMessage::countInboxMessages(Yii::app()->user->getId(), $c);

    if (Yii::app()->request->isAjaxRequest) {
      if (isset($_POST['pages'])) {
        $this->pageHtml = $this->renderPartial('_message', array(
          'messages' => $messages,
          'offset' => $offset,
        ), true);
      }
      else $this->pageHtml = $this->renderPartial('inbox', array(
        'messages' => $messages,
        'offset' => $offset,
        'offsets' => $offsets,
      ), true);
    }
    else $this->render('inbox', array(
      'messages' => $messages,
      'offset' => $offset,
      'offsets' => $offsets,
    ));
  }
}