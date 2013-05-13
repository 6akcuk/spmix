<?php

/**
 * This is the model class for table "orders_sms_delivery_logs".
 *
 * The followings are the available columns in table 'orders_sms_delivery_logs':
 * @property string $log_id
 * @property integer $delivery_id
 * @property string $phone
 * @property integer $status
 * @property string $message_id
 *
 * @property OrderSmsDelivery $delivery
 */
class OrderSmsDeliveryLog extends CActiveRecord
{
  const STATUS_QUEUE = 0;
  const STATUS_JOB = 1;
  const STATUS_SENDED = 2;
  const STATUS_RECEIVED = 3;
  const STATUS_REJECTED = 4;

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return OrderSmsDeliveryLog the static model class
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
		return 'orders_sms_delivery_logs';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('delivery_id, phone, status, message_id', 'required'),
			array('delivery_id, status', 'numerical', 'integerOnly'=>true),
			array('phone', 'length', 'max'=>20),
			array('message_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('log_id, delivery_id, phone, status, message_id', 'safe', 'on'=>'search'),
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
      'delivery' => array(self::BELONGS_TO, 'OrderSmsDelivery', 'delivery_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'log_id' => 'Log',
			'delivery_id' => 'Delivery',
			'phone' => 'Phone',
			'status' => 'Status',
			'message_id' => 'Message',
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

		$criteria->compare('log_id',$this->log_id,true);
		$criteria->compare('delivery_id',$this->delivery_id);
		$criteria->compare('phone',$this->phone,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('message_id',$this->message_id,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

  public function getStatus() {
    switch ($this->status) {
      case self::STATUS_QUEUE:
        return 'В очереди';
        break;
      case self::STATUS_JOB:
        return 'В обработке';
        break;
      case self::STATUS_SENDED:
        return 'Отправлено';
        break;
      case self::STATUS_RECEIVED:
        return 'Получено';
        break;
      case self::STATUS_REJECTED:
        return 'Отклонено';
        break;
    }
  }
}