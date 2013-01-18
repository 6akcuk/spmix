<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Sum
 * Date: 23.11.12
 * Time: 22:39
 * To change this template use File | Settings | File Templates.
 */

class ProfilesController extends Controller {
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

    public function actionIndex($id) {
        $userinfo = User::model()->with('profile')->findByPk($id);
        $friends = $userinfo->profile->getFriends();
        $friendsNum = $userinfo->profile->countFriends();

        if (Yii::app()->request->isAjaxRequest) {
            $this->pageHtml = $this->renderPartial('index', array('userinfo' => $userinfo, 'friends' => $friends, 'friendsNum' => $friendsNum), true);
        }
        else $this->render('index', array('userinfo' => $userinfo, 'friends' => $friends, 'friendsNum' => $friendsNum));
    }

    public function actionReputation($id, $offset = 0) {
        $act = (isset($_GET['act']))  ? $_GET['act'] : 'show';
        $value = (isset($_POST['value'])) ? intval($_POST['value']) : 0;
        $comment = (isset($_POST['comment'])) ? trim($_POST['comment']) : '';

        switch ($act) {
            case 'increase':
                if ($value <= 0) {
                    throw new CHttpException(500, 'Неверное значение репутации');
                    return;
                }

                if ($id == Yii::app()->user->getId()) {
                    throw new CHttpException(500, 'Нельзя '. Yii::t('app', '0#самому|1#самой', Yii::app()->user->model->profile->genderToInt()) .' себе повышать репутацию');
                    return;
                }

                if (Yii::app()->user->checkAccess('users.profiles.increaseReputation') &&
                    (
                        (!Yii::app()->user->checkAccess('users.profiles.increaseReputationAny') &&
                            in_array($value, array(1, 5))
                        )
                        ||
                        Yii::app()->user->checkAccess('users.profiles.increaseReputationAny')
                    )
                )
                {
                    $reputation = new ProfileReputation();
                    $reputation->author_id = Yii::app()->user->getId();
                    $reputation->owner_id = $id;
                    $reputation->value = $value;
                    $reputation->comment = $comment;

                    if (!$reputation->save()) {
                        throw new CHttpException(500, 'Не удалось создать запись в БД');
                        return;
                    }

                    $conn = $reputation->getDbConnection();
                    $command = $conn->createCommand("UPDATE `profiles` SET positive_rep = positive_rep + ". $value ." WHERE `user_id` = ". $id);
                    if (!$command->execute()) {
                        $reputation->delete();
                        throw new CHttpException(500, 'Не удалось увеличить репутацию пользователю');
                        return;
                    }

                    $profile = Profile::model()->findByPk($id);
                    echo json_encode(array('positive_rep' => $profile->positive_rep));
                    exit;
                }
                else {
                    throw new CHttpException(403, 'В доступе отказано');
                }
                break;
            case 'decrease':
                if ($value <= 0) {
                    throw new CHttpException(500, 'Неверное значение репутации');
                    return;
                }

                if ($id == Yii::app()->user->getId()) {
                    throw new CHttpException(500, 'Нельзя '. Yii::t('app', '0#самому|1#самой', Yii::app()->user->model->profile->genderToInt()) .' себе понижать репутацию');
                    return;
                }

                if (Yii::app()->user->checkAccess('users.profiles.decreaseReputation') &&
                    (
                        (!Yii::app()->user->checkAccess('users.profiles.decreaseReputationAny') &&
                            in_array($value, array(1, 5))
                        )
                            ||
                            Yii::app()->user->checkAccess('users.profiles.decreaseReputationAny')
                    ) &&
                    !Yii::app()->user->checkAccess('users.profiles.decreaseReputationUser')
                )
                {
                    $reputation = new ProfileReputation();
                    $reputation->author_id = Yii::app()->user->getId();
                    $reputation->owner_id = $id;
                    $reputation->value = -$value;
                    $reputation->comment = $comment;

                    if (!$reputation->save()) {
                        throw new CHttpException(500, 'Не удалось создать запись в БД');
                        return;
                    }

                    $conn = $reputation->getDbConnection();
                    $command = $conn->createCommand("UPDATE `profiles` SET negative_rep = negative_rep + ". $value ." WHERE `user_id` = ". $id);
                    if (!$command->execute()) {
                        $reputation->delete();
                        throw new CHttpException(500, 'Не удалось уменьшить репутацию пользователю');
                        return;
                    }

                    $profile = Profile::model()->findByPk($id);
                    echo json_encode(array('negative_rep' => $profile->negative_rep));
                    exit;
                }
                else {
                    throw new CHttpException(403, 'В доступе отказано');
                }
                break;
            case 'show':
                $user = User::model()->with('profile')->findByPk($id);

                $criteria = new CDbCriteria();
                $criteria->limit = Yii::app()->getModule('users')->reputationPerPage;
                $criteria->offset = $offset;
                $criteria->order = 'rep_date DESC';
                $criteria->addCondition('owner_id = :id');
                $criteria->params[':id'] = $id;

                $reputations = ProfileReputation::model()->with('author', 'author.profile')->findAll($criteria);

                $criteria->limit = 0;
                $reputationsNum = ProfileReputation::model()->count($criteria);

                if (Yii::app()->request->isAjaxRequest) {
                    if (isset($_POST['pages'])) {
                        $this->pageHtml = $this->renderPartial('_reputation', array(
                            'data' => $reputations,
                            'offset' => $offset,
                        ), true);
                    }
                    else $this->pageHtml = $this->renderPartial('reputation', array(
                        'user' => $user,
                        'data' => $reputations,
                        'offset' => $offset,
                        'offsets' => $reputationsNum,
                    ), true);
                }
                else $this->render('reputation', array('user' => $user, 'data' => $reputations, 'offset' => $offset, 'offsets' => $reputationsNum,));

                break;
        }
    }

