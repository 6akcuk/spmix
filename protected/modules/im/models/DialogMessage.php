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
			array('dialog_id, creation_date, author_id, message', 'required'),
			array('author_id', 'numerical', 'integerOnly'=>true),
			array('message_id', 'length', 'max'=>20),
			array('dialog_id', 'length', 'max'=>10),
			array('attaches, message_delete', 'safe'),
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
}