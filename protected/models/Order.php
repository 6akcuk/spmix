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
 * @property string $size
 * @property string $color
 * @property integer $amount
 * @property string $price
 * @property string $total_price
 * @property string $client_comment
 * @property string $org_comment
 * @property string $status
 * @property string $oic
 * @property integer $anonymous
 *
 * @property Good $good
 */
class Order extends CActiveRecord
{
    const STATUS_AWAITING = 'Awaiting';
    const STATUS_CANCELED = 'Canceled';
    const STATUS_PAID = 'Paid';

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
			array('purchase_id, good_id, customer_id, size, color, amount, price, total_price', 'required', 'on' => 'create'),
			array('purchase_id, good_id, customer_id, amount, anonymous', 'numerical', 'integerOnly'=>true),
			array('price, total_price', 'length', 'max'=>10),
			array('client_comment, org_comment', 'length', 'max'=>200),
			array('status', 'length', 'max'=>8),
			array('oic', 'length', 'max'=>100),
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
            'good' => array(self::BELONGS_TO, 'Good', 'good_id'),
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
            'size' => 'Размер',
            'color' => 'Цвет',
			'amount' => 'Количество',
			'price' => 'Цена',
			'total_price' => 'Итог. цена',
			'client_comment' => 'Комментарий для организатора',
			'org_comment' => 'Комментарий организатора',
			'status' => 'Статус',
			'oic' => 'ЦВЗ',
			'anonymous' => 'Анонимно',
		);
	}

    public function beforeSave() {
        if (parent::beforeSave()) {
            if ($this->isNewRecord) {
                $this->creation_date = date("Y-m-d H:i:s");
                $this->status = self::STATUS_AWAITING;
            }

            return true;
        }
        else return false;
    }
}