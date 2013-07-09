<?php

/**
 * This is the model class for table "market_goods".
 *
 * The followings are the available columns in table 'market_goods':
 * @property string $good_id
 * @property integer $author_id
 * @property integer $city_id
 * @property string $add_date
 * @property string $name
 * @property string $description
 * @property string $image
 * @property string $price
 * @property string $currency
 * @property string $size
 * @property string $color
 * @property string $delivery
 * @property string $phone
 * @property integer $is_used
 * @property integer $is_org
 *
 * @property User $author
 */
class MarketGood extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return MarketGood the static model class
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
		return 'market_goods';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('author_id, image, name, price, currency, size, color, phone, is_used, is_org', 'required'),
			array('author_id, is_used, is_org', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>200),
			array('price, delivery', 'length', 'max'=>10),
			array('currency', 'length', 'max'=>3),
			array('size, color', 'length', 'max'=>100),
			array('phone', 'length', 'max'=>20),
      array('description', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('good_id, author_id, add_date, name, description, price, currency, size, color, delivery, phone, is_used, is_org', 'safe', 'on'=>'search'),
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
      'author' => array(self::BELONGS_TO, 'User', 'author_id', 'with' => 'profile'),
      'searchcat' => array(self::HAS_ONE, 'MarketGoodCategory', 'good_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'good_id' => 'Good',
			'author_id' => 'Author',
			'add_date' => 'Add Date',
			'name' => 'Название',
			'description' => 'Описание',
			'price' => 'Цена',
			'currency' => 'Валюта',
			'size' => 'Размер',
			'color' => 'Цвет',
			'delivery' => 'Доставка',
			'phone' => 'Телефон',
			'is_used' => 'Поместить в раздел "Барахолка"',
			'is_org' => 'Is Org',
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

		$criteria->compare('good_id',$this->good_id,true);
		$criteria->compare('author_id',$this->author_id);
		$criteria->compare('add_date',$this->add_date,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('price',$this->price,true);
		$criteria->compare('currency',$this->currency,true);
		$criteria->compare('size',$this->size,true);
		$criteria->compare('color',$this->color,true);
		$criteria->compare('delivery',$this->delivery,true);
		$criteria->compare('phone',$this->phone,true);
		$criteria->compare('is_used',$this->is_used);
		$criteria->compare('is_org',$this->is_org);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

  public function showPrice() {
    return ($this->delivery) ? floatval($this->price + $this->delivery) : $this->price;
  }

  public function beforeSave() {
    if (parent::beforeSave()) {
      if ($this->isNewRecord)
        $this->add_date = date("Y-m-d H:i:s");

      return true;
    }
    else return false;
  }
}