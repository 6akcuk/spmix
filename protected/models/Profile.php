<?php

/**
 * This is the model class for table "profiles".
 *
 * The followings are the available columns in table 'profiles':
 * @property integer $user_id
 * @property string $lastname
 * @property string $firstname
 * @property string $middlename
 * @property string $phone
 * @property string $photo
 * @property integer $city_id
 * @property integer $positive_rep
 * @property integer $negative_rep
 * @property string $status
 * @property integer $email_notify
 * @property integer $sms_notify
 * @property string $about
 * @property string $gender
 * @property City $city
 */
class Profile extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Profile the static model class
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
		return 'profiles';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('user_id, lastname, firstname, middlename, phone, city_id, gender', 'required'),
			array('user_id, city_id, positive_rep, negative_rep, email_notify, sms_notify', 'numerical', 'integerOnly'=>true),
			array('lastname, photo', 'length', 'max'=>128),
			array('firstname, middlename', 'length', 'max'=>64),
			array('phone', 'length', 'max'=>32),
			array('status, about', 'length', 'max'=>255),
			array('gender', 'length', 'max'=>6),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('user_id, lastname, firstname, middlename, phone, photo, city_id, positive_rep, negative_rep, status, email_notify, sms_notify, about, gender', 'safe', 'on'=>'search'),
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
            'city' => array(self::HAS_ONE, 'City', array('id' => 'city_id')),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'user_id' => 'User',
			'lastname' => 'Lastname',
			'firstname' => 'Firstname',
			'middlename' => 'Middlename',
			'phone' => 'Phone',
			'photo' => 'Photo',
			'city_id' => 'City',
			'positive_rep' => 'Positive Rep',
			'negative_rep' => 'Negative Rep',
			'status' => 'Status',
			'email_notify' => 'Email Notify',
			'sms_notify' => 'Sms Notify',
			'about' => 'About',
			'gender' => 'Gender',
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

		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('lastname',$this->lastname,true);
		$criteria->compare('firstname',$this->firstname,true);
		$criteria->compare('middlename',$this->middlename,true);
		$criteria->compare('phone',$this->phone,true);
		$criteria->compare('photo',$this->photo,true);
		$criteria->compare('city_id',$this->city_id);
		$criteria->compare('positive_rep',$this->positive_rep);
		$criteria->compare('negative_rep',$this->negative_rep);
		$criteria->compare('status',$this->status,true);
		$criteria->compare('email_notify',$this->email_notify);
		$criteria->compare('sms_notify',$this->sms_notify);
		$criteria->compare('about',$this->about,true);
		$criteria->compare('gender',$this->gender,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}