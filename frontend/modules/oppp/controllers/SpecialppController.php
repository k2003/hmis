<?php

namespace frontend\modules\oppp\controllers;

use yii\web\Controller;
use yii\db\Query;
use Yii;
use yii\data\ArrayDataProvider;
use mdm\admin\components\AccessControl;
use yii\filters\VerbFilter;

class SpecialppController extends Controller {  
    
    public function behaviors(){
        return [
            'access' => [
                'class' => AccessControl::className(),
            ],

            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }
    public function actionIndex() {

        $date1 = "";
        $date2 = "";
        if (Yii::$app->request->isPost) {
            $date1 = $_POST['date1'];
            $date2 = $_POST['date2'];
        }
          
        
        $sql = "SELECT pp.pp_special_id AS pp_id
        ,(SELECT hospitalcode FROM opdconfig) AS hospcode
        ,p.person_id AS pid
        ,CONCAT(p.pname,p.fname,' ',p.lname) AS full_name
        ,o.seq_id AS seq
        ,v.vstdate AS date_serv
        ,ppt.pp_special_type_name AS ppspecial
        ,doc.name AS  provider
        ,pp.entry_datetime AS d_update
        FROM pp_special pp
        INNER JOIN pp_special_type ppt ON ppt.pp_special_type_id=pp.pp_special_type_id
        LEFT OUTER JOIN vn_stat v On v.vn=pp.vn
        LEFT OUTER JOIN  ovst_seq o ON o.vn=v.vn
        LEFT OUTER JOIN person p ON p.cid=v.cid
        INNER JOIN doctor doc ON doc.code=pp.doctor
        WHERE v.vstdate  BETWEEN '$date1' AND '$date2'";
        
        $data = Yii::$app->db2->createCommand($sql)->queryAll();
        $dataProvider = new ArrayDataProvider([
            'allModels'=>$data,
        ]);

        return $this->render('index', ['dataProvider' => $dataProvider, 'date1' => $date1, 'date2' => $date2]);
    }  
}

