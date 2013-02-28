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
			array('author_id, hoop_id, hoop_type, creation_date, answer_to, text, attaches', 'required'),
			array('author_id', 'numerical', 'integerOnly'=>true),
			array('hoop_id, hoop_type', 'length', 'max'=>10),
			array('answer_to', 'length', 'max'=>20),
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
			'creation_date' => 'Creation Date',
			'answer_to' => 'Answer To',
			'text' => 'Text',
			'attaches' => 'Attaches',
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

		$criteria->compare('comment_id',$this->comment_id,true);
		$criteria->compare('author_id',$this->author_id);
		$criteria->compare('hoop_id',$this->hoop_id,true);
		$criteria->compare('hoop_type',$this->hoop_type,true);
		$criteria->compare('creation_date',$this->creation_date,true);
		$criteria->compare('answer_to',$this->answer_to,true);
		$criteria->compare('text',$this->text,true);
		$criteria->compare('attaches',$this->attaches,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}