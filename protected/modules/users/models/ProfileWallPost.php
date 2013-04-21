<?php

/**
 * This is the model class for table "profile_wall_posts".
 *
 * The followings are the available columns in table 'profile_wall_posts':
 * @property string $post_id
 * @property integer $wall_id
 * @property integer $author_id
 * @property string $add_date
 * @property string $reply_to
 * @property string $post
 * @property string $attaches
 * @property string $post_delete
 *
 * @property User $author
 */
class ProfileWallPost extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return ProfileWallPost the static model class
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
		return 'profile_wall_posts';
	}

  public function defaultScope() {
    return array(
      'condition' => 'post_delete IS NULL',
    );
  }

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('wall_id, author_id, post', 'required'),
			array('wall_id, author_id', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('post_id, wall_id, author_id, add_date, post, attaches', 'safe', 'on'=>'search'),
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
			'post_id' => 'Post',
			'wall_id' => 'Wall',
			'author_id' => 'Author',
			'add_date' => 'Add Date',
			'post' => 'Post',
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

		$criteria->compare('post_id',$this->post_id,true);
		$criteria->compare('wall_id',$this->wall_id);
		$criteria->compare('author_id',$this->author_id);
		$criteria->compare('add_date',$this->add_date,true);
		$criteria->compare('post',$this->post,true);
		$criteria->compare('attaches',$this->attaches,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

  public function beforeSave() {
    if (parent::beforeSave()) {
      if ($this->getIsNewRecord())
        $this->add_date = date("Y-m-d H:i:s");

      return true;
    }
    else return false;
  }

  public function afterSave() {
    if ($this->isNewRecord) {
      $feed = new Feed();
      $feed->owner_type = 'user';
      $feed->owner_id = $this->wall_id;
      $feed->event_type = ($this->reply_to) ? 'new reply' : 'new post';
      $feed->event_link_id = $this->post_id;
      $feed->save();
    }
  }

  public function markAsDeleted() {
    $this->post_delete = date("Y-m-d H:i:s");
    $this->save(true, array('post_delete'));
  }

  public function restore() {
    $this->post_delete = null;
    $this->save(true, array('post_delete'));
  }
}