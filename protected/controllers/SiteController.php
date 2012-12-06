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
            $result = array();

            if($model->validate()) {
                $user->setState('regform', $model->attributes);

                // Непосредственно регистрируем пользователя
                if ($step == 4) {
                    $user->clearStates();

                    $user = new User();
                    /** @var $user User */
                    $user->email = $model->email;
                    $salt = $user->generateSalt();
                    $password = $model->password;
                    $user->password = $user->hashPassword($password, $salt);
                    $user->salt = $salt;
                    $ustatus = $user->save();

                    if ($ustatus) {
                        $profile = new Profile();
                        $profile->city_id = $model->city;
                        $profile->attributes = $model->attributes;
                        $profile->user_id = $user->id;
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
                            $result['step'] = 4;
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
        PhoneConfirmation::model()->deleteAll('phone = :phone', array(':phone' => $_POST['phone']));
        $pc = new PhoneConfirmation();
        $pc->phone = $_POST['phone'];
        $pc->generateCode();
        $pc->save();

        echo json_encode(array(
            'message' => 'Код отправлен '. $pc->code,
        ));
        exit;
    }
}