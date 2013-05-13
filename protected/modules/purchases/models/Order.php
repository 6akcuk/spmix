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
 * @property integer $org_tax
 * @property string $delivery
 * @property string $total_price
 * @property string $payed
 * @property string $client_comment
 * @property string $org_comment
 * @property string $status
 * @property integer $anonymous
 *
 * @property User $customer
 * @property Good $good
 * @property Purchase $purchase
 * @property OrderPayment $payment
 * @property GoodGrid $grid
 * @property OrderHistory $history
 * @property OrderOic $oic
 */
class Order extends CActiveRecord
{
  const STATUS_PROCEEDING = 'Proceeding';
  const STATUS_REFUSED = 'Refused';
  const STATUS_ACCEPTED = 'Accepted';
  const STATUS_RANGE_ACCEPTED = 'Range Accepted';
  const STATUS_OUT_OF_STOCK = 'Out of Stock';
  const STATUS_DELIVERED = 'Delivered';
  const STATUS_AWAITING = 'Awaiting';
  const STATUS_PAID = 'Paid';
  const STATUS_WAIT_FOR_DELIVER = 'Wait For Deliver';

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
      array('purchase_id, customer_id, amount, price', 'required'),
      array('purchase_id, good_id, customer_id, amount', 'numerical', 'integerOnly' => true),

			array('size, color, client_comment', 'length', 'max' => 200, 'on' => 'create, edit_own, quick'),
      array('anonymous', 'numerical', 'integerOnly' => true, 'on' => 'create, edit_own'),
      array('price, delivery, total_price, status, org_comment, payed, org_tax', 'unsafe', 'on' => 'create, edit_own, quick'),

      array('org_tax', 'numerical', 'integerOnly' => true, 'on' => 'edit_org'),
      array('price, payed, status', 'length', 'max' => 12, 'on' => 'edit_org'),
      array('delivery', 'length', 'max' => 9, 'on' => 'edit_org'),
      array('org_comment', 'length', 'max' => 200, 'on' => 'edit_org'),
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
      'customer' => array(self::BELONGS_TO, 'User', 'customer_id', 'with' => 'profile'),
      'good' => array(self::BELONGS_TO, 'Good', 'good_id', 'condition' => 'good_delete IS NULL'),
      'purchase' => array(self::BELONGS_TO, 'Purchase', 'purchase_id'),
      'payment' => array(self::HAS_ONE, 'OrderPayment', 'order_id'),
      'history' => array(self::HAS_MANY, 'OrderHistory', 'order_id', 'with' => 'author'),
      'oic' => array(self::BELONGS_TO, 'OrderOic', 'purchase_id', 'condition' => 'oic.customer_id = '. Yii::app()->user->getId()),
      'custom_oic' => array(self::BELONGS_TO, 'OrderOic', 'purchase_id', 'condition' => 'custom_oic.customer_id = t.customer_id'),
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
      'org_tax' => 'Орг. сбор',
      'delivery' => 'Стоимость доставки',
			'total_price' => 'Итог. цена',
      'payed' => 'Оплачено',
			'client_comment' => 'Комментарий для организатора',
			'org_comment' => 'Комментарий организатора',
			'status' => 'Статус',
			'oic' => 'Место выдачи',
			'anonymous' => 'Анонимно',
		);
	}

    public function beforeSave() {
        if (parent::beforeSave()) {
            if ($this->isNewRecord) {
                $this->creation_date = date("Y-m-d H:i:s");
                $this->status = self::STATUS_PROCEEDING;
            }

            return true;
        }
        else return false;
    }

    public static function getStatusDataArray() {
        return array(
          'В обработке' => self::STATUS_PROCEEDING,
          'Отказ' => self::STATUS_REFUSED,
          'Принят орг-ом' => self::STATUS_ACCEPTED,
          'Принят орг-ом в ряд' => self::STATUS_RANGE_ACCEPTED,
          'Нет в наличии' => self::STATUS_OUT_OF_STOCK,
          'Ожидание оплаты' => self::STATUS_AWAITING,
          'Оплачен' => self::STATUS_PAID,
          'Ожидание выдачи' => self::STATUS_WAIT_FOR_DELIVER,
          'Получен' => self::STATUS_DELIVERED,
        );
    }

  public function canEdit() {
    if (in_array($this->purchase->state, array(Purchase::STATE_DRAFT, Purchase::STATE_CALL_STUDY)) ||
      (
        $this->purchase->state == Purchase::STATE_ORDER_COLLECTION &&
          in_array($this->status, array(Order::STATUS_PROCEEDING, Order::STATUS_REFUSED, Order::STATUS_ACCEPTED))
      ) ||
      (
        $this->purchase->state == Purchase::STATE_REORDER &&
          in_array($this->status, array(Order::STATUS_PROCEEDING, Order::STATUS_REFUSED))
      )
    )
      return true;
    else
      return false;
  }

  public function canDelete() {
    if (in_array($this->purchase->state, array(Purchase::STATE_DRAFT, Purchase::STATE_CALL_STUDY)) ||
      (
        $this->purchase->state == Purchase::STATE_ORDER_COLLECTION &&
          in_array($this->status, array(Order::STATUS_PROCEEDING, Order::STATUS_REFUSED, Order::STATUS_ACCEPTED))
      ) ||
      (
        in_array($this->purchase->state, array(
          Purchase::STATE_REORDER,
          Purchase::STATE_STOP,
          Purchase::STATE_PAY,
          Purchase::STATE_DISTRIBUTION,
          Purchase::STATE_COMPLETED
        )) &&
          in_array($this->status, array(Order::STATUS_PROCEEDING, Order::STATUS_REFUSED))
      )
    )
      return true;
    else
      return false;
  }

  public function afterSave() {
    if ($this->isNewRecord) {
      $c = new CDbCriteria();
      $c->compare('user_id', Yii::app()->user->getId());
      $c->compare('sub_type', Subscription::TYPE_PURCHASE);
      $c->compare('sub_link_id', $this->purchase_id);

      $sub = Subscription::model()->find($c);
      if (!$sub) {
        $sub = new Subscription();
        $sub->user_id = Yii::app()->user->getId();
        $sub->sub_type = Subscription::TYPE_PURCHASE;
        $sub->sub_link_id = $this->purchase_id;
        $sub->save();
      }
    }
  }
}