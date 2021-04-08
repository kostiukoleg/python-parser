<?php
use yupe\components\controllers\FrontController;
Yii::import('application.modules.search.models.*');

class SearchController extends FrontController
{
    public $breadcrumbs = array();
    //Метод первоначальной загрузки страницы поиска
    public function actionSearch()
    {
 		if (Yii::app()->getRequest()->getParam('request')) {

 			$request1 = Yii::app()->getRequest()->getParam('request');
 			$request = explode(" ", $request1);

 			$request = array_filter($request, function($element) {
			    return !empty($element);
			});
			//$n_request = count($request);

 			$regexp ="";
 			foreach ($request as $re) {
 				$re = mb_strtoupper($re);
 				if ($re != '') {
 					$regexp .= " AND UPPER(p.name) REGEXP '[[:<:]]".$re."'";
 				}
 			}
 			//$regexp = substr($regexp, 0, -1);
            //Определяем общее число товаров соответствующих запросу
            try {
                $connection = Yii::app()->db;
                $command = $connection->createCommand("SELECT c.locale_r, c.locale_c, p.category_id, b.slug, b.name, p.sup_type  FROM main_store_product p LEFT JOIN main_company c ON (c.id = p.company_id) LEFT JOIN main_store_category b ON (b.id = p.category_id) WHERE (p.sup_type = 1 OR p.sup_type = 2) AND p.status = 1".$regexp."");
                $rows_products_all=$command->queryAll();
                $command->execute();
            } catch (Exception $e) {
                echo json_encode( 'Выброшено исключение: ',  $e->getMessage(), "\n");
            }
            $num_products = 0;
            $num_service = 0;
            //Выбираем все регионы
            try {
                $connection = Yii::app()->db;
                $command = $connection->createCommand("SELECT id, title   FROM main_locale WHERE type = 1");
                $list_regions1 = $command->queryAll();
            } catch (Exception $e) {
                echo json_encode( 'Выброшено исключение: ',  $e->getMessage(), "\n");
            }
            $list_regions = array();
            foreach ($list_regions1 as $reg) {
                $list_regions[$reg['id']]['title'] = $reg['title'];
                $list_regions[$reg['id']]['count'] = 0;
            }
            $region = Yii::app()->getRequest()->getPost('region');
            //Выбираем все города
            //Если регион не равен 0 то вытягиваем его города
            $list_city = array();
            if ($region != 0) {
                try {
                    $connection = Yii::app()->db;
                    $command = $connection->createCommand("SELECT id, title   FROM main_locale WHERE type = 3 AND parent_id = ".$region."");
                    $list_city1 = $command->queryAll();
                } catch (Exception $e) {
                    echo json_encode( 'Выброшено исключение: ',  $e->getMessage(), "\n");
                }
                foreach ($list_city1 as $reg) {
                    $list_city[$reg['id']]['title'] = $reg['title'];
                    $list_city[$reg['id']]['count'] = 0;
                }
            }
            //Подсчитываем количество товаров по регионам и категориям
            $category_list = array();
            foreach ($rows_products_all as $r_p_a) {

                if (isset($list_regions[$r_p_a['locale_r']])) {
                    $list_regions[$r_p_a['locale_r']]['count'] = $list_regions[$r_p_a['locale_r']]['count']+1;
                }
                if (isset($category_list[$r_p_a['category_id']])) {
                    $category_list[$r_p_a['category_id']]['count'] = $category_list[$r_p_a['category_id']]['count'] +1;
                }
                else{
                    $category_list[$r_p_a['category_id']]['count'] = 1;
                    $category_list[$r_p_a['category_id']]['slug'] = $r_p_a['slug'];
                    $category_list[$r_p_a['category_id']]['name'] = $r_p_a['name'];
                }
                if ($r_p_a['sup_type'] == 1) {
                    $num_products++;
                }
                if ($r_p_a['sup_type'] == 2) {
                    $num_service++;
                }
            }
            $cat_li = array();
            //Сортируем категории по количеству результатов (для начала делаем его двумерным так проще))
            foreach ($category_list as $key => $c_l) {
               $cat_li[$key] = $c_l['count'];
            }
            arsort($cat_li);
            $i = 0;
            $c = array();
            foreach ($cat_li as $key => $value) {
                $c[$key] = $category_list[$key];
                $i++;
            }
            $category_list = $c;
            //Выбираем первых 5 результатов по товарам для отображения на главной странице поиска
            try {
                $connection = Yii::app()->db;
                $command = $connection->createCommand("SELECT p.id, p.is_special, p.top_level, p.currency_id, p.name, p.slug, uu.phone, cc.phone_2, cc.phone_3, cc.phone_4, p.price, mc.value, p.description, p.image, p.company_id, p.measure, c.plan, c.name AS company_name, l.title FROM main_store_product p LEFT JOIN main_company c ON (c.id = p.company_id) LEFT JOIN main_company_contacts cc ON(cc.company_id = c.id) LEFT JOIN main_user_user uu ON(uu.id = c.owner_id) LEFT JOIN main_locale l ON(l.id = c.locale_c) LEFT JOIN main_currency mc ON (mc.id = p.currency_id) WHERE p.sup_type = 1 AND p.status = 1".$regexp." ORDER BY p.top_level DESC, p.update_time DESC, p.create_time DESC, p.real_update_time DESC, p.create_time DESC LIMIT 5 ");
                $rows_products=$command->queryAll();
                $command->execute();
            } catch (Exception $e) {
                echo json_encode( 'Выброшено исключение: ',  $e->getMessage(), "\n");
            }
            //Выбираем первых 5 результатов по услугам для отображения на главной странице поиска
            try {
                $connection = Yii::app()->db;
                $command = $connection->createCommand("SELECT p.id, p.is_special, p.top_level, p.currency_id, p.name, p.slug, uu.phone, cc.phone_2, cc.phone_3, cc.phone_4, p.price, mc.value, p.description, p.image, p.company_id, p.measure, c.plan, c.name AS company_name, l.title FROM main_store_product p LEFT JOIN main_company c ON (c.id = p.company_id) LEFT JOIN main_company_contacts cc ON(cc.company_id = c.id) LEFT JOIN main_user_user uu ON(uu.id = c.owner_id) LEFT JOIN main_locale l ON(l.id = c.locale_c) LEFT JOIN main_currency mc ON (mc.id = p.currency_id) WHERE p.sup_type = 2 AND p.status = 1".$regexp." ORDER BY p.top_level DESC, p.update_time DESC, p.create_time DESC, p.real_update_time DESC, p.create_time DESC LIMIT 5");
                $rows_servise=$command->queryAll();
                $command->execute();
            } catch (Exception $e) {
                echo json_encode( 'Выброшено исключение: ', $e->getMessage(), "\n");
            }
	 	}
        //Если есть валюта то преобразовуем цены на сайте
        $all_pos = $num_products + $num_service;
        $this->render(
            'index',
            [
                'result_search_servise' => $rows_servise,
                'result_search_product' => $rows_products,
                'num_service' => $num_service,
                'num_products' => $num_products,
                'request' => $request1,
                'all_pos' => $all_pos,
                'list_regions' => $list_regions,
                'category_list' => $category_list,
                'list_city' => $list_city
            ]
        );
    }
    //МЕТОДЫ С КОТОРЫМЫ РАБОТАЮТ AJAX ЗАПРОСЫ
    //Метод определения количества всех товаров и услуг с учетом фильтров
    public function actionSelectPrice(){
        $price_err = '';
        //Считываем все значения фильтров
        $request = Yii::app()->getRequest()->getPost('request');
        $page = Yii::app()->getRequest()->getPost('page');
        $view = Yii::app()->getRequest()->getPost('view');
        $sup_type = Yii::app()->getRequest()->getPost('sup_type');
        $region = Yii::app()->getRequest()->getPost('region');
        $city = Yii::app()->getRequest()->getPost('city');
        $price_from = Yii::app()->getRequest()->getPost('price_from');
        $price_to = Yii::app()->getRequest()->getPost('price_to');
        $category = Yii::app()->getRequest()->getPost('category');
        $num_products = 0;
        //Формируем условия фильтра
            /*
            p - таблица товара
            c - таблица категории
            b - таблица компании
            l - таблица городов и областей
            */
            //Формируем часть запроса SELECT
            //Определяем количество товаров
            //Формируем часть запроса WHERE
            if ($sup_type == 1) {
                $where = 'WHERE p.sup_type = 1 AND p.status = 1';
                $where_no_cat = 'WHERE p.sup_type = 1 AND p.status = 1';
            }
            else if ($sup_type == 2) {
                $where = 'WHERE p.sup_type = 2 AND p.status = 1';
                $where_no_cat = 'WHERE p.sup_type = 2 AND p.status = 1';
            }
            else if ($sup_type == 0) {
                $where = 'WHERE (p.sup_type = 1 OR p.sup_type = 2) AND p.status = 1';
                $where_no_cat = 'WHERE (p.sup_type = 1 OR p.sup_type = 2) AND p.status = 1';
            }
            if ($price_from != 0) {
                $where .= ' AND p.price > '.$price_from.'';
                $where_no_cat .= ' AND p.price > '.$price_from.'';
            }
            if ($price_to > $price_from) {
                $where .= ' AND p.price < '.$price_to.'';
                $where_no_cat .= ' AND p.price < '.$price_to.'';
            }
            if ($category != 0) {
                $where .= ' AND p.category_id = '.$category.'';
            }
            $where_no_reg = $where;
            if ($region != 0){
                $where .= ' AND b.locale_r = '.$region.'';
                $where_no_cat .= ' AND b.locale_r = '.$region.'';
            }
            if ($city != 0) {
                $where .= ' AND b.locale_c = '.$city.'';
                $where_no_cat .= ' AND b.locale_c = '.$city.'';
            }
            $request = explode(" ", $request);

            $request = array_filter($request, function($element) {
                return !empty($element);
            });
            //$n_request = count($request);
            $rege = '';
            foreach ($request as $re) {
                $re = mb_strtoupper($re);
                if ($re != '') {
                    $where .= " AND UPPER(p.name) REGEXP '[[:<:]]".$re."'";
                    $where_no_reg .= " AND UPPER(p.name) REGEXP '[[:<:]]".$re."'";
                    $where_no_cat .= " AND UPPER(p.name) REGEXP '[[:<:]]".$re."'";
                    $rege .= " AND UPPER(p.name) REGEXP '[[:<:]]".$re."'";
                }
            }
            //Запускаем сам запрос к БД
            //Определяем общее число товаров соответствующих запросу без учета региона
            try {
                $connection = Yii::app()->db;
                $command = $connection->createCommand("SELECT b.locale_r, b.locale_c FROM main_store_product p LEFT JOIN main_company b ON (b.id = p.company_id) LEFT JOIN main_store_category c ON (c.id = p.category_id) ".$where_no_reg."");
                $rows_products_all_no_reg=$command->queryAll();
                $command->execute();
            } catch (Exception $e) {
                $price_err .= 'Определяем общее число товаров соответствующих запросу без учета региона ' . $e->getMessage() . "\n";
            }
            //Выбираем все регионы
            try {
                $connection = Yii::app()->db;
                $command = $connection->createCommand("SELECT id, title FROM main_locale WHERE type = 1");
                $list_regions1 = $command->queryAll();
            } catch (Exception $e) {
                $price_err .= 'Выбираем все регионы: ' . $e->getMessage() . "\n";
            }
            $list_regions = array();
            foreach ($list_regions1 as $reg) {
                $list_regions[$reg['id']]['title'] = $reg['title'];
                $list_regions[$reg['id']]['count'] = 0;
            }
            //Если регион не равен 0 то вытягиваем его города
            if ($region != 0) {
                $list_city = array();
                try {
                    $connection = Yii::app()->db;
                    $command = $connection->createCommand("SELECT id, title FROM main_locale WHERE type = 3 AND parent_id = ".$region."");
                    $list_city1 = $command->queryAll();
                } catch (Exception $e) {
                    $price_err .= 'Если регион не равен 0 то вытягиваем его города ' . $e->getMessage() . "\n";
                }
                foreach ($list_city1 as $reg) {
                    $list_city[$reg['id']]['title'] = $reg['title'];
                    $list_city[$reg['id']]['count'] = 0;
                }
            }
            foreach ($rows_products_all_no_reg as $r_p_a) {
                if (isset($list_regions[$r_p_a['locale_r']])) {
                    $list_regions[$r_p_a['locale_r']]['count']++;
                }
                if ($region != 0) {
                    if(isset($list_city[$r_p_a['locale_c']])){
                        $list_city[$r_p_a['locale_c']]['count']++;
                    }
                }
            }
            //Определяем общее число товаров соответствующих запросу
            try {
                $connection = Yii::app()->db;
                $command = $connection->createCommand("SELECT b.locale_r, b.locale_c, p.category_id, p.sup_type, c.slug, c.name FROM main_store_product p LEFT JOIN main_company b ON (b.id = p.company_id) LEFT JOIN main_store_category c ON (c.id = p.category_id) ".$where."");
                $rows_products_all=$command->queryAll();
                $command->execute();
            } catch (Exception $e) {
                $price_err .= 'Определяем общее число товаров соответствующих запросу ' . $e->getMessage() . "\n";
            }
            $num_products = count($rows_products_all);
            $serv_all = 0;
            $prod_all = 0;
            //Определяемся количество товаров и услуг
            foreach ($rows_products_all as $r_v) {
                if ($r_v['sup_type'] == '1') {
                    $prod_all++;
                }
                if ($r_v['sup_type'] == '2') {
                    $serv_all++;
                }
            }
            //Если мы на главной странице поиска то выбираем нужное количество товаров и услуг для отображения
            if ($page == 0) {
                //Вытягиваем товары
                //Формируем условие выборки
                $select_products = 'WHERE p.sup_type = 1 AND p.status = 1';
                if ($region != 0) {
                    $select_products .= ' AND b.locale_r = '.$region.'';
                }
                if ($city != 0) {
                    $select_products .= ' AND b.locale_c = '.$city.'';
                }
                if ($category != 0) {
                    $select_products .= ' AND p.category_id = '.$category.'';
                }
                if ($price_from != 0) {
                    $select_products .= ' AND p.price > '.$price_from.'';
                }
                if (($price_to != 0)&&($price_to > $price_from)) {
                    $select_products .= ' AND p.price < '.$price_to.'';
                }
                $select_products .= $rege;
                if ($view == 'grid') {
                    $limit_tovar = 6;
                }
                if ($view == 'list') {
                    $limit_tovar = 5;
                } else {
                    $limit_tovar = 5;  
                }
                //Выбираем первых 5 результатов по товарам для отображения на главной странице поиска
                try {
                    $connection = Yii::app()->db;
                    $command = $connection->createCommand("SELECT p.id, p.is_special, p.top_level, p.currency_id, p.name, p.slug, uu.phone, cc.phone_2, cc.phone_3, cc.phone_4, p.price, mc.value, p.description, p.image, p.company_id, p.measure, b.plan, b.name AS company_name, l.title FROM main_store_product p LEFT JOIN main_company b ON (b.id = p.company_id) LEFT JOIN main_company_contacts cc ON(cc.company_id = b.id) LEFT JOIN main_user_user uu ON(uu.id = b.owner_id)  LEFT JOIN main_locale l ON(l.id = b.locale_c) LEFT JOIN main_currency mc ON (mc.id = p.currency_id) ".$select_products." ORDER BY p.top_level DESC, p.update_time DESC, p.create_time DESC, p.real_update_time DESC, p.create_time DESC LIMIT ".$limit_tovar."");
                    $row_pro=$command->queryAll();
                    $command->execute();
                } catch (Exception $e) {
                    $price_err .= 'Выброшено исключение: ' . $e->getMessage() . "\n";
                }
                //Вытягиваем услуги
                //Формируем условие выборки
                $select_service = 'WHERE p.sup_type = 2 AND p.status = 1';
                if ($region != 0) {
                    $select_service .= ' AND b.locale_r = '.$region.'';
                }
                if ($city != 0) {
                    $select_service .= ' AND b.locale_r = '.$city.'';
                }
                if ($category != 0) {
                    $select_service .= ' AND p.category_id = '.$category.'';
                }
                if ($price_from != 0) {
                    $select_service .= ' AND p.price > '.$price_from.'';
                }
                if (($price_to != 0)&&($price_to > $price_from)) {
                    $select_service .= ' AND p.price < '.$price_to.'';
                }
                $select_service .= $rege;
                if ($view == 'grid') {
                    $limit_tovar = 6;
                }
                if ($view == 'list') {
                    $limit_tovar = 5;
                } else {
                    $limit_tovar = 5;
                }
                //Выбираем первых 5 результатов по услугам для отображения на главной странице поиска
                try {
                    $connection = Yii::app()->db;
                    $command = $connection->createCommand("SELECT p.id, p.is_special, p.top_level, p.currency_id, p.name, p.slug, uu.phone, cc.phone_2, cc.phone_3, cc.phone_4, p.price, mc.value, p.description, p.image, p.company_id, p.measure, b.plan, b.name AS company_name, l.title FROM main_store_product p LEFT JOIN main_company b ON (b.id = p.company_id) LEFT JOIN main_company_contacts cc ON(cc.company_id = b.id) LEFT JOIN main_user_user uu ON(uu.id = b.owner_id) LEFT JOIN main_locale l ON(l.id = b.locale_c) LEFT JOIN main_currency mc ON (mc.id = p.currency_id) ".$select_service." ORDER BY p.top_level DESC, p.update_time DESC, p.create_time DESC, p.real_update_time DESC, p.create_time DESC LIMIT ".$limit_tovar."");
                    $row_ser=$command->queryAll();
                    $command->execute();
                } catch (Exception $e) {
                    $price_err .= 'Выброшено исключение: ' . $e->getMessage() . "\n";
                }
            }
            //Формируем внешний вид и передаем на отображения
            $region_html = '<option value="0">---</option>';
            foreach ($list_regions as $key => $l_r) {
                if ($key == $region) {
                    $region_html .= '<option value="'.$key.'" selected data-name="'.$l_r['title'].'">'.$l_r['title'].' ('.$l_r['count'].')</option>';
                }
                else{
                    if ($l_r['count'] == 0) {
                        $region_html .= '<option value="'.$key.'" disabled data-name="'.$l_r['title'].'">'.$l_r['title'].' ('.$l_r['count'].')</option>';
                    }
                    else{
                        $region_html .= '<option value="'.$key.'" data-name="'.$l_r['title'].'">'.$l_r['title'].' ('.$l_r['count'].')</option>';
                    }
                }
            }
            $data['region'] = $region_html;
            $city_html = '';
            if ($region!=0) {
                $city_html = '<option value="0">---</option>';
                foreach ($list_city as $key => $l_s) {
                    if ($key == $city) {
                        if ($city == 0) {
                            $city_html .= '<option value="'.$key.'" selected>---</option>';
                        }
                        else{
                            $city_html .= '<option value="'.$key.'" selected data-name="'.$l_s['title'].'">'.$l_s['title'].' ('.$l_s['count'].')</option>';
                        }
                    }
                    else{
                        if ($l_s == 0) {
                            $city_html .= '<option value="'.$key.'" disabled data-name="'.$l_s['title'].'">'.$l_s['title'].' ('.$l_s['count'].')</option>';
                        }
                        else{
                            $city_html .= '<option value="'.$key.'" data-name="'.$l_s['title'].'">'.$l_s['title'].' ('.$l_s['count'].')</option>';
                        }
                    }
                }
            }
            else{
                $city_html = '<option value="0" selected>---</option>';
            }
            $data['city'] = $city_html;
            if ($prod_all > 0) {
                $pr = '<h2>Товары - '.$prod_all.'</h2>';
            }
            else{
                $pr = '<h2>За задаными критериями товаров не найдено.</h2>';
            }
            $pr .= '<input type="hidden" id="count_tovar" value="'.$prod_all.'">';

            if ($view == 'list') {
                $pr .= '<div id="yw2" class="list-view">';
                $pr .= '<div class="row items">';
                foreach ($row_pro as $r_p) {
                    if ($r_p['price'] == 0) {
                        $price = 'Договорная';
                    } else{
                        if (!empty($r_p['value'])) {
                            $price = number_format($r_p['price']*$r_p['value'], 2, ',', ' ').' грн';
                        }
                        else{
                            $price = number_format($r_p['price'], 2, ',', ' ').' грн';
                        }
                    }
                       $pr .= '<div class="materials-box col-sm-4 top horizontal">';
                       $pr .= '<div class="materials-inner" style="position: relative;">';
                       $pr .= '<div class="materials-img" style="min-height: 80px;">';
                       $pr .= '<a href="/product/'.$r_p['slug'].'.html">';
                        if ((!empty($r_p['image']))) {
                           $pr .= '<img style="" class="img-responsive" src="https://domstroy.com.ua/uploads/store/product/'.$r_p['image'].'" alt="'.$r_p['name'].'" title="'.$r_p['name'].'" />';
                        } else{
                           $pr .= '<img style="height:100%;" class="img-responsive" src="https://domstroy.com.ua/uploads/thumbs/avatars/10000x10000_avatar.png" alt="'.$r_p['name'].'" title="'.$r_p['name'].'">';
                        }
                       $pr .= '</a>';
                        if ($r_p['is_special'] > 0) {
                           $pr .= '<div><div class="mark mark-order" style="position: absolute;top: 5%;left: -1%; background-color: red; color:white;">Акция</div></div>';
                        }
                        if ($r_p['top_level'] > 0) {
                           $pr .= '<div style="float: right;margin-right: 90px; margin-top:30px;"><span class="coints" style="*margin-left: 80%;">'.$r_p['top_level'].'</span></div>';
                        }
                       $pr .= '</div>';
                       $pr .= '<div class="materials-text clearfix">';
                       $pr .= '<a href="/product/'.$r_p['slug'].'.html">';
                       $pr .= '<span style="margin: 0; padding: 0;font-weight: bold;" class="title">'.$r_p['name'].'</span>';
                       $pr .= '</a>';
                       $pr .= '</br>';
                       $pr .= '</br>';
                       $pr .= '<span>Город: </span><span style="padding-top: 7px;padding-right: 12px;padding-bottom: 7px;border-radius: 5px;font-weight:bold;">'.$r_p['title'].'</span>';
                       $pr .= '<div class="executor" style="padding-top: 10px;">';
                       $pr .= '<ul>';
                       $pr .= '<li>Продавец <p><b>'.$r_p['company_name'].'</b></p></li>';
                       $pr .= '</ul>';
                       $pr .= '<div class="row" style="margin:0;padding:0;">';
                       $pr .= '<div class="col-sm-6" style="float: left;min-width: 220px;padding: 0;">';
                       if($r_p['plan'] !== '5' && $r_p['plan'] !== '6' && $r_p['plan'] !== '21'){
                       $pr .= '<span style="font-weight: bold;">Контакты:</span></br>';
                        if ($r_p['phone'] !=''){
                           $pr .= '<div style="width: 100%;">';
                           $pr .= '<span id="phone0_0_'.$r_p['company_id'].'_'.$r_p['id'].'" style="color:#003366;float: left;">'.substr_replace($r_p['phone'],'xxx-xx-xx', 8).'&nbsp;</span>';
                           $pr .= '<a href="tel:'.$r_p['phone'].'">';
                           $pr .= '<span id="phone1_0_'.$r_p['company_id'].'_'.$r_p['id'].'" style="color:#003366;float: left;display: none; cursor: default; width:100%;">'.$r_p['phone'].'</span>';
                           $pr .= '</a>';
                           $pr .= '<span id="phone2_0_'.$r_p['company_id'].'_'.$r_p['id'].'" data-number="0" data-product="'.$r_p['id'].'" data-company="'.$r_p['company_id'].'" data-phone="'.$r_p['phone'].'" data-name="'.$r_p['name'].'" data-companyname="'.$r_p['company_name'].'" data-price="'.$price.'" style="color:#003366; border-bottom: 1px dotted;" onClick="VisiblePhone(this)"> показать</span>';
                           $pr .= '</div>';
                        }
                        if ($r_p['phone_2'] !=''){
                           $pr .= '<div style="width: 100%;">';
                           $pr .= '<span id="phone0_1_'.$r_p['company_id'].'_'.$r_p['id'].'" style="color:#003366;float: left;">'.substr_replace($r_p['phone_2'],'xxx-xx-xx', 8).'&nbsp;</span>';
                           $pr .= '<a href="tel:'.$r_p['phone_2'].'">';
                           $pr .= '<span id="phone1_1_'.$r_p['company_id'].'_'.$r_p['id'].'" style="color:#003366;float: left;display: none; cursor: default; width:100%;">'.$r_p['phone_2'].'</span>';
                           $pr .= '</a>';
                           $pr .= '<span id="phone2_1_'.$r_p['company_id'].'_'.$r_p['id'].'" data-number="1" data-product="'.$r_p['id'].'" data-company="'.$r_p['company_id'].'" data-phone="'.$r_p['phone_2'].'" data-name="'.$r_p['name'].'" data-companyname="'.$r_p['company_name'].'" data-price="'.$price.'" style="color:#003366; border-bottom: 1px dotted;" onClick="VisiblePhone(this)"> показать</span>';
                           $pr .= '</div>';
                        }
                        if ($r_p['phone_3'] !=''){
                           $pr .= '<div style="width: 100%;">';
                           $pr .= '<span id="phone0_2_'.$r_p['company_id'].'_'.$r_p['id'].'" style="color:#003366;float: left;">'.substr_replace($r_p['phone_3'],'xxx-xx-xx', 8).'&nbsp;</span>';
                           $pr .= '<a href="tel:'.$r_p['phone_3'].'">';
                           $pr .= '<span id="phone1_2_'.$r_p['company_id'].'_'.$r_p['id'].'" style="color:#003366;float: left;display: none; cursor: default; width:100%;">'.$r_p['phone_3'].'</span>';
                           $pr .= '</a>';
                           $pr .= '<span id="phone2_2_'.$r_p['company_id'].'_'.$r_p['id'].'" data-number="2" data-product="'.$r_p['id'].'" data-company="'.$r_p['company_id'].'" data-phone="'.$r_p['phone_3'].'" data-name="'.$r_p['name'].'" data-companyname="'.$r_p['company_name'].'" data-price="'.$price.'" style="color:#003366; border-bottom: 1px dotted;" onClick="VisiblePhone(this)"> показать</span>';
                           $pr .= '</div>';
                        }
                        if ($r_p['phone_4'] !=''){
                           $pr .= '<div style="width: 100%;">';
                           $pr .= '<span id="phone0_3_'.$r_p['company_id'].'_'.$r_p['id'].'" style="color:#003366;float: left;">'.substr_replace($r_p['phone_4'],'xxx-xx-xx', 8).'&nbsp;</span>';
                           $pr .= '<a href="tel:'.$r_p['phone_4'].'">';
                           $pr .= '<span id="phone1_3_'.$r_p['company_id'].'_'.$r_p['id'].'" style="color:#003366;float: left;display: none; cursor: default; width:100%;">'.$r_p['phone_4'].'</span>';
                           $pr .= '</a>';
                           $pr .= '<span id="phone2_3_'.$r_p['company_id'].'_'.$r_p['id'].'" data-number="3" data-product="'.$r_p['id'].'" data-company="'.$r_p['company_id'].'" data-phone="'.$r_p['phone_4'].'" data-name="'.$r_p['name'].'" data-companyname="'.$r_p['company_name'].'" data-price="'.$price.'" style="color:#003366; border-bottom: 1px dotted;" onClick="VisiblePhone(this)"> показать</span>';
                           $pr .= '</div>';
                        }
                        }
                       $pr .= '</div>';
                       $pr .= '<div>';
                       $pr .= '<p class="price2" style="font-size:18px;"><span style="color:red;">';
                        if ($r_p['price'] == 0) {
                           $pr .= 'Договорная';
                        } else{
                            if (!empty($r_p['value'])) {
                               $pr .= ' '.number_format($r_p['price']*$r_p['value'], 2, ',', ' ').' грн</span></p>';
                            }
                            else{
                               $pr .= ' '.number_format($r_p['price'], 2, ',', ' ').' грн</span></p>';
                            }
                        }
                       $pr .= '<p class="price2">';
                       $pr .= '<button style="float: right;float:unset;margin-bottom: 20px;" class="add-to-cart" onclick="cart.add('.$r_p['id'].'); return false;">Купить</button>';
                       $pr .= '</p>';
                       $pr .= '</div>';
                       $pr .= '</div>';
                       $pr .= '</div>';
                       $pr .= '</div>';
                       $pr .= '</div>';
                       $pr .= '</div>';

                }
                $pr .= '</div>';
                $pr .= '</div>';
            }
            if ($view == 'grid') {
                $pr = '';
                if ($prod_all > 0) {
                    $pr = '<h2>Товары - '.$prod_all.'</h2>';
                }
                else{
                    $pr = '<h2>За задаными критериями товаров не найдено.</h2>';
                }
                $pr .= '<input type="hidden" id="count_tovar" value="'.$prod_all.'">';
                $pr .= '<div id="yw2" class="list-view">';
                $pr .= '<div class="rows items">';
                foreach ($row_pro as $r_p) {
                    if ($p_s['price'] == 0) {
                        $price = 'Договорная';
                    } else{
                        if (!empty($r_p['value'])) {
                            $price = number_format($r_p['price']*$r_p['value'], 2, ',', ' ').' грн';
                        }
                        else{
                            $price = number_format($r_p['price'], 2, ',', ' ').' грн';
                        }
                    }
                    $pr .= '<div class="materials-box col-sm-4 top vertical" style="height: 494px;">';
                       $pr .= '<div class="materials-inner" style="position: relative;">';
                       $pr .= '<div class="materials-img" style="min-height: 80px;">';
                       $pr .= '<a href="/product/'.$r_p['slug'].'.html">';
                        if ((!empty($r_p['image']))) {
                           $pr .= '<img style="" class="img-responsive" src="https://domstroy.com.ua/uploads/store/product/'.$r_p['image'].'" alt="'.$r_p['name'].'" title="'.$r_p['name'].'" />';
                        } else{
                           $pr .= '<img style="height:100%;" class="img-responsive" src="https://domstroy.com.ua/uploads/thumbs/avatars/10000x10000_avatar.png" alt="'.$r_p['name'].'" title="'.$r_p['name'].'">';
                        }
                       $pr .= '</a>';
                        if ($r_p['is_special'] > 0) {
                           $pr .= '<div><div class="mark mark-order" style="position: absolute;top: 5%;left: -1%; background-color: red; color:white;">Акция</div></div>';
                        }
                        if ($r_p['top_level'] > 0) {
                           $pr .= '<div style="float: right;margin-right: 90px; margin-top:30px;"><span class="coints" style="*margin-left: 80%;">'.$r_p['top_level'].'</span></div>';
                        }
                       $pr .= '</div>';
                       $pr .= '<div class="materials-text clearfix">';
                       $pr .= '<a href="/product/'.$r_p['slug'].'.html">';
                       $pr .= '<span style="margin: 0; padding: 0;font-weight: bold;" class="title">'.$r_p['name'].'</span>';
                       $pr .= '</a>';
                       $pr .= '</br>';
                       $pr .= '</br>';
                       $pr .= '<span>Город: </span><span style="padding-top: 7px;padding-right: 12px;padding-bottom: 7px;border-radius: 5px;font-weight:bold;">'.$r_p['title'].'</span>';
                       $pr .= '<div class="executor" style="padding-top: 10px;">';
                       $pr .= '<ul>';
                       $pr .= '<li>Продавец <p><b>'.$r_p['company_name'].'</b></p></li>';
                       $pr .= '</ul>';
                       $pr .= '<div class="row" style="margin:0;padding:0;">';
                       $pr .= '<div class="col-sm-6" style="float: left;min-width: 220px;padding: 0;">';
                       if($r_p['plan'] !== '5' && $r_p['plan'] !== '6' && $r_p['plan'] !== '21'){
                       $pr .= '<span style="font-weight: bold;">Контакты:</span></br>';
                        if ($r_p['phone'] !=''){
                           $pr .= '<div style="width: 100%;">';
                           $pr .= '<span id="phone0_0_'.$r_p['company_id'].'_'.$r_p['id'].'" style="color:#003366;float: left;">'.substr_replace($r_p['phone'],'xxx-xx-xx', 8).'&nbsp;</span>';
                           $pr .= '<a href="tel:'.$r_p['phone'].'">';
                           $pr .= '<span id="phone1_0_'.$r_p['company_id'].'_'.$r_p['id'].'" style="color:#003366;float: left;display: none; cursor: default; width:100%;">'.$r_p['phone'].'</span>';
                           $pr .= '</a>';
                           $pr .= '<span id="phone2_0_'.$r_p['company_id'].'_'.$r_p['id'].'" data-number="0" data-product="'.$r_p['id'].'" data-company="'.$r_p['company_id'].'" data-phone="'.$r_p['phone'].'" data-name="'.$r_p['name'].'" data-companyname="'.$r_p['company_name'].'" data-price="'.$price.'" style="color:#003366; border-bottom: 1px dotted;" onClick="VisiblePhone(this)"> показать</span>';
                           $pr .= '</div>';
                        }
                        if ($r_p['phone_2'] !=''){
                           $pr .= '<div style="width: 100%;">';
                           $pr .= '<span id="phone0_1_'.$r_p['company_id'].'_'.$r_p['id'].'" style="color:#003366;float: left;">'.substr_replace($r_p['phone_2'],'xxx-xx-xx', 8).'&nbsp;</span>';
                           $pr .= '<a href="tel:'.$r_p['phone_2'].'">';
                           $pr .= '<span id="phone1_1_'.$r_p['company_id'].'_'.$r_p['id'].'" style="color:#003366;float: left;display: none; cursor: default; width:100%;">'.$r_p['phone_2'].'</span>';
                           $pr .= '</a>';
                           $pr .= '<span id="phone2_1_'.$r_p['company_id'].'_'.$r_p['id'].'" data-number="1" data-product="'.$r_p['id'].'" data-company="'.$r_p['company_id'].'" data-phone="'.$r_p['phone_2'].'" data-name="'.$r_p['name'].'" data-companyname="'.$r_p['company_name'].'" data-price="'.$price.'" style="color:#003366; border-bottom: 1px dotted;" onClick="VisiblePhone(this)"> показать</span>';
                           $pr .= '</div>';
                        }
                        if ($r_p['phone_3'] !=''){
                           $pr .= '<div style="width: 100%;">';
                           $pr .= '<span id="phone0_2_'.$r_p['company_id'].'_'.$r_p['id'].'" style="color:#003366;float: left;">'.substr_replace($r_p['phone_3'],'xxx-xx-xx', 8).'&nbsp;</span>';
                           $pr .= '<a href="tel:'.$r_p['phone_3'].'">';
                           $pr .= '<span id="phone1_2_'.$r_p['company_id'].'_'.$r_p['id'].'" style="color:#003366;float: left;display: none; cursor: default; width:100%;">'.$r_p['phone_3'].'</span>';
                           $pr .= '</a>';
                           $pr .= '<span id="phone2_2_'.$r_p['company_id'].'_'.$r_p['id'].'" data-number="2" data-product="'.$r_p['id'].'" data-company="'.$r_p['company_id'].'" data-phone="'.$r_p['phone_3'].'" data-name="'.$r_p['name'].'" data-companyname="'.$r_p['company_name'].'" data-price="'.$price.'" style="color:#003366; border-bottom: 1px dotted;" onClick="VisiblePhone(this)"> показать</span>';
                           $pr .= '</div>';
                        }
                        if ($r_p['phone_4'] !=''){
                           $pr .= '<div style="width: 100%;">';
                           $pr .= '<span id="phone0_3_'.$r_p['company_id'].'_'.$r_p['id'].'" style="color:#003366;float: left;">'.substr_replace($r_p['phone_4'],'xxx-xx-xx', 8).'&nbsp;</span>';
                           $pr .= '<a href="tel:'.$r_p['phone_4'].'">';
                           $pr .= '<span id="phone1_3_'.$r_p['company_id'].'_'.$r_p['id'].'" style="color:#003366;float: left;display: none; cursor: default; width:100%;">'.$r_p['phone_4'].'</span>';
                           $pr .= '</a>';
                           $pr .= '<span id="phone2_3_'.$r_p['company_id'].'_'.$r_p['id'].'" data-number="3" data-product="'.$r_p['id'].'" data-company="'.$r_p['company_id'].'" data-phone="'.$r_p['phone_4'].'" data-name="'.$r_p['name'].'" data-companyname="'.$r_p['company_name'].'" data-price="'.$price.'" style="color:#003366; border-bottom: 1px dotted;" onClick="VisiblePhone(this)"> показать</span>';
                           $pr .= '</div>';
                        }
                    }
                       $pr .= '</div>';
                       $pr .= '<div>';
                       $pr .= '<p class="price2" style="font-size:18px;"><span style="color:red;">';
                        if ($r_p['price'] == 0) {
                           $pr .= 'Договорная';
                        } else{
                            if (!empty($r_p['value'])) {
                               $pr .= ' '.number_format($r_p['price']*$r_p['value'], 2, ',', ' ').' грн</span></p>';
                            }
                            else{
                               $pr .= ' '.number_format($r_p['price'], 2, ',', ' ').' грн</span></p>';
                            }
                        }
                       $pr .= '<p class="price2">';
                       $pr .= '<button style="float: right;float:unset;margin-bottom: 20px;" class="add-to-cart" onclick="cart.add('.$r_p['id'].'); return false;">Купить</button>';
                       $pr .= '</p>';
                       $pr .= '</div>';
                       $pr .= '</div>';
                       $pr .= '</div>';
                       $pr .= '</div>';
                       $pr .= '</div>';
                       $pr .= '</div>';
                }
                $pr .= '</div>';
                $pr .= '</div>';
            }

            if ($prod_all>5) {
                $pr .= '<div class="materials-box col-sm-4 top horizontal" style="text-align: center;">';
                $pr .= '<span style="background-color: #2a4864;color: #fff!important;padding-left: 15px;padding-right: 15px;padding-top: 15px;padding-bottom: 15px;border-radius: 5px;" onclick="AllProducts()">Показать все найденые товары</span>';
                $pr .= '</div>';
            }
            $data['pr'] = $pr;

            if ($serv_all > 0) {
                $sr = '<h2>Услуги - '.$serv_all.'</h2>';
            }
            else{
                $sr = '<h2>За задаными критериями услуг не найдено.</h2>';
            }
            $sr .= '<input type="hidden" id="count_service" value="'.$serv_all.'">';
            if ($view == 'list') {
                $sr .= '<div id="yw2" class="list-view">';
                $sr .= '<div class="row items">';
                foreach ($row_ser as $r_p) {
                    if ($r_p['price'] == 0) {
                        $price = 'Договорная';
                    } else{
                        if (!empty($r_p['value'])) {
                            $price = number_format($r_p['price']*$r_p['value'], 2, ',', ' ').' грн';
                        }
                        else{
                            $price = number_format($r_p['price'], 2, ',', ' ').' грн';
                        }
                    }

                       $sr .= '<div class="materials-box col-sm-4 top horizontal">';
                       $sr .= '<div class="materials-inner" style="position: relative;">';
                       $sr .= '<div class="materials-img" style="min-height: 80px;">';
                       $sr .= '<a href="/product/'.$r_p['slug'].'.html">';
                        if ((!empty($r_p['image']))) {
                           $sr .= '<img style="" class="img-responsive" src="https://domstroy.com.ua/uploads/store/product/'.$r_p['image'].'" alt="'.$r_p['name'].'" title="'.$r_p['name'].'" />';
                        } else{
                           $sr .= '<img style="height:100%;" class="img-responsive" src="https://domstroy.com.ua/uploads/thumbs/avatars/10000x10000_avatar.png" alt="'.$r_p['name'].'" title="'.$r_p['name'].'">';
                        }
                       $sr .= '</a>';
                        if ($r_p['is_special'] > 0) {
                           $sr .= '<div><div class="mark mark-order" style="position: absolute;top: 5%;left: -1%; background-color: red; color:white;">Акция</div></div>';
                        }
                        if ($r_p['top_level'] > 0) {
                           $sr .= '<div style="float: right;margin-right: 90px; margin-top:30px;"><span class="coints" style="*margin-left: 80%;">'.$r_p['top_level'].'</span></div>';
                        }
                       $sr .= '</div>';
                       $sr .= '<div class="materials-text clearfix">';
                       $sr .= '<a href="/product/'.$r_p['slug'].'.html">';
                       $sr .= '<span style="margin: 0; padding: 0;font-weight: bold;" class="title">'.$r_p['name'].'</span>';
                       $sr .= '</a>';
                       $sr .= '</br>';
                       $sr .= '</br>';
                       $sr .= '<span>Город: </span><span style="padding-top: 7px;padding-right: 12px;padding-bottom: 7px;border-radius: 5px;font-weight:bold;">'.$r_p['title'].'</span>';
                       $sr .= '<div class="executor" style="padding-top: 10px;">';
                       $sr .= '<ul>';
                       $sr .= '<li>Продавец <p><b>'.$r_p['company_name'].'</b></p></li>';
                       $sr .= '</ul>';
                       $sr .= '<div class="row" style="margin:0;padding:0;">';
                       $sr .= '<div class="col-sm-6" style="float: left;min-width: 220px;padding: 0;">';
                       if($r_p['plan'] !== '5' && $r_p['plan'] !== '6' && $r_p['plan'] !== '21'){
                       $sr .= '<span style="font-weight: bold;">Контакты:</span></br>';
                        if ($r_p['phone'] !=''){
                           $sr .= '<div style="width: 100%;">';
                           $sr .= '<span id="phone0_0_'.$r_p['company_id'].'_'.$r_p['id'].'" style="color:#003366;float: left;">'.substr_replace($r_p['phone'],'xxx-xx-xx', 8).'&nbsp;</span>';
                           $sr .= '<a href="tel:'.$r_p['phone'].'">';
                           $sr .= '<span id="phone1_0_'.$r_p['company_id'].'_'.$r_p['id'].'" style="color:#003366;float: left;display: none; cursor: default; width:100%;">'.$r_p['phone'].'</span>';
                           $sr .= '</a>';
                           $sr .= '<span id="phone2_0_'.$r_p['company_id'].'_'.$r_p['id'].'" data-number="0" data-product="'.$r_p['id'].'" data-company="'.$r_p['company_id'].'" data-phone="'.$r_p['phone'].'" data-name="'.$r_p['name'].'" data-companyname="'.$r_p['company_name'].'" data-price="'.$price.'" style="color:#003366; border-bottom: 1px dotted;" onClick="VisiblePhone(this)"> показать</span>';
                           $sr .= '</div>';
                        }
                        if ($r_p['phone_2'] !=''){
                           $sr .= '<div style="width: 100%;">';
                           $sr .= '<span id="phone0_1_'.$r_p['company_id'].'_'.$r_p['id'].'" style="color:#003366;float: left;">'.substr_replace($r_p['phone_2'],'xxx-xx-xx', 8).'&nbsp;</span>';
                           $sr .= '<a href="tel:'.$r_p['phone_2'].'">';
                           $sr .= '<span id="phone1_1_'.$r_p['company_id'].'_'.$r_p['id'].'" style="color:#003366;float: left;display: none; cursor: default; width:100%;">'.$r_p['phone_2'].'</span>';
                           $sr .= '</a>';
                           $sr .= '<span id="phone2_1_'.$r_p['company_id'].'_'.$r_p['id'].'" data-number="1" data-product="'.$r_p['id'].'" data-company="'.$r_p['company_id'].'" data-phone="'.$r_p['phone_2'].'" data-name="'.$r_p['name'].'" data-companyname="'.$r_p['company_name'].'" data-price="'.$price.'" style="color:#003366; border-bottom: 1px dotted;" onClick="VisiblePhone(this)"> показать</span>';
                           $sr .= '</div>';
                        }
                        if ($r_p['phone_3'] !=''){
                           $sr .= '<div style="width: 100%;">';
                           $sr .= '<span id="phone0_2_'.$r_p['company_id'].'_'.$r_p['id'].'" style="color:#003366;float: left;">'.substr_replace($r_p['phone_3'],'xxx-xx-xx', 8).'&nbsp;</span>';
                           $sr .= '<a href="tel:'.$r_p['phone_3'].'">';
                           $sr .= '<span id="phone1_2_'.$r_p['company_id'].'_'.$r_p['id'].'" style="color:#003366;float: left;display: none; cursor: default; width:100%;">'.$r_p['phone_3'].'</span>';
                           $sr .= '</a>';
                           $sr .= '<span id="phone2_2_'.$r_p['company_id'].'_'.$r_p['id'].'" data-number="2" data-product="'.$r_p['id'].'" data-company="'.$r_p['company_id'].'" data-phone="'.$r_p['phone_3'].'" data-name="'.$r_p['name'].'" data-companyname="'.$r_p['company_name'].'" data-price="'.$price.'" style="color:#003366; border-bottom: 1px dotted;" onClick="VisiblePhone(this)"> показать</span>';
                           $sr .= '</div>';
                        }
                        if ($r_p['phone_4'] !=''){
                           $sr .= '<div style="width: 100%;">';
                           $sr .= '<span id="phone0_3_'.$r_p['company_id'].'_'.$r_p['id'].'" style="color:#003366;float: left;">'.substr_replace($r_p['phone_4'],'xxx-xx-xx', 8).'&nbsp;</span>';
                           $sr .= '<a href="tel:'.$r_p['phone_4'].'">';
                           $sr .= '<span id="phone1_3_'.$r_p['company_id'].'_'.$r_p['id'].'" style="color:#003366;float: left;display: none; cursor: default; width:100%;">'.$r_p['phone_4'].'</span>';
                           $sr .= '</a>';
                           $sr .= '<span id="phone2_3_'.$r_p['company_id'].'_'.$r_p['id'].'" data-number="3" data-product="'.$r_p['id'].'" data-company="'.$r_p['company_id'].'" data-phone="'.$r_p['phone_4'].'" data-name="'.$r_p['name'].'" data-companyname="'.$r_p['company_name'].'" data-price="'.$price.'" style="color:#003366; border-bottom: 1px dotted;" onClick="VisiblePhone(this)"> показать</span>';
                           $sr .= '</div>';
                        }
                        }
                       $sr .= '</div>';
                       $sr .= '<div>';
                       $sr .= '<p class="price2" style="font-size:18px;"><span style="color:red;">';
                        if ($r_p['price'] == 0) {
                           $sr .= 'Договорная';
                        } else{
                            if (!empty($r_p['value'])) {
                               $sr .= ' '.number_format($r_p['price']*$r_p['value'], 2, ',', ' ').' грн</span></p>';
                            }
                            else{
                               $sr .= ' '.number_format($r_p['price'], 2, ',', ' ').' грн</span></p>';
                            }
                        }
                       $sr .= '<p class="price2">';
                       $sr .= '<button style="float: right;float:unset;margin-bottom: 20px;" class="add-to-cart" onclick="cart.add('.$r_p['id'].'); return false;">Купить</button>';
                       $sr .= '</p>';
                       $sr .= '</div>';
                       $sr .= '</div>';
                       $sr .= '</div>';
                       $sr .= '</div>';
                       $sr .= '</div>';
                       $sr .= '</div>';
                }
                $sr .= '</div>';
                $sr .= '</div>';
            }
            if ($view == 'grid') {
                $sr .= '<div id="yw2" class="list-view">';
                $sr .= '<div class="row items">';
                foreach ($row_ser as $p_s) {
                    if ($p_s['price'] == 0) {
                        $price = 'Договорная';
                    } else{
                        if (!empty($p_s['value'])) {
                            $price = number_format($p_s['price']*$p_s['value'], 2, ',', ' ').' грн';
                        }
                        else{
                            $price = number_format($p_s['price'], 2, ',', ' ').' грн';
                        }
                    }
                    $sr .= '<div class="materials-box col-sm-4 top vertical" style="height: 494px;">';
                       $sr .= '<div class="materials-inner" style="position: relative;">';
                       $sr .= '<div class="materials-img" style="min-height: 80px;">';
                       $sr .= '<a href="/product/'.$p_s['slug'].'.html">';
                        if ((!empty($p_s['image']))) {
                           $sr .= '<img style="" class="img-responsive" src="https://domstroy.com.ua/uploads/store/product/'.$p_s['image'].'" alt="'.$p_s['name'].'" title="'.$p_s['name'].'" />';
                        } else{
                           $sr .= '<img style="height:100%;" class="img-responsive" src="https://domstroy.com.ua/uploads/thumbs/avatars/10000x10000_avatar.png" alt="'.$p_s['name'].'" title="'.$p_s['name'].'">';
                        }
                       $sr .= '</a>';
                        if ($p_s['is_special'] > 0) {
                           $sr .= '<div><div class="mark mark-order" style="position: absolute;top: 5%;left: -1%; background-color: red; color:white;">Акция</div></div>';
                        }
                        if ($p_s['top_level'] > 0) {
                           $sr .= '<div style="float: right;margin-right: 90px; margin-top:30px;"><span class="coints" style="*margin-left: 80%;">'.$p_s['top_level'].'</span></div>';
                        }
                       $sr .= '</div>';
                       $sr .= '<div class="materials-text clearfix">';
                       $sr .= '<a href="/product/'.$p_s['slug'].'.html">';
                       $sr .= '<span style="margin: 0; padding: 0;font-weight: bold;" class="title">'.$p_s['name'].'</span>';
                       $sr .= '</a>';
                       $sr .= '</br>';
                       $sr .= '</br>';
                       $sr .= '<span>Город: </span><span style="padding-top: 7px;padding-right: 12px;padding-bottom: 7px;border-radius: 5px;font-weight:bold;">'.$p_s['title'].'</span>';
                       $sr .= '<div class="executor" style="padding-top: 10px;">';
                       $sr .= '<ul>';
                       $sr .= '<li>Продавец <p><b>'.$p_s['company_name'].'</b></p></li>';
                       $sr .= '</ul>';
                       $sr .= '<div class="row" style="margin:0;padding:0;">';
                       $sr .= '<div class="col-sm-6" style="float: left;min-width: 220px;padding: 0;">';
                       if($p_s['plan'] !== '5' && $p_s['plan'] !== '6' && $p_s['plan'] !== '21'){
                       $sr .= '<span style="font-weight: bold;">Контакты:</span></br>';
                        if ($p_s['phone'] !=''){
                           $sr .= '<div style="width: 100%;">';
                           $sr .= '<span id="phone0_0_'.$p_s['company_id'].'_'.$p_s['id'].'" style="color:#003366;float: left;">'.substr_replace($p_s['phone'],'xxx-xx-xx', 8).'&nbsp;</span>';
                           $sr .= '<a href="tel:'.$p_s['phone'].'">';
                           $sr .= '<span id="phone1_0_'.$p_s['company_id'].'_'.$p_s['id'].'" style="color:#003366;float: left;display: none; cursor: default; width:100%;">'.$p_s['phone'].'</span>';
                           $sr .= '</a>';
                           $sr .= '<span id="phone2_0_'.$p_s['company_id'].'_'.$p_s['id'].'" data-number="0" data-product="'.$p_s['id'].'" data-company="'.$p_s['company_id'].'" data-phone="'.$p_s['phone'].'" data-name="'.$p_s['name'].'" data-companyname="'.$p_s['company_name'].'" data-price="'.$price.'" style="color:#003366; border-bottom: 1px dotted;" onClick="VisiblePhone(this)"> показать</span>';
                           $sr .= '</div>';
                        }
                        if ($p_s['phone_2'] !=''){
                           $sr .= '<div style="width: 100%;">';
                           $sr .= '<span id="phone0_1_'.$p_s['company_id'].'_'.$p_s['id'].'" style="color:#003366;float: left;">'.substr_replace($p_s['phone_2'],'xxx-xx-xx', 8).'&nbsp;</span>';
                           $sr .= '<a href="tel:'.$p_s['phone_2'].'">';
                           $sr .= '<span id="phone1_1_'.$p_s['company_id'].'_'.$p_s['id'].'" style="color:#003366;float: left;display: none; cursor: default; width:100%;">'.$p_s['phone_2'].'</span>';
                           $sr .= '</a>';
                           $sr .= '<span id="phone2_1_'.$p_s['company_id'].'_'.$p_s['id'].'" data-number="1" data-product="'.$p_s['id'].'" data-company="'.$p_s['company_id'].'" data-phone="'.$p_s['phone_2'].'" data-name="'.$p_s['name'].'" data-companyname="'.$p_s['company_name'].'" data-price="'.$price.'" style="color:#003366; border-bottom: 1px dotted;" onClick="VisiblePhone(this)"> показать</span>';
                           $sr .= '</div>';
                        }
                        if ($p_s['phone_3'] !=''){
                           $sr .= '<div style="width: 100%;">';
                           $sr .= '<span id="phone0_2_'.$p_s['company_id'].'_'.$p_s['id'].'" style="color:#003366;float: left;">'.substr_replace($p_s['phone_3'],'xxx-xx-xx', 8).'&nbsp;</span>';
                           $sr .= '<a href="tel:'.$p_s['phone_3'].'">';
                           $sr .= '<span id="phone1_2_'.$p_s['company_id'].'_'.$p_s['id'].'" style="color:#003366;float: left;display: none; cursor: default; width:100%;">'.$p_s['phone_3'].'</span>';
                           $sr .= '</a>';
                           $sr .= '<span id="phone2_2_'.$p_s['company_id'].'_'.$p_s['id'].'" data-number="2" data-product="'.$p_s['id'].'" data-company="'.$p_s['company_id'].'" data-phone="'.$p_s['phone_3'].'" data-name="'.$p_s['name'].'" data-companyname="'.$p_s['company_name'].'" data-price="'.$price.'" style="color:#003366; border-bottom: 1px dotted;" onClick="VisiblePhone(this)"> показать</span>';
                           $sr .= '</div>';
                        }
                        if ($p_s['phone_4'] !=''){
                           $sr .= '<div style="width: 100%;">';
                           $sr .= '<span id="phone0_3_'.$p_s['company_id'].'_'.$p_s['id'].'" style="color:#003366;float: left;">'.substr_replace($p_s['phone_4'],'xxx-xx-xx', 8).'&nbsp;</span>';
                           $sr .= '<a href="tel:'.$p_s['phone_4'].'">';
                           $sr .= '<span id="phone1_3_'.$p_s['company_id'].'_'.$p_s['id'].'" style="color:#003366;float: left;display: none; cursor: default; width:100%;">'.$p_s['phone_4'].'</span>';
                           $sr .= '</a>';
                           $sr .= '<span id="phone2_3_'.$p_s['company_id'].'_'.$p_s['id'].'" data-number="3" data-product="'.$p_s['id'].'" data-company="'.$p_s['company_id'].'" data-phone="'.$p_s['phone_4'].'" data-name="'.$p_s['name'].'" data-companyname="'.$p_s['company_name'].'" data-price="'.$price.'" style="color:#003366; border-bottom: 1px dotted;" onClick="VisiblePhone(this)"> показать</span>';
                           $sr .= '</div>';
                        }
                        }
                       $sr .= '</div>';
                       $sr .= '<div>';
                       $sr .= '<p class="price2" style="font-size:18px;"><span style="color:red;">';
                        if ($p_s['price'] == 0) {
                           $sr .= 'Договорная';
                        } else{
                            if (!empty($p_s['value'])) {
                               $sr .= ' '.number_format($p_s['price']*$p_s['value'], 2, ',', ' ').' грн</span></p>';
                            }
                            else{
                               $sr .= ' '.number_format($p_s['price'], 2, ',', ' ').' грн</span></p>';
                            }
                        }
                       $sr .= '<p class="price2">';
                       $sr .= '<button style="float: right;float:unset;margin-bottom: 20px;" class="add-to-cart" onclick="cart.add('.$p_s['id'].'); return false;">Купить</button>';
                       $sr .= '</p>';
                       $sr .= '</div>';
                       $sr .= '</div>';
                       $sr .= '</div>';
                       $sr .= '</div>';
                       $sr .= '</div>';
                       $sr .= '</div>';
                }
            }
            $sr .= '</div>';
            $sr .= '</div>';
            if ($serv_all > 5) {
                $sr .= '<div class="materials-box col-sm-4 top horizontal" style="text-align: center;">';
                $sr .= '<span style="background-color: #2a4864;color: #fff!important;padding-left: 15px;padding-right: 15px;padding-top: 15px;padding-bottom: 15px;border-radius: 5px;" onclick="AllService()">Показать все найденые услуги</span>';
                $sr .= '</div>';
            }
            $data['sr'] = $sr;
            echo json_encode($data);
    }

