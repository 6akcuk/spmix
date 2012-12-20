<?php

/**
 * This is the model class for table "orders_payments".
 *
 * The followings are the available columns in table 'orders_payments':
 * @property string $payment_id
 * @property string $order_id
 * @property integer $payer_id
 * @property string $datetime
 * @property string $status
 * @property string $pay_id
 * @property string $description
 *
 * @property Order $order
 * @property ProfilePaydetail $paydetails
 */
class OrderPayment extends CActiveRecord
{
    const STATUS_AWAITING = 'Awaiting payment';
    const STATUS_PERFORMED = 'Performed payment';

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return OrderPayment the static model class
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
		return 'orders_payments';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('order_id, pay_id, description', 'required', 'on' => 'create'),
			array('order_id, pay_id', 'length', 'max'=>10),
			array('status', 'length', 'max'=>9),
			array('description', 'length', 'max'=>200),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('payment_id, order_id, datetime, status, pay_id, description', 'safe', 'on'=>'search'),
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
            'order' => array(self::BELONGS_TO, 'Order', 'order_id'),
            'paydetails' => array(self::HAS_ONE, 'ProfilePaydetail', 'pay_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'payment_id' => 'Payment',
			'order_id' => 'Order',
			'datetime' => 'Дата платежа',
			'status' => 'Статус',
			'pay_id' => 'Реквизиты',
			'description' => 'Информация о платеже',
		);
	}

    public function beforeSave() {
        if (parent::beforeSave()) {
            if ($this->getIsNewRecord()) {
                $this->datetime = date("Y-m-d H:i:s");
                $this->status = self::STATUS_AWAITING;
            }

            return true;
        }
        else return false;
    }
}