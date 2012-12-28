<?php

/**
 * This is the model class for table "goods_grid".
 *
 * The followings are the available columns in table 'goods_grid':
 * @property string $grid_id
 * @property integer $purchase_id
 * @property string $good_id
 * @property string $size
 * @property string $colors
 */
class GoodGrid extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return GoodGrid the static model class
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
		return 'goods_grid';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('purchase_id, good_id, size, colors', 'required', 'on' => 'create'),
			array('purchase_id', 'numerical', 'integerOnly'=>true),
			array('good_id', 'length', 'max'=>10),
			array('size', 'length', 'max'=>20),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('grid_id, purchase_id, good_id, size, colors', 'safe', 'on'=>'search'),
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
			'grid_id' => 'Grid',
			'purchase_id' => 'Purchase',
			'good_id' => 'Good',
			'size' => 'Size',
			'colors' => 'Colors',
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

		$criteria->compare('grid_id',$this->grid_id,true);
		$criteria->compare('purchase_id',$this->purchase_id);
		$criteria->compare('good_id',$this->good_id,true);
		$criteria->compare('size',$this->size,true);
		$criteria->compare('colors',$this->colors,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}