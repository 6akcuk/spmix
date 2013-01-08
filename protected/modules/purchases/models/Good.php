<?php

/**
 * This is the model class for table "goods".
 *
 * The followings are the available columns in table 'goods':
 * @property string $good_id
 * @property integer $purchase_id
 * @property integer $is_quick
 * @property integer $is_range
 * @property string $name
 * @property string $price
 * @property string $currency
 * @property string $description
 * @property string $artikul
 * @property string $url
 * @property string $good_delete
 *
 * @property Purchase $purchase
 * @property array $grid
 * @property array $ranges
 * @property GoodImages $image
 * @property array $images
 * @property array $oic
 * @property array $orders
 * @property integer $ordersNum
 * @property float $ordersSum
 */
class Good extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Good the static model class
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
		return 'goods';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('purchase_id, name, price, currency', 'required', 'on' => 'create'),
            array('purchase_id, name, artikul, price, is_quick', 'required', 'on' => 'quick'),
			array('purchase_id, is_quick', 'numerical', 'integerOnly'=>true),
            array('is_range, description', 'safe'),
			array('name', 'length', 'max'=>100),
			array('price', 'length', 'max'=>10),
			array('currency', 'length', 'max'=>3),
			array('artikul', 'length', 'max'=>50),
			array('url', 'length', 'max'=>200),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('good_id, purchase_id, name, price, currency, description, artikul, url, sizes, colors', 'safe', 'on'=>'search'),
		);
	}

    public function defaultScope() {
        return array(
            'condition' => 'good_delete IS NULL',
        );
    }

    public function scopes() {
        return array(
            'quick' => array(
                'condition' => 'is_quick = 0'
            )
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
            'purchase' => array(self::BELONGS_TO, 'Purchase', 'purchase_id'),
            'grid' => array(self::HAS_MANY, 'GoodGrid', array('good_id' => 'good_id'), 'order' => 'size'),
            'ranges' => array(self::HAS_MANY, 'GoodRange', array('good_id' => 'good_id')),
            'image' => array(self::HAS_ONE, 'GoodImages', array('good_id' => 'good_id')),
            'images' => array(self::HAS_MANY, 'GoodImages', array('good_id' => 'good_id')),
            'oic' => array(self::HAS_MANY, 'PurchaseOic', array('purchase_id' => 'purchase_id')),
            'orders' => array(self::HAS_MANY, 'Order', 'good_id'),
            'ordersNum' => array(self::STAT, 'Order', 'good_id'),
            'ordersSum' => array(self::STAT, 'Order', 'good_id', 'select' => 'SUM(total_price)'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'good_id' => 'Good',
			'purchase_id' => 'Purchase',
            'is_range' => 'Использовать ряды',
			'name' => 'Название',
			'price' => 'Цена',
			'currency' => 'Валюта',
			'description' => 'Описание',
			'artikul' => 'Артикул',
			'url' => 'URL',
			'sizes' => 'Размер',
			'colors' => 'Цвет',
		);
	}

    public function countImages()
    {
        return GoodImages::model()->count('good_id = :good_id', array(':good_id' => $this->good_id));
    }
}