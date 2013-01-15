<?php

/**
 * This is the model class for table "purchase_history".
 *
 * The followings are the available columns in table 'purchase_history':
 * @property string $history_id
 * @property integer $purchase_id
 * @property integer $author_id
 * @property string $datetime
 * @property string $msg
 * @property string $params
 */
class PurchaseHistory extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return PurchaseHistory the static model class
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
		return 'purchase_history';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('purchase_id, author_id, msg, params', 'required'),
			array('purchase_id, author_id', 'numerical', 'integerOnly'=>true),
			array('msg, params', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('history_id, purchase_id, author_id, msg, params', 'safe', 'on'=>'search'),
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
			'history_id' => 'History',
			'purchase_id' => 'Purchase',
			'author_id' => 'Author',
			'msg' => 'Msg',
			'params' => 'Params',
		);
	}

    public function beforeSave() {
        if (parent::beforeSave()) {
            if ($this->isNewRecord)
                $this->datetime = date("Y-m-d H:i:s");

            return true;
        }
        else return false;
    }
}