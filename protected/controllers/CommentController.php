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

  public function actionAdd($hoop_id, $hoop_type) {
    switch  ($hoop_type) {
      case 'good':
        /** @var $hoop Good */
        $hoop = Good::model()->with('purchase')->findByPk($hoop_id);
        break;
    }

    $comment = new Comment();
    $comment->author_id = Yii::app()->user->getId();
    $comment->hoop_id = $hoop_id;
    $comment->hoop_type = $hoop_type;
    $comment->attributes = $_POST['Comment'];

    $attaches = array();
    if (isset($_POST['Comment']['attach'])) {
      $k = min(sizeof($_POST['Comment']['attach']), 3);
      for ($i = 0; $i < $k; $i++) {
        $attaches[] = $_POST['Comment']['attach'][$i];
      }
    }

    $comment->attaches = json_encode($attaches);
    $result = array();

    if ($comment->save()) {
      $result['success'] = true;
    }
    else {
      foreach ($comment->getErrors() as $attr => $error) {
        $result[ActiveHtml::activeId($comment, $attr)] = $error;
      }
    }

    echo json_encode($result);
    exit;
  }
}