<?php

/**
 * This is the model class for table "orders_oic".
 *
 * The followings are the available columns in table 'orders_oic':
 * @property string $pk
 * @property string $purchase_id
 * @property integer $customer_id
 * @property string $oic_name
 * @property string $oic_price
 * @property integer $payed
 */
class OrderOic extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return OrderOic the static model class
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
		return 'orders_oic';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('purchase_id, customer_id, oic_name, oic_price', 'required'),
			array('customer_id', 'numerical', 'integerOnly'=>true),
			array('purchase_id', 'length', 'max'=>10),
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
			'pk' => 'Pk',
			'purchase_id' => 'Purchase',
			'customer_id' => 'Customer',
			'oic_id' => 'Oic',
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

		$criteria->compare('pk',$this->pk,true);
		$criteria->compare('purchase_id',$this->purchase_id,true);
		$criteria->compare('customer_id',$this->customer_id);
		$criteria->compare('oic_id',$this->oic_id,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}