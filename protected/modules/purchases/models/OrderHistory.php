<?php

/**
 * This is the model class for table "orders_history".
 *
 * The followings are the available columns in table 'orders_history':
 * @property string $history_id
 * @property string $order_id
 * @property integer $author_id
 * @property string $datetime
 * @property string $msg
 * @property string $params
 *
 * @property User $author
 */
class OrderHistory extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return OrderHistory the static model class
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
		return 'orders_history';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('order_id, author_id, msg, params', 'required'),
			array('author_id', 'numerical', 'integerOnly'=>true),
			array('order_id', 'length', 'max'=>10),
			array('msg, params', 'length', 'max'=>200),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('history_id, order_id, author_id, datetime, msg, params', 'safe', 'on'=>'search'),
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'history_id' => 'History',
			'order_id' => 'Order',
			'author_id' => 'Author',
			'datetime' => 'Datetime',
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