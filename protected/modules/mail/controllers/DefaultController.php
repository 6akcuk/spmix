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
        'c' => $c,
        'offset' => $offset,
        'offsets' => $offsets,
      ), true);
    }
    else $this->render('inbox', array(
      'messages' => $messages,
      'c' => $c,
      'offset' => $offset,
      'offsets' => $offsets,
    ));
  }

  public function actionMark_readed() {
    $items = $_POST['items'];
    $criteria = new CDbCriteria();
    $criteria->addInCondition('message_id', $items);
    $back = array();

    $messages = DialogMessage::model()->with('dialog', 'dialog.members', array('isNew' => array('joinType' => 'LEFT JOIN')))->findAll($criteria);
    /** @var $message DialogMessage */
    foreach ($messages as $message) {
      if (!$message->isNew) continue;
      elseif ($message->author_id == Yii::app()->user->getId()) continue;

      $found = false;
      foreach ($message->dialog->members as $member) {
        if ($member->member_id == Yii::app()->user->getId()) $found = true;
      }

      if (!$found) continue;

      $del = new CDbCriteria();
      $del->compare('req_link_id', $message->message_id);
      $del->compare('req_type', ProfileRequest::TYPE_PM);
      $del->compare('owner_id', Yii::app()->user->getId());
      ProfileRequest::model()->deleteAll($del);

      $back[] = $message->message_id;
    }

    $criteria = new CDbCriteria();
    $criteria->addCondition('owner_id = :id');
    $criteria->addCondition('viewed = 0');
    $criteria->addCondition('req_type = :type');
    $criteria->params[':id'] = Yii::app()->user->getId();
    $criteria->params[':type'] = ProfileRequest::TYPE_PM;
    $this->pageCounters['pm'] = ProfileRequest::model()->count($criteria);

    echo json_encode(array('success' => true, 'backlist' => $back, 'pm' => $this->pageCounters['pm']));
    exit;
  }

  public function actionMark_new() {
    $items = $_POST['items'];
    $criteria = new CDbCriteria();
    $criteria->addInCondition('message_id', $items);
    $back = array();

    $messages = DialogMessage::model()->with('dialog', 'dialog.members', array('isNew' => array('joinType' => 'LEFT JOIN')))->findAll($criteria);
    /** @var $message DialogMessage */
    foreach ($messages as $message) {
      if ($message->isNew) continue;
      elseif ($message->author_id == Yii::app()->user->getId()) continue;

      $found = false;
      foreach ($message->dialog->members as $member) {
        if ($member->member_id == Yii::app()->user->getId()) $found = true;
      }

      if (!$found) continue;

      $request = new ProfileRequest();
      $request->req_type = ProfileRequest::TYPE_PM;
      $request->req_link_id = $message->message_id;
      $request->owner_id = Yii::app()->user->getId();
      $request->save();

      $back[] = $message->message_id;
    }

    $criteria = new CDbCriteria();
    $criteria->addCondition('owner_id = :id');
    $criteria->addCondition('viewed = 0');
    $criteria->addCondition('req_type = :type');
    $criteria->params[':id'] = Yii::app()->user->getId();
    $criteria->params[':type'] = ProfileRequest::TYPE_PM;
    $this->pageCounters['pm'] = ProfileRequest::model()->count($criteria);

    echo json_encode(array('success' => true, 'backlist' => $back, 'pm' => $this->pageCounters['pm']));
    exit;
  }
}