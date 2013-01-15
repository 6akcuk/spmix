<?php

/**
 * This is the model class for table "purchase_mod_requests".
 *
 * The followings are the available columns in table 'purchase_mod_requests':
 * @property string $mod_request_id
 * @property string $purchase_id
 * @property string $request_date
 * @property integer $moderator_id
 * @property integer $status
 * @property string $message
 *
 * @property User $moderator
 */
class PurchaseModRequest extends CActiveRecord
{
    const STATUS_NEW = 0;
    const STATUS_VIEWING = 1;
    const STATUS_CLOSED = 2;

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return PurchaseModRequest the static model class
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
		return 'purchase_mod_requests';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('purchase_id', 'required'),
			array('moderator_id, status', 'numerical', 'integerOnly'=>true),
			array('purchase_id', 'length', 'max'=>10),
			array('message', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('mod_request_id, purchase_id, request_date, moderator_id, status, message', 'safe', 'on'=>'search'),
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
            'moderator' => array(self::BELONGS_TO, 'User', 'moderator_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'mod_request_id' => 'Mod Request',
			'purchase_id' => 'Purchase',
			'request_date' => 'Request Date',
			'moderator_id' => 'Moderator',
			'status' => 'Status',
			'message' => 'Message',
		);
	}

    public function beforeSave() {
        if (parent::beforeSave()) {
            if ($this->isNewRecord) {
                $this->request_date = date("Y-m-d H:i:s");
                $this->status = self::STATUS_NEW;
            }

            return true;
        }
        else return false;
    }
}