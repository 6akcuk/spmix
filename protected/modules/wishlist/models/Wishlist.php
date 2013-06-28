<?php

/**
 * This is the model class for table "wishlists".
 *
 * The followings are the available columns in table 'wishlists':
 * @property integer $wishlist_id
 * @property integer $city_id
 * @property integer $author_id
 * @property integer $type
 * @property string $add_date
 * @property string $shortstory
 */
class Wishlist extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Wishlist the static model class
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
		return 'wishlists';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('city_id, author_id, type, add_date, shortstory', 'required'),
			array('city_id, author_id, type', 'numerical', 'integerOnly'=>true),
			array('shortstory', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('wishlist_id, city_id, author_id, type, add_date, shortstory', 'safe', 'on'=>'search'),
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
			'wishlist_id' => 'Wishlist',
			'city_id' => 'City',
			'author_id' => 'Author',
			'type' => 'Type',
			'add_date' => 'Add Date',
			'shortstory' => 'Shortstory',
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

		$criteria->compare('wishlist_id',$this->wishlist_id);
		$criteria->compare('city_id',$this->city_id);
		$criteria->compare('author_id',$this->author_id);
		$criteria->compare('type',$this->type);
		$criteria->compare('add_date',$this->add_date,true);
		$criteria->compare('shortstory',$this->shortstory,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}