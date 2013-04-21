<?php

/**
 * This is the model class for table "feed".
 *
 * The followings are the available columns in table 'feed':
 * @property string $feed_id
 * @property string $add_date
 * @property string $owner_type
 * @property string $owner_id
 * @property string $event_type
 * @property string $event_link_id
 * @property string $event_text
 */
class Feed extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Feed the static model class
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
		return 'feed';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('owner_type, owner_id, event_type, event_link_id', 'required'),
			array('owner_type, event_type', 'length', 'max'=>40),
			array('owner_id, event_link_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('feed_id, add_date, owner_type, owner_id, event_type, event_link_id', 'safe', 'on'=>'search'),
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
			'feed_id' => 'Feed',
			'add_date' => 'Add Date',
			'owner_type' => 'Owner Type',
			'owner_id' => 'Owner',
			'event_type' => 'Event Type',
			'event_link_id' => 'Event Link',
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

		$criteria->compare('feed_id',$this->feed_id,true);
		$criteria->compare('add_date',$this->add_date,true);
		$criteria->compare('owner_type',$this->owner_type,true);
		$criteria->compare('owner_id',$this->owner_id,true);
		$criteria->compare('event_type',$this->event_type,true);
		$criteria->compare('event_link_id',$this->event_link_id,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

  public function beforeSave() {
    if (parent::beforeSave()) {
      if ($this->getIsNewRecord())
        $this->add_date = date("Y-m-d H:i:s");

      return true;
    }
    else return false;
  }
}