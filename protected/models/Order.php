<?php

/**
 * This is the model class for table "orders".
 *
 * The followings are the available columns in table 'orders':
 * @property integer $order_id
 * @property integer $purchase_id
 * @property integer $good_id
 * @property integer $customer_id
 * @property string $creation_date
 * @property integer $amount
 * @property string $price
 * @property string $total_price
 * @property string $client_comment
 * @property string $org_comment
 * @property string $status
 * @property string $oic
 * @property integer $anonymous
 */
class Order extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Order the static model class
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
		return 'orders';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('purchase_id, good_id, customer_id, creation_date, amount, price, total_price, client_comment, org_comment, status, oic, anonymous', 'required'),
			array('purchase_id, good_id, customer_id, amount, anonymous', 'numerical', 'integerOnly'=>true),
			array('price, total_price', 'length', 'max'=>10),
			array('client_comment, org_comment', 'length', 'max'=>200),
			array('status', 'length', 'max'=>8),
			array('oic', 'length', 'max'=>100),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('order_id, purchase_id, good_id, customer_id, creation_date, amount, price, total_price, client_comment, org_comment, status, oic, anonymous', 'safe', 'on'=>'search'),
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
			'order_id' => 'Order',
			'purchase_id' => 'Purchase',
			'good_id' => 'Good',
			'customer_id' => 'Customer',
			'creation_date' => 'Creation Date',
			'amount' => 'Amount',
			'price' => 'Price',
			'total_price' => 'Total Price',
			'client_comment' => 'Client Comment',
			'org_comment' => 'Org Comment',
			'status' => 'Status',
			'oic' => 'Oic',
			'anonymous' => 'Anonymous',
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

		$criteria->compare('order_id',$this->order_id);
		$criteria->compare('purchase_id',$this->purchase_id);
		$criteria->compare('good_id',$this->good_id);
		$criteria->compare('customer_id',$this->customer_id);
		$criteria->compare('creation_date',$this->creation_date,true);
		$criteria->compare('amount',$this->amount);
		$criteria->compare('price',$this->price,true);
		$criteria->compare('total_price',$this->total_price,true);
		$criteria->compare('client_comment',$this->client_comment,true);
		$criteria->compare('org_comment',$this->org_comment,true);
		$criteria->compare('status',$this->status,true);
		$criteria->compare('oic',$this->oic,true);
		$criteria->compare('anonymous',$this->anonymous);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}