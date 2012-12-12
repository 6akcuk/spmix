<?php

/**
 * This is the model class for table "goods".
 *
 * The followings are the available columns in table 'goods':
 * @property string $good_id
 * @property integer $purchase_id
 * @property string $name
 * @property string $price
 * @property string $currency
 * @property string $description
 * @property string $artikul
 * @property string $url
 * @property string $sizes
 * @property string $colors
 * @property string $on_delete
 *
 * @property Purchase $purchase
 * @property GoodImages $image
 * @property array $images
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
			array('purchase_id, name, price, currency', 'required'),
			array('purchase_id', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>100),
			array('price', 'length', 'max'=>10),
			array('currency', 'length', 'max'=>3),
			array('artikul', 'length', 'max'=>50),
			array('url, sizes, colors', 'length', 'max'=>200),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('good_id, purchase_id, name, price, currency, description, artikul, url, sizes, colors', 'safe', 'on'=>'search'),
		);
	}

    public function defaultScope() {
        return array(
            'condition' => 't.on_delete IS NULL',
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
            'image' => array(self::HAS_ONE, 'GoodImages', array('good_id' => 'good_id')),
            'images' => array(self::HAS_MANY, 'GoodImages', array('good_id' => 'good_id')),
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
			'name' => 'Название',
			'price' => 'Цена',
			'currency' => 'Валюта',
			'description' => 'Описание',
			'artikul' => 'Артикул',
			'url' => 'URL',
			'sizes' => 'Размеры',
			'colors' => 'Цвета',
		);
	}

    public function countImages()
    {
        return GoodImages::model()->count('good_id = :good_id', array(':good_id' => $this->good_id));
    }
}