    //Метод формирования страниц результатов товаров/услуг
    public function actionProducts(){
        //Получаем все необходимые данные для формирования страниц поиска
        if (Yii::app()->getRequest()->getIsPostRequest() && Yii::app()->getRequest()->getIsAjaxRequest()){
            $request1 = Yii::app()->getRequest()->getPost('request');
            $view = Yii::app()->getRequest()->getPost('view');
            $sup_type = Yii::app()->getRequest()->getPost('sup_type');
            $region = Yii::app()->getRequest()->getPost('region');
            $city = Yii::app()->getRequest()->getPost('city');
            $price_from = Yii::app()->getRequest()->getPost('price_from');
            $price_to = Yii::app()->getRequest()->getPost('price_to');
            $page_num = (int)Yii::app()->getRequest()->getPost('page_num');
            $category = (int)Yii::app()->getRequest()->getPost('category');
            $num_products = 0;
            if ($sup_type == 1) {
                $where = 'WHERE p.sup_type = 1 AND p.status = 1';
                $where_no_cat = 'WHERE p.sup_type = 1 AND p.status = 1';
            }
            else if ($sup_type == 2) {
                $where = 'WHERE p.sup_type = 2 AND p.status = 1';
                $where_no_cat = 'WHERE p.sup_type = 2 AND p.status = 1';
            }
            if ($price_from != 0) {
                $where .= ' AND p.price > '.$price_from.'';
            }
            if ($category != 0) {
                $where .= ' AND p.category_id = '.$category.'';
            }
            if ($price_to > $price_from) {
                $where .= ' AND p.price < '.$price_to.'';
            }
            $where_no_reg = $where;
            if ($region != 0){
                $where .= ' AND b.locale_r = '.$region.'';
            }
            if ($city != 0) {
                $where .= ' AND b.locale_c = '.$city.'';
            }
            //Формируем массив значений запроса
            $request = explode(" ", $request1);
            //Очищаем от пустых значений
            $request = array_filter($request, function($element) {
                return !empty($element);
            });
            $filter = ' ';
            //Формируем часть запроса с условиями
            //Учитываем значение фильтра регион
            //if ($region!=0) {
            //    $filter .= 'AND '
            //}

            $regexp ="";
            foreach ($request as $re) {
                $re = mb_strtoupper($re);
                if ($re != '') {
                    $where .= " AND UPPER(p.name) REGEXP '[[:<:]]".$re."'";
                    $where_no_cat .= " AND UPPER(p.name) REGEXP '[[:<:]]".$re."'";
                }
            }
            //Определяем общее число товаров или услуг для перерисовки категорий без их учета
            $connection = Yii::app()->db;
            $command = $connection->createCommand("SELECT b.locale_r, b.locale_c, p.category_id, c.slug, c.name, p.sup_type, c.name, c.slug  FROM main_store_product p LEFT JOIN main_company b ON (b.id = p.company_id) LEFT JOIN main_store_category c ON (c.id = p.category_id) $where_no_cat");
            $rows_products_allall=$command->queryAll();
            //Подсчитываем количество товаров по регионам и категориям
            //Выбираем все регионы
            $connection = Yii::app()->db;
            $command = $connection->createCommand("SELECT id, title   FROM main_locale WHERE type = 1");
            $list_regions1 = $command->queryAll();
            $list_regions = array();
            foreach ($list_regions1 as $reg) {
                $list_regions[$reg['id']]['title'] = $reg['title'];
                $list_regions[$reg['id']]['count'] = 0;
            }
            $category_list = array();
            foreach ($rows_products_allall as $r_p_a) {
                if ($category == 0) {
                    if (isset($list_regions[$r_p_a['locale_r']])) {
                        $list_regions[$r_p_a['locale_r']]['count'] = $list_regions[$r_p_a['locale_r']]['count']+1;
                    }
                }
                if (isset($category_list[$r_p_a['category_id']])) {
                    $category_list[$r_p_a['category_id']]['count'] = $category_list[$r_p_a['category_id']]['count'] +1;
                }
                else{
                    $category_list[$r_p_a['category_id']]['count'] = 1;
                    $category_list[$r_p_a['category_id']]['slug'] = $r_p_a['slug'];
                    $category_list[$r_p_a['category_id']]['name'] = $r_p_a['name'];
                }
            }
            $cat_li = array();
            //Сортируем категории по количеству результатов (для начала делаем его двумерным так проще))
            foreach ($category_list as $key => $c_l) {
               $cat_li[$key] = $c_l['count'];
            }
            arsort($cat_li);
            $i = 0;
            $c = array();
            foreach ($cat_li as $key => $value) {
                $c[$key] = $category_list[$key];
                $i++;
            }
            $category_list = $c;
            //Прорисовуем внешний вид категорий и областей
            $cat_html = '';
            foreach ($category_list as $key => $c_l) {
                $cat_html .='<li class="select_cat" id="id_'.$key.'" data-name="'.$c_l['name'].'" style="height:30px; list-style-type: none;cursor:pointer;color: #3498db;" data-id="'.$key.'" onclick="SelectCategory(this)">'.$c_l['name'].' - ('.$c_l['count'].')</li></br>';
            }
            //Определяем общее число товаров/услуг соответствующих запросу для формирования пагинации по страницам
            $connection = Yii::app()->db;
            $command = $connection->createCommand("SELECT b.locale_r, b.locale_c, p.category_id, c.slug, c.name, p.sup_type  FROM main_store_product p LEFT JOIN main_company b ON (b.id = p.company_id) LEFT JOIN main_store_category c ON (c.id = p.category_id) $where");
            $rows_products_all=$command->queryAll();
            $command->execute();
            $num_products = count($rows_products_all);
            //определяем нужное количество позиций для отображения
            if ($category != 0) {
                foreach ($rows_products_all as $r_p_a) {
                    if (isset($list_regions[$r_p_a['locale_r']])) {
                        $list_regions[$r_p_a['locale_r']]['count'] = $list_regions[$r_p_a['locale_r']]['count']+1;
                    }
                }
            }
            $city_html = '';
            if ($region !=0) {
                $list_city = array();
                $connection = Yii::app()->db;
                $command = $connection->createCommand("SELECT id, title   FROM main_locale WHERE type = 3 AND parent_id = ".$region."");
                $list_city1 = $command->queryAll();

                foreach ($list_city1 as $reg) {
                    $list_city[$reg['id']]['title'] = $reg['title'];
                    $list_city[$reg['id']]['count'] = 0;
                }
                foreach ($rows_products_all as $r_p_a) {
                    if ($region != 0) {
                        if(isset($list_city[$r_p_a['locale_c']])){
                            $list_city[$r_p_a['locale_c']]['count']++;
                        }
                    }
                }

                $city_html = '<option value="0">---</option>';
                foreach ($list_city as $key => $l_s) {
                    if ($key == $city) {
                        $city_html .= '<option value="'.$key.'" selected data-name="'.$l_s['title'].'">'.$l_s['title'].' ('.$l_s['count'].')</option>';
                    }
                    else{
                        if ($l_s == 0) {
                            $city_html .= '<option value="'.$key.'" disabled data-name="'.$l_s['title'].'">'.$l_s['title'].' ('.$l_s['count'].')</option>';
                        }
                        else{
                            $city_html .= '<option value="'.$key.'" data-name="'.$l_s['title'].'">'.$l_s['title'].' ('.$l_s['count'].')</option>';
                        }
                    }
                }
            }
            else{
                $city_html .= '<option value="0">---</option>';
            }
            $region_html = '<option value="0">---</option>';
            foreach ($list_regions as $key => $l_r) {
                if ($key == $region) {
                    $region_html .= '<option value="'.$key.'" selected data-name="'.$l_r['title'].'">'.$l_r['title'].' ('.$l_r['count'].')</option>';
                }
                else{
                    if ($l_r['count'] == 0) {
                        $region_html .= '<option value="'.$key.'" disabled data-name="'.$l_r['title'].'">'.$l_r['title'].' ('.$l_r['count'].')</option>';
                    }
                    else{
                        $region_html .= '<option value="'.$key.'" data-name="'.$l_r['title'].'">'.$l_r['title'].' ('.$l_r['count'].')</option>';
                    }
                }
            }
            if ($view == 'list') {
               $count = 10;
            }
            else if($view == 'grid'){
                $count = 15;
            }
            //определяем общее количество страниц
            if (($num_products%$count) == 0) {
                $count_page = (int)($num_products/$count);
            }
            else{
                $count_page = (int)(($num_products/$count)+1);
            }
            //определяем стартовую позицию
            if ($page_num == 1) {
               $pos_start = 0;
            }
            else{
                $pos_start = (($page_num-1)*$count);
            }
            //Формируем сам запрос
            $connection = Yii::app()->db;
            $command = $connection->createCommand("SELECT p.id, p.is_special, p.top_level, p.currency_id, p.name, p.slug, uu.phone, cc.phone_2, cc.phone_3, cc.phone_4, p.price, mc.value, p.description, p.image, p.company_id, p.measure, b.plan, b.name AS company_name, l.title FROM main_store_product p LEFT JOIN main_company b ON (b.id = p.company_id) LEFT JOIN main_company_contacts cc ON(cc.company_id = b.id) LEFT JOIN main_user_user uu ON(uu.id = b.owner_id)LEFT JOIN main_locale l ON(l.id = b.locale_c) LEFT JOIN main_currency mc ON (mc.id = p.currency_id) ".$where." ORDER BY p.top_level DESC, p.update_time DESC, p.create_time DESC, p.real_update_time DESC, p.create_time DESC LIMIT ".$pos_start.",".$count." ");
            $rows_products=$command->queryAll();
            $command->execute();
            //Вычисляем диапазон показа
            $st = $pos_start+1;
            $end = $pos_start+10;

            //Формируем отображения результата
            if ($view == 'list') {
                $html = '';
                $html .= '<div id="yw2" class="list-view">';
                $html .= '<div class="rows items">';
                $html .= '<p>Показано результаты с '.$st.' по '.$end.'</p>';
                foreach ($rows_products as $p_s) {
                    if ($p_s['price'] == 0) {
                        $price = 'Договорная';
                    } else{
                        if (!empty($p_s['value'])) {
                            $price = number_format($p_s['price']*$p_s['value'], 2, ',', ' ').' грн';
                        }
                        else{
                            $price = number_format($p_s['price'], 2, ',', ' ').' грн';
                        }
                    }

                       $html .= '<div class="materials-box col-sm-4 top horizontal">';
                       $html .= '<div class="materials-inner" style="position: relative;">';
                       $html .= '<div class="materials-img" style="min-height: 80px;">';
                       $html .= '<a href="/product/'.$p_s['slug'].'.html">';
                        if ((!empty($p_s['image']))) {
                           $html .= '<img style="" class="img-responsive" src="https://domstroy.com.ua/uploads/store/product/'.$p_s['image'].'" alt="'.$p_s['name'].'" title="'.$p_s['name'].'" />';
                        } else{
                           $html .= '<img style="height:100%;" class="img-responsive" src="https://domstroy.com.ua/uploads/thumbs/avatars/10000x10000_avatar.png" alt="'.$p_s['name'].'" title="'.$p_s['name'].'">';
                        }
                       $html .= '</a>';
                        if ($p_s['is_special'] > 0) {
                           $html .= '<div><div class="mark mark-order" style="position: absolute;top: 5%;left: -1%; background-color: red; color:white;">Акция</div></div>';
                        }
                        if ($p_s['top_level'] > 0) {
                           $html .= '<div style="float: right;margin-right: 90px; margin-top:30px;"><span class="coints" style="*margin-left: 80%;">'.$p_s['top_level'].'</span></div>';
                        }
                       $html .= '</div>';
                       $html .= '<div class="materials-text clearfix">';
                       $html .= '<a href="/product/'.$p_s['slug'].'.html">';
                       $html .= '<span style="margin: 0; padding: 0;font-weight: bold;" class="title">'.$p_s['name'].'</span>';
                       $html .= '</a>';
                       $html .= '</br>';
                       $html .= '</br>';
                       $html .= '<span>Город: </span><span style="padding-top: 7px;padding-right: 12px;padding-bottom: 7px;border-radius: 5px;font-weight:bold;">'.$p_s['title'].'</span>';
                       $html .= '<div class="executor" style="padding-top: 10px;">';
                       $html .= '<ul>';
                       $html .= '<li>Продавец <p><b>'.$p_s['company_name'].'</b></p></li>';
                       $html .= '</ul>';
                       $html .= '<div class="row" style="margin:0;padding:0;">';
                       $html .= '<div class="col-sm-6" style="float: left;min-width: 220px;padding: 0;">';
                       if($p_s['plan'] !== '5' && $p_s['plan'] !== '6' && $p_s['plan'] !== '21'){
                        $html .= '<span style="font-weight: bold;">Контакты:</span></br>';
                            if ($p_s['phone'] !=''){
                            $html .= '<div style="width: 100%;">';
                            $html .= '<span id="phone0_0_'.$p_s['company_id'].'_'.$p_s['id'].'" style="color:#003366;float: left;">'.substr_replace($p_s['phone'],'xxx-xx-xx', 8).'&nbsp;</span>';
                            $html .= '<a href="tel:'.$p_s['phone'].'">';
                            $html .= '<span id="phone1_0_'.$p_s['company_id'].'_'.$p_s['id'].'" style="color:#003366;float: left;display: none; cursor: default; width:100%;">'.$p_s['phone'].'</span>';
                            $html .= '</a>';
                            $html .= '<span id="phone2_0_'.$p_s['company_id'].'_'.$p_s['id'].'" data-number="0" data-product="'.$p_s['id'].'" data-company="'.$p_s['company_id'].'" data-phone="'.$p_s['phone'].'" data-name="'.$p_s['name'].'" data-companyname="'.$p_s['company_name'].'" data-price="'.$price.'" style="color:#003366; border-bottom: 1px dotted;" onClick="VisiblePhone(this)"> показать</span>';
                            $html .= '</div>';
                            }
                            if ($p_s['phone_2'] !=''){
                            $html .= '<div style="width: 100%;">';
                            $html .= '<span id="phone0_1_'.$p_s['company_id'].'_'.$p_s['id'].'" style="color:#003366;float: left;">'.substr_replace($p_s['phone_2'],'xxx-xx-xx', 8).'&nbsp;</span>';
                            $html .= '<a href="tel:'.$p_s['phone_2'].'">';
                            $html .= '<span id="phone1_1_'.$p_s['company_id'].'_'.$p_s['id'].'" style="color:#003366;float: left;display: none; cursor: default; width:100%;">'.$p_s['phone_2'].'</span>';
                            $html .= '</a>';
                            $html .= '<span id="phone2_1_'.$p_s['company_id'].'_'.$p_s['id'].'" data-number="1" data-product="'.$p_s['id'].'" data-company="'.$p_s['company_id'].'" data-phone="'.$p_s['phone_2'].'" data-name="'.$p_s['name'].'" data-companyname="'.$p_s['company_name'].'" data-price="'.$price.'" style="color:#003366; border-bottom: 1px dotted;" onClick="VisiblePhone(this)"> показать</span>';
                            $html .= '</div>';
                            }
                            if ($p_s['phone_3'] !=''){
                            $html .= '<div style="width: 100%;">';
                            $html .= '<span id="phone0_2_'.$p_s['company_id'].'_'.$p_s['id'].'" style="color:#003366;float: left;">'.substr_replace($p_s['phone_3'],'xxx-xx-xx', 8).'&nbsp;</span>';
                            $html .= '<a href="tel:'.$p_s['phone_3'].'">';
                            $html .= '<span id="phone1_2_'.$p_s['company_id'].'_'.$p_s['id'].'" style="color:#003366;float: left;display: none; cursor: default; width:100%;">'.$p_s['phone_3'].'</span>';
                            $html .= '</a>';
                            $html .= '<span id="phone2_2_'.$p_s['company_id'].'_'.$p_s['id'].'" data-number="2" data-product="'.$p_s['id'].'" data-company="'.$p_s['company_id'].'" data-phone="'.$p_s['phone_3'].'" data-name="'.$p_s['name'].'" data-companyname="'.$p_s['company_name'].'" data-price="'.$price.'" style="color:#003366; border-bottom: 1px dotted;" onClick="VisiblePhone(this)"> показать</span>';
                            $html .= '</div>';
                            }
                            if ($p_s['phone_4'] !=''){
                            $html .= '<div style="width: 100%;">';
                            $html .= '<span id="phone0_3_'.$p_s['company_id'].'_'.$p_s['id'].'" style="color:#003366;float: left;">'.substr_replace($p_s['phone_4'],'xxx-xx-xx', 8).'&nbsp;</span>';
                            $html .= '<a href="tel:'.$p_s['phone_4'].'">';
                            $html .= '<span id="phone1_3_'.$p_s['company_id'].'_'.$p_s['id'].'" style="color:#003366;float: left;display: none; cursor: default; width:100%;">'.$p_s['phone_4'].'</span>';
                            $html .= '</a>';
                            $html .= '<span id="phone2_3_'.$p_s['company_id'].'_'.$p_s['id'].'" data-number="3" data-product="'.$p_s['id'].'" data-company="'.$p_s['company_id'].'" data-phone="'.$p_s['phone_4'].'" data-name="'.$p_s['name'].'" data-companyname="'.$p_s['company_name'].'" data-price="'.$price.'" style="color:#003366; border-bottom: 1px dotted;" onClick="VisiblePhone(this)"> показать</span>';
                            $html .= '</div>';
                            }
                        }
                       $html .= '</div>';
                       $html .= '<div>';
                       $html .= '<p class="price2" style="font-size:18px;"><span style="color:red;">';
                        if ($p_s['price'] == 0) {
                           $html .= 'Договорная';
                        } else{
                            if (!empty($p_s['value'])) {
                               $html .= ' '.number_format($p_s['price']*$p_s['value'], 2, ',', ' ').' грн</span></p>';
                            }
                            else{
                               $html .= ' '.number_format($p_s['price'], 2, ',', ' ').' грн</span></p>';
                            }
                        }
                       $html .= '<p class="price2">';
                       $html .= '<button style="float: right;float:unset;margin-bottom: 20px;" class="add-to-cart" onclick="cart.add('.$p_s['id'].'); return false;">Купить</button>';
                       $html .= '</p>';
                       $html .= '</div>';
                       $html .= '</div>';
                       $html .= '</div>';
                       $html .= '</div>';
                       $html .= '</div>';
                       $html .= '</div>';



                }
                $html .= '</div>';
                $html .= '</div>';
            }
            if ($view == 'grid') {
                $html = '';
                $html .= '<div id="yw2" class="list-view">';
                $html .= '<div class="rows items">';
                $html .= '<p>Показано результаты с '.$st.' по '.$end.'</p>';
                foreach ($rows_products as $p_s) {
                    if ($p_s['price'] == 0) {
                        $price = 'Договорная';
                    } else{
                        if (!empty($p_s['value'])) {
                            $price = number_format($p_s['price']*$p_s['value'], 2, ',', ' ').' грн';
                        }
                        else{
                            $price = number_format($p_s['price'], 2, ',', ' ').' грн';
                        }
                    }
                    $html .= '<div class="materials-box col-sm-4 top vertical" style="height: 494px;">';
                       $html .= '<div class="materials-inner" style="position: relative;">';
                       $html .= '<div class="materials-img" style="min-height: 80px;">';
                       $html .= '<a href="/product/'.$p_s['slug'].'.html">';
                        if ((!empty($p_s['image']))) {
                           $html .= '<img style="" class="img-responsive" src="https://domstroy.com.ua/uploads/store/product/'.$p_s['image'].'" alt="'.$p_s['name'].'" title="'.$p_s['name'].'" />';
                        } else{
                           $html .= '<img style="height:100%;" class="img-responsive" src="https://domstroy.com.ua/uploads/thumbs/avatars/10000x10000_avatar.png" alt="'.$p_s['name'].'" title="'.$p_s['name'].'">';
                        }
                       $html .= '</a>';
                        if ($p_s['is_special'] > 0) {
                           $html .= '<div><div class="mark mark-order" style="position: absolute;top: 5%;left: -1%; background-color: red; color:white;">Акция</div></div>';
                        }
                        if ($p_s['top_level'] > 0) {
                           $html .= '<div style="float: right;margin-right: 90px; margin-top:30px;"><span class="coints" style="*margin-left: 80%;">'.$p_s['top_level'].'</span></div>';
                        }
                       $html .= '</div>';
                       $html .= '<div class="materials-text clearfix">';
                       $html .= '<a href="/product/'.$p_s['slug'].'.html">';
                       $html .= '<span style="margin: 0; padding: 0;font-weight: bold;margin-left: -50%;" class="title">'.$p_s['name'].'</span>';
                       $html .= '</a>';
                       $html .= '</br>';
                       $html .= '</br>';
                       $html .= '<span>Город: </span><span style="padding-top: 7px;padding-right: 12px;padding-bottom: 7px;border-radius: 5px;font-weight:bold;">'.$p_s['title'].'</span>';
                       $html .= '<div class="executor" style="padding-top: 10px;">';
                       $html .= '<ul>';
                       $html .= '<li>Продавец <p><b>'.$p_s['company_name'].'</b></p></li>';
                       $html .= '</ul>';
                       $html .= '<div class="row" style="margin:0;padding:0;">';
                       $html .= '<div class="col-sm-6" style="float: left;min-width: 220px;padding: 0;">';
                       if($p_s['plan'] !== '5' && $p_s['plan'] !== '6' && $p_s['plan'] !== '21'){
                       $html .= '<span style="font-weight: bold;">Контакты:</span></br>';
                        if ($p_s['phone'] !=''){
                           $html .= '<div style="width: 100%;">';
                           $html .= '<span id="phone0_0_'.$p_s['company_id'].'_'.$p_s['id'].'" style="color:#003366;float: left;">'.substr_replace($p_s['phone'],'xxx-xx-xx', 8).'&nbsp;</span>';
                           $html .= '<a href="tel:'.$p_s['phone'].'">';
                           $html .= '<span id="phone1_0_'.$p_s['company_id'].'_'.$p_s['id'].'" style="color:#003366;float: left;display: none; cursor: default; width:100%;">'.$p_s['phone'].'</span>';
                           $html .= '</a>';
                           $html .= '<span id="phone2_0_'.$p_s['company_id'].'_'.$p_s['id'].'" data-number="0" data-product="'.$p_s['id'].'" data-company="'.$p_s['company_id'].'" data-phone="'.$p_s['phone'].'" data-name="'.$p_s['name'].'" data-companyname="'.$p_s['company_name'].'" data-price="'.$price.'" style="color:#003366; border-bottom: 1px dotted;" onClick="VisiblePhone(this)"> показать</span>';
                           $html .= '</div>';
                        }
                        if ($p_s['phone_2'] !=''){
                           $html .= '<div style="width: 100%;">';
                           $html .= '<span id="phone0_1_'.$p_s['company_id'].'_'.$p_s['id'].'" style="color:#003366;float: left;">'.substr_replace($p_s['phone_2'],'xxx-xx-xx', 8).'&nbsp;</span>';
                           $html .= '<a href="tel:'.$p_s['phone_2'].'">';
                           $html .= '<span id="phone1_1_'.$p_s['company_id'].'_'.$p_s['id'].'" style="color:#003366;float: left;display: none; cursor: default; width:100%;">'.$p_s['phone_2'].'</span>';
                           $html .= '</a>';
                           $html .= '<span id="phone2_1_'.$p_s['company_id'].'_'.$p_s['id'].'" data-number="1" data-product="'.$p_s['id'].'" data-company="'.$p_s['company_id'].'" data-phone="'.$p_s['phone_2'].'" data-name="'.$p_s['name'].'" data-companyname="'.$p_s['company_name'].'" data-price="'.$price.'" style="color:#003366; border-bottom: 1px dotted;" onClick="VisiblePhone(this)"> показать</span>';
                           $html .= '</div>';
                        }
                        if ($p_s['phone_3'] !=''){
                           $html .= '<div style="width: 100%;">';
                           $html .= '<span id="phone0_2_'.$p_s['company_id'].'_'.$p_s['id'].'" style="color:#003366;float: left;">'.substr_replace($p_s['phone_3'],'xxx-xx-xx', 8).'&nbsp;</span>';
                           $html .= '<a href="tel:'.$p_s['phone_3'].'">';
                           $html .= '<span id="phone1_2_'.$p_s['company_id'].'_'.$p_s['id'].'" style="color:#003366;float: left;display: none; cursor: default; width:100%;">'.$p_s['phone_3'].'</span>';
                           $html .= '</a>';
                           $html .= '<span id="phone2_2_'.$p_s['company_id'].'_'.$p_s['id'].'" data-number="2" data-product="'.$p_s['id'].'" data-company="'.$p_s['company_id'].'" data-phone="'.$p_s['phone_3'].'" data-name="'.$p_s['name'].'" data-companyname="'.$p_s['company_name'].'" data-price="'.$price.'" style="color:#003366; border-bottom: 1px dotted;" onClick="VisiblePhone(this)"> показать</span>';
                           $html .= '</div>';
                        }
                        if ($p_s['phone_4'] !=''){
                           $html .= '<div style="width: 100%;">';
                           $html .= '<span id="phone0_3_'.$p_s['company_id'].'_'.$p_s['id'].'" style="color:#003366;float: left;">'.substr_replace($p_s['phone_4'],'xxx-xx-xx', 8).'&nbsp;</span>';
                           $html .= '<a href="tel:'.$p_s['phone_4'].'">';
                           $html .= '<span id="phone1_3_'.$p_s['company_id'].'_'.$p_s['id'].'" style="color:#003366;float: left;display: none; cursor: default; width:100%;">'.$p_s['phone_4'].'</span>';
                           $html .= '</a>';
                           $html .= '<span id="phone2_3_'.$p_s['company_id'].'_'.$p_s['id'].'" data-number="3" data-product="'.$p_s['id'].'" data-company="'.$p_s['company_id'].'" data-phone="'.$p_s['phone_4'].'" data-name="'.$p_s['name'].'" data-companyname="'.$p_s['company_name'].'" data-price="'.$price.'" style="color:#003366; border-bottom: 1px dotted;" onClick="VisiblePhone(this)"> показать</span>';
                           $html .= '</div>';
                        }
                       }
                       $html .= '</div>';
                       $html .= '<div>';
                       $html .= '<p class="price2" style="font-size:18px;"><span style="color:red;">';
                        if ($p_s['price'] == 0) {
                           $html .= 'Договорная';
                        } else{
                            if (!empty($p_s['value'])) {
                               $html .= ' '.number_format($p_s['price']*$p_s['value'], 2, ',', ' ').' грн</span></p>';
                            }
                            else{
                               $html .= ' '.number_format($p_s['price'], 2, ',', ' ').' грн</span></p>';
                            }
                        }
                       $html .= '<p class="price2">';
                       $html .= '<button style="float: right;float:unset;margin-bottom: 20px;" class="add-to-cart" onclick="cart.add('.$p_s['id'].'); return false;">Купить</button>';
                       $html .= '</p>';
                       $html .= '</div>';
                       $html .= '</div>';
                       $html .= '</div>';
                       $html .= '</div>';
                       $html .= '</div>';
                       $html .= '</div>';
                }
                $html .= '</div>';
                $html .= '</div>';
            }

            $html .= '</div>';
            //Формируем пагинацию
            if ($count_page > 1) {
                $html .= '<div class="pagination"><div>';
                $html .= '<ul class="pagination pull-right">';
                if ($page_num == 1) {
                    $html .= '<li class="previous disabled">';
                    $html .= '<a>«</a>';
                    $html .= '</li>';
                }
                else{
                    $page_prev = $page_num - 1;
                    $html .= '<li class="previous" style="cursor:pointer;">';
                    $html .= '<a data-page="'.$page_prev.'" style="cursor:pointer;" onClick="SelectPage(this)">«</a>';
                    $html .= '</li>';
                }
                for ($i=$page_num; $i < $page_num+5; $i++){
                    if ($i == $page_num) {
                        $html .= '<li class=" active" style="transform: none;">';
                        $html .= '<a data-page="'.$page_num.'">'.$page_num.'</a>';
                        $html .= '</li>';
                    }
                    else{
                        $html .= '<li style="transform: none;">';
                        $html .= '<a data-page="'.$i.'" style="cursor:pointer;" onClick="SelectPage(this)">'.$i.'</a>';
                        $html .= '</li>';
                    }
                    if (($i == ($page_num+5))||($i == $count_page)) {
                        break;
                    }
                }
                if (($page_num+5)<$count_page) {
                    $page_next = $page_num+5;
                    $html .= '<li class="next">';
                    $html .= '<a data-page="'.$page_next.'" style="cursor:pointer;" onClick="SelectPage(this)">»</a>';
                    $html .= '</li>';
                }

                $html .= '</ul>';
                $html .= '<div style="clear: both;"></div></div></div>';
            }

            $data['html'] = $html;
            $data['category'] = $cat_html;
            $data['region'] = $region_html;
            $data['city'] = $city_html;
            echo json_encode($data);
            //Возвращаем результат
        }
        else{
            echo 'Результатов не найдено';
        }
    }

    //Метод формирование
}