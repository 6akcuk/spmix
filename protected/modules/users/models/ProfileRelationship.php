<?php

/**
 * This is the model class for table "profile_relationships".
 *
 * The followings are the available columns in table 'profile_relationships':
 * @property string $rel_id
 * @property integer $from_id
 * @property integer $to_id
 * @property integer $rel_type
 * @property string $message
 *
 * @property User $friend
 */
class ProfileRelationship extends CActiveRecord
{
    const TYPE_OUTCOME = -1;
    const TYPE_INCOME = 0;
    const TYPE_FRIENDS = 1;

    public $friend;

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return ProfileRelationship the static model class
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
		return 'profile_relationships';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('from_id, to_id, rel_type', 'required'),
			array('from_id, to_id, rel_type', 'numerical', 'integerOnly'=>true),
			array('message', 'length', 'max'=>200),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('rel_id, from_id, to_id, rel_type, message', 'safe', 'on'=>'search'),
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'rel_id' => 'Rel',
			'from_id' => 'From',
			'to_id' => 'To',
			'rel_type' => 'Rel Type',
			'message' => 'Message',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('rel_id',$this->rel_id,true);
		$criteria->compare('from_id',$this->from_id);
		$criteria->compare('to_id',$this->to_id);
		$criteria->compare('rel_type',$this->rel_type);
		$criteria->compare('message',$this->message,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}