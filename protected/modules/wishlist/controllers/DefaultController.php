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
      array(
        'ext.DevelopFilter',
      )
    );
  }

  public function init() {
    parent::init();

    if (isset($_GET['act']))
      $this->defaultAction = $_GET['act'];
  }

  public function actionIndex() {
    $criteria = new CDbCriteria();
    $criteria->compare('type', 1);
    $criteria->limit = Yii::app()->getModule('wishlist')->wishesPerPage;
    $criteria->order = 'add_date DESC';

    $wants = Wishlist::model()->with('city', 'author.profile')->findAll($criteria);
    $wantsNum = Wishlist::model()->count($criteria);

    $criteria = new CDbCriteria();
    $criteria->compare('type', 2);
    $criteria->limit = Yii::app()->getModule('wishlist')->wishesPerPage;
    $criteria->order = 'add_date DESC';

    $cans = Wishlist::model()->with('city', 'author.profile')->findAll($criteria);
    $cansNum = Wishlist::model()->count($criteria);

    if (Yii::app()->request->isAjaxRequest) {
      $this->pageHtml = $this->renderPartial('index', array(
        'wants' => $wants,
        'wantsNum' => $wantsNum,
        'cans' => $cans,
        'cansNum' => $cansNum,
      ), true);
    }
    else $this->render('index', array(
      'wants' => $wants,
      'wantsNum' => $wantsNum,
      'cans' => $cans,
      'cansNum' => $cansNum,
    ));
  }

  public function actionAdd($type) {
    $wishlist = new Wishlist();
    $wishlist->type = $type;

    if (isset($_POST['shortstory'])) {
      $wishlist->shortstory = $_POST['shortstory'];
      $wishlist->author_id = Yii::app()->user->getId();
      $wishlist->city_id = Yii::app()->user->model->profile->city_id;

      $result = array();

      if ($wishlist->save()) {
        $result['success'] = true;
        $result['message'] = 'Пожелание успешно добавлено';
      }
      else {
        $errors = array();
        foreach ($wishlist->getErrors() as $attr => $error) {
          $errors[] = (is_array($error)) ? implode('<br/>', $error) : $error;
        }

        $result['errors'] = implode('<br/>', $errors);
      }

      echo json_encode($result);
      exit;
    }

    $this->pageHtml = $this->renderPartial('add_box', array(
      'wishlist' => $wishlist,
    ), true);
  }

  public function actionEdit($id) {
    $wishlist = Wishlist::model()->findByPk($id);

    if (Yii::app()->user->checkAccess(RBACFilter::getHierarchy() .'Super') ||
      Yii::app()->user->checkAccess(RBACFilter::getHierarchy() .'Own', array('wishlist' => $wishlist))) {
      if (isset($_POST['shortstory'])) {
        $wishlist->shortstory = $_POST['shortstory'];

        $result = array();

        if ($wishlist->save(true, array('shortstory'))) {
          $result['success'] = true;
          $result['message'] = 'Изменения успешно сохранены';
        }
        else {
          $errors = array();
          foreach ($wishlist->getErrors() as $attr => $error) {
            $errors[] = (is_array($error)) ? implode('<br/>', $error) : $error;
          }

          $result['errors'] = implode('<br/>', $errors);
        }

        echo json_encode($result);
        exit;
      }

      $this->pageHtml = $this->renderPartial('edit_box', array(
        'wishlist' => $wishlist,
      ), true);
    }
    else
      throw new CHttpException(403, 'У Вас нет прав на редактирование объявления');
  }
}