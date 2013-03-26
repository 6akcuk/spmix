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

    $dialogs = Dialog::getDialogs(Yii::app()->user->getId(), $offset);

    $criteria = new CDbCriteria();
    $criteria->addCondition('member.member_id = :id');
    $criteria->params[':id'] = Yii::app()->user->getId();
    $criteria->limit = 0;
    $criteria->group = 'dialog.dialog_id';
    $dialogsNum = Dialog::model()->with('members')->count('members.member_id = :id', array(':id' => Yii::app()->user->getId())); //DialogMember::model()->with('dialog', 'dialog.lastMessage')->count($criteria);

    if (Yii::app()->request->isAjaxRequest) {
      if (isset($_POST['pages'])) {
        $this->pageHtml = $this->renderPartial('_dialog', array(
          'dialogs' => $dialogs,
          'offset' => $offset,
        ), true);
      }
      else $this->pageHtml = $this->renderPartial('index', array(
        'dialogs' => $dialogs,
        'c' => $c,
        'offset' => $offset,
        'offsets' => $dialogsNum,
      ), true);
    }
    else $this->render('index', array('dialogs' => $dialogs, 'c' => $c, 'offset' => $offset, 'offsets' => $dialogsNum,));
  }
}