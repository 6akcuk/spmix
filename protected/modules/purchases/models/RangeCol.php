<?php

/**
 * This is the model class for table "range_cols".
 *
 * The followings are the available columns in table 'range_cols':
 * @property string $col_id
 * @property string $range_id
 * @property string $order_id
 * @property integer $owner_id
 * @property string $size
 * @property string $color
 */
class RangeCol extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return RangeCol the static model class
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
		return 'range_cols';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('range_id, order_id, owner_id', 'required'),
			array('owner_id', 'numerical', 'integerOnly'=>true),
			array('range_id, order_id', 'length', 'max'=>10),
			array('size, color', 'length', 'max'=>200),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('col_id, range_id, order_id, owner_id, size, color', 'safe', 'on'=>'search'),
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
			'col_id' => 'Col',
			'range_id' => 'Range',
			'order_id' => 'Order',
			'owner_id' => 'Owner',
			'size' => 'Size',
			'color' => 'Color',
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

		$criteria->compare('col_id',$this->col_id,true);
		$criteria->compare('range_id',$this->range_id,true);
		$criteria->compare('order_id',$this->order_id,true);
		$criteria->compare('owner_id',$this->owner_id);
		$criteria->compare('size',$this->size,true);
		$criteria->compare('color',$this->color,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}