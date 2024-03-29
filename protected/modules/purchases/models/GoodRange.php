<?php

/**
 * This is the model class for table "good_ranges".
 *
 * The followings are the available columns in table 'good_ranges':
 * @property string $range_id
 * @property string $good_id
 * @property integer $filled
 *
 * @property array|RangeCol $cols
 */
class GoodRange extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return GoodRange the static model class
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
		return 'good_ranges';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('good_id, filled', 'required'),
			array('filled', 'numerical', 'integerOnly'=>true),
			array('good_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('range_id, good_id, filled', 'safe', 'on'=>'search'),
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
      'cols' => array(self::HAS_MANY, 'RangeCol', 'range_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'range_id' => 'Range',
			'good_id' => 'Good',
			'filled' => 'Filled',
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

		$criteria->compare('range_id',$this->range_id,true);
		$criteria->compare('good_id',$this->good_id,true);
		$criteria->compare('filled',$this->filled);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}