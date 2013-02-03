<?php

/**
 * This is the model class for table "dialog_messages".
 *
 * The followings are the available columns in table 'dialog_messages':
 * @property string $message_id
 * @property string $dialog_id
 * @property string $creation_date
 * @property integer $author_id
 * @property string $message
 * @property string $attaches
 * @property string $message_delete
 *
 * @property User $author
 * @property Dialog $dialog
 * @property ProfileRequest $isNew
 * @property ProfileRequest $isNewIn
 * @property ProfileRequest $isNewOut
 */
class DialogMessage extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return DialogMessage the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'dialog_messages';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('dialog_id, author_id, message', 'required'),
			array('author_id', 'numerical', 'integerOnly'=>true),
			array('message_id', 'length', 'max'=>20),
			array('dialog_id', 'length', 'max'=>10),
			array('creation_date, attaches, message_delete', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('message_id, dialog_id, creation_date, author_id, message, attaches, message_delete', 'safe', 'on'=>'search'),
		);
	}

    public function defaultScope() {
        return array(
            'condition' => 'message_delete IS NULL',
        );
    }

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
            'author' => array(self::BELONGS_TO, 'User', 'author_id'),
            'dialog' => array(self::BELONGS_TO, 'Dialog', 'dialog_id'),
            'isNew' => array(self::HAS_ONE, 'ProfileRequest', 'req_link_id', 'condition' => 'req_type = '. ProfileRequest::TYPE_PM .' AND viewed = 0'),
            'isNewIn' => array(self::HAS_ONE, 'ProfileRequest', 'req_link_id', 'condition' => 'owner_id = '. Yii::app()->user->getId() .' AND req_type = '. ProfileRequest::TYPE_PM .' AND viewed = 0'),
            'isNewOut' => array(self::HAS_ONE, 'ProfileRequest', 'req_link_id', 'condition' => 'owner_id != '. Yii::app()->user->getId() .' AND req_type = '. ProfileRequest::TYPE_PM .' AND viewed = 0'),
        );
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'message_id' => 'Message',
			'dialog_id' => 'Dialog',
			'creation_date' => 'Creation Date',
			'author_id' => 'Author',
			'message' => 'Message',
			'attaches' => 'Attaches',
			'message_delete' => 'Message Delete',
		);
	}

    public function beforeSave() {
        if (parent::beforeSave()) {
            if ($this->isNewRecord)
                $this->creation_date = date("Y-m-d H:i:s");

            return true;
        }
        else return false;
    }

    /**
     * Отправить сообщение (Instant Message)
     *
     * @param $recipients       Получатели
     * @param $message          Текст сообщения
     * @param string $title     Заголовок беседы (конференции)
     * @param array $attaches   Прикрепления к сообщению
     */
    public static function send($recipients, $message, $title = '', $attaches = array()) {
        // Создание беседы (конференции)
        if (sizeof($recipients) > 1) {
            $dialog = new Dialog();
            $dialog->leader_id = Yii::app()->user->getId();
            $dialog->title = $title;
            $dialog->type = Dialog::TYPE_CONFERENCE;
            if (!$dialog->save())
                return array('success' => false, 'message' => 'Не удалось создать беседу');

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
                return array('success' => false, 'message' => 'Собеседники не прошли проверку валидации');
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
              $raw = file_get_contents('/var/log/im_dialog.log');
              if (!$raw) $raw = '';
              $raw .= ":user = ". Yii::app()->user->getId() .", :id = ". $recipients[0] ."\n";
              file_put_contents('/var/log/im_dialog.log', $raw);

                $dialog = new Dialog();
                $dialog->leader_id = Yii::app()->user->getId();
                $dialog->type = Dialog::TYPE_TET;
                if (!$dialog->save())
                    return array('success' => false, 'message' => 'Не удалось создать диалог с пользователем');

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
                    return array('success' => false, 'message' => 'Собеседники не прошли проверку валидации');
                }

                foreach ($dialogMembers as &$member) {
                    $member->save();
                }
            }
            else {
                $dialog = $dialogMembers[0]->dialog;
            }
        }
        else
            return array('success' => false, 'message' => 'Вы не выбрали получателя сообщения');

        $dialogMessage = new DialogMessage();
        $dialogMessage->dialog_id = $dialog->dialog_id;
        $dialogMessage->author_id = Yii::app()->user->getId();
        $dialogMessage->message = $message;
        $dialogMessage->attaches = (sizeof($attaches)) ? json_encode($attaches) : '';

        if (!$dialogMessage->save()) {
            return array('success' => false, 'message' => 'Сообщение не было доставлено');
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

            return array(
              'success' => true,
              'url' => '/im?sel='. $dialog->dialog_id,
              'msg' => 'Ваше сообщение успешно отправлено',
            );
        }
    }
}