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

	public function actionIndex()
	{
        if (!Yii::app()->user->getIsGuest()) {
            $this->redirect('http://spmix.ru/id'. Yii::app()->user->getId());
        }

        $this->render('index');
	}

    public function actionSetCity() {
        $cookies = Yii::app()->getRequest()->getCookies();
        $cookies->add('cur_city', intval($_POST['city_id']));

        echo json_encode(array('success' => true, 'msg' => 'Изменения сохранены'));
        exit;
    }

    public function actionError() {
        if($error=Yii::app()->errorHandler->error)
        {
            if(Yii::app()->request->isAjaxRequest)
                echo $error['message'];
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
                $result['success'] = true;
                $result['id'] = Yii::app()->user->getId();
            }
            else {
                foreach ($model->getErrors() as $attr => $error) {
                    $result[ActiveHtml::activeId($model, $attr)] = $error;
                }
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
            PhoneConfirmation::model()->deleteAll('phone = :phone', array(':phone' => $model->phone));
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
}