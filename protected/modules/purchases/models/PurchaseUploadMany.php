<?php

/**
 * This is the model class for table "purchase_upload_many".
 *
 * The followings are the available columns in table 'purchase_upload_many':
 * @property string $pk
 * @property integer $purchase_id
 * @property string $photo
 * @property string $timestamp
 */
class PurchaseUploadMany extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return PurchaseUploadMany the static model class
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
		return 'purchase_upload_many';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('purchase_id, photo', 'required'),
			array('purchase_id', 'numerical', 'integerOnly'=>true),
			array('pk', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('pk, purchase_id, photo, timestamp', 'safe', 'on'=>'search'),
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
			'pk' => 'Pk',
			'purchase_id' => 'Purchase',
			'photo' => 'Photo',
			'timestamp' => 'Timestamp',
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

		$criteria->compare('pk',$this->pk,true);
		$criteria->compare('purchase_id',$this->purchase_id);
		$criteria->compare('photo',$this->photo,true);
		$criteria->compare('timestamp',$this->timestamp,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

  public function beforeSave() {
    if (parent::beforeSave()) {
      if ($this->isNewRecord)
        $this->timestamp = date("Y-m-d H:i:s");

      return true;
    }
    else return false;
  }
}