<?php

/**
 * This is the model class for table "users".
 *
 * The followings are the available columns in table 'users':
 * @property string $id
 * @property string $login
 * @property string $email
 * @property string $password
 * @property string $usergroup
 * @property string $hash
 * @property string $salt
 * @property string $lockedauth_until
 * @property string $regdate
 * @property string $lastvisit
 *
 * @property RbacAssignment $role
 * @property Profile $profile
 */
class User extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return User the static model class
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
		return 'users';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('email, password, salt', 'required'),
			array('email', 'email'),
            array('login', 'length', 'min' => 3, 'max' => 30),
            array('hash', 'length', 'max' => 50),
            array('salt', 'length', 'min' => 6, 'max' => 20),
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
            'role' => array(self::HAS_ONE, 'RbacAssignment', 'userid'),
            'profile' => array(self::HAS_ONE, 'Profile', 'user_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
            'login' => 'Логин',
			'email' => 'E-Mail',
			'password' => 'Пароль',
			'hash' => 'Cookie Hash',
			'lockedauth_until' => 'Блокировка доступа до',
		);
	}

    public function generateSalt() {
        $length = mt_rand(6, 20);
        $chars = 'abcdefghijklmnopqrstuvwxyz0123456789_-';
        $result = array();

        for ($i = 1; $i <= $length; $i++) {
            $result[] = $chars[mt_rand(0, strlen($chars) - 1)];
        }

        return implode('', $result);
    }

    public function generatePassword() {
        $length = 7;
        $chars = 'abcdefhkmnpqrsuvwxyz2345678';
        $result = array();

        for ($i = 1; $i <= $length; $i++) {
            $char = $chars[mt_rand(0, strlen($chars) - 1)];
            if (mt_rand(0, 1) === 1) $char = strtoupper($char);

            $result[] = $char;
        }

        return implode('', $result);
    }

    public function hashPassword($password, $salt) {
        return md5(md5($password . $salt) . $salt);
    }

    public function isOnline() {
        return (strtotime($this->lastvisit) >= time() - Yii::app()->getModule('users')->onlineInterval * 60);
    }

    public function beforeSave() {
        if (parent::beforeSave()) {
            if ($this->isNewRecord)
                $this->regdate = date("Y-m-d H:i:s");

            return true;
        }
        else return false;
    }
}