    public function actionDeleteReputation() {
        $id = intval($_POST['id']);
        $reputation = ProfileReputation::model()->findByPk($id);

        if (Yii::app()->user->checkAccess(RBACFilter::getHierarchy() .'Super') ||
            Yii::app()->user->checkAccess(RBACFilter::getHierarchy() .'Own', array('reputation' => $reputation))) {
            $reputation->reputation_delete = date("Y-m-d H:i:s");
            $value = ($reputation->value < 0) ? -$reputation->value : $reputation->value;
            $field = ($reputation->value < 0) ? 'negative_rep' : 'positive_rep';

            if (!$reputation->save(true, array('reputation_delete'))) {
                throw new CHttpException(500, 'Не удалось установить маркер удаления');
                return;
            }

            $conn = $reputation->getDbConnection();
            $command = $conn->createCommand("UPDATE `profiles` SET `". $field ."` = `". $field ."` - ". $value ." WHERE `user_id` = ". $reputation->owner_id);
            if (!$command->execute()) {
                $reputation->reputation_delete = null;
                $reputation->save(true, array('reputation_delete'));

                throw new CHttpException(500, 'Не удалось обновить репутацию пользователя');
                return;
            }

            $_SESSION['reputation.'. $id .'.hash'] = $hash = substr(md5(time() . $id . 'tt'), 0, 8);

            echo json_encode(array('success' => true, 'html' => 'Репутация удалена. <a onclick="return Profile.restoreReputation('. $id .', \''. $hash .'\')">Восстановить</a>'));
            exit;
        }
        else
            throw new CHttpException(403, 'В доступе отказано');
    }
    public function actionRestoreReputation() {
        $id = intval($_POST['id']);
        $reputation = ProfileReputation::model()->resetScope()->findByPk($id);

        if (Yii::app()->user->checkAccess(RBACFilter::getHierarchy() .'Super') ||
            Yii::app()->user->checkAccess(RBACFilter::getHierarchy() .'Own', array('reputation' => $reputation))) {
            if (!isset($_SESSION['reputation.'. $id .'.hash']) || $_SESSION['reputation.'. $id .'.hash'] != $_POST['hash']) {
                throw new CHttpException(500, 'Неверный формат запроса');
                exit;
            }

            $reputation->reputation_delete = null;
            $value = ($reputation->value < 0) ? -$reputation->value : $reputation->value;
            $field = ($reputation->value < 0) ? 'negative_rep' : 'positive_rep';

            if (!$reputation->save(true, array('reputation_delete'))) {
                throw new CHttpException(500, 'Не удалось снять маркер удаления');
                exit;
            }

            $conn = $reputation->getDbConnection();
            $command = $conn->createCommand("UPDATE `profiles` SET `". $field ."` = `". $field ."` + ". $value ." WHERE `user_id` = ". $reputation->owner_id);
            if (!$command->execute()) {
                $reputation->reputation_delete = date("Y-m-d H:i:s");
                $reputation->save(true, array('reputation_delete'));

                throw new CHttpException(500, 'Не удалось обновить репутацию пользователя');
                exit;
            }

            unset($_SESSION['reputation.'. $id .'.hash']);

            echo json_encode(array('success' => true));
            exit;
        }
        else
            throw new CHttpException(403, 'В доступе отказано');
    }

    public function actionEdit() {
        /** @var $userinfo User */
        $userinfo = User::model()->with('profile')->findByPk(Yii::app()->user->getId());

        if (isset($_POST['Profile'])) {
            $userinfo->profile->setScenario('edit');
            $userinfo->profile->attributes = $_POST['Profile'];

            if ($userinfo->profile->save()) {
                ProfilePaydetail::model()->deleteAll('user_id = :user_id', array(':user_id' => Yii::app()->user->getId()));

                foreach ($_POST['ProfilePaydetail']['paysystem_name'] as $idx => $oiv) {
                    $pay = $_POST['ProfilePaydetail'];

                    $model = new ProfilePaydetail();
                    $model->user_id = Yii::app()->user->getId();
                    $model->paysystem_name = $pay['paysystem_name'][$idx];
                    $model->paysystem_details = $pay['paysystem_details'][$idx];
                    $model->save();
                }

                $result['success'] = true;
            }
            else {
                foreach ($userinfo->profile->getErrors() as $attr => $error) {
                    $result[ActiveHtml::activeId($userinfo->profile, $attr)] = $error;
                }
            }

            echo json_encode($result);
            exit;
        }

        if (Yii::app()->request->isAjaxRequest) {
            $this->pageHtml = $this->renderPartial('edit', array('userinfo' => $userinfo), true);
        }
        else $this->render('edit', array('userinfo' => $userinfo));
    }
}