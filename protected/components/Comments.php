<?php
/**
 * Created by JetBrains PhpStorm.
 * User: denis
 * Date: 28.02.13
 * Time: 9:57
 * To change this template use File | Settings | File Templates.
 */
Yii::import('zii.widgets.CPortlet');

class Comments extends CPortlet {
  public $hoop_id;
  public $hoop_type;

  protected function renderContent() {
    $criteria = new CDbCriteria();
    $criteria->order = 'creation_date DESC';
    $criteria->compare('hoop_id', $this->hoop_id);
    $criteria->compare('hoop_type', $this->hoop_type);

    $commentsNum = Comment::model()->count($criteria);

    if ($commentsNum > 10) $criteria->limit = 3;
    $comments = array_reverse(Comment::model()->with('author', 'author.profile')->findAll($criteria));

    $this->render('comments', array('comments' => $comments, 'offsets' => $commentsNum));
  }
}