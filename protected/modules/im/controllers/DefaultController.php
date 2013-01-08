<?php

class DefaultController extends Controller
{
    public function filters() {
        return array(
            array(
                'ext.AjaxFilter.AjaxFilter'
            ),
            array(
                'ext.RBACFilter.RBACFilter'
            ),
            array(
                'ext.DevelopFilter'
            ),
        );
    }

    public function init() {
        parent::init();

        if (isset($_GET['sel']) && $_GET['sel'] == -1)
            $this->defaultAction = 'create';
    }

    public function actionIndex($offset = 0)
	{
        $c = (isset($_REQUEST['c'])) ? $_REQUEST['c'] : array();

        $criteria = new CDbCriteria();
        $criteria->limit = $this->module->dialogsPerPage;
        $criteria->offset = $offset;

        $criteria->addCondition('t.member_id = :id');
        $criteria->params[':id'] = Yii::app()->user->getId();
        $criteria->order = 'lastMessage.creation_date';

        $dialogs = DialogMember::model()->with('dialog', 'dialog.lastMessage')->findAll($criteria);

        $criteria->limit = 0;
        $dialogsNum = DialogMember::model()->with('dialog', 'dialog.lastMessage')->count($criteria);

        if (Yii::app()->request->isAjaxRequest) {
            if (isset($_POST['pages'])) {
                $this->pageHtml = $this->renderPartial('_dialog', array(
                    'dialogs' => $dialogs,
                    'offset' => $offset,
                ), true);
            }
            else $this->pageHtml = $this->renderPartial('index', array(
                'dialogs' => $dialogs,
                'c' => $c,
                'offset' => $offset,
                'offsets' => $dialogsNum,
            ), true);
        }
        else $this->render('index', array('peoples' => $dialogs, 'c' => $c, 'offset' => $offset, 'offsets' => $dialogsNum,));
	}
}