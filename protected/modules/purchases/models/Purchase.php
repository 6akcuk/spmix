<?php

/**
 * This is the model class for table "purchases".
 *
 * The followings are the available columns in table 'purchases':
 * @property string $purchase_id
 * @property string $name
 * @property integer $author_id
 * @property integer $category_id
 * @property integer $city_id
 * @property string $create_date
 * @property string $stop_date
 * @property string $status
 * @property string $state
 * @property integer $accept_add
 * @property string $min_sum
 * @property integer $min_num
 * @property string $supplier_url
 * @property integer $hide_supplier
 * @property string $price_url
 * @property string $message
 * @property string $org_tax
 * @property string $image
 * @property integer $vip
 * @property integer $mod_request_id
 * @property integer $mod_confirmation
 * @property string $purchase_delete
 *
 * @property City $city
 * @property User $author
 * @property PurchaseModRequest $mod_request
 * @property PurchaseCategory $category
 * @property PurchaseExternal $external *
 * @property array $history
 * @property array $oic
 *
 * @property string $ordersNum
 * @property string $ordersSum
 * @property string $goodsNum
 */
class Purchase extends CActiveRecord
{
    const STATUS_MINIMUM = 'Minimum';
    const STATUS_STANDARD = 'Standard';
    const STATUS_VIP = 'Vip';

    const STATE_DRAFT = 'Draft';
    const STATE_CALL_STUDY = 'Call Study';
    const STATE_ORDER_COLLECTION = 'Order Collection';
    const STATE_STOP = 'Stop';
    const STATE_REORDER = 'Reorder';
    const STATE_PAY = 'Pay';
    const STATE_CARGO_FORWARD = 'Cargo Forward';
    const STATE_DISTRIBUTION = 'Distribution';
    const STATE_COMPLETED = 'Completed';

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Purchase the static model class
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
		return 'purchases';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
            array('name, author_id, category_id, city_id, status', 'required', 'on' => 'create'),
            array('name, author_id, category_id, status, state, min_sum, min_num, org_tax', 'required', 'on' => 'edit_own_notconfirmed'),
            array('author_id, status, state, min_sum, min_num', 'required', 'on' => 'edit_own_confirmed'),
            array('mod_confirmation', 'safe', 'on' => 'edit_super_admin, edit_super_moderator'),

            array('image, hide_supplier, stop_date', 'safe'),
			array('author_id, category_id, city_id, accept_add, min_num, vip, mod_confirmation', 'numerical', 'integerOnly'=>true),
			array('price_url, message', 'length', 'max'=>255),
            array('name, supplier_url', 'length', 'max' => 255, 'on' => 'edit_own_notconfirmed, edit_super_admin, edit_super_moderator'),
			array('status', 'length', 'max'=>8),
			array('state', 'length', 'max'=>16),
			array('min_sum', 'length', 'max'=>10),
			array('org_tax', 'length', 'max'=>4, 'on' => 'edit_own_notconfirmed, edit_super_admin, edit_super_moderator'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('purchase_id, name, author_id, category_id, city_id, create_date, stop_date, status, state, min_sum, min_num, supplier_url, price_url, message, org_tax, image, vip, mod_confirmation, mod_reason, sizes', 'safe', 'on'=>'search'),
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
            'category' => array(self::BELONGS_TO, 'PurchaseCategory', 'category_id'),
            'author' => array(self::BELONGS_TO, 'User', 'author_id'),
            'mod_request' => array(self::BELONGS_TO, 'PurchaseModRequest', 'mod_request_id', 'order' => 'mod_request.request_date DESC'),
            'external' => array(self::BELONGS_TO, 'PurchaseExternal', 'purchase_id'),
            'history' => array(self::HAS_MANY, 'PurchaseHistory', 'purchase_id', 'order' => 'history.datetime DESC'),
            'oic' => array(self::HAS_MANY, 'PurchaseOic', 'purchase_id'),
            'orders' => array(self::HAS_MANY, 'Order', 'purchase_id'),
            'ordersNum' => array(self::STAT, 'Order', 'purchase_id'),
            'ordersSum' => array(self::STAT, 'Order', 'purchase_id', 'select' => 'SUM(total_price)'),
            'goodsNum' => array(self::STAT, 'Good', 'purchase_id', 'condition' => 'is_quick = 0'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
        return array(
            'purchase_id' => 'ID закупки',
            'name' => 'Название',
            'category_id' => 'Категория',
            'city_id' => 'Город',
            'create_date' => 'Дата создания',
            'stop_date' => 'Дата окончания (стопа)',
            'status' => 'Статус',
            'state' => 'Статус закупки',
            'accept_add' => 'Разрешить добавление товаров пользователями',
            'min_sum' => 'Мин. сумма заказа',
            'min_num' => 'Мин. кол-во заказов',
            'supplier_url' => 'Ссылка на сайт поставщика',
            'hide_supplier' => 'Скрыть сайт поставщика',
            'price_url' => 'Ссылка на прайс',
            'message' => 'Оповещение',
            'org_tax' => '% наценки организатора',
            'image' => 'Аватар',
            'vip' => 'VIP',
            'mod_confirmation' => 'Подтверждение модератора',
            'mod_reason' => 'Сообщение организатору',
            'sizes' => 'Размеры',
        );
	}

    public function defaultScope() {
        return array(
            'condition' => "purchase_delete IS NULL",
        );
    }

    public function getMinimalPercentage() {
        $sum_perc = ceil((floatval($this->min_sum) > 0) ? ($this->ordersSum / $this->min_sum) * 100 : 0);
        $num_perc = ceil((floatval($this->min_num) > 0) ? ($this->ordersNum / $this->min_num) * 100 : 0);
        return ($num_perc > $sum_perc) ? $num_perc : $sum_perc;
    }

    public function getPriceWithTax($price) {
        return floatval($price) * ($this->org_tax / 100 + 1);
    }

    public static function getStatusDataArray() {
        return array(
            'Минимум' => self::STATUS_MINIMUM,
            'Стандарт' => self::STATUS_STANDARD,
            'Vip' => self::STATUS_VIP,
        );
    }

    public static function getNonConfirmedStateArray() {
      return array(
        'Черновик' => self::STATE_DRAFT,
        'Изучение спроса' => self::STATE_CALL_STUDY,
        'Сбор заказов' => self::STATE_ORDER_COLLECTION,
      );
    }

    public static function getStateDataArray() {
        return array(
            'Черновик' => self::STATE_DRAFT,
            'Изучение спроса' => self::STATE_CALL_STUDY,
            'Сбор заказов' => self::STATE_ORDER_COLLECTION,
            'Приостановлено' => self::STATE_STOP,
            'Дозаказ' => self::STATE_REORDER,
            'Оплата' => self::STATE_PAY,
            'Ждем груз' => self::STATE_CARGO_FORWARD,
            'Раздача' => self::STATE_DISTRIBUTION,
            'Завершено' => self::STATE_COMPLETED,
        );
    }
    public static function getStateSearchArray() {
        return array(
            'Изучение спроса' => self::STATE_CALL_STUDY,
            'Сбор заказов' => self::STATE_ORDER_COLLECTION,
            'В работе' => 'Progress',
            'Завершено' => self::STATE_COMPLETED,
        );
    }

    public function beforeSave() {
        if (parent::beforeSave()) {
            if ($this->getIsNewRecord())
                $this->create_date = date("Y-m-d H:i:s");

            return true;
        }
        else return false;
    }
}