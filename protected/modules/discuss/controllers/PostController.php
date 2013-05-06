<?php

class PostController extends Controller
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

  public function init()
  {
    parent::init();

    if (isset($_GET['act']))
      $this->defaultAction = $_GET['act'];
  }

  public function actionIndex($forum_id, $theme_id, $offset = 0) {
    /** @var DiscussForum $forum */
    $forum = DiscussForum::model()->findByPk($forum_id);
    $theme = DiscussTheme::model()->with('author')->findByPk($theme_id);
    if ($forum->access_city > 0 && $forum->access_city != DiscussForum::getUserCity())
      throw new CHttpException(403, 'В доступе отказано. В Вашем городе данный форум отключен');

    if ($forum->access_rights > DiscussForum::getNumericRight())
      throw new CHttpException(403, 'В доступе отказано. Вы не состоите в нужной группе');

    $criteria = new CDbCriteria();
    $criteria->compare('t.forum_id', $forum_id);
    $criteria->compare('t.theme_id', $theme_id);
    $criteria->order = 't.add_date DESC';
    $criteria->offset = $offset;
    $criteria->limit = Yii::app()->getModule('discuss')->themesPerPage;

    $posts = DiscussPost::model()->with('author.profile')->findAll($criteria);

    $criteria->limit = 0;
    $postsNum = DiscussPost::model()->count($criteria);

    if (Yii::app()->request->isAjaxRequest) {
      if (isset($_POST['pages'])) {
        $this->pageHtml = $this->renderPartial('_posts', array(
          'posts' => $posts,
          'offset' => $offset
        ), true);
      }
      else $this->pageHtml = $this->renderPartial('index', array(
        'forum' => $forum,
        'theme' => $theme,
        'posts' => $posts,
        'postsNum' => $postsNum,
        'offset' => $offset,
      ), true);
    }
    else $this->render('index', array(
      'forum' => $forum,
      'theme' => $theme,
      'posts' => $posts,
      'postsNum' => $postsNum,
      'offset' => $offset,
    ));
  }
}