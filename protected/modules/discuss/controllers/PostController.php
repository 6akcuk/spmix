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

  public function actionIndex($forum_id, $theme_id, $offset = 0, $post = 0, $scroll = 0) {
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

    $postsNum = DiscussPost::model()->count($criteria);
    if ($post > 0) {
      $pc = clone $criteria;
      $pc->addCondition('t.post_id < :id');
      $pc->params[':id'] = $post;

      $beforeNum = DiscussPost::model()->count($pc);
      $beforePages = floor($beforeNum / Yii::app()->getModule('discuss')->postsPerPage);

      $offset = ($beforePages * Yii::app()->getModule('discuss')->postsPerPage) - Yii::app()->getModule('discuss')->postsPerPage;
    }

    $criteria->limit = Yii::app()->getModule('discuss')->postsPerPage;

    if ($offset === 'last') {
      $pages = ceil($postsNum / $criteria->limit);
      $offset = $pages * $criteria->limit - $criteria->limit;
    }

    $criteria->offset = $offset;

    $posts = DiscussPost::model()->with('author.profile')->findAll($criteria);

    if (Yii::app()->request->isAjaxRequest) {
      if (isset($_POST['pages'])) {
        $this->pageHtml = $this->renderPartial('_posts', array(
          '_post' => $post,
          'posts' => $posts,
          'offset' => $offset
        ), true);
      }
      else $this->pageHtml = $this->renderPartial('index', array(
        'forum' => $forum,
        'theme' => $theme,
        '_post' => $post,
        'posts' => $posts,
        'postsNum' => $postsNum,
        'offset' => $offset,
        'scroll' => $scroll,
      ), true);
    }
    else $this->render('index', array(
      'forum' => $forum,
      'theme' => $theme,
      '_post' => $post,
      'posts' => $posts,
      'postsNum' => $postsNum,
      'offset' => $offset,
      'scroll' => $scroll,
    ));
  }

  public function actionCreate($forum_id, $theme_id) {
    /** @var DiscussForum $forum */
    $forum = DiscussForum::model()->findByPk($forum_id);
    if (!$forum)
      throw new CHttpException(404, 'Форум не найден');

    if ($forum->access_city > 0 && $forum->access_city != DiscussForum::getUserCity())
      throw new CHttpException(403, 'В доступе отказано. В Вашем городе данный форум отключен');

    if ($forum->access_rights > DiscussForum::getNumericRight())
      throw new CHttpException(403, 'В доступе отказано. Вы не состоите в нужной группе');

    $theme = DiscussTheme::model()->findByPk($theme_id);
    if (!$theme)
      throw new CHttpException(404, 'Тема не найдена');

    if ($theme->forum_id != $forum_id)
      throw new CHttpException(500, 'Ошибка синхронизации');

    if (isset($_POST['post'])) {
      $post = new DiscussPost();
      $post->forum_id = $forum_id;
      $post->theme_id = $theme_id;
      $post->author_id = Yii::app()->user->getId();
      $post->post = $_POST['post'];
      $post->attaches = json_encode(isset($_POST['attaches']) ? $_POST['attaches'] : array());
      $post->save();

      echo json_encode(array(
        'success' => true,
      ));
      exit;
    }
  }
}