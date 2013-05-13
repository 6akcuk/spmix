<?php

/**
 * This is the model class for table "orders_sms_deliveries".
 *
 * The followings are the available columns in table 'orders_sms_deliveries':
 * @property integer $delivery_id
 * @property integer $purchase_id
 * @property integer $author_id
 * @property string $add_date
 * @property string $message
 * @property integer $users_num
 * @property integer $sms_num
 */
class OrderSmsDelivery extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return OrderSmsDelivery the static model class
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
		return 'orders_sms_deliveries';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('purchase_id, author_id, message, users_num, sms_num', 'required'),
			array('purchase_id, author_id, users_num, sms_num', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('delivery_id, author_id, add_date, message, users_num, sms_num', 'safe', 'on'=>'search'),
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
			'delivery_id' => 'Delivery',
			'author_id' => 'Author',
			'add_date' => 'Add Date',
			'message' => 'Message',
			'users_num' => 'Users Num',
			'sms_num' => 'Sms Num',
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

		$criteria->compare('delivery_id',$this->delivery_id);
		$criteria->compare('author_id',$this->author_id);
		$criteria->compare('add_date',$this->add_date,true);
		$criteria->compare('message',$this->message,true);
		$criteria->compare('users_num',$this->users_num);
		$criteria->compare('sms_num',$this->sms_num);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

  public function beforeSave() {
    if (parent::beforeSave()) {
      if ($this->isNewRecord)
        $this->add_date = date("Y-m-d H:i:s");

      return true;
    }
    else return false;
  }
}