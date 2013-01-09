<?php

/**
 * This is the model class for table "dialogs".
 *
 * The followings are the available columns in table 'dialogs':
 * @property string $dialog_id
 * @property integer $leader_id
 * @property integer $type
 * @property string $title
 *
 * @property User $leader
 * @property array|DialogMember|null $members
 * @property array|DialogMessage|null $messages
 * @property DialogMessage $lastMessage
 */
class Dialog extends CActiveRecord
{
    const TYPE_TET = 0;
    const TYPE_CONFERENCE = 1;

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Dialog the static model class
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
		return 'dialogs';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('leader_id, type', 'required'),
			array('leader_id, type', 'numerical', 'integerOnly'=>true),
			array('dialog_id', 'length', 'max'=>10),
			array('title', 'length', 'max'=>100),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('dialog_id, leader_id, type, title', 'safe', 'on'=>'search'),
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
            'leader' => array(self::BELONGS_TO, 'User', 'leader_id'),
            'member' => array(self::HAS_ONE, 'DialogMember', 'dialog_id'),
            'members' => array(self::HAS_MANY, 'DialogMember', 'dialog_id'),
            'lastMessage' => array(self::HAS_ONE, 'DialogMessage', 'dialog_id', 'order' => 'creation_date DESC'),
            'messages' => array(self::HAS_MANY, 'DialogMessage', 'dialog_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'dialog_id' => 'Dialog',
			'leader_id' => 'Leader',
			'type' => 'Type',
			'title' => 'Title',
		);
	}
}