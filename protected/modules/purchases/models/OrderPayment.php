<?php

/**
 * This is the model class for table "orders_payments".
 *
 * The followings are the available columns in table 'orders_payments':
 * @property string $payment_id
 * @property integer $payer_id
 * @property string $datetime
 * @property string $status
 * @property string $sum
 * @property string $pay_id
 * @property string $description
 *
 * @property Order $order
 * @property User $payer
 * @property ProfilePaydetail $paydetails
 */
class OrderPayment extends CActiveRecord
{
  const STATUS_AWAITING = 'Awaiting payment';
  const STATUS_PERFORMED = 'Performed payment';
  const STATUS_REFUSED = 'Refused payment';

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
			array('sum, description', 'required', 'on' => 'create'),
			array('pay_id', 'length', 'max'=>10),
			array('status', 'length', 'max'=>20),
			array('description', 'length', 'max'=>200),
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
      'orders' => array(self::HAS_MANY, 'OrderPaymentLink', 'payment_id'),
      'payer' => array(self::BELONGS_TO, 'User', 'payer_id'),
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

    public static function getStatusArray() {
        return array(
            Yii::t('purchase', self::STATUS_AWAITING) => self::STATUS_AWAITING,
            Yii::t('purchase', self::STATUS_PERFORMED) => self::STATUS_PERFORMED,
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

  public static function getAllPaymentsForOrg($org_id, $offset = 0, $c = array()) {
    $result = array();

    $command = Yii::app()->db->createCommand("
SELECT *, t.datetime AS t_datetime, t.status AS t_status FROM `orders_payments` AS t
  INNER JOIN `orders_payment_link` AS link ON link.payment_id = t.payment_id
  INNER JOIN `orders` AS `order` ON `order`.order_id = link.order_id
  INNER JOIN `purchases` AS purchase ON purchase.purchase_id = `order`.purchase_id
  INNER JOIN `users` AS user ON user.id = t.payer_id
  INNER JOIN `profiles` AS profile ON profile.user_id = user.id
WHERE purchase.author_id = ". $org_id ."
GROUP BY t.payment_id
ORDER BY t.datetime DESC
LIMIT ". $offset .", ". Yii::app()->getModule('purchases')->paymentsPerPage);

    $dataReader = $command->query();
    while (($row = $dataReader->read()) !== false) {
      $p = new OrderPayment();
      $p->payment_id = $row['payment_id'];
      $p->payer_id = $row['payer_id'];
      $p->datetime = $row['t_datetime'];
      $p->sum = $row['sum'];
      $p->status = $row['t_status'];
      $p->description = $row['description'];

      $p->payer = new User();
      $p->payer->id = $row['user_id'];
      $p->payer->login = $row['login'];

      $p->payer->profile = new Profile();
      $p->payer->profile->firstname = $row['firstname'];
      $p->payer->profile->lastname = $row['lastname'];

      $result[] = $p;
    }

    return $result;
  }

  public static function countAllPaymentsForOrg($org_id) {
    $command = Yii::app()->db->createCommand("
SELECT COUNT(*) AS num FROM `orders_payments` AS t
  INNER JOIN `orders_payment_link` AS link ON link.payment_id = t.payment_id
  INNER JOIN `orders` AS `order` ON `order`.order_id = link.order_id
  INNER JOIN `purchases` AS purchase ON purchase.purchase_id = `order`.purchase_id
WHERE purchase.author_id = ". $org_id ."
GROUP BY t.payment_id");

    $row = $command->queryRow();
    return $row['num'];
  }
}