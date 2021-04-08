<?php

//use phpexcel\PHPExcel\Classes\PHPExcel;
require __DIR__ . '/../../../../vendor/phpexcel/PHPExcel/Classes/PHPExcel.php';

/**
 *
 **/
Yii::import('application.modules.company.models.*');
Yii::import('application.modules.store.models.*');
Yii::import('application.modules.currency.models.*');
Yii::import('application.modules.viewed.models.*');
Yii::import('application.modules.measurements.models.*');
Yii::import('application.modules.measurements.helpers.*');
class StatisticsController extends \yupe\components\controllers\FrontController
{
    public $user = null;
    public $company = null;
    public $cat_list = [];
    public $plan = null;
    public $id = '';
    
    public function filters()
    {
        return [
	    'accessControl',
            //['yupe\filters\YFrontAccessControl'],
        ];
    }
   
   public function accessRules()
   {
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('viber'),
				'users'=>array('*'),
			),
            array('allow',  // allow all users to perform 'index' and 'view' actions
            'actions'=>array('callback'),
            'users'=>array('*'),
            ),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('index','getdatagraphic'),
				'users'=>array('@'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
    }

    public function beforeAction($action)
    {
        $this->user = Yii::app()->getUser()->getProfile();

       /* if ($this->user === null) {

            Yii::app()->getUser()->setFlash(
                yupe\widgets\YFlashMessages::ERROR_MESSAGE,
                Yii::t('UserModule.user', 'User not found.')
            );

            Yii::app()->getUser()->logout();

            $this->redirect(
                ['/user/account/login']
            );
        }*/
        if ($this->user !== null) {
        	$this->company = Company::model()->find(['condition'=>'owner_id='.$this->user->id]);

        //if($this->user->getCompany()->sup_type !== '1'){
        //    $this->redirect(
        //        ['/cabinet']
        //    );
        //}
        	$this->plan = Plan::model()->findByPk($this->company->plan);
	}

        return true;
    }      
    
    public function actionViber()
    {                           
        if(Yii::app()->request->isAjaxRequest){
            $date = date("Y-m-d");
            $connection = Yii::app()->db;
            $this->id = $_POST['url'];
            $this->id = explode('/', $this->id);
            $this->id = array_reverse($this->id);
            if($this->id[1] == 'master'){
                $this->id = explode('-', $this->id[0]);
                $this->id = $this->id[0];
            } else if($this->id[1] == 'company'){
                $this->id = explode('-', $this->id[0]);
                $this->id = $this->id[0];
            } else if($this->id[1] == 'product'){
                $slug = explode('.', $this->id[0]);
                $this->id = Product::model()->getIdBySlug($slug[0]);
                $this->id = $this->id[0];
            } else if($this->id[1] == 'service'){
                $slug = explode('.', $this->id[0]);
                $this->id = Product::model()->getIdBySlug($slug[0]);
                $this->id = $this->id[0];
            }
            $command=$connection->createCommand('SELECT * FROM `main_company_viewed` WHERE `date`="'.$date.'" AND `company_id`="'.$this->id.'" ');
            $rows=$command->queryAll();
            $count = count($rows);

            if ($this->id>0) {
                if ($count==0) {
                    $command=$connection->createCommand('INSERT INTO `main_company_viewed` (`company_id`, `date`, `viber_click`) VALUES ('.$this->id.', "'.$date.'", 1)');
                    $command->execute();
                }
                else if ($count==1) {
                    $views = $rows[0]['viber_click']+1;
                    $command=$connection->createCommand('UPDATE `main_company_viewed` SET `viber_click` ="'.$views.'" WHERE `id`="'.$rows[0]['id'].'" ');
                    $command->execute();
                }
            }
	    }
    }

