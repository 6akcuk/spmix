<?php

/**
 * This is the model class for table "comments".
 *
 * The followings are the available columns in table 'comments':
 * @property string $comment_id
 * @property integer $author_id
 * @property string $hoop_id
 * @property string $hoop_type
 * @property string $creation_date
 * @property string $answer_to
 * @property string $text
 * @property string $attaches
 *
 * @property User $author
 */
class Comment extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Comment the static model class
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
		return 'comments';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('author_id, hoop_id, hoop_type, text', 'required'),
      array('author_id, attaches', 'unsafe'),
			array('author_id', 'numerical', 'integerOnly' => true),
			array('hoop_id, hoop_type', 'length', 'max'=>10),
			array('answer_to', 'length', 'max'=>20),
      array('text', 'length', 'min' => 2, 'max' => 4096),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('comment_id, author_id, hoop_id, hoop_type, creation_date, answer_to, text, attaches', 'safe', 'on'=>'search'),
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
			'comment_id' => 'Comment',
			'author_id' => 'Author',
			'hoop_id' => 'Hoop',
			'hoop_type' => 'Hoop Type',
			'creation_date' => 'Дата создания',
			'answer_to' => 'Answer To',
			'text' => 'Комментарий',
			'attaches' => 'Прикрепления',
		);
	}

  public function beforeSave() {
    if (parent::beforeSave()) {
      if ($this->isNewRecord)
        $this->creation_date = date("Y-m-d H:i:s");
      return true;
    }
    else return false;
  }
}