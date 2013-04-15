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

  public function actionAutoReportPhpError() {
    $report = new Autoreport();
    $report->user_id = Yii::app()->user->getId();
    $report->datetime = date("Y-m-d H:i:s");
    $report->url = substr($_POST['url'], 0, 100);
    $report->response = $_POST['text'];
    if (!$report->save()) {
      var_dump($report->getErrors());
    }

    exit;
  }

  public function _actionPreviewDialog() {
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

  public function actionViewMailTpl($template) {
    $this->renderPartial('//mail/'. $template);
    Yii::app()->end();
  }

  /**
   * Создает записи настроек оповещений пользователей (11.04.2013)
   */
  public function _actionPatch6() {
    $counter = 0;
    $users = Profile::model()->with(array('notifies' => array('joinType' => 'LEFT JOIN')))->findAll();
    /** @var $user Profile */
    foreach ($users as $user) {
      if (!$user->notifies) {
        $user->notifies = new ProfileNotify();
        $user->notifies->user_id = $user->user_id;
        $user->notifies->save();
        $counter++;
      }
    }

    echo "Обработано ". $counter ." пользователей";
    exit;
  }

  // Тест почтовых систем
  public function _actionPatch5() {
    Yii::import('application.vendors.*');
    require_once 'Mail/Mail.php';

    $mail = Mail::getInstance();
    $mail->setSender(array(Yii::app()->params['noreplymail'], Yii::app()->params['noreplyname']));
    $mail->IsMail();

    $mail->sendMail(Yii::app()->params['noreplymail'], Yii::app()->params['noreplyname'], '6akcuk@gmail.com', 'Тест почты', 'Письмо обыкновенное', true, null, null, null);
    $mail->ClearAddresses();
  }

  /**
   * Переводит старые заказы на новую систему
   *
   * От 05.02.2013
   *
   * Не актуален
   */
  public function _actionPatch4() {
    $user = new User();
    echo $user->hashPassword('79273413817', 'oh9p-epx4bb');
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
  public function _actionPatch2() {
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
  public function _actionPatch1() {
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
                else {
                  if (!isset($_SESSION['global.jumper'])) $this->redirect('/id'. Yii::app()->user->getId());
                  else {
                    $this->redirect($_SESSION['global.jumper']);
                    unset($_SESSION['global.jumper']);
                  }
                }
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

  public function actionInvite($id) {
    $_SESSION['invite.id'] = $id;
    $this->redirect('/register');
  }

    public function actionRegister($step = 1) {
        /** @var $user WebUser */
        $user = Yii::app()->user;
        $this->layout = '//layouts/edge';
        if (isset($_POST['step'])) $step = intval($_POST['step']);

        $model = new RegisterForm('step'. $step);
        $model->attributes = $user->getState('regform', null);

      if (isset($_SESSION['invite.id']) && !$model->invite_code) $model->invite_code = $_SESSION['invite.id'];

        if (isset($_POST['RegisterForm'])) {
            $model->attributes=$_POST['RegisterForm'];
            $model->phone = '7'. preg_replace('#[^0-9]#', '', $model->phone);
            $result = array();

            if($model->validate()) {
              $model->phone = substr($model->phone, 1);
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

                          $notify = new ProfileNotify();
                          $notify->user_id = $user->id;
                          $notify->save();

                          $cookies = Yii::app()->getRequest()->getCookies();
                          $cookies->remove('cur_city');

                          $city = new CHttpCookie('cur_city', intval($profile->city_id));
                          $city->expire = time() + (60 * 60 * 24 * 30 * 12 * 20);
                          $cookies->add('cur_city', $city);

                          if ($model->invite_code) {
                            $invite = new UserInvite();
                            $invite->master_id = $model->invite_code;
                            $invite->child_id = $user->id;
                            $invite->datetime = date("Y-m-d H:i:s");
                            $invite->save();

                            $reputation = new ProfileReputation();
                            $reputation->author_id = $user->id;
                            $reputation->owner_id = $model->invite_code;
                            $reputation->value = Yii::app()->getModule('users')->inviteReputationBonus;
                            $reputation->comment = 'За приглашение пользователя '. $user->login .' #'. $invite->invite_id;

                            if ($reputation->save()) {
                              $conn = $reputation->getDbConnection();
                              $command = $conn->createCommand("UPDATE `profiles` SET positive_rep = positive_rep + ". $reputation->value ." WHERE `user_id` = ". $reputation->owner_id);
                              $command->execute();
                            }
                          }

                          $result['success'] = true;
                          $result['step'] = 5;
                          $result['id'] = $user->id;

                          if (isset($_SESSION['global.jumper'])) {
                            $this->redirect($_SESSION['global.jumper']);
                            unset($_SESSION['global.jumper']);
                            Yii::app()->end();
                          }
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

  public function actionForgot() {
    $this->layout = '//layouts/edge';

    // Вторая страница восстановления пароля
    if (isset($_GET['code'])) {
      /** @var $user User */
      $user = (isset($_GET['user_id'])) ? User::model()->findByPk($_GET['user_id']) : User::model()->find('email = :mail', array(':mail' => $_GET['email']));

      if (!$user)
        throw new CHttpException(500, 'Пользователь не найден');

      if (strtotime($user->pwdresetstamp) < (time() - 300))
        throw new CHttpException(500, 'Сгенерируйте новый код восстановления, т.к. истек срок действия текущего');

      if ($user->pwdresetfaults > 5)
        throw new CHttpException(500, 'Вы ошиблись более 5 раз при вводе кода восстановления, сгенерируйте новый');

      if ($user->pwdresethash != $_GET['code']) {
        $user->pwdresetfaults++;
        $user->save(true, array('pwdresetfaults'));

        throw new CHttpException(500, 'Код восстановления не совпадает');
      }

      if (isset($_POST['new_password'])) {
        $new_pwd = trim($_POST['new_password']);
        $rpt_pwd = trim($_POST['new_password_rpt']);

        if ($new_pwd != $rpt_pwd) {
          $result['new_password'] = 'Пароли не совпадают';

          echo json_encode($result);
          exit;
        }

        if (strlen($new_pwd) < 3) {
          $result['new_password'] = 'Длина пароля не меньше 3-х символов';

          echo json_encode($result);
          exit;
        }

        $user->password = $user->hashPassword($new_pwd, $user->salt);
        $user->pwdresetfaults = 0;
        $user->pwdresethash = null;
        $user->pwdresetstamp = null;

        $user->save(true, array('password', 'pwdresetfaults', 'pwdresethash', 'pwdresetstamp'));

        // Сообщить об изменениях
        $sms = new SmsDelivery(Yii::app()->params['smsUsername'], Yii::app()->params['smsPassword']);
        $sms->SendMessage($user->profile->phone, Yii::app()->params['smsNumber'], 'На вашем аккаунте '. $user->email .' был изменен пароль');

        Yii::import('application.vendors.*');
        require_once 'Mail/Mail.php';

        $mail = Mail::getInstance();
        $mail->setSender(array(Yii::app()->params['noreplymail'], Yii::app()->params['noreplyname']));
        $mail->IsMail();

        $html = $this->renderPartial("//mail/report_change_password", array('password' => $new_pwd), true);

        $mail->sendMail(Yii::app()->params['noreplymail'], Yii::app()->params['noreplyname'], $user->email, 'Смена пароля на SPMIX', $html, true, null, null, null);
        $mail->ClearAddresses();

        $result = array('success' => true, 'msg' => 'Пароль успешно изменен');
        echo json_encode($result);
        exit;
      }

      if (Yii::app()->request->isAjaxRequest) {
        $this->pageHtml = $this->renderPartial('forgot2', array('code' => $_GET['code'], 'email' => $user->email), true);
      }
      else $this->render('forgot2', array('code' => $_GET['code'], 'email' => $user->email));
      return;
    }

    if (isset($_POST['email'])) {
      $email = $_POST['email'];
      $type = $_POST['type'];
      /** @var $user User */
      $user = User::model()->with('profile')->find('email = :mail', array(':mail' => $email));

      if (!$user)
        throw new CHttpException(404, 'Данный E-Mail не зарегистрирован на сайте');

      switch ($type) {
        case 'cellular':
          $pc = new PhoneConfirmation();
          $pc->generateCode();
          $user->pwdresetfaults = 0;
          $user->pwdresethash = $pc->code;
          $user->pwdresetstamp = date("Y-m-d H:i:s");
          $user->save(true, array('pwdresetfaults', 'pwdresethash', 'pwdresetstamp'));

          $sms = new SmsDelivery(Yii::app()->params['smsUsername'], Yii::app()->params['smsPassword']);
          $sms->SendMessage($user->profile->phone, Yii::app()->params['smsNumber'], 'Код восстановления '. $pc->code .'. Проигнорируйте, если вы не запрашивали.');

          $result['msg'] = 'Код восстановления отправлен на Ваш сотовый телефон '. preg_replace("/.*([0-9]{4})$/i", "*******$1", $user->profile->phone);
          break;
        case 'email':
          $user->pwdresetfaults = 0;
          $user->pwdresethash = md5(rand(1000000, 9999999) . $user->email);
          $user->pwdresetstamp = date("Y-m-d H:i:s");
          $user->save(true, array('pwdresetfaults', 'pwdresethash', 'pwdresetstamp'));

          Yii::import('application.vendors.*');
          require_once 'Mail/Mail.php';

          $mail = Mail::getInstance();
          $mail->setSender(array(Yii::app()->params['noreplymail'], Yii::app()->params['noreplyname']));
          $mail->IsMail();

          $html = $this->renderPartial("//mail/forgot_password", array('id' => $user->id, 'code' => $user->pwdresethash), true);

          $mail->sendMail(Yii::app()->params['noreplymail'], Yii::app()->params['noreplyname'], $user->email, 'Восстановление доступа к SPMIX', $html, true, null, null, null);
          $mail->ClearAddresses();

          $result['msg'] = 'Код восстановления отправлен на указанный Вами E-Mail';
          break;
      }

      echo json_encode($result);
      exit;
    }

    if (Yii::app()->request->isAjaxRequest) {
      $this->pageHtml = $this->renderPartial('forgot', null, true);
    }
    else $this->render('forgot', null);
  }
}