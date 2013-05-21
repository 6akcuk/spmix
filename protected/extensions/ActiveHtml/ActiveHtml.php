<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Sum
 * Date: 25.09.12
 * Time: 9:47
 * To change this template use File | Settings | File Templates.
 */

class ActiveHtml extends CHtml {
    static $assetPublished = false;
    static $uploadId = 0;

    public static function publishAssets() {
      if (!self::$assetPublished) {
        $path = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('ext.ActiveHtml.assets'), false, -1, true);
        Yii::app()->getClientScript()->registerScriptFile($path .'/activehtmlelements.js', CClientScript::POS_HEAD, 'after jquery-');
        Yii::app()->getClientScript()->registerCssFile($path .'/activehtmlelements.css');

        // Scroll Fix
        Yii::app()->getClientScript()->registerScriptFile($path .'/scrollfix.js', CClientScript::POS_HEAD, 'before activehtmlelements, after jquery-');

        ActiveHtml::$assetPublished = true;
      }
    }

    public static function addClass($class, $htmlOptions = array()) {
        if (isset($htmlOptions['class']))
            $htmlOptions['class'] .= " ". $class;
        else $htmlOptions['class'] = $class;

        return $htmlOptions;
    }

    public static function fieldPlaceholder($field, $name, $value = '', $htmlOptions = array()) {
        ActiveHtml::publishAssets();

        if (isset($htmlOptions['placeholder'])) {
            $placeholder = $htmlOptions['placeholder'];
            unset($htmlOptions['placeholder']);
        }
        else $placeholder = '';

        return self::openTag('span', array('class' => 'input_placeholder')) .
            self::inputField($field, $name, $value, $htmlOptions) .
            self::label($placeholder, $name) .
            self::closeTag('span');
    }

    public static function inputPlaceholder($name, $value = '', $htmlOptions = array()) {
        return self::fieldPlaceholder('text', $name, $value, $htmlOptions);
    }

    public static function emailPlaceholder($email, $value = '', $htmlOptions = array()) {
        return self::fieldPlaceholder('email', $email, $value, $htmlOptions);
    }

    public static function passwordPlaceholder($name, $value = '', $htmlOptions = array()) {
        return self::fieldPlaceholder('password', $name, $value, $htmlOptions);
    }

    public static function activeFieldPlaceholder($field, $model, $attribute, $htmlOptions = array()) {
        ActiveHtml::publishAssets();

        self::resolveNameID($model, $attribute, $htmlOptions);
        return self::openTag('span', array('class' => 'input_placeholder')) .
            self::activeInputField($field, $model, $attribute, $htmlOptions) .
            self::activeLabel($model, $attribute) .
            self::closeTag('span');
    }

    public static function activeInputPlaceholder($model, $attribute, $htmlOptions = array()) {
        return self::activeFieldPlaceholder('text', $model, $attribute, $htmlOptions = array());
    }

    public static function activeEmailPlaceholder($model, $attribute, $htmlOptions = array()) {
        return self::activeFieldPlaceholder('email', $model, $attribute, $htmlOptions = array());
    }

    public static function activePasswordPlaceholder($model, $attribute, $htmlOptions = array()) {
        return self::activeFieldPlaceholder('password', $model, $attribute, $htmlOptions = array());
    }

    public static function smartTextarea($name, $value = '', $htmlOptions = array()) {
        ActiveHtml::publishAssets();

        if (isset($htmlOptions['placeholder'])) {
            $placeholder = $htmlOptions['placeholder'];
            unset($htmlOptions['placeholder']);
        }
        else $placeholder = '';

        return self::openTag('span', array('class' => 'input_placeholder smarttext')) .
            self::textArea($name, $value, $htmlOptions) .
            self::label($placeholder, $name) .
            self::closeTag('span');
    }

    public static function activeSmartTextarea($model, $attribute, $htmlOptions = array()) {
        ActiveHtml::publishAssets();

        self::resolveNameID($model, $attribute, $htmlOptions);
        return self::openTag('span', array('class' => 'input_placeholder smarttext')) .
            self::activeTextArea($model, $attribute, $htmlOptions) .
            self::activeLabel($model, $attribute) .
            self::closeTag('span');
    }

    public static function dropdown($name, $default = '', $value = '', $data = array(), $htmlOptions = array()) {
      ActiveHtml::publishAssets();

      $items = array();

      $data = array_flip($data);
      //array_unshift($data, '- '. $default .' -');
      $data = array('' => '- '. $default .' -') + $data;
      return self::dropDownList($name, $value, $data, $htmlOptions);
/*
        if (sizeof($data) > 0) {
            $items[] = '<li><a data-value="" class="default">'. $default .'</a></li>';
            foreach ($data as $nm => $vl) {
                if ($nm) {
                    $items[] = '<li><a data-value="'. $vl .'">'. $nm .'</a></li>';
                    if ($vl == $value) $current = $nm;
                }
            }
        }
        if (!isset($current)) $current = $default;

        $htmlOptions = ActiveHtml::addClass('dropdown', $htmlOptions);

        return self::openTag('div', array('class' => 'dropdown')) .
            self::hiddenField($name, $value, $htmlOptions) .
            self::openTag('span', array('class' => 'text')) .
                $current .
            self::closeTag('span') .
            self::openTag('span', array('class' => 'arrow iconify_down_b')) .
            self::closeTag('span') .
            self::openTag('ul') .
                implode('', $items) .
            self::closeTag('ul') .
            self::closeTag('div');*/
    }

    public static function activeDropdown(CModel $model, $attribute, $data, $htmlOptions = array()) {
        $labels = $model->attributeLabels();
        return self::dropdown(
            self::resolveName($model, $attribute),
            $labels[$attribute],
            self::resolveValue($model, $attribute),
            $data,
            $htmlOptions
        );
    }

    public static function inputCalendar($name, $value = '', $label = 'Дата', $htmlOptions = array()) {
        ActiveHtml::publishAssets();

        return self::openTag('a', array('class' => 'input_calendar tt')) .
            self::openTag('em', array('class' => 'iconify_x_a')) .
            self::closeTag('em') .
            self::inputField('hidden', $name, $value, $htmlOptions) .
            self::openTag('span') .
                $label .
            self::closeTag('span') .
            self::closeTag('a');
    }

    public static function activeInputCalendar($model, $attribute, $htmlOptions = array()) {
        $labels = $model->attributeLabels();
        return self::inputCalendar(
            self::resolveName($model, $attribute),
            self::resolveValue($model, $attribute),
            $labels[$attribute],
            $htmlOptions
        );
    }

    public static function upload($name, $value, $label = 'Выберите файл', $htmlOptions = array()) {
        ActiveHtml::publishAssets();

        $image = (isset($htmlOptions['data-image'])) ? $htmlOptions['data-image'] : 'b';
        $onchange = (isset($htmlOptions['onchange'])) ? str_replace('{id}', self::$uploadId, $htmlOptions['onchange']) : 'Upload.onStart('. self::$uploadId .')';
        return self::hiddenField($name, $value) .
            self::openTag('div', array('id' => 'file_button_'. self::$uploadId, 'class' => 'fileupload', 'data-image' => $image)) .
                self::openTag('div', array('class' => 'fileprogress')) .
                    'Идет загрузка '.
                    self::openTag('a', array('class' => 'iconify_x_a tt', 'title' => 'Отменить загрузку', 'onclick' => 'Upload.cancel('. self::$uploadId .')')) .
                    self::closeTag('a') . '<br/>' .
                    self::image('/images/progress_small.gif', '') .
                self::closeTag('div') .
                self::openTag('div', array('class' => 'filedata')) .
                    self::openTag('a', array('class' => 'tt', 'title' => 'Удалить', 'onclick' => 'Upload.deleteFile('. self::$uploadId .')')) .
                      self::openTag('em', array('class' => 'icon-remove icon-white')) .
                      self::closeTag('em') .
                    self::closeTag('a') .
                self::closeTag('div') .
                self::openTag('div', array('class' => 'filebutton')) .
                    self::fileField('photo', '', array('id' => 'file_upload_'. self::$uploadId++, 'onchange' => $onchange)) .
                    self::openTag('a', array('class' => 'attach')) .
                        $label .
                    self::closeTag('a') .
                self::closeTag('div') .
            self::closeTag('div');
    }

    public static function activeUpload(CModel $model, $attribute, $label = '', $htmlOptions = array()) {
        return self::upload(
            self::resolveName($model, $attribute),
            self::resolveValue($model, $attribute),
            $label,
            $htmlOptions
        );
    }

    public static function showUploadImage($data, $size = 'b', $htmlOptions = array()) {
      $images = json_decode($data, true);
      if (isset($images[$size]))
        return self::image('http://cs'. $images[$size][2] .'.'. Yii::app()->params['domain'] .'/'. $images[$size][0] .'/'. $images[$size][1], '', $htmlOptions);
      else {
        $htmlOptions['width'] = 'auto';
        $htmlOptions['height'] = 'auto';

        if ($size == 'b') {
          $htmlOptions['width'] = 100; $htmlOptions['height'] = 100;
        }
        elseif ($size == 'c') {
          $htmlOptions['width'] = 70; $htmlOptions['height'] = 70;
        }

        return self::image('/images/camera_a.gif', '', $htmlOptions);
      }
    }
    public static function getImageUrl($data, $size = 'b') {
        $images = json_decode($data, true);
        return (isset($images[$size]))
          ? 'http://cs'. $images[$size][2] .'.'. Yii::app()->params['domain'] .'/'. $images[$size][0] .'/'. $images[$size][1]
          : '/images/camera_a.gif';
    }

    public static function link($text, $url = '#', $htmlOptions = array()) {
        ActiveHtml::publishAssets();

        if (!isset($htmlOptions['onclick']) && $url != '#') {
            if (isset($htmlOptions['nav'])) {
                $nav = $htmlOptions['nav'];
                $navi = array();
                foreach ($nav as $i => $a) {
                    $navi[] = $i .": ". (is_string($a) ? "'". $a ."'" : $a);
                }
                unset($htmlOptions['nav']);
            }

            $htmlOptions['onclick'] = 'return nav.go(this, event, '. (isset($navi) ? '{'. implode(", ", $navi) .'}' : 'null') .');';
        }

        return parent::link($text, $url, $htmlOptions);
    }

    public static function price($price, $currency = 'RUR') {
        return number_format($price, 0, ',', ' ') . ' '. Yii::t('app', $currency);
    }

  public static function qVKMenu($selected, $items, $htmlOptions = array()) {
    $htmlOptions['rel'] = 'menu';

    return
      self::openTag('a', $htmlOptions) . $selected . self::closeTag('a') .
      self::openTag('div', array('class' => 'qmenu')) .
        self::openTag('table') .
          self::openTag('tr') .
            self::openTag('td', array('class' => 'side')) .
              self::openTag('div') . self::closeTag('div') .
            self::closeTag('td') .
            self::openTag('td') .
              self::openTag('div', array('class' => 'header')) .
                '<div>'. $selected .'</div>' .
              self::closeTag('div') .
              self::openTag('div', array('class' => 'body')) .
                $items .
              self::closeTag('div') .
            self::closeTag('td') .
            self::openTag('td', array('class' => 'side')) .
              self::openTag('div') . self::closeTag('div') .
            self::closeTag('td') .
          self::closeTag('tr') .
          self::openTag('tr') .
            self::openTag('td', array('colspan' => 3)) .
              '<div class="bottom"></div>' .
              '<div class="bottom2"></div>' .
            self::closeTag('td') .
          self::closeTag('tr') .
        self::closeTag('table') .
      self::closeTag('div');
  }

    public static function date($date, $showTime = true, $shortMonth = false, $showYear = false, $useTimezone = false) {
        // включение часового пояса
        if($useTimezone) {
            if(!Yii::app()->user->getIsGuest()) $timezone = new DateTimeZone('');
            else $timezone = new DateTimeZone('Europe/Moscow'); //GMT - но вроде надо другой пояс использовать

            $now = new DateTime('now');
            $now->setTimezone( $timezone );

            $yes = new DateTime('yesterday', $timezone );

            $md = new DateTime($date);
            $md->setTimezone( $timezone );
        }
        else {
            $now = new DateTime('now');
            $yes = new DateTime('yesterday');
            $md = new DateTime($date);
        }

        if ($date)  {
            if($md->format('Y-m-d') == $now->format('Y-m-d')) return Yii::t('app', 'сегодня') . (($showTime) ? ' '. Yii::t('app', 'в') .' '. $md->format('H:i') : '');
            elseif($md->format('Y-m-d') == $yes->format('Y-m-d')) return Yii::t('app', 'вчера') . (($showTime) ? ' '. Yii::t('app', 'в') .' '. $md->format('H:i') : '');
            else return intval( $md->format('d') ) .' '. Yii::t('app', 'месяц_'. (($shortMonth) ? 'к' : '') . $md->format('m')) .' '. (($showYear) ? $md->format('Y') : '') . (($showTime) ? ' '. Yii::t('app', 'в') .' '. $md->format('H:i') : '');
        }
        else return '';
    }

    // время назад
    public static function timeback($date, $useTimezone = false)
    {
        // включение часового пояса
        if(!Yii::app()->user->getIsGuest() && $useTimezone) $timezone = new DateTimeZone('');
        else $timezone = new DateTimeZone( 'Europe/Moscow' ); //GMT - но вроде надо другой пояс использовать

        $md = new DateTime($date);
        $md->setTimezone( $timezone );

        $now = new DateTime('now');
        $now->setTimezone( $timezone );

        $ext = $md->getTimestamp();
        $now = $now->getTimestamp();

        $sec = $now - $ext;

      if ($sec <= 5) {
        return Yii::t('app', 'только что');
      }

        if($sec <= 59) {
            return Yii::t('app', '{n} секунду|{n} секунды|{n} секунд', $sec) .' '. Yii::t('app', 'назад');
        }

        $min = floor($sec / 60);

        if($min <= 59) {
            return Yii::t('app', '{n} минуту|{n} минуты|{n} минут', $min) .' '. Yii::t('app', 'назад');
        }

        $hour = floor($min / 60);

        if($hour <= 3) {
            return Yii::t('app', '{n} час|{n} часа|{n} часов', $hour) .' '. Yii::t('app', 'назад');
        }

      return self::date($date, true, true);

      $days = floor($hour / 24);

      if($days <= 31) {
          return Yii::t('app', '{n} день|{n} дня|{n} дней', $days) .' '. Yii::t('app', 'назад');
      }
    }

  // склонение слова по падежам
  public static function lex($p, $word)
  {
    if(preg_match("/[ ]{1,}/si", $word)) {
      $words = explode(" ", $word);

      foreach($words as &$word)
      {
        $word = self::lex($p, $word);
      }

      return implode(" ", $words);
    }

    // падеж
    switch($p) {
      case 1:

        return $word;

        break;
      case 2:

        if(!preg_match("/[^A-z]/si", $word)) return $word;
        if(!preg_match("/[^0-9]/si", $word)) return $word;

        $last = substr($word, -2);
        $prelast = substr($word, - 4);
        $superlast = substr($word, -6);
        $prelast_ltr = substr($prelast, 0, 2);

        if( $last == 'а' && !in_array($prelast_ltr, array('ж', 'ш', 'к', 'в')) ) return substr($word, 0, -2) .'ы';
        elseif( $last == 'а' && in_array($prelast_ltr, array('ж', 'ш', 'к')) ) return substr($word, 0, -2) .'и';
        elseif( in_array($last, array('я')) ) return substr($word, 0, -2) .'и';
        elseif( $last == 'ь' ) return substr($word, 0, -2) .'я';
        elseif( $prelast == 'ей' ) return substr($word, 0, -4) .'eя';
        elseif( $prelast == 'ое' ) return substr($word, 0, -2) .'го';
        elseif( $prelast == 'ый' ) return substr($word, 0, -4) .'ого';
        elseif( $superlast == 'ний' ) return substr($word, 0, -2) .'я';
        elseif( $prelast == 'ий' ) return substr($word, 0, -4) .'ого';
        elseif( $prelast == 'ай' ) return substr($word, 0, -4) .'ая';
        elseif( $prelast == 'ва' ) return substr($word, 0, -4) .'вой';
        elseif( in_array($prelast_ltr, array('е')) && in_array($last, array('б', 'г', 'д', 'ж', 'з', 'к', 'л', 'м', 'н', 'п', 'с', 'ф', 'х', 'ц', 'ч', 'ш', 'щ')) ) return substr($word, 0, -4) . $last . 'а';
        elseif( in_array($last, array('б', 'в', 'г', 'д', 'ж', 'з', 'к', 'л', 'м', 'н', 'п', 'р', 'с', 'т', 'ф', 'х', 'ц', 'ч', 'ш', 'щ')) ) return $word .'а';
        else return $word;

        break;

      case 3:

        $last = substr($word, -2);
        $prelast = substr($word, -4);

        if( in_array($last, array('а', 'я', 'ь')) ) return substr($word, 0, -2) .'е';
        elseif( $prelast == 'ое' || $prelast == 'ый' ) return substr($word, 0, -4) .'ом';
        elseif( $prelast == 'ий' ) return substr($word, 0, -4) .'е';
        elseif( $prelast == 'ай' ) return substr($word, 0, -4) .'ае';
        elseif( $prelast == 'ия' ) return substr($word, 0, -4) .'ии';
        elseif( $last == 'о' || $last == 'ы' ) return $word; // анопово
        elseif( $last == 'с') return $word .'у';
        else return $word .'е';

        break;

      case 5:

        if(!preg_match("/[^A-z]/si", $word)) return $word;
        if(!preg_match("/[^0-9]/si", $word)) return $word;

        $last = substr($word, -2);
        $prelast = substr($word, -4);
        $superlast = substr($word, -6);
        $prelast_ltr = substr($prelast, 0, 2);

        if( $last == 'а' && $prelast_ltr != 'ш' ) return substr($word, 0, -2) .'ой';
        elseif( $last == 'я' ) return substr($word, 0, -2) .'ей';
        elseif( $prelast == 'ев' ) return $word .'ым';
        elseif( $prelast == 'ел' ) return substr($word, 0, -4) .'лом';
        elseif( $superlast == 'ний' ) return substr($word, 0, -2) .'ем';
        elseif( $prelast == 'ий' ) return substr($word, 0, -4) .'им';
        elseif( $last == 'ь' ) return substr($word, 0, -2) .'ем';
        elseif( $prelast == 'ец' ) return substr($word, 0, -4) .'цем';
        else return $word .'ом';

        break;
    }
  }

  public static function filesize($size) {
    if( $size >= 1073741824 ) {
      $size = round( $size / 1073741824 * 100 ) / 100 . " ". Yii::t('app', 'Гб');
    } elseif( $size >= 1048576 ) {
      $size = round( $size / 1048576 * 100 ) / 100 . " ". Yii::t('app', 'Мб');
    } elseif( $size >= 1024 ) {
      $size = round( $size / 1024 * 100 ) / 100 . " ". Yii::t('app', 'Кб');
    } else {
      $size = $size . " ". Yii::t('app', 'б');
    }
    return $size;
  }
}