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
                'ext.DevelopFilter'
            ),
        );
    }

    public function init() {
        parent::init();

        if (isset($_GET['sel']) && $_GET['sel'] == -1)
            $this->defaultAction = 'create';
    }

    public function actionIndex($offset = 0)
	{
        $c = (isset($_REQUEST['c'])) ? $_REQUEST['c'] : array();

        $criteria = new CDbCriteria();
        $criteria->limit = $this->module->dialogsPerPage;
        $criteria->offset = $offset;

        $criteria->addCondition('member.member_id = :id');
        $criteria->params[':id'] = Yii::app()->user->getId();
        $criteria->order = 'lastMessage.creation_date DESC';

        $dialogs = Dialog::model()->with('member', 'leader', 'lastMessage')->findAll($criteria);
        //$dialogs = DialogMember::model()->with('dialog', 'dialog.leader', 'dialog.leader.profile', 'dialog.lastMessage')->findAll($criteria);

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

    public function actionCreate() {
        if (isset($_POST['recipients'])) {
            $recipients = $_POST['recipients'];
            $title = $_POST['title'];
            $message = $_POST['message'];

            // Создание беседы (конференции)
            if (sizeof($recipients) > 1) {
                $dialog = new Dialog();
                $dialog->leader_id = Yii::app()->user->getId();
                $dialog->title = $title;
                $dialog->type = Dialog::TYPE_TET;
                if (!$dialog->save()) {
                    echo json_encode(array('success' => false, 'message' => 'Не удалось создать беседу'));
                    exit;
                }

                // Members
                $dialogMembers = array();
                $memberSuccessful = false;

                foreach ($recipients as $idx => $recipient) {
                    $dialogMembers[$idx] = new DialogMember();
                    $dialogMembers[$idx]->dialog_id = $dialog->dialog_id;
                    $dialogMembers[$idx]->member_id = $recipient;

                    $memberSuccessful = $dialogMembers[$idx]->validate();
                }

                $idx = sizeof($dialogMembers);
                $dialogMembers[$idx] = new DialogMember();
                $dialogMembers[$idx]->dialog_id = $dialog->dialog_id;
                $dialogMembers[$idx]->member_id = Yii::app()->user->getId();

                $memberSuccessful = $dialogMembers[$idx]->validate();

                if (!$memberSuccessful) {
                    $dialog->delete();

                    echo json_encode(array('success' => false, 'message' => 'Собеседники не прошли проверку валидации'));
                    exit;
                }

                foreach ($dialogMembers as &$member) {
                    $member->save();
                }
            }
            elseif (sizeof($recipients) == 1) {
                $criteria = new CDbCriteria();
                $criteria->condition = 't.member_id = :id AND dialog.type = :type';
                $criteria->params[':id'] = $recipients[0];
                $criteria->params[':type'] = Dialog::TYPE_TET;

                $dialogMembers = DialogMember::model()->with('dialog')->findAll($criteria);
                if (!$dialogMembers) {
                    $dialog = new Dialog();
                    $dialog->leader_id = Yii::app()->user->getId();
                    $dialog->type = Dialog::TYPE_TET;
                    if (!$dialog->save()) {
                        echo json_encode(array('success' => false, 'message' => 'Не удалось создать диалог с пользователем'));
                        exit;
                    }

                    // Members
                    $dialogMembers = array();
                    $memberSuccessful = false;

                    foreach ($recipients as $idx => $recipient) {
                        $dialogMembers[$idx] = new DialogMember();
                        $dialogMembers[$idx]->dialog_id = $dialog->dialog_id;
                        $dialogMembers[$idx]->member_id = $recipient;

                        $memberSuccessful = $dialogMembers[$idx]->validate();
                    }

                    $idx = sizeof($dialogMembers);
                    $dialogMembers[$idx] = new DialogMember();
                    $dialogMembers[$idx]->dialog_id = $dialog->dialog_id;
                    $dialogMembers[$idx]->member_id = Yii::app()->user->getId();

                    $memberSuccessful = $dialogMembers[$idx]->validate();

                    if (!$memberSuccessful) {
                        $dialog->delete();

                        echo json_encode(array('success' => false, 'message' => 'Собеседники не прошли проверку валидации'));
                        exit;
                    }

                    foreach ($dialogMembers as &$member) {
                        $member->save();
                    }
                }
                else $dialog = $dialogMembers[0]->dialog;
            }
            else {
                echo json_encode(array('success' => false, 'message' => 'Вы не выбрали получателя сообщения'));
                exit;
            }

            $dialogMessage = new DialogMessage();
            $dialogMessage->dialog_id = $dialog->dialog_id;
            $dialogMessage->author_id = Yii::app()->user->getId();
            $dialogMessage->message = $message;

            if (!$dialogMessage->save()) {
                echo json_encode(array('success' => false, 'message' => 'Сообщение не было доставлено'));
                exit;
            }
            else {
                /** @var $member DialogMember */
                $selfRecipient = 0;
                foreach ($dialogMembers as $member) {
                    // Можно отправлять самому себе сообщения
                    if ($member->member_id == Yii::app()->user->getId() && $selfRecipient != 1) {
                        $selfRecipient++;
                        continue;
                    }

                    $request = new ProfileRequest();
                    $request->owner_id = $member->member_id;
                    $request->req_type = ProfileRequest::TYPE_PM;
                    $request->req_link_id = $dialogMessage->message_id;
                    $request->save();
                }

                echo json_encode(array('success' => true, 'url' => '/im?sel='. $dialog->dialog_id));
                exit;
            }
        }

        $friends = Yii::app()->user->model->profile->getAllFriends(null);

        if (Yii::app()->request->isAjaxRequest) {
            $this->pageHtml = $this->renderPartial('create', array('friends' => $friends), true);
        }
        else $this->render('create', array('friends' => $friends));
    }
}