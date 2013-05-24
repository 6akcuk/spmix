<?php

/**
 * This is the model class for table "goods".
 *
 * The followings are the available columns in table 'goods':
 * @property string $good_id
 * @property integer $purchase_id
 * @property integer $is_quick
 * @property integer $is_range
 * @property string $name
 * @property string $price
 * @property string $delivery
 * @property string $currency
 * @property string $description
 * @property string $artikul
 * @property string $url
 * @property string $range
 * @property string $good_hidden
 *
 * @property Purchase $purchase
 * @property array|GoodSize $sizes
 * @property array|GoodColor $colors
 * @property array $grid
 * @property array $ranges
 * @property GoodImages $image
 * @property array $images
 * @property array $oic
 * @property array $orders
 * @property integer $ordersNum
 * @property float $ordersSum
 */
class Good extends CActiveRecord
{
  const FEED_NEW_GOOD = 'new good';

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Good the static model class
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
		return 'goods';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('purchase_id, name, price, currency', 'required', 'on' => 'create'),
      array('purchase_id, name, artikul, price, is_quick', 'required', 'on' => 'quick'),
			array('purchase_id, is_quick', 'numerical', 'integerOnly'=>true),
      array('is_range, description, range', 'safe'),
			array('name', 'length', 'max'=>100),
			array('price', 'length', 'max'=>12),
      array('delivery', 'length', 'max' => 9),
			array('currency', 'length', 'max'=>3),
			array('artikul', 'length', 'max'=>50),
			array('url', 'length', 'max'=>200),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('good_id, purchase_id, name, price, currency, description, artikul, url, sizes, colors', 'safe', 'on'=>'search'),
		);
	}

  public function defaultScope() {
    return array(
      'condition' => 'good_hidden IS NULL',
    );
  }

    public function scopes() {
        return array(
            'quick' => array(
                'condition' => 'is_quick = 0'
            )
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
      'purchase' => array(self::BELONGS_TO, 'Purchase', 'purchase_id'),
      'sizes' => array(self::HAS_MANY, 'GoodSize', 'good_id'),
      'colors' => array(self::HAS_MANY, 'GoodColor', 'good_id'),
      'grid' => array(self::HAS_MANY, 'GoodGrid', array('good_id' => 'good_id'), 'order' => 'size'),
      'ranges' => array(self::HAS_MANY, 'GoodRange', array('good_id' => 'good_id')),
      'image' => array(self::HAS_ONE, 'GoodImages', array('good_id' => 'good_id')),
      'images' => array(self::HAS_MANY, 'GoodImages', array('good_id' => 'good_id')),
      'oic' => array(self::HAS_MANY, 'PurchaseOic', array('purchase_id' => 'purchase_id')),
      'orders' => array(self::HAS_MANY, 'Order', 'good_id'),
      'ordersNum' => array(self::STAT, 'Order', 'good_id', 'select' => 'SUM(amount)'),
      'ordersSum' => array(self::STAT, 'Order', 'good_id', 'select' => 'SUM(total_price)'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'good_id' => 'Good',
			'purchase_id' => 'Purchase',
      'is_range' => 'Использовать ряды',
			'name' => 'Название',
			'price' => 'Цена',
      'delivery' => 'Стоимость доставки',
			'currency' => 'Валюта',
			'description' => 'Описание',
			'artikul' => 'Артикул',
			'url' => 'URL',
      'range' => 'Настройка рядов',
			'sizes' => 'Размер',
			'colors' => 'Цвет',
		);
	}

  public function afterSave() {
    if ($this->getIsNewRecord()) {
      $feed = new Feed();
      $feed->event_type = self::FEED_NEW_GOOD;
      $feed->event_link_id = $this->good_id;
      $feed->owner_type = 'purchase';
      $feed->owner_id = $this->purchase_id;
      $feed->save();
    }
  }

    public function getEndPrice($new_price = null, $new_delivery = null) {
        return floatval(($new_price) ?: $this->price) * ($this->purchase->org_tax / 100 + 1) + (($new_delivery) ?: $this->delivery);
    }
  public function getEndCustomPrice($org_tax, $new_price = null, $new_delivery = null) {
    return ceil(floatval(($new_price) ?: $this->price) * ($org_tax / 100 + 1)) + (($new_delivery) ?: $this->delivery);
  }

    public function countImages()
    {
        return GoodImages::model()->count('good_id = :good_id', array(':good_id' => $this->good_id));
    }

  public function copyFromAnother($good_id) {
    $sizes = GoodSize::model()->findAll('good_id = :gid', array(':gid' => $good_id));

    foreach ($sizes as $size) {
      $newsize = new GoodSize();
      $newsize->good_id = $this->good_id;
      $newsize->adv_price = $size->adv_price;
      $newsize->size = $size->size;
      $newsize->save();
    }

    $colors = GoodColor::model()->findAll('good_id = :gid', array(':gid' => $good_id));
    foreach ($colors as $color) {
      $newcolor = new GoodColor();
      $newcolor->good_id = $this->good_id;
      $newcolor->color = $color->color;
      $newcolor->save();
    }

    $images = GoodImages::model()->findAll('good_id = :gid', array(':gid' => $good_id));
    foreach ($images as $image) {
      $newimage = new GoodImages();
      $newimage->image = $image->image;
      $newimage->good_id = $this->good_id;
      $newimage->tag_color = $image->tag_color;
      $newimage->save();
    }
  }

  /**
   * @return array
   */
  public function getRangeStructure() {
    $structure = array();

    if ($this->is_range && $this->range) {
      preg_match("'\\[cols\\](.*?)\\[\/cols\\]'si", $this->range, $cols_string);
      if (isset($cols_string[1])) {
        preg_match_all("'\\[col\\](.*?)\\[\/col\\]'si", $cols_string[1], $cols_arr);

        foreach ($cols_arr[1] as $col) {
          preg_match("'\\[size\\](.*?)\\[\/size\\]'si", $col, $size);
          preg_match("'\\[color\\](.*?)\\[\/color\\]'si", $col, $color);

          $helper = array();
          if (isset($size[1])) $helper['size'] = $size[1];
          if (isset($color[1])) $helper['color'] = $color[1];

          $structure[] = $helper;
        }
      }
    }

    return $structure;
  }

  /**
   * Построить ряды на основе сделанных заказов
   *
   * @return null|array
   */
  public function buildRanges() {
    $ranges = null;
    $cur_range_length = 1;
    $struct = $this->getRangeStructure();

    if ($this->is_range) {
      // если ряды имеют одну строку
      if (!stristr($this->range, '[rows]')) {
        if ($this->orders) {
          /** @var $order Order */
          foreach ($this->orders as $order) {
            for ($i=1; $i<=$order->amount; $i++) {
              $added = false;

              if ($ranges === null) {
                $ranges[$cur_range_length] = array('tag' => '', 'items' => array());

                foreach ($struct as $col) {
                  if (!$added &&
                    (!isset($col['size']) || (isset($col['size']) && $col['size'] == $order->size)) &&
                    (!isset($col['color']) || (isset($col['color']) && $col['color'] == $order->color))
                  ) {
                    $added = true;
                    $o = $order;
                  }
                  else $o = null;

                  $ranges[$cur_range_length]['items'][] = $o;
                }

                $cur_range_length++;
                continue;
              }
              else {
                foreach ($ranges as $range => &$range_data) {
                  foreach ($range_data['items'] as $idx => &$item) {
                    $col = $struct[$idx];

                    if ($item == null &&
                      (!isset($col['size']) || (isset($col['size']) && $col['size'] == $order->size)) &&
                      (!isset($col['color']) || (isset($col['color']) && $col['color'] == $order->color))
                    ) {
                      $item = $order;

                      $added = true;
                      break 2;
                    }
                  }
                }
              }

              if (!$added) {
                $ranges[$cur_range_length] = array('tag' => '', 'items' => array());

                foreach ($struct as $col) {
                  if (!$added &&
                    (!isset($col['size']) || (isset($col['size']) && $col['size'] == $order->size)) &&
                    (!isset($col['color']) || (isset($col['color']) && $col['color'] == $order->color))
                  ) {
                    $added = true;
                    $o = $order;
                  }
                  else $o = null;

                  $ranges[$cur_range_length]['items'][] = $o;
                }

                $cur_range_length++;
                continue;
              }
            }
          }
        }
      }
    }

    return $ranges;
  }
}