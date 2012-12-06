<?php
/**
 * Created by JetBrains PhpStorm.
 * User: denis
 * Date: 26.11.12
 * Time: 10:08
 * To change this template use File | Settings | File Templates.
 */

class PurchaseForm extends CFormModel {
    public $name;
    public $category_id;
    public $city_id;
    public $stop_date;
    public $status;
    public $state;
    public $min_sum;
    public $min_num;
    public $supplier_url;
    public $price_url;
    public $message;
    public $org_tax;
    public $image;
    public $vip;
    public $mod_confirmation;
    public $mod_reason;
    public $sizes;

    public function rules() {
        return array(
            array('name, category_id, city_id, stop_date, status, image', 'required', 'on' => 'create')
        );
    }

    public function attributeLabels() {
        return array(
            'name' => 'Название',
            'category_id' => 'Категория',
            'city_id' => 'Город',
            'stop_date' => 'Дата окончания (стопа)',
            'status' => 'Статус',
            'state' => 'Статус закупки',
            'min_sum' => 'Мин. сумма заказа',
            'min_num' => 'Мин. кол-во заказов',
            'supplier_url' => 'Ссылка на сайт поставщика',
            'price_url' => 'Ссылка на прайс',
            'message' => 'Оповещение',
            'org_tax' => '% наценки организатора',
            'image' => 'Аватар',
            'vip' => 'VIP',
        );
    }
}