    public function actionCallback()
    {                           
        if(Yii::app()->request->isAjaxRequest){
            $date = date("Y-m-d");
            $connection = Yii::app()->db;
            $this->id = $_POST['url'];
            $this->id = explode('/', $this->id);
            $this->id = array_reverse($this->id);
            if($this->id[1] == 'master'){
                $this->id = explode('-', $this->id[0]);
                $this->id = $this->id[0];
            } else if($this->id[1] == 'company'){
                $this->id = explode('-', $this->id[0]);
                $this->id = $this->id[0];
            } else if($this->id[1] == 'product'){
                $slug = explode('.', $this->id[0]);
                $this->id = Product::model()->getIdBySlug($slug[0]);
                $this->id = $this->id[0];
            } else if($this->id[1] == 'service'){
                $slug = explode('.', $this->id[0]);
                $this->id = Product::model()->getIdBySlug($slug[0]);
                $this->id = $this->id[0];
            }
            $command=$connection->createCommand('SELECT * FROM `main_company_viewed` WHERE `date`="'.$date.'" AND `company_id`="'.$this->id.'" ');
            $rows=$command->queryAll();
            $count = count($rows);

            if ($this->id>0) {
                if ($count==0) {
                    $command=$connection->createCommand('INSERT INTO `main_company_viewed` (`company_id`, `date`, `callback`) VALUES ('.$this->id.', "'.$date.'", 1)');
                    $command->execute();
                }
                else if ($count==1) {
                    $views = $rows[0]['callback']+1;
                    $command=$connection->createCommand('UPDATE `main_company_viewed` SET `callback` ="'.$views.'" WHERE `id`="'.$rows[0]['id'].'" ');
                    $command->execute();
                }
            }
	    }
    }
    
