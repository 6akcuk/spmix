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
 * @property integer $reply_to
 * @property string $text
 * @property string $attaches
 * @property string $comment_delete
 *
 * @property User $author
 * @property Profile $reply
 */
class Comment extends CActiveRecord
{
  const FEED_NEW_COMMENT = 'new comment';

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

  public function defaultScope() {
    return array(
      'condition' => 'comment_delete IS NULL',
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
			array('author_id, hoop_id, hoop_type, text', 'required'),
      array('author_id, attaches', 'unsafe'),
			array('author_id, reply_to', 'numerical', 'integerOnly' => true),
			array('hoop_id, hoop_type', 'length', 'max'=>10),
			array('text', 'length', 'min' => 2, 'max' => 4096),
      array('text', 'filter', 'filter' => array($obj = new CHtmlPurifier(),'purify')),
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
      'reply' => array(self::BELONGS_TO, 'Profile', 'reply_to', 'joinType' => 'LEFT JOIN'),
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
			'answer_to' => 'Ответ',
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

  public function afterSave() {
    if ($this->getIsNewRecord()) {
      if ($this->reply_to) {
        $req = new ProfileRequest();
        $req->req_type = ProfileRequest::TYPE_COMMENT_ANSWER;
        $req->owner_id = $this->reply_to;
        $req->req_link_id = $this->comment_id;
        $req->save();
      }

      $feed = new Feed();
      $feed->event_type = self::FEED_NEW_COMMENT;
      $feed->event_link_id = $this->comment_id;
      $feed->owner_type = $this->hoop_type;
      $feed->owner_id = $this->hoop_id;
      $feed->save();

      $c = new CDbCriteria();
      $c->compare('user_id', Yii::app()->user->getId());
      $c->compare('sub_type', $this->hoop_type);
      $c->compare('sub_link_id', $this->hoop_id);

      $sub = Subscription::model()->find($c);
      if (!$sub) {
        $sub = new Subscription();
        $sub->user_id = Yii::app()->user->getId();
        $sub->sub_type = Subscription::TYPE_PURCHASE;
        $sub->sub_link_id = $this->purchase_id;
        $sub->save();
      }
    }
  }

  public function markAsDeleted() {
    $this->comment_delete = date("Y-m-d H:i:s");
    $result = $this->save(true, array('comment_delete'));

    $cr = new CDbCriteria();
    $cr->compare('owner_type', $this->hoop_type);
    $cr->compare('owner_id', $this->hoop_id);
    $cr->compare('event_type', 'new comment');
    $cr->compare('event_link_id', $this->comment_id);

    $feed = Feed::model()->find($cr);
    if ($feed) $feed->markAsDeleted();

    if ($this->reply_to) {
      $cr = new CDbCriteria();
      $cr->compare('req_type', ProfileRequest::TYPE_COMMENT_ANSWER);
      $cr->compare('owner_id', $this->reply_to);
      $cr->compare('req_link_id', $this->comment_id);

      $req = ProfileRequest::model()->find($cr);
      if ($req) $req->delete();
    }

    return $result;
  }

  public function restore() {
    $this->comment_delete = null;
    $result = $this->save(true, array('comment_delete'));

    $cr = new CDbCriteria();
    $cr->compare('owner_type', $this->hoop_type);
    $cr->compare('owner_id', $this->hoop_id);
    $cr->compare('event_type', 'new comment');
    $cr->compare('event_link_id', $this->comment_id);

    $feed = Feed::model()->find($cr);
    if ($feed) $feed->restore();

    if ($this->reply_to) {
      $req = new ProfileRequest();
      $req->req_type = ProfileRequest::TYPE_COMMENT_ANSWER;
      $req->owner_id = $this->reply_to;
      $req->req_link_id = $this->comment_id;
      $req->save();
    }

    return $result;
  }
}