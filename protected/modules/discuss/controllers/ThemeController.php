<?php

class ThemeController extends Controller
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

  public function actionIndex($forum_id, $offset = 0)
  {
    /** @var DiscussForum $forum */
    $forum = DiscussForum::model()->findByPk($forum_id);
    if ($forum->access_city > 0 && $forum->access_city != DiscussForum::getUserCity())
      throw new CHttpException(403, 'В доступе отказано. В Вашем городе данный форум отключен');

    if ($forum->access_rights > DiscussForum::getNumericRight())
      throw new CHttpException(403, 'В доступе отказано. Вы не состоите в нужной группе');

    $criteria = new CDbCriteria();
    $criteria->compare('t.forum_id', $forum_id);
    $criteria->order = 't.fixed DESC, lastPost.add_date DESC';
    $criteria->offset = $offset;
    $criteria->limit = Yii::app()->getModule('discuss')->themesPerPage;

    $themes = DiscussTheme::model()->with('postsNum', array('lastPost' => array('limit' => 1)), 'lastPost.author.profile')->findAll($criteria);

    $criteria->limit = 0;
    $themesNum = DiscussTheme::model()->count($criteria);

    if (Yii::app()->request->isAjaxRequest) {
      if (isset($_POST['pages'])) {
        $this->pageHtml = $this->renderPartial('_themes', array(
          'themes' => $themes,
          'offset' => $offset
        ), true);
      }
      else $this->pageHtml = $this->renderPartial('index', array(
        'forum' => $forum,
        'themes' => $themes,
        'themesNum' => $themesNum,
        'offset' => $offset,
      ), true);
    }
    else $this->render('index', array(
      'forum' => $forum,
      'themes' => $themes,
      'themesNum' => $themesNum,
      'offset' => $offset,
    ));
  }

  public function actionCreate($forum_id)
  {
    /** @var DiscussForum $forum */
    $forum = DiscussForum::model()->findByPk($forum_id);
    if ($forum->access_city > 0 && $forum->access_city != DiscussForum::getUserCity())
      throw new CHttpException(403, 'В доступе отказано. В Вашем городе данный форум отключен');

    if ($forum->access_rights > DiscussForum::getNumericRight())
      throw new CHttpException(403, 'В доступе отказано. Вы не состоите в нужной группе');

    if (isset($_POST['title'])) {
      $theme = new DiscussTheme();
      $theme->forum_id = $forum_id;
      $theme->title = $_POST['title'];
      $theme->author_id = Yii::app()->user->getId();
      if ($theme->save()) {
        $post = new DiscussPost();
        $post->forum_id = $forum_id;
        $post->theme_id = $theme->theme_id;
        $post->author_id = Yii::app()->user->getId();
        $post->post = $_POST['post'];
        $post->attaches = json_encode(isset($_POST['attaches']) ? $_POST['attaches'] : array());
        $post->save();

        echo json_encode(array(
          'success' => true,
          'url' => '/discuss'. $forum_id .'_'. $theme->theme_id,
        ));
        exit;
      }
      else
        throw new CHttpException(500, 'Не удалось создать тему');
    }

    if (Yii::app()->request->isAjaxRequest) {
      $this->pageHtml = $this->renderPartial('create', array(
        'forum' => $forum,
      ), true);
    }
    else $this->render('create', array(
      'forum' => $forum,
    ));
  }
}