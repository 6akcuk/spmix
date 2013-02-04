<?php

class SiteController extends Controller
{
    public function filters() {
        return array(
            array(
                'ext.AjaxFilter.AjaxFilter'
            ),
        );
    }

	/**
	 * Declares class-based actions.
	 */
	public function actions()
	{

	}

  public function actionPreviewDialog() {
    Yii::import('application.modules.im.models.*');

    $criteria = new CDbCriteria();
    $criteria->compare('t.member_id', 1);
    $criteria->compare('twin.member_id', 3);
    $criteria->compare('dialog.type', Dialog::TYPE_TET);

    /** @var $db CDbConnection */
    $db = Yii::app()->db;
    $command = $db->createCommand("
SELECT * FROM `dialog_members` AS t
  INNER JOIN `dialog_members` AS twin ON twin.dialog_id = t.dialog_id
  INNER JOIN `dialogs` AS dialog ON dialog.dialog_id = t.dialog_id
WHERE twin.member_id = 111 AND t.member_id = 1 AND dialog.type = 0");

    $row = $command->queryRow();

    var_dump($row);
  }

  /* Исправляет проблему дублирования диалогов */
  public function actionPatch3() {
    Yii::import('application.modules.im.models.*');

    $criteria = new CDbCriteria();
    $criteria->compare('t.type', Dialog::TYPE_TET);
    $criteria->order = 't.dialog_id, members.member_id';

    $fix = array();
    $dialogs = Dialog::model()->with('members')->findAll($criteria);
    /** @var $dialog Dialog */
    foreach ($dialogs as $dialog) {
      $hash = array();
      foreach ($dialog->members as $member) {
        $hash[] = $member->member_id;
      }
      $hash = implode(',', $hash);

      if (!isset($fix[$hash])) {
        $fix[$hash] = $dialog->dialog_id;
        echo 'Parent dialog for hash '. $hash .' is '. $fix[$hash] .'<br>';
      }
      else {
        echo 'Moving '. $dialog->dialog_id .' to '. $fix[$hash] .'<br>';

        Dialog::model()->deleteByPk($dialog->dialog_id);
        DialogMember::model()->deleteAll('dialog_id = :id', array(':id' => $dialog->dialog_id));
        DialogMessage::model()->updateAll(array('dialog_id' => $fix[$hash]), 'dialog_id = :id', array(':id' => $dialog->dialog_id));
      }
    }

    exit;
  }

  /* Исправляет старые записи с синтаксисом size[price] */
  public function actionPatch2() {
    Yii::import('application.modules.purchases.models.*');
    $sizes = GoodSize::model()->findAll("adv_price = 0 AND size LIKE '%[%'");

    foreach ($sizes as $size) {
      if (preg_match("/\[([0-9\.]{1,})\]/i", $size->size, $price)) {
        $size->adv_price = trim($price[1]);
        $size->size = trim(preg_replace("/\[[0-9\.]{1,}\]$/i", "", $size->size));
      }
      else $size->adv_price = 0;

      $size->save(true, array('size', 'adv_price'));
    }
  }

  /* */
  public function actionPatch1() {
    Yii::import('application.modules.purchases.models.*');
    $grids = GoodGrid::model()->with(array('good' => array('joinType' => 'INNER JOIN')))->findAll();

    $range_clear = array();
    $ranges = array();

    /** @var $grid GoodGrid */
    foreach ($grids as $grid) {
      $colors = json_decode($grid->colors, true);

      $size = new GoodSize();
      $size->good_id = $grid->good_id;
      $size->size = $grid->size;
      if (!$size->save()) continue;

      if ($grid->good->is_range) $ranges[$grid->good_id][] = '[col][size]'. $grid->size .'[/size][/col]';

      foreach ($colors as $color) {
        $clr = new GoodColor();
        $clr->color = $color;
        $clr->good_id = $grid->good_id;
        $clr->save();
      }

      $grid->delete();
    }

    foreach ($ranges as $good_id => $sizes) {
      /** @var $good Good */
      $good = Good::model()->findByPk($good_id);
      $good->range = '[cols]'. implode('', $sizes) .'[/cols]';
      $good->save(true, array('range'));
    }
  }

	public function actionIndex()
	{
        if (!Yii::app()->user->getIsGuest()) {
            $this->redirect('http://spmix.ru/id'. Yii::app()->user->getId());
        }

        $this->render('index');
	}

    public function actionSetCity() {
        $cookies = Yii::app()->getRequest()->getCookies();
        $cookies->remove('cur_city');

        $city = new CHttpCookie('cur_city', intval($_POST['city_id']));
        $city->expire = time() + (60 * 60 * 24 * 30 * 12 * 20);
        $cookies->add('cur_city', $city);

        echo json_encode(array('success' => true, 'msg' => 'Изменения сохранены'));
        exit;
    }

    public function actionError() {
        if($error=Yii::app()->errorHandler->error)
        {
            if(Yii::app()->request->isAjaxRequest)
                $this->pageHtml = $error['message'];
            else
                $this->render('error', $error);
        }
    }

    public function actionLogin() {
        $model=new LoginForm;

        // collect user input data
        if(isset($_POST['LoginForm']))
        {
            $model->attributes=$_POST['LoginForm'];
            $result = array();

            if($model->validate() && $model->login()) {
                if (Yii::app()->getRequest()->isAjaxRequest) {
                    $result['success'] = true;
                    $result['id'] = Yii::app()->user->getId();
                }
                else $this->redirect('/id'. Yii::app()->user->getId());
            }
            else {
                foreach ($model->getErrors() as $attr => $error) {
                    $result[$attr] = $error;
                }
            }

            if (!Yii::app()->getRequest()->isAjaxRequest) {
                $_SESSION['LoginForm.errors'] = $result;
                $this->redirect('/');
            }

            echo json_encode($result);
            exit;
        }
    }

    public function actionLogout() {
        Yii::app()->user->logout();
        $this->redirect(Yii::app()->homeUrl);
    }

    public function actionRegister($step = 1) {
        /** @var $user WebUser */
        $user = Yii::app()->user;
        $this->layout = '//layouts/edge';
        if (isset($_POST['step'])) $step = intval($_POST['step']);

        $model = new RegisterForm('step'. $step);
        $model->attributes = $user->getState('regform', null);

        if (isset($_POST['RegisterForm'])) {
            $model->attributes=$_POST['RegisterForm'];
            $model->phone = preg_replace('#[^0-9]#', '', $model->phone);
            $result = array();

            if($model->validate()) {
                $user->setState('regform', $model->attributes);

                // Непосредственно регистрируем пользователя
                if ($step == 5) {
                    $user->clearStates();

                    $user = new User();
                    /** @var $user User */
                    $user->email = $model->email;
                    $user->login = $model->login;
                    $salt = $user->generateSalt();
                    $password = $model->password;
                    $user->password = $user->hashPassword($password, $salt);
                    $user->salt = $salt;
                    $ustatus = $user->save();

                    if ($ustatus) {
                        $profile = new Profile();
                        $profile->city_id = $model->city;
                        $profile->attributes = $model->attributes;
                        $profile->phone = '7'. $profile->phone;
                        $profile->user_id = $user->id;
                        $profile->sms_notify = 1;
                        $pstatus = $profile->save();

                        if ($pstatus) {
                            /** @var $auth IAuthManager */
                            $auth = Yii::app()->getAuthManager();
                            $auth->assign('Пользователь', $user->id);

                            $loginform = new LoginForm();
                            $loginform->email = $model->email;
                            $loginform->password = $password;
                            $loginform->login();

                            $result['success'] = true;
                            $result['step'] = 5;
                            $result['id'] = $user->id;
                        }
                        else {
                            $user->delete();

                            foreach ($pstatus->getErrors() as $attr => $error) {
                                $result[$attr] = $error;
                            }
                        }
                    }
                    else {
                        foreach ($user->getErrors() as $attr => $error) {
                            $result[$attr] = $error;
                        }
                    }
                }
                else {
                    if ($step == 4) {
                        $this->actionSendSMSRegister();
                    }

                    $result['success'] = true;
                    $result['step'] = $step;
                }
            }
            else {
                foreach ($model->getErrors() as $attr => $error) {
                    $result[ActiveHtml::activeId($model, $attr)] = $error;
                }
            }

            echo json_encode($result);
            exit;
        }

        switch ($step) {
            case 1:
                $cities = City::model()->findAll();
                $array = array('model' => $model, 'cities' => $cities);
                break;
            default:
                $array = array('model' => $model);
                break;
        }

        if (Yii::app()->request->isAjaxRequest) {
            $this->pageHtml = $this->renderPartial('register/step'. $step, $array, true);
        }
        else $this->render('register/step'. $step, $array);
    }

    public function actionSendSMSRegister() {
        $user = Yii::app()->user;
        $model = new RegisterForm('step4');
        $model->attributes = $user->getState('regform', null);

        if ($model->phone) {
            PhoneConfirmation::model()->deleteAll('phone = :phone', array(':phone' => '7'. $model->phone));
            $pc = new PhoneConfirmation();
            $pc->phone = '7'. $model->phone;
            $pc->generateCode();
            $pc->save();

            $sms = new SmsDelivery(Yii::app()->params['smsUsername'], Yii::app()->params['smsPassword']);
            $sms->SendMessage($pc->phone, Yii::app()->params['smsNumber'], 'Ваш код регистрации '. $pc->code);

            echo json_encode(array(
                'success' => 'true',
                'step' => 4,
                'message' => 'Код отправлен',
            ));
        }
        else {
            echo json_encode(array(
                'message' => 'Данные неверны',
            ));
        }
        exit;
    }

    public function actionNullForm() {
        echo 'OK';
        exit;
    }
}