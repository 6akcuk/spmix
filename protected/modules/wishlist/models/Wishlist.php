<?php

/**
 * This is the model class for table "wishlists".
 *
 * The followings are the available columns in table 'wishlists':
 * @property integer $wishlist_id
 * @property integer $city_id
 * @property integer $author_id
 * @property integer $type
 * @property string $add_date
 * @property string $shortstory
 *
 * @property City $city
 * @property User $author
 */
class Wishlist extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Wishlist the static model class
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
		return 'wishlists';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('city_id, author_id, type, shortstory', 'required'),
			array('city_id, author_id, type', 'numerical', 'integerOnly'=>true),
			array('shortstory', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('wishlist_id, city_id, author_id, type, add_date, shortstory', 'safe', 'on'=>'search'),
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
      'city' => array(self::BELONGS_TO, 'City', 'city_id'),
      'author' => array(self::BELONGS_TO, 'User', 'author_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'wishlist_id' => 'Wishlist',
			'city_id' => 'City',
			'author_id' => 'Author',
			'type' => 'Type',
			'add_date' => 'Add Date',
			'shortstory' => 'Shortstory',
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

		$criteria->compare('wishlist_id',$this->wishlist_id);
		$criteria->compare('city_id',$this->city_id);
		$criteria->compare('author_id',$this->author_id);
		$criteria->compare('type',$this->type);
		$criteria->compare('add_date',$this->add_date,true);
		$criteria->compare('shortstory',$this->shortstory,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

  public static function countFriendsWishes($user_id, $type) {
    $db = Yii::app()->db;

    $command = $db->createCommand("
    SELECT COUNT(w.wishlist_id) AS num FROM `subscriptions` s
      INNER JOIN `wishlists` w ON w.author_id = s.sub_link_id
      INNER JOIN `cities` c ON c.id = w.city_id
      INNER JOIN `users` u ON u.id = w.author_id
      INNER JOIN `profiles` p ON p.user_id = u.id
      WHERE s.user_id = ". $user_id ."
        AND s.sub_type = 'user'
        AND w.type = ". $type ."
    ORDER BY w.add_date DESC");

    $result = $command->queryRow();
    return $result['num'];
  }

  public static function getFriendsWishes($user_id, $type, $offset = 0) {
    $result = array();
    /** @var CDbConnection $db */
    $db = Yii::app()->db;

    $command = $db->createCommand("
    SELECT w.*, c.name AS city_name, u.login, p.firstname, p.lastname, p.photo FROM `subscriptions` s
      INNER JOIN `wishlists` w ON w.author_id = s.sub_link_id
      INNER JOIN `cities` c ON c.id = w.city_id
      INNER JOIN `users` u ON u.id = w.author_id
      INNER JOIN `profiles` p ON p.user_id = u.id
      WHERE s.user_id = ". $user_id ."
        AND s.sub_type = 'user'
        AND w.type = ". $type ."
    ORDER BY w.add_date DESC
    LIMIT ". $offset .", ". Yii::app()->getModule('wishlist')->wishesPerPage);

    /** @var CDbDataReader $reader */
    $reader = $command->query();
    while (($row = $reader->read()) !== false) {
      $wish = new Wishlist();
      $wish->wishlist_id = $row['wishlist_id'];
      $wish->author_id = $row['author_id'];
      $wish->city_id = $row['city_id'];
      $wish->shortstory = $row['shortstory'];
      $wish->add_date = $row['add_date'];

      $city = new City();
      $city->id = $row['city_id'];
      $city->name = $row['city_name'];

      $user = new User();
      $user->id = $row['author_id'];
      $user->login = $row['login'];

      $profile = new Profile();
      $profile->user_id = $row['author_id'];
      $profile->firstname = $row['firstname'];
      $profile->lastname = $row['lastname'];
      $profile->photo = $row['photo'];

      $user->profile = $profile;

      $wish->city = $city;
      $wish->author = $user;

      $result[] = $wish;
    }

    return $result;
  }

  public function beforeSave() {
    if (parent::beforeSave()) {
      if ($this->getIsNewRecord())
        $this->add_date = date("Y-m-d H:i:s");

      return true;
    }
    else return false;
  }
}