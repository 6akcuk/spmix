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
        );
    }

    public function init() {
        parent::init();

        if (isset($_GET['action']))
            $this->defaultAction = $_GET['action'];

        if (isset($_GET['sel']) && $_GET['sel'] == -1)
            $this->defaultAction = 'create';
        elseif (isset($_GET['sel']) && $_GET['sel'] > 0)
            $this->defaultAction = 'show';
    }

    public function actionIndex($offset = 0)
	{
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

    public function actionCreate($id = 0) {
        if (isset($_POST['recipients'])) {
            $recipients = $_POST['recipients'];
            $title = $_POST['title'];
            $message = $_POST['message'];

            // Создание беседы (конференции)
            if (sizeof($recipients) > 1) {
                $dialog = new Dialog();
                $dialog->leader_id = Yii::app()->user->getId();
                $dialog->title = $title;
                $dialog->type = Dialog::TYPE_CONFERENCE;
                if (!$dialog->save()) {
                    echo json_encode(array('success' => false, 'message' => 'Не удалось создать беседу'));
                    exit;
                }

                // Members
                $dialogMembers = array();
                $memberSuccessful = false;

                for ($idx = 0; $idx < sizeof($recipients); $idx++) {
                    $dialogMembers[$idx] = new DialogMember();
                    $dialogMembers[$idx]->dialog_id = $dialog->dialog_id;
                    $dialogMembers[$idx]->member_id = $recipients[$idx];

                    $memberSuccessful = $dialogMembers[$idx]->validate();
                }

                $idx = sizeof($recipients);
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
                $criteria->condition = 'twin.member_id = :user AND t.member_id = :id AND dialog.type = :type';
                $criteria->params[':user'] = Yii::app()->user->getId();
                $criteria->params[':id'] = $recipients[0];
                $criteria->params[':type'] = Dialog::TYPE_TET;

                $dialogMembers = DialogMember::model()->with('twin', 'dialog')->findAll($criteria);
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

                    $dialogMembers[0] = new DialogMember();
                    $dialogMembers[0]->dialog_id = $dialog->dialog_id;
                    $dialogMembers[0]->member_id = $recipients[0];

                    $memberSuccessful = $dialogMembers[0]->validate();

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
                else {
                    $dialog = $dialogMembers[0]->dialog;
                }
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
                $length = sizeof($dialogMembers);
                for ($i=0; $i < $length; $i++) {
                    $member = $dialogMembers[$i];

                    // Можно отправлять самому себе сообщения
                    if ($member->member_id == Yii::app()->user->getId()) {
                        $selfRecipient++;
                    }
                    else {
                        $request = new ProfileRequest();
                        $request->owner_id = $member->member_id;
                        $request->req_type = ProfileRequest::TYPE_PM;
                        $request->req_link_id = $dialogMessage->message_id;
                        $request->save();
                    }
                }

                echo json_encode(array('success' => true, 'url' => '/im?sel='. $dialog->dialog_id));
                exit;
            }
        }

        $friends = Yii::app()->user->model->profile->getAllFriends(null);
        $guest = ($id) ? User::model()->with('profile', 'profile.city')->findByPk($id) : null;

        if (Yii::app()->request->isAjaxRequest) {
            $this->pageHtml = $this->renderPartial('create', array('friends' => $friends, 'guest' => $guest), true);
        }
        else $this->render('create', array('friends' => $friends, 'guest' => $guest));
    }

    public function actionShow($sel, $offset = 0) {
        $criteria = new CDbCriteria();
        $criteria->limit = Yii::app()->getModule('im')->messagesPerPage;
        $criteria->offset = $offset;

        $criteria->addCondition('dialog_id = :id');
        $criteria->params[':id'] = $sel;

        $criteria->order = 'creation_date DESC';

        $dialog = Dialog::model()->with(array('members' => array('limit' => 4)))->findByPk($sel);
        $messages = DialogMessage::model()->with('author', array('isNewIn' => array('joinType' => 'LEFT JOIN')), array('isNewOut' => array('joinType' => 'LEFT JOIN')))->findAll($criteria);

        $messages = array_reverse($messages);

        $criteria->limit = 0;
        $messagesNum = DialogMessage::model()->count($criteria);

        if (Yii::app()->request->isAjaxRequest) {
            if (isset($_POST['pages'])) {
                $this->pageHtml = $this->renderPartial('_im', array(
                    'messages' => $messages,
                    'offset' => $offset,
                ), true);
            }
            else $this->pageHtml = $this->renderPartial('show', array(
                'dialog' => $dialog,
                'messages' => $messages,
                'offset' => $offset,
                'offsets' => $messagesNum,
            ), true);
        }
        else $this->render('show', array('dialog' => $dialog, 'messages' => $messages, 'offset' => $offset, 'offsets' => $messagesNum,));
    }

    public function actionSend($id) {
        $criteria = new CDbCriteria();
        $criteria->addCondition('dialog_id = :id');
        $criteria->addCondition('member_id = :mid');

        $criteria->params = array(
            ':id' => $id,
            ':mid' => Yii::app()->user->getId(),
        );

        $member = DialogMember::model()->find($criteria);
        if ($member) {
            $message = new DialogMessage();
            $message->dialog_id = $id;
            $message->author_id = Yii::app()->user->getId();
            $message->message = $_POST['msg'];

            if ($message->save()) {
                $dialogMembers = DialogMember::model()->findAll('dialog_id = :id', array(':id' => $id));

                /** @var $member DialogMember */
                $selfRecipient = 0;
                foreach ($dialogMembers as $member) {
                    // Можно отправлять самому себе сообщения
                    if ($member->member_id == Yii::app()->user->getId()) {
                        $selfRecipient++;
                    }
                    else {
                        $request = new ProfileRequest();
                        $request->owner_id = $member->member_id;
                        $request->req_type = ProfileRequest::TYPE_PM;
                        $request->req_link_id = $message->message_id;
                        $request->save();
                    }
                }

                $success = true;
                $msg = '';
            }
            else {
                $success = false;
                $msg = 'Ошибка при отправке сообщения';
            }
        }
        else {
            $success = false;
            $msg = 'Вы не состоите в этом диалоге';
        }

        echo json_encode(array('success' => $success, 'message' => $msg));
        exit;
    }

    public function actionPeer($id, $timestamp) {
        $criteria = new CDbCriteria();
        $criteria->addCondition('dialog_id = :id');
        $criteria->addCondition('member_id = :mid');

        $criteria->params = array(
            ':id' => $id,
            ':mid' => Yii::app()->user->getId(),
        );

        $member = DialogMember::model()->find($criteria);
        if ($member) {
            $criteria = new CDbCriteria();
            $criteria->addCondition('dialog_id = :id');
            $criteria->addCondition('creation_date > :date');
            $criteria->params[':id'] = $id;
            $criteria->params[':date'] = date("Y-m-d H:i:s", $timestamp);

            $criteria->order = 'creation_date DESC';

            $messages = DialogMessage::model()->with('author', array('isNewIn' => array('joinType' => 'LEFT JOIN')), array('isNewOut' => array('joinType' => 'LEFT JOIN')))->findAll($criteria);
            $messages = array_reverse($messages);

            $html = $this->renderPartial('_im', array(
                'messages' => $messages,
            ), true);

            echo json_encode(array('success' => true, 'html' => $html, 'counters' => $this->pageCounters));
        }
        else {
            echo json_encode(array('success' => false, 'message' => 'Вы не состоите в этом диалоге'));
        }

        exit;
    }

    public function actionViewed($id) {
        $criteria = new CDbCriteria();
        $criteria->addCondition('req_type = :type');
        $criteria->addCondition('req_link_id = :id');
        $criteria->addCondition('owner_id = :owner');

        $criteria->params = array(
            ':type' => ProfileRequest::TYPE_PM,
            ':id' => $id,
            ':owner' => Yii::app()->user->getId(),
        );
        $request = ProfileRequest::model()->find($criteria);
        if ($request && $request->delete()) {
            $this->pageCounters['pm']--;
            $success = true;
        }
        else $success = false;

        echo json_encode(array('success' => $success, 'counters' => $this->pageCounters));
        exit;
    }
}