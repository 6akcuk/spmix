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
 * @property string $min_sum
 * @property integer $min_num
 * @property string $supplier_url
 * @property string $price_url
 * @property string $message
 * @property string $org_tax
 * @property string $image
 * @property integer $vip
 * @property integer $mod_confirmation
 * @property string $mod_reason
 * @property string $sizes
 *
 * @property City $city
 * @property Profile $author
 * @property PurchaseCategory $category
 * @property PurchaseExternal $external
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
            array('name, author_id, category_id, city_id, stop_date, status, image', 'required', 'on' => 'create'),
            array('name, author_id, category_id, city_id, stop_date, status, state, min_sum, min_num, org_tax', 'required', 'on' => 'edit'),

			array('author_id, category_id, city_id, min_num, vip, mod_confirmation', 'numerical', 'integerOnly'=>true),
			array('name, supplier_url, price_url, message, image, mod_reason, sizes', 'length', 'max'=>255),
			array('status', 'length', 'max'=>8),
			array('state', 'length', 'max'=>16),
			array('min_sum', 'length', 'max'=>10),
			array('org_tax', 'length', 'max'=>3),
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
            'author' => array(self::BELONGS_TO, 'Profile', 'author_id'),
            'external' => array(self::BELONGS_TO, 'PurchaseExternal', 'purchase_id'),
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
            'min_sum' => 'Мин. сумма заказа',
            'min_num' => 'Мин. кол-во заказов',
            'supplier_url' => 'Ссылка на сайт поставщика',
            'price_url' => 'Ссылка на прайс',
            'message' => 'Оповещение',
            'org_tax' => '% наценки организатора',
            'image' => 'Аватар',
            'vip' => 'VIP',
            'mod_confirmation' => 'Подтверждение модератора',
            'mod_reason' => 'Причина отказа',
            'sizes' => 'Размеры',
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

		$criteria->compare('purchase_id',$this->purchase_id,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('author',$this->author);
		$criteria->compare('category_id',$this->category_id);
		$criteria->compare('city_id',$this->city_id);
		$criteria->compare('create_date',$this->create_date,true);
		$criteria->compare('stop_date',$this->stop_date,true);
		$criteria->compare('status',$this->status,true);
		$criteria->compare('state',$this->state,true);
		$criteria->compare('min_sum',$this->min_sum,true);
		$criteria->compare('min_num',$this->min_num);
		$criteria->compare('supplier_url',$this->supplier_url,true);
		$criteria->compare('price_url',$this->price_url,true);
		$criteria->compare('message',$this->message,true);
		$criteria->compare('org_tax',$this->org_tax,true);
		$criteria->compare('image',$this->image,true);
		$criteria->compare('vip',$this->vip);
		$criteria->compare('mod_confirmation',$this->mod_confirmation);
		$criteria->compare('mod_reason',$this->mod_reason,true);
		$criteria->compare('sizes',$this->sizes,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

    public static function getStatusDataArray() {
        return array(
            'Минимум' => self::STATUS_MINIMUM,
            'Стандарт' => self::STATUS_STANDARD,
            'Vip' => self::STATUS_VIP,
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