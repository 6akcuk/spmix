<?php

/**
 * This is the model class for table "sites_list".
 *
 * The followings are the available columns in table 'sites_list':
 * @property string $id
 * @property integer $city_id
 * @property string $site
 * @property integer $purchase_id
 * @property integer $author_id
 * @property string $shortstory
 * @property string $datetime
 *
 * @property User $author
 */
class SiteList extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return SiteList the static model class
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
		return 'sites_list';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('city_id, site, author_id, shortstory', 'required'),
			array('city_id, purchase_id, author_id', 'numerical', 'integerOnly'=>true),
			array('site, shortstory', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, city_id, site, purchase_id, author_id, datetime', 'safe', 'on'=>'search'),
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
      'author' => array(self::BELONGS_TO, 'User', 'author_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'city_id' => 'Город',
			'site' => 'Сайт',
			'purchase_id' => 'Закупка',
			'author_id' => 'Author',
      'shortstory' => 'Краткое описание',
			'datetime' => 'Datetime',
		);
	}

  public function beforeSave() {
    if (parent::beforeSave()) {
      $this->datetime = date("Y-m-d H:i:s");
      return true;
    }
    else return false;
  }
}