    public function actionIndex()
    {
	// $statSearch = new StatSearch();
        // if (($data = Yii::app()->getRequest()->getQuery('StatSearch')) !== null) {
        //     $statSearch->setAttributes($data);
        // }

        // $sqlWhere = '';
        // if ($statSearch->date_from) {
        //     $sqlWhere .= ' AND `date` >="'.date('Y-m-d', strtotime($statSearch->date_from)).'"';
        // }
        // if ($statSearch->date_to) {
        //     $sqlWhere .= ' AND `date` <="'.date('Y-m-d', strtotime($statSearch->date_to)).'"';
        // }

        // $sql = 'SELECT DISTINCT `viewed`.`date`, `viewed`.`company_id` FROM
        //         ((SELECT `main_viewed`.`date`, `main_viewed`.`company_id` FROM `main_viewed` WHERE `main_viewed`.`company_id` = '.$this->company->id.$sqlWhere.')
        //         UNION ALL
        //         (SELECT `main_company_viewed`.`date`, `main_company_viewed`.`company_id` FROM `main_company_viewed` WHERE `main_company_viewed`.`company_id` = '.$this->company->id.$sqlWhere.')) `viewed`
        //         ORDER BY `viewed`.`date` DESC';

        // $count = Yii::app()->db->createCommand('SELECT COUNT(*) FROM (' . $sql . ') as count_alias')->queryScalar();

        // $dataProvider = new CSqlDataProvider($sql, [
        //     'keyField' => 'date',
        //     'totalItemCount' => $count,
        //     'pagination' => [
        //         'pageSize' => 20,
        //     ],
        // ]);
         // if (($this->company->id == 1833)||($this->company->id == 1101)){//||($this->company->id == 2505)||($this->company->id == 1101)) {
                $id_company = $this->company->id;
                //готуємо дані для відображення статистики
                //БАНЕР
                $date = date("Y-m-d");
                $date = strtotime($date);
                $date = strtotime("-31 day", $date);
                $date_t = date('Y-m-d', $date);

                $banner_id = array();
                $banner_id = Yii::app()->getDb()->createCommand()
                    ->select('image, id, start_time, end_time')
                    ->from('{{banner_banner}}')
                    ->where('company_id = :id_company', [':id_company' => $id_company])
                    ->queryAll();
                if (!empty($banner_id)) {
                    foreach ($banner_id as $key => $b) {
                        $banner_viewed = Yii::app()->getDb()->createCommand()
                            ->select('date, SUM(showed) AS showed, SUM(redirected) AS redir')
                            ->from('{{banner_views}}')
                            ->where('banner_id = :b', [':b' => $b['id']])
                            ->group('date')
                            ->queryAll();
                        //узнаєм загальну суму показів та переходів
                        if (!empty($banner_viewed)) {
                            $all_baner_vieved = 0;
                            $all_baner_red = 0;
                            foreach ($banner_viewed as $b_v) {
                                if(!empty($b_v['showed'])){
                                    $all_baner_vieved += $b_v['showed'];
                                }
                                if (!empty($b_v['redir'])) {
                                    $all_baner_red += $b_v['redir'];
                                }
                            }
                        }
                        $banner_id[$key]['all_baner_vieved'] = $all_baner_vieved;
                        $banner_id[$key]['all_baner_red'] = $all_baner_red;

                    }
                }

                //БАНЕР
                //СТОРІНКА КОМПАНІЇ ТА ПЕРЕГЛЯДИ ТЕЛЕФОНІВ
                $vieved_company = Yii::app()->getDb()->createCommand()
                    ->select('views, date, views_phone, viber_click, callback')  //viber_click
                    ->from('{{company_viewed}}')
                    ->where('company_id = :id_company AND date > :date_t', [':id_company' => $id_company, ':date_t' => $date_t])
                    ->order('date DESC')
                    ->queryAll();
                //СТОРІНКА КОМПАНІЇ
                $vieved_company_1 = array_reverse($vieved_company);
                $array_finish = array();
                $data_stroka = '[';
		        $data_viber_click = '[';
                $data_callback = '[';
                $data_viewed_company = '[';
                $data_viewed_product = '[';
                $data_viewed_phone = '[';
                foreach ($vieved_company_1 as $v) {
                    $data_stroka .= "'".date('d.m',strtotime($v['date']))."',";
                    $data_viewed_company .= "'".$v['views']."',";
                    $data_viewed_phone .= "'".$v['views_phone']."',";
		            $data_viber_click .= "'".$v['viber_click']."',";
                    $data_callback .= "'".$v['callback']."',";
                }
                $data_stroka = substr($data_stroka, 0, -1);
                $data_stroka = $data_stroka.']';

                $data_viber_click = substr($data_viber_click, 0, -1);
                $data_viber_click = $data_viber_click.']';

                $data_callback = substr($data_callback, 0, -1);
                $data_callback = $data_callback.']';

                $data_viewed_company = substr($data_viewed_company, 0, -1);
                $data_viewed_company = $data_viewed_company.']';

                $data_viewed_phone = substr($data_viewed_phone, 0, -1);
                $data_viewed_phone = $data_viewed_phone.']';

                //ПЕРЕГЛЯД ПРОДУКТІВ
                $viewed_products = Yii::app()->getDb()->createCommand()
                    ->select('SUM(views) AS viewed_product, date')
                    ->from('{{viewed}}')
                    ->where('company_id = :id_company AND date > :date_t', [':id_company' => $id_company, ':date_t' => $date_t])
                    ->group('date')
                    ->order('date DESC')
                    ->queryAll();

                //ПЕРЕГЛЯД ПРОДУКТІВ
                 $viewed_products_1 = array_reverse($viewed_products);
                 foreach ($viewed_products_1 as $v) {
                     $data_viewed_product .= "'".$v['viewed_product']."',";
                 }
                 $data_viewed_product = substr($data_viewed_product, 0, -1);
                 $data_viewed_product = $data_viewed_product.']';
                $this->render('new_statistic',
                    [
                        'banner' => $banner_id,
                        'vieved_company_phone' => $vieved_company,
                        'viewed_products' => $viewed_products,
                        'data_stroka' => $data_stroka,
                        'data_viewed_company' => $data_viewed_company,
                        'data_viewed_phone' => $data_viewed_phone,
                        'data_viewed_product' => $data_viewed_product,
			            'data_viber_click' => $data_viber_click,
                        'data_callback' => $data_callback,
                        'company' => $this->company,
                    ]
                );
         // } else{
         //    $this->render('index',
         //        [
         //            'dataProvider' => $dataProvider,
         //            'company' => $this->company,
         //            'statSearch' => $statSearch,

         //        ]
         //    );
         // }
    }
    public function actiongetdatagraphic($arr_data){
        $id_company = $this->company->id;
        $arr_d = explode('-', $arr_data);
        $period = $arr_d[0];
        $select = $arr_d[1];
        // $date = date("Y-m-d");
        // $date = strtotime($date);
        // $date = strtotime("-31 day", $date);
        // $date_t = date('Y-m-d', $date);

        //визначаємо вибраний графік
        if($select == 's_2'){
            //виборку робимо в залежності від періоду
            if ($period == 'p_2'){
                $vieved_company = Yii::app()->getDb()->createCommand()
                    ->select('SUM(views) AS views, date')
                    ->from('{{company_viewed}}')
                    ->where('company_id = :id_company', [':id_company' => $id_company])
                    ->group('YEAR(date), MONTH(date)')
                    ->order('id')
                    ->queryAll();
                $str_date = array();
                $str_viewed = array();
                foreach ($vieved_company as $v_c) {
                    $str_date[] = date("m/Y",strtotime($v_c['date']));
                    $str_viewed[] = $v_c['views'];
                }
                $data['text_legend'] = 'Ежемесячная статистика просмотра компании за весь период';
                $data['str_date'] = $str_date;
                $data['viewed'] = $str_viewed;
                $result = json_encode($data);
                echo $result;
            }else if($period == 'p_3'){
                $vieved_company = Yii::app()->getDb()->createCommand()
                    ->select('SUM(views) AS views, date')
                    ->from('{{company_viewed}}')
                    ->where('company_id = :id_company', [':id_company' => $id_company])
                    ->group('YEAR(date)')
                    ->order('id')
                    ->queryAll();
                $str_date = array();
                $str_viewed = array();
                foreach ($vieved_company as $v_c) {
                    $str_date[] = date("Y",strtotime($v_c['date']));
                    $str_viewed[] = $v_c['views'];
                }
                $data['text_legend'] = 'Сравнительная годовая статистика просмотра компании';
                $data['str_date'] = $str_date;
                $data['viewed'] = $str_viewed;
                $result = json_encode($data);
                echo $result;
            }
        } else if($select == 's_3'){
            if ($period == 'p_2'){
                $viewed_products = Yii::app()->getDb()->createCommand()
                    ->select('SUM(views) AS viewed_product, date')
                    ->from('{{viewed}}')
                    ->where('company_id = :id_company', [':id_company' => $id_company])
                    ->group('YEAR(date), MONTH(date)')
                    ->order('id')
                    ->queryAll();
                $str_date = array();
                $str_viewed = array();
                foreach ($viewed_products as $v_c) {
                    $str_date[] = date("m/Y",strtotime($v_c['date']));
                    $str_viewed[] = $v_c['viewed_product'];
                }
                $data['text_legend'] = 'Ежемесячная статистика просмотра обьявлений за весь период';
                $data['str_date'] = $str_date;
                $data['viewed'] = $str_viewed;
                $result = json_encode($data);
                echo $result;
            }else if($period == 'p_3'){
                $viewed_products = Yii::app()->getDb()->createCommand()
                    ->select('SUM(views) AS viewed_product, date')
                    ->from('{{viewed}}')
                    ->where('company_id = :id_company', [':id_company' => $id_company])
                    ->group('YEAR(date)')
                    ->order('id')
                    ->queryAll();
                $str_date = array();
                $str_viewed = array();
                foreach ($viewed_products as $v_c) {
                    $str_date[] = date("Y",strtotime($v_c['date']));
                    $str_viewed[] = $v_c['viewed_product'];
                }
                $data['text_legend'] = 'Сравнительная годовая статистика просмотра обьявлений';
                $data['str_date'] = $str_date;
                $data['viewed'] = $str_viewed;
                $result = json_encode($data);
                echo $result;
            }
        } else if($select == 's_4'){
            if ($period == 'p_2'){
                $vieved_company = Yii::app()->getDb()->createCommand()
                    ->select('SUM(views_phone) AS views_phone, SUM(successfully_phone) AS successfully_phone, SUM(unsuccessfully_phone) AS unsuccessfully_phone, date')
                    ->from('{{company_viewed}}')
                    ->where('company_id = :id_company', [':id_company' => $id_company])
                    ->group('YEAR(date), MONTH(date)')
                    ->order('id')
                    ->queryAll();
                $str_date = array();
                $str_viewed = array();
                $str_sucviewed = array();
                $str_unsucviewed = array();
                foreach ($vieved_company as $v_c) {
                    $str_date[] = date("m/Y",strtotime($v_c['date']));
                    $str_viewed[] = $v_c['views_phone'];
                    $str_sucviewed[] = $v_c['successfully_phone'];
                    $str_unsucviewed[] = $v_c['unsuccessfully_phone'];
                }
                $data['text_legend'] = 'Ежемесячная статистика просмотра номеров телефона за весь период';
                $data['str_date'] = $str_date;
                $data['viewed'] = $str_viewed;
                $data['successfully_phone'] = $str_sucviewed;
                $data['unsuccessfully_phone'] = $str_unsucviewed;
                $result = json_encode($data);
                echo $result;
            }else if($period == 'p_3'){
                $vieved_company = Yii::app()->getDb()->createCommand()
                    ->select('SUM(views_phone) AS views_phone, SUM(successfully_phone) AS successfully_phone, SUM(unsuccessfully_phone) AS unsuccessfully_phone, date')
                    ->from('{{company_viewed}}')
                    ->where('company_id = :id_company', [':id_company' => $id_company])
                    ->group('YEAR(date)')
                    ->order('id')
                    ->queryAll();
                $str_date = array();
                $str_viewed = array();
                $str_sucviewed = array();
                $str_unsucviewed = array();
                foreach ($vieved_company as $v_c) {
                    $str_date[] = date("Y",strtotime($v_c['date']));
                    $str_viewed[] = $v_c['views_phone'];
                    $str_sucviewed[] = $v_c['successfully_phone'];
                    $str_unsucviewed[] = $v_c['unsuccessfully_phone'];
                }
                $data['text_legend'] = 'Сравнительная годовая статистика просмотра номеров телефона';
                $data['str_date'] = $str_date;
                $data['viewed'] = $str_viewed;
                $data['successfully_phone'] = $str_sucviewed;
                $data['unsuccessfully_phone'] = $str_unsucviewed;
                $result = json_encode($data);
                echo $result;
            }
        } else if($select == 's_5'){
            if ($period == 'p_2'){
                $vieved_company = Yii::app()->getDb()->createCommand()
                    ->select('date')
                    ->from('{{company_viewed}}')
                    ->where('company_id = :id_company', [':id_company' => $id_company])
                    ->group('YEAR(date), MONTH(date)')
                    ->order('id')
                    ->queryAll();
                $red_company = Yii::app()->getDb()->createCommand()
                    ->select('SUM(hits) AS hits, date')
                    ->from('{{company_hits}}')
                    ->where('company_id = :id_company', [':id_company' => $id_company])
                    ->group('YEAR(date), MONTH(date)')
                    ->order('id')
                    ->queryAll();
                $str_date = array();
                $str_viewed = array();
                foreach ($vieved_company as $v_c) {
                    $status = 0;
                    $dat = date("m/Y",strtotime($v_c['date']));
                    foreach ($red_company as $v) {
                        $dat2 = date("m/Y",strtotime($v['date']));
                        if ($dat == $dat2) {
                            $str_date[] = $dat2;
                            $str_viewed[] = $v['hits'];
                            $status = 1;
                        }
                    }
                    if ($status == 0) {
                        $str_date[] = $dat;
                        $str_viewed[] = '0';
                        $status = 0;
                    }
                }
                $data['text_legend'] = 'Ежемесячная статистика переходов на сайт компании за весь период';
                $data['str_date'] = $str_date;
                $data['viewed'] = $str_viewed;
                $result = json_encode($data);
                echo $result;
            }else if($period == 'p_3'){
                $vieved_company = Yii::app()->getDb()->createCommand()
                    ->select('date')
                    ->from('{{company_viewed}}')
                    ->where('company_id = :id_company', [':id_company' => $id_company])
                    ->group('YEAR(date)')
                    ->order('id')
                    ->queryAll();
                $red_company = Yii::app()->getDb()->createCommand()
                    ->select('SUM(hits) AS hits, date')
                    ->from('{{company_hits}}')
                    ->where('company_id = :id_company', [':id_company' => $id_company])
                    ->group('YEAR(date)')
                    ->order('id')
                    ->queryAll();
                $str_date = array();
                $str_viewed = array();
                foreach ($vieved_company as $v_c) {
                    $status = 0;
                    $dat = date("Y",strtotime($v_c['date']));
                    foreach ($red_company as $v) {
                        $dat2 = date("Y",strtotime($v['date']));
                        if ($dat == $dat2) {
                            $str_date[] = $dat2;
                            $str_viewed[] = $v['hits'];
                            $status = 1;
                        }
                    }
                    if ($status == 0) {
                        $str_date[] = $dat;
                        $str_viewed[] = '0';
                        $status = 0;
                    }
                }
                $data['text_legend'] = 'Сравнительная годовая статистика переходов на сайт компании';
                $data['str_date'] = $str_date;
                $data['viewed'] = $str_viewed;
                $result = json_encode($data);
                echo $result;
            }
        } else if($select == 's_6'){
            if ($period == 'p_2'){
                $vieved_company = Yii::app()->getDb()->createCommand()
                    ->select('SUM(viber_click) AS viber_click, date')
                    ->from('{{company_viewed}}')
                    ->where('company_id = :id_company', [':id_company' => $id_company])
                    ->group('YEAR(date), MONTH(date)')
                    ->order('id')
                    ->queryAll();
                $str_date = array();
                $str_viewed = array();     
                foreach ($vieved_company as $v_c) {
                    $str_date[] = date("m/Y",strtotime($v_c['date']));
                    $str_viewed[] = $v_c['viber_click'];              
                }
                $data['text_legend'] = 'Ежемесячная статистика просмотра Viber за весь период';
                $data['str_date'] = $str_date;
                $data['viewed'] = $str_viewed;                   
                $result = json_encode($data);
                echo $result;
            }else if($period == 'p_3'){
                $vieved_company = Yii::app()->getDb()->createCommand()
                    ->select('SUM(viber_click) AS viber_click, date')
                    ->from('{{company_viewed}}')
                    ->where('company_id = :id_company', [':id_company' => $id_company])
                    ->group('YEAR(date)')
                    ->order('id')
                    ->queryAll();
                $str_date = array();
                $str_viewed = array();
                foreach ($vieved_company as $v_c) {
                    $str_date[] = date("Y",strtotime($v_c['date']));
                    $str_viewed[] = $v_c['viber_click'];              
                }
                $data['text_legend'] = 'Сравнительная годовая статистика просмотра Viber';
                $data['str_date'] = $str_date;
                $data['viewed'] = $str_viewed;                   
                $result = json_encode($data);
                echo $result;
            }
        } else if($select == 's_7'){
            if ($period == 'p_2'){
                $vieved_company = Yii::app()->getDb()->createCommand()
                    ->select('SUM(callback) AS callback, date')
                    ->from('{{company_viewed}}')
                    ->where('company_id = :id_company', [':id_company' => $id_company])
                    ->group('YEAR(date), MONTH(date)')
                    ->order('id')
                    ->queryAll();
                $str_date = array();
                $str_viewed = array();     
                foreach ($vieved_company as $v_c) {
                    $str_date[] = date("m/Y",strtotime($v_c['date']));
                    $str_viewed[] = $v_c['callback'];              
                }
                $data['text_legend'] = 'Ежемесячная статистика заказов обратного звонка за весь период';
                $data['str_date'] = $str_date;
                $data['viewed'] = $str_viewed;                   
                $result = json_encode($data);
                echo $result;
            }else if($period == 'p_3'){
                $vieved_company = Yii::app()->getDb()->createCommand()
                    ->select('SUM(callback) AS callback, date')
                    ->from('{{company_viewed}}')
                    ->where('company_id = :id_company', [':id_company' => $id_company])
                    ->group('YEAR(date)')
                    ->order('id')
                    ->queryAll();
                $str_date = array();
                $str_viewed = array();
                foreach ($vieved_company as $v_c) {
                    $str_date[] = date("Y",strtotime($v_c['date']));
                    $str_viewed[] = $v_c['callback'];              
                }
                $data['text_legend'] = 'Сравнительная годовая статистика заказов обратного звонка';
                $data['str_date'] = $str_date;
                $data['viewed'] = $str_viewed;                   
                $result = json_encode($data);
                echo $result;
            }
        }
    }

    public function getdatatable(){
        echo '<pre>';
        print_r('hello');
        echo '<pre>';
        die('ok');
    }
}
