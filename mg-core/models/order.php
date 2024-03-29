<?php

/**
 * Модель: Order
 *
 * Класс Models_Order реализует логику взаимодействия с заказами покупателей.
 * - Проверяет корректность ввода данных в форме оформления заказа;
 * - Добавляет заказ в базу данных.
 * - Отправляет сообщения на электронные адреса пользователя и администраторов, при успешном оформлении заказа.
 * - Удаляет заказ из базы данных.
 *
 * @author Авдеев Марк <mark-avdeev@mail.ru>

 * @package moguta.cms
 * @subpackage Model
 */
class Models_Order {

  // ФИО покупателя.
  private $fio;
  // Электронный адрес покупателя.
  private $email;
  // Телефон покупателя.
  private $phone;
  // Адрес покупателя.
  private $address;
  // Флаг нового пользователя.
  public $newUser = false;
  // Комментарий покупателя.
  private $info;
  // Дата доставки.
  private $dateDelivery;
  // Массив способов оплаты.
  private $_paymentArray = array();
  // ip пользователя при заказе
  private $ip;
  // Статичный массив статусов.
  static $status = array(
    0 => 'NOT_CONFIRMED',
    1 => 'EXPECTS_PAYMENT',
    2 => 'PAID',
    3 => 'IN_DELIVERY',
    4 => 'CANSELED',
    5 => 'EXECUTED',
    6 => 'PROCESSING',
  );

  function __construct() {
    $res = DB::query('SELECT  *  FROM `'.PREFIX.'payment` ORDER BY `sort`');
    $i = 0;
    while ($row = DB::fetchAssoc($res)) {
      $newparam = array();
      $param = json_decode($row['paramArray']);
      foreach ($param as $key=>$value) {
        if ($value != '') {
          $value = CRYPT::mgDecrypt($value);
        }
      $newparam[$key] = $value;
      }
      $row['paramArray'] = CRYPT::json_encode_cyr($newparam);
      $this->_paymentArray[$row['id']] = $row;
    };
  }

  /**
   * Проверяет корректность ввода данных в форму обратной связи и регистрацию в системе покупателя.
   *
   * @param array $arrayData  массив с ведёнными пользователем данными.
   * @return bool|string $error сообщение с ошибкой в случае некорректных данных.
   */                                                
  public function isValidData($arrayData, $reqire = array('email','phone','payment'),$createUser = true , $error = null) {
    $result = null;
    $this->newUser = false;
    if($createUser){
      // Если электронный адрес зарегистрирован в системе.
      $currenUser = USER::getThis();
      if ($currenUser->email != trim($arrayData['email'])) {
        if (USER::getUserInfoByEmail($arrayData['email'])) {
          $error = "<span class='user-exist'>Пользователь с таким email существует. 
            Пожалуйста, <a href='".SITE."/enter?location=".SITE.$_SERVER['REQUEST_URI']."'>войдите в систему</a> используя 
            свой электронный адрес и пароль!</span>";
          // Иначе новый пользователь.
        } else {
          $this->newUser = true;
        }
      }
    }
    
    if(in_array('email', $reqire)&&(MG::getSetting('requiredFields')=='true')){
      // Корректность емайл.
      if (!preg_match('/^[-._a-zA-Z0-9]+@(?:[a-zA-Z0-9][-a-zA-Z0-9]{0,61}+\.)+[a-zA-Z]{2,6}$/', $arrayData['email'])) {
        $error = "<span class='order-error-email'>E-mail введен некорректно!</span>";
      }
    }
    
    if(in_array('phone', $reqire)&&(MG::getSetting('requiredFields')=='true')){
      // Наличие телефона.
      if (empty($arrayData['phone'])) {
        $error = "<span class='no-phone'>Введите верный номер телефона!</span>";
      }
    }
    
    if(in_array('payment', $reqire)){
      // Неуказан способ оплаты.
      if (empty($arrayData['payment'])) {
        $error = "<span class='no-phone'>Выберите способ оплаты!</span>";
      }
    }
    if (CAPTCHA_ORDER == '1'){
      // если при оформлении капча.
      if (empty($arrayData['capcha']) || (strtolower($arrayData['capcha']) != strtolower($_SESSION['capcha']))) {
        $error = "<span class='no-phone'>Неверно введен код с картинки!</span>";
      }
    } 
       // проверка  наличия товара товаров, пока человек оформляет заказ -товар может купить другой пользователь
    if (!empty($_SESSION['cart'])) {
      foreach ($_SESSION['cart'] as $key => $item) {  
        if ($item['variantId']){          
          $res_var = DB::query('
            SELECT  pv.id, p.`title`, pv.`title_variant`, pv.count
            FROM `'.PREFIX.'product_variant` pv   
            LEFT JOIN `'.PREFIX.'product` as p ON 
            p.id = pv.product_id   
            WHERE pv.id ='.DB::quote($item['variantId'])); 
          if ($prod = DB::fetchArray($res_var)){
            if ($prod['count'] >=0 && $prod['count'] < $_SESSION['cart'][$key]['count']){
              if ($prod['count'] == 0) {
                $error .= "<p>Товара ".$prod['title'].' '.$prod['title_variant']." уже нет в наличии. Для оформления заказа его необходимо удалить из корзины.</p>";
              } else {
                $error .= "<p>Товар ".$prod['title'].' '.$prod['title_variant']." доступен в количестве ".$prod['count']." шт. Для оформления заказа измените количество в корзине.</p>";
              }              
            }            
          }          
        }else{  
          $res_pr = DB::query('
            SELECT id, title, count
            FROM `'.PREFIX.'product` p 
            WHERE id ='.DB::quote($item['id']));      
          if ($prod = DB::fetchArray($res_pr)){
            if ($prod['count'] >=0 && $prod['count'] < $_SESSION['cart'][$key]['count']){
              if ($prod['count'] == 0) {
                $error .= "<p>Товара ".$prod['title']." уже нет в наличии. Для оформления заказа его необходимо удалить из корзины.</p>";
              } else {
                $error .= "<p>Товар ".$prod['title']." доступен в количестве ".$prod['count']." шт. Для оформления заказа измените количество в корзине.</p>";
              }   
            }            
          }   
        }
           
      }
    }
    
    // Если нет ошибок, то заносит информацию в поля класса.
    if (!empty($error)) {
      $result = $error;
    } else {

      $this->fio = trim($arrayData['fio']);
      $this->email = trim($arrayData['email']);
      $this->phone = trim($arrayData['phone']);
      $this->address = trim($arrayData['address']);
      $this->info = trim($arrayData['info']);
      $this->delivery = $arrayData['delivery'];
      $this->dateDelivery = $arrayData['date_delivery'];
      $deliv = new Delivery();
      $this->delivery_cost = $deliv->getCostDelivery($arrayData['delivery']);
      $this->payment = $arrayData['payment'];
      $cart = new Models_Cart();
      $this->summ = $cart->getTotalSumm();
      $this->ip = $_SERVER['REMOTE_ADDR'];
      $result = false;
      $this->addNewUser();      
    
    }
    $args = func_get_args();
    $args['this'] = &$this;
    return MG::createHook(__CLASS__."_".__FUNCTION__, $result, $args);
  }

  /**
   * Если заказ оформляется впервые на нового покупателя, то создает новую запись в таблице пользователей.     
   */
  public function addNewUser() {
    // Если заказ производит новый пользователь, то регистрируем его
    if (MG::getSetting('autoRegister') == "true") {
      $activity = 0;
      if (MG::getSetting('confirmRegistration') == 'false') {
        $activity = 1;
      }
      if ($this->newUser) {
        USER::add(
          array(
            'email' => $this->email,
            'role' => 2,
            'name' => $this->fio ? $this->fio : 'Пользователь',
            'pass' => crypt(time()),
            'address' => $this->address,
            'phone' => $this->phone,
            'ip' => $this->ip,
            'nameyur' => $_POST['yur_info']['nameyur'],
            'adress' => $_POST['yur_info']['adress'],
            'inn' => $_POST['yur_info']['inn'],
            'kpp' => $_POST['yur_info']['kpp'],
            'bank' => $_POST['yur_info']['bank'],
            'bik' => $_POST['yur_info']['bik'],
            'ks' => $_POST['yur_info']['ks'],
            'rs' => $_POST['yur_info']['rs'],
            'activity' => $activity
          )
        );
      }
    }
  }

  /**
   * Сохраняет заказ в базу сайта.
   * Добавляет в массив корзины третий параметр 'цена товара', для сохранения в заказ.
   * Это нужно для тогою чтобы в последствии вывести детальную информацию о заказе.
   * Если оставить только id то информация может оказаться неверной, так как цены меняются.
   * @return int $id номер заказа.
   */
  public function addOrder($adminOrder = false) {
    $itemPosition = new Models_Product();
    $cart = new Models_Cart();
    $catalog = new Models_Catalog();
    $categoryArray = $catalog->getCategoryArray();
    $this->summ = 0;
    $currencyRate = MG::getSetting('currencyRate');   
    $currencyShopIso = MG::getSetting('currencyShopIso');

    // Массив запросов на обновление количества товаров.
    $updateCountProd = array();
    $variant_update = array();
    $product_update = array();
    // Добавляем в массив корзины параметр 'цена товара'.
    if ($adminOrder) {

      $this->email = $adminOrder['user_email'];
      $this->phone = $adminOrder['phone'];
      $this->address = $adminOrder['address'];
      $this->delivery = $adminOrder['delivery_id'];
      $this->dateDelivery = $adminOrder['date_delivery'];
      $this->delivery_cost = $adminOrder['delivery_cost'];
      $this->payment = $adminOrder['payment_id'];
      $this->fio = $adminOrder['name_buyer'];
      $formatedDate = date('Y-m-d H:i:s'); // Форматированная дата ГГГГ-ММ-ДД ЧЧ:ММ:СС.

      foreach ($adminOrder['order_content'] as $item) {
               
        $product = $itemPosition->getProduct($item['id']);
        $_SESSION['couponCode'] = $item['coupon'];
        $product['category_url'] = $product['category_url'] ? $product['category_url'] : 'catalog';
        $productUrl = $product['category_url'].'/'.$product['url'];
        $itemCount = $item['count'];
        if (!empty($product)) {
          $fulPrice = $item['fulPrice']; // полная стоимость без скидки
          $product['price'] = $item['price'];
          // если выбран формат без копеек, то округляем стоимость до ворматирования. 
          if(in_array(MG::getSetting('priceFormat'), array('1234','1 234','1,234'))){
            $product['price'] = round($item['price']);
          }
          $discount = 0;
          if (!empty($item['price'])&&(!empty($item['coupon'])||(stristr($item['discSyst'], 'true')!==false))) {
            $discount = 100 - ($product['price'] * 100) / $fulPrice;
          }

          $productPositions[] = array(
            'id' => $product['id'],
            'name' => $item['title'],
            'url' => $productUrl,
            'code' => $item['code'],
            'price' => $product['price'],
            'count' => $itemCount,
            'property' => $item['property'],
            'coupon' => $_SESSION['couponCode'],
            'discount' => round($discount, 1),
            'fulPrice' => $fulPrice,
            'weight' => $product['weight'],
            'currency_iso' => $currencyShopIso,
            'discSyst' => !empty($item['discSyst'])?$item['discSyst']:'',
          );


          $this->summ += $product['price'] * $itemCount;

          // По ходу формируем массив запросов на обновление количества товаров.
          if ($item['variant'] == 0) {
            $product['count'] = ($product['count'] - $itemCount) >= 0 ? $product['count'] - $itemCount : 0;
            if (!empty($product_update[$product['id']])) {
              $product_update[$product['id']] = ($product_update[$product['id']] - $itemCount) >= 0 ? $product_update[$product['id']] - $itemCount : 0;     
            } else {
              $product_update[$product['id']] = $product['count'];
            }
          } else {

            $count = DB::query('
              SELECT count
              FROM `'.PREFIX.'product_variant`
              WHERE id = '.DB::quote($item['variant']));
            $count = DB::fetchAssoc($count);

            $product['count'] = ($count['count'] - $itemCount) >= 0 ? $count['count'] - $itemCount : 0;            
            if (!empty($variant_update[$item['variant']])) {
              $variant_update[$item['variant']] = ($variant_update[$item['variant']] - $itemCount) >= 0 ? $variant_update[$item['variant']] - $itemCount : 0;     
            } else {
              $variant_update[$item['variant']] = $product['count'];
            }            
            $variants = $itemPosition->getVariants($product['id']);
            $firstVariant = reset($variants);
            if ($firstVariant['id'] == $item['variant']) {
              // если приобретен вариант товара, то выясним является ли он первым в наборе, если да то обновим информацию в mg_product
              if (!empty($product_update[$product['id']])) {
                $product_update[$product['id']] = ($product_update[$product['id']] - $itemCount) >= 0 ? $product_update[$product['id']] - $itemCount : 0;     
              } else {
                $product_update[$product['id']] = $product['count'];
              }              
            }
          }
          $updateCountProd[] = "UPDATE `".PREFIX."product` SET `count_buy`= `count_buy` + 1 WHERE `id`=".DB::quote($product['id']);         
        }
      }
    } elseif (!empty($_SESSION['cart'])) {
      foreach ($_SESSION['cart'] as $item) {
        $product = $itemPosition->getProduct($item['id']);

        // Дописываем к массиву продуктов данные о выбранных характеристиках из корзины покупок, чтобы приплюсовать к сумме заказа.
        if ($item['id'] == $product['id']) {
          $product['property_html'] = $item['propertyReal'];
        }

        $variant = null;
        $discount = null;
        $promocode = null;
        if (!empty($item['variantId']) && $item['id'] == $product['id']) {
          $variants = $itemPosition->getVariants($product['id']);
          $variant = $variants[$item['variantId']];
          $product['price'] = $variant['price_course'];
          $fulPrice = $product['price'];
          $priceWithCoupon = $cart->applyCoupon($_SESSION['couponCode'], $product['price'], $product);
          $priceWithDiscount = $cart->applyDiscountSystem($product['price'], $product); 
          $product['price'] = $cart->customPrice(array(
            'product' => $product,
            'priceWithCoupon' => $priceWithCoupon, 
            'priceWithDiscount' => $priceWithDiscount['price'],
          ));
          $product['price'] = round($product['price'], 2);
          $product['variant_id'] = $item['variantId'];
          $product['code'] = $variant['code'];
          $product['count'] = $variant['count'];
          $product['weight'] = $variant['weight'];
          $product['title'] .= " ".$variant['title_variant'];
          $discountSystem = $priceWithDiscount['discounts']; 
          $promocode = $priceWithDiscount['discounts'] !='' ? $priceWithDiscount['promo'] : $_SESSION['couponCode'];
          //По ходу формируем массив запросов на обновление количества товаров
          $resCount = $variant['code'];
          $resCount = ($variant['count'] - $item['count']) >= 0 ? $variant['count'] - $item['count'] : 0;
           if (!empty($variant_update[$item['variantId']])) {
              $variant_update[$item['variantId']] = ($variant_update[$item['variantId']] - $item['count']) >= 0 ? $variant_update[$item['variantId']] - $item['count'] : 0;     
            } else {
              $variant_update[$item['variantId']] = $resCount;
            } 
        }
        $product['category_url'] = $product['category_url'] ? $product['category_url'] : 'catalog';
        $productUrl = $product['category_url'].'/'.$product['url'];

        // Если куки не актуальны исключает попадание несуществующего продукта в заказ
        if (!empty($product)) {
          if (!$variant) {
            $product['price'] = $product['price_course'];
            // если выбран формат без копеек, то округляем стоимость до ворматирования. 
            if(in_array(MG::getSetting('priceFormat'), array('1234','1 234','1,234'))){
              $product['price'] = round($product['price_course']);
            }
            $fulPrice = $product['price'];
          }
          $product['price'] = SmalCart::plusPropertyMargin($fulPrice, $product['property_html'], $currencyRate[$product['currency_iso']]);
          $fulPrice = $product['price'];
          $tempPrice = $product['price'];
          $priceWithCoupon = $cart->applyCoupon($_SESSION['couponCode'], $product['price'], $product);
          $priceWithDiscount = $cart-> applyDiscountSystem($product['price'], $product); 
          $product['price'] = $cart->customPrice(array(
            'product' => $product,
            'priceWithCoupon' => $priceWithCoupon, 
            'priceWithDiscount' => $priceWithDiscount['price'],
          ));                      
          $discountSystem = $priceWithDiscount['discounts']; 
          $promocode = $priceWithDiscount['discounts'] !='' ? $priceWithDiscount['promo'] : $_SESSION['couponCode'];

          $discount = 0;
          if (!empty($tempPrice)) {
            $discount = 100 - ($product['price'] * 100) / $tempPrice;
          }
          $tempPriceCeil = (string) ($product['price']*100);
          $product['price'] = ceil($tempPriceCeil)/100;          

          $productPositions[] = array(
            'id' => $product['id'],
            'variant_id' => $product['variant_id'],
            'name' => $product['title'],
            'url' => $productUrl,
            'code' => $product['code'],
            'price' => $product['price'],
            'count' => $item['count'],
            'property' => $item['property'],
            'coupon' => $promocode,
            'discount' => round($discount),
            'fulPrice' => $fulPrice,
            'weight' => $product['weight'],
            'currency_iso' => $currencyShopIso,
            'discSyst' => $discountSystem ? $discountSystem : '',
          );


          $this->summ += $product['price'] * $item['count'];

          if (!$resCount) {
            $resCount = ($product['count'] - $item['count']) >= 0 ? $product['count'] - $item['count'] : 0;
          }

          //По ходу формируем массив запросов на обновление количества товаров
          if (!$variant) {  
            if (!empty($product_update[$product['id']])) {
                $product_update[$product['id']] = ($product_update[$product['id']] - $item['count']) >= 0 ? $product_update[$product['id']] - $item['count'] : 0;     
              } else {
                $product_update[$product['id']] = $resCount;
              } 
          }else{                      
            $firstVariant = reset($variants);     
            if($firstVariant['id']==$item['variantId']){             
              // если приобретен вариант товара, то выясним является ли он первым в наборе, если да то обновим информацию в mg_product              
              if (!empty($product_update[$product['id']])) {
                $product_update[$product['id']] = ($product_update[$product['id']] - $item['count']) >= 0 ? $product_update[$product['id']] - $item['count'] : 0;     
              } else {
                $product_update[$product['id']] = $resCount;
              } 
            }    
          };
          $updateCountProd[] = "UPDATE `".PREFIX."product` SET `count_buy`= `count_buy` + 1 WHERE `id`=".DB::quote($product['id']);
          $resCount = null;    
        }
      }
    }

    // Сериализует данные в строку для записи в бд.
    $orderContent = addslashes(serialize($productPositions));

    // Сериализует данные в строку для записи в бд информации об юридическом лице.
    $yurInfo = '';
    if (!empty($adminOrder['yur_info'])) {
      $yurInfo = addslashes(serialize($adminOrder['yur_info']));
    }
    if (!empty($_POST['yur_info'])) {
      $yurInfo = addslashes(serialize($_POST['yur_info']));
    }
    
    // Создает новую модель корзины, чтобы узнать сумму заказа.
    $cart = new Models_Cart();

    // Генерируем уникальный хэш для подтверждения заказа.
    $hash = $this->_getHash($this->email);
    
    //Достаем настройки заказов, чтобы установить статус для нового заказа.
    $propertyOrder = MG::getOption('propertyOrder');
    $propertyOrder = stripslashes($propertyOrder);
    $propertyOrder = unserialize($propertyOrder);
    //Если установлен статус для новых заказов по умолчанию "ожидает оплаты", 
    //для способов оплаты "наличными" или "наложенным" меняем на "в доставке"
    $order_status_id = (in_array($this->payment, array(3, 4)) && $propertyOrder['order_status'] == 1) ? 3 : $propertyOrder['order_status'];
    
    // Формируем массив параметров для SQL запроса.
    $array = array(
      'user_email' => $this->email,
      'summ' => number_format($this->summ, 2, '.', ''),
      'order_content' => $orderContent,
      'phone' => $this->phone,
      'address' => $this->address,
      'delivery_id' => $this->delivery,
      'delivery_cost' => MG::numberDeFormat(MG::numberFormat($this->delivery_cost)),
      'payment_id' => $this->payment,
      'paided' => '0',
      'status_id' => (int)$order_status_id,
      'confirmation' => $hash,
      'yur_info' => $yurInfo,
      'name_buyer' => $this->fio,
      'date_delivery' => $this->dateDelivery,
      'user_comment' => $this->info,
      'ip'=> $_SERVER['REMOTE_ADDR'],
    );    

    // Если заказ оформляется через админку.
    if ($adminOrder) {
      $array['comment'] = $adminOrder['comment'];
      $array['status_id'] = $adminOrder['status_id'];
      $array['date_delivery'] = $adminOrder['date_delivery'];
      DB::buildQuery("INSERT INTO `".PREFIX."order` SET add_date = now(), ", $array);
    } else {

      // Отдает на обработку  родительской функции buildQuery.
      DB::buildQuery("INSERT INTO `".PREFIX."order` SET add_date = now(), ", $array);
    }

    // Заказ номер id добавлен в базу.
    $id = null;
    $id = DB::insertId();
    $_SESSION['usedCouponCode'] = $_SESSION['couponCode'];
    unset($_SESSION['couponCode']);

    $orderNumber = $this->getOrderNumber($id);
    $hashStatus = '';
    $linkToStatus = '';
    if (MG::getSetting('autoRegister') == "false" && !USER::isAuth()) {
      $hashStatus = md5($id.$this->email.rand(9999));
      $linkToStatus = '<a href="'.SITE.'/order?hash='.$hashStatus.'" target="blank">'.SITE.'/order?hash='.$hashStatus.'</a>';
    }
    DB::query("UPDATE `".PREFIX."order` SET `number`= ".DB::quote($orderNumber).", `hash`=".DB::quote($hashStatus)." WHERE `id`=".DB::quote($id)."");
    
    // Ссылка для подтверждения заказа
    $link = 'ссылке <a href="'.SITE.'/order?sec='.$hash.'&id='.$id.'" target="blank">'.SITE.'/order?sec='.$hash.'&id='.$id.'</a>';
    $table = "";

    // Формирование тела письма.
    if ($id) {
      // Уменьшаем количество купленных товаров
      if (!empty($updateCountProd)) {
        foreach ($updateCountProd as $sql) {
          DB::query($sql);
        }
        

        foreach ($productPositions as $product) {
          Storage::clear(md5('ControllersProduct'.$product['url']));
        }
      }
      if (!empty($product_update)) {
        foreach ($product_update as $id_upd => $count_upd) {
          DB::query("UPDATE `".PREFIX."product` SET `count`= ".DB::quote($count_upd)." WHERE `id`=".DB::quote($id_upd)." AND `count`>0");
        }
      }
      if (!empty($variant_update)) {
        foreach ($variant_update as $id_upd => $count_upd) {
          DB::query("UPDATE `".PREFIX."product_variant` SET `count`= ".DB::quote($count_upd)." WHERE `id`=".DB::quote($id_upd)." AND `count`>0");
        }
      }

      // Если заказ создался, то уменьшаем количество товаров на складе.
      $settings = MG::get('settings');
      $delivery = $this->getDeliveryMethod(false, $this->delivery);
      $sitename = $settings['sitename'];
      $currency = MG::getSetting('currency');
      $paymentArray = $this->getPaymentMethod($this->payment);
      $subj = 'Оформлена заявка №'.($orderNumber != "" ? $orderNumber : $id).' на сайте '.$sitename;

      foreach ($productPositions as &$item) {
        foreach ($item as &$v) {
          $v = rawurldecode($v);
        }
      }

      $paramToMail = array(
        'orderNumber' => $orderNumber,
        'siteName' => MG::getSetting('sitename'),
        'delivery' => $delivery,
        'currency' => MG::getSetting('currency'),
        'fio' => $this->fio,
        'email' => $this->email,
        'phone' => $this->phone,
        'address' => $this->address,
        'delivery' => $delivery['description'],
        'payment' => $paymentArray['name'],
        'adminOrder' => $adminOrder,
        'result' => $this->summ,
        'deliveryCost' => $this->delivery_cost,
        'date_delivery' => $this->dateDelivery,
        'total' => $this->delivery_cost + $this->summ,
        'confirmLink' => $link,
        'ip' => $this->ip,
        'lastvisit' => $_SESSION['lastvisit'],
        'firstvisit' => $_SESSION['firstvisit'],
        'supportEmail' => MG::getSetting('noReplyEmail'),
        'shopName' => MG::getSetting('shopName'),
        'shopPhone' => MG::getSetting('shopPhone'),
        'formatedDate' => date('Y-m-d H:i:s'),
        'productPositions' => $productPositions,
        'couponCode' => $_SESSION['usedCouponCode'],
        'toKnowStatus' => $linkToStatus,
        'userComment' => $this->info,
        'yur_info' => unserialize(stripcslashes($yurInfo)) ,
      );      
      $emailToUser = MG::layoutManager('email_order', $paramToMail);

      $paramToMail['adminMail'] = true;
      $emailToAdmin = MG::layoutManager('email_order_admin', $paramToMail);

      $mails = explode(',', MG::getSetting('adminEmail'));

      foreach ($mails as $mail) {
        if (preg_match('/^[-._a-zA-Z0-9]+@(?:[a-zA-Z0-9][-a-zA-Z0-9]+\.)+[a-zA-Z]{2,6}$/', $mail)) {
          Mailer::addHeaders(array("Reply-to" => $this->email));
          Mailer::sendMimeMail(array(
            'nameFrom' => $this->fio,
            'emailFrom' => MG::getSetting('noReplyEmail'),
            'nameTo' => $sitename,
            'emailTo' => $mail,
            'subject' => $subj,
            'body' => $emailToAdmin,
            'html' => true
          ));
        }
      }

      // Отправка заявки пользователю.
      Mailer::sendMimeMail(array(
        'nameFrom' => $sitename,
        'emailFrom' => MG::getSetting('noReplyEmail'),
        'nameTo' => $this->fio,
        'emailTo' => $this->email,
        'subject' => $subj,
        'body' => $emailToUser,
        'html' => true
      ));

      // Если заказ успешно записан, то очищает корзину.
      if (!$adminOrder) {
        $cart->clearCart();
      }
    }
    
    $result =array('id'=>$id, 'orderNumber' => $orderNumber);
    // Возвращаем номер созданного заказа.
    $args = func_get_args();
    return MG::createHook(__CLASS__."_".__FUNCTION__, $result, $args);
  }

  /**
   * Отправляет сообщение о смене статуса заказа его владельцу.
   * @param int $id номер заказа.
   * @param int $statusId новый статус.
   */
  public function sendStatusToEmail($id, $statusId) {
    $order = $this->getOrder('id = '.DB::quote(intval($id)));
    $lang = MG::get('lang');
    $statusArray = self::$status;
    $statusName = $lang[$statusArray[$statusId]];
    $statusOldName = $lang[$statusArray[$order[$id]['status_id']]];

    $paramToMail = array(
      'orderInfo' => $order[$id],
      'statusId' => $statusId,
      'statusName' => $statusName,
      'statusOldName' => $statusOldName
    );
    if ($statusName !== $statusOldName) {

      $emailToUser = MG::layoutManager('email_order_status', $paramToMail);

      Mailer::addHeaders(array("Reply-to" => MG::getSetting('noReplyEmail')));
      Mailer::sendMimeMail(array(
        'nameFrom' => MG::getSetting('noReplyEmail'),
        'emailFrom' => MG::getSetting('noReplyEmail'),
        'nameTo' => $order[$id]['user_email'],
        'emailTo' => $order[$id]['user_email'],
        'subject' => "Заказ №".$order[$id]['number']." ".$statusName,
        'body' => $emailToUser,
        'html' => true
      ));
    } else {
      return false;
    }
  }

  /**
   * Изменяет данные о заказе
   *
   * @param array $array массив с данными о заказе.
   * @return bool
   */
  public function updateOrder($array, $informUser = false) {
    $id = $array['id'];
    unset($array['id']);

    $this->refreshCountProducts($id, $array['status_id']);

    if (!empty($array['status_id']) && $informUser == 'true') {
      $this->sendStatusToEmail($id, $array['status_id']);
    }

    $result = false;
    if (!empty($id)) {  
      if (DB::query('
        UPDATE `'.PREFIX.'order`
        SET '.DB::buildPartQuery($array).'
        WHERE id = '.DB::quote($id))) {
        $result = true;
      }
    }

    $args = func_get_args();
    return MG::createHook(__CLASS__."_".__FUNCTION__, $result, $args);
  }

  /**
   * Пересчитывает количество остатков продуктов при отмене заказа.
   * @param int $orderId id заказа.
   * @param int $status_id новый статус заказа.
   * @return bool
   */
  public function refreshCountProducts($orderId, $status_id) {
    // Если статус меняется на "Отменен", то пересчитываем остатки продуктов из заказа.
    $order = $this->getOrder(' id = '.DB::quote(intval($orderId)));

    // Увеличиваем колличество товаров. 
    if ($status_id == 4) {
      if (($order[$orderId]['status_id'] != 4)&&($order[$orderId]['status_id'] != 5)) {
        $order_content = unserialize(stripslashes($order[$orderId]['order_content']));
        $product = new Models_Product();
        foreach ($order_content as $item) {
          $product->increaseCountProduct($item['id'], $item['code'], $item['count']);
        }
      }
    } else {
      // Уменьшаем колличество товаров. 
      if ($order[$orderId]['status_id'] == 4) {
        $order_content = unserialize(stripslashes($order[$orderId]['order_content']));
        $product = new Models_Product();
        foreach ($order_content as $item) {
          $product->decreaseCountProduct($item['id'], $item['code'], $item['count']);
        }
      }
    }    
    $result = true;
    $args = func_get_args();
    return MG::createHook(__CLASS__."_".__FUNCTION__, $result, $args);
  }

  /**
   * Удаляет заказ из базы данных.
   * @param int $id id удаляемого заказа
   * @param mixed $arrayId массив id товаров, которые требуется удалить
   * @return bool
   */
  public function deleteOrder($id, $arrayId = null) {
    $result = false;
    if (empty($arrayId)) {
      if (DB::query('
        DELETE
        FROM `'.PREFIX.'order`
        WHERE id = %d
      ', $id)) {
        $result = true;
      }
    } else {
      $where = '('.implode(',', $arrayId).')';
      if (DB::query('
        DELETE
        FROM `'.PREFIX.'order`
        WHERE id in %s
      ', $where)) {
        $result = true;
      }
    }

    $args = func_get_args();
    return MG::createHook(__CLASS__."_".__FUNCTION__, $result, $args);
  }

  /**
   * Возвращает массив заказов подцепляя данные о способе доставки.
   * @param string $where необязательный параметр формирующий условия поиска заказа, например: id = 1
   * @return array массив заказов
   */
  public function getOrder($where = '') {

    if ($where) {
      $where = 'WHERE '.$where;
    }

    $result = DB::query('
      SELECT  *
      FROM `'.PREFIX.'order`'.$where.' ORDER BY id desc');

    while ($order = DB::fetchAssoc($result)) {

      $delivery = $this->getDeliveryMethod(false, $order['delivery_id']);
      $order['description'] = $delivery['description'];
      $order['cost'] = $delivery['cost'];
      // декодируем параметры заказа
      $order['order_content'] = unserialize(stripslashes($order['order_content']));
      foreach ($order['order_content'] as &$item) {
        foreach ($item as &$v) {
          $v = rawurldecode($v);
        }
      }
      $order['order_content'] = addslashes(serialize($order['order_content']));

      $orderArray[$order['id']] = $order;
    }
    return $orderArray;
  }

  /**
   * Устанавливает переданный статус заказа.
   *
   * @param int $id - номер заказа.
   * @param int $statusId - статус заказа.
   * @return boolean результат выполнения метода.
   */
  public function setOrderStatus($id, $statusId) {
    $res = DB::query('
      UPDATE `'.PREFIX.'order`
      SET status_id = %d
      WHERE id = %d', $statusId, $id);

    if ($res) {
      return true;
    }

    return false;
  }

  /**
   * Генерация случайного хэша.
   * @param string $string - строка, на основе которой готовится хэш.
   * @return string случайный хэш
   */
  public function _getHash($string) {
    $hash = htmlspecialchars(crypt($string));
    return $hash;
  }

  /**
   * Получение данных о способах доставки.
   *
   * @return array массив содержащий способы доставки.
   */
  public function getDeliveryMethod($returnArray = true, $id = -1) {

    if ($returnArray) {

      $deliveryArray = array();
      $result = DB::query('SELECT  *  FROM `'.PREFIX.'delivery` ORDER BY `sort`');
      while ($delivery = DB::fetchAssoc($result)) {
        $deliveryArray[$delivery['id']] = $delivery;
        $deliveryIds[] = $delivery['id'];
      }

      if (!empty($deliveryIds)) {
        $in = 'in('.implode(',', $deliveryIds).')';
        $deliveryCompareArray = array();
        $res = DB::query('
          SELECT  *  
          FROM `'.PREFIX.'delivery_payment_compare` 
          WHERE `delivery_id` '.$in);
        while ($row = DB::fetchAssoc($res)) {
          $deliveryCompareArray[$row['delivery_id']][] = $row;
        }
      }

      foreach ($deliveryArray as &$item) {
        // Получаем доступные методы оплаты $delivery['paymentMethod'] для данного способа доставки.

        $jsonStr = '{';
        if (!empty($deliveryCompareArray[$item['id']])) {
          foreach ($deliveryCompareArray[$item['id']] as $compareMethod) {
            $jsonStr .= '"'.$compareMethod['payment_id'].'":'.$compareMethod['compare'].',';
          }
          $jsonStr = substr($jsonStr, 0, strlen($jsonStr) - 1);
        }
        $jsonStr .= '}';
        $item['paymentMethod'] = $jsonStr;
      }

      return $deliveryArray;
    } elseif ($id >= 0) {
      $result = DB::query('
        SELECT description, cost, free, plugin
        FROM `'.PREFIX.'delivery`
        WHERE id = %d', $id);
      return DB::fetchAssoc($result);
    }
  }

  /**
   * Проверяет, существуют ли способы доставки.
   */
  public function DeliveryExist() {
    if (DB::numRows(DB::query('SELECT  *  FROM `'.PREFIX.'delivery` ORDER BY id'))) {
      return true;
    }
    return false;
  }

  /**
   * Расшифровка по id статуса заказа.
   *
   * @param int $statusId - id статуса заказа.
   * @return string
   */
  public function getOrderStatus($statusId) {
    return self::$status[$statusId['status_id']];
  }

  /**
   * Расшифровка по id методов оплаты.
   *
   * @param int $paymentId
   * @return array
   */
  public function getPaymentMethod($paymentId) {

    if (count($this->_paymentArray) < $paymentId) {
      return false;
    }

    //получаем доступные методы доставки $this->_paymentArray[$paymentId]['deliveryMethod'] для данного способа оплаты
    //массив соответствия доставки к данному методу.
    $compareArray = $this->getCompareMethod('payment_id', $paymentId);

    if (count($compareArray)) {
      $jsonStr = '{';

      foreach ($compareArray as $compareMethod) {
        $jsonStr .= '"'.$compareMethod['delivery_id'].'":'.$compareMethod['compare'].',';
      }

      $jsonStr = substr($jsonStr, 0, strlen($jsonStr) - 1);
      $jsonStr .= '}';
      $this->_paymentArray[$paymentId]['deliveryMethod'] = $jsonStr;
    }else{
	  $this->_paymentArray[$paymentId]['deliveryMethod'] = '{}';
	}
    return $this->_paymentArray[$paymentId];
  }

  /**
   * Получает набор всех способов доставки.
   * @return array
   */
  public function getPaymentBlocksMethod() {

    $paymentArray = array();
    foreach ($this->_paymentArray as $payment) {
      $paymentArray[$payment['id']] = $payment;
      $paymentIds[] = intval($payment['id']);
    }
    $compareArray = array();
    if (!empty($paymentIds)) {
      $in = 'in('.implode(',', $paymentIds).')';
      $res = DB::query('
          SELECT  *  
          FROM `'.PREFIX.'delivery_payment_compare` 
          WHERE `payment_id` '.$in);
      while ($row = DB::fetchAssoc($res)) {
        $compareArray[$row['payment_id']][] = $row;
      }
    }

    foreach ($paymentArray as &$item) {

      // Получаем доступные методы оплаты $delivery['paymentMethod'] для данного способа доставки.
      $jsonStr = '{';
      if (empty($compareArray[$item['id']])) {
        continue;
      }

      foreach ($compareArray[$item['id']] as $compareMethod) {
        $jsonStr .= '"'.$compareMethod['delivery_id'].'":'.$compareMethod['compare'].',';
      }
      $jsonStr = substr($jsonStr, 0, strlen($jsonStr) - 1);
      $jsonStr .= '}';
      $item['deliveryMethod'] = $jsonStr;
    }

    return $paymentArray;
  }

  /**
   * Возвращает весь список способов оплаты в ассоциативном массиве с индексами.
   * @return array
   */
  public function getListPayment() {
    $result = array();
    $res = DB::query('SELECT  *  FROM `'.PREFIX.'payment`');

    while ($row = DB::fetchAssoc($res)) {
      $result[$row['id']] = $row['name'];
    }
    return $result;
  }

  /**
   * Возвращает максимальную сумму заказа.
   * @return array
   */
  public function getMaxPrice() {
    $res = DB::query('
      SELECT MAX(`summ`+`delivery_cost`) as summ 
      FROM `'.PREFIX.'order`');

    if ($row = DB::fetchObject($res)) {
      $result = $row->summ;
    }

    return $result;
  }

  /**
   * Возвращает минимальную сумму заказа.
   * @return array
   */
  public function getMinPrice() {
    $res = DB::query('
      SELECT MIN(`summ`+`delivery_cost`) as summ 
      FROM `'.PREFIX.'order`'
    );
    if ($row = DB::fetchObject($res)) {
      $result = $row->summ;
    }
    return $result;
  }

  /**
   * Возвращает дату последнего заказа.
   * @return array
   */
  public function getMaxDate() {
    $res = DB::query('
      SELECT MAX(add_date) as res 
      FROM `'.PREFIX.'order`');

    if ($row = DB::fetchObject($res)) {
      $result = $row->res;
    }

    return $result;
  }

  /**
   * Возвращает дату первого заказа.
   * @return array
   */
  public function getMinDate() {
    $res = DB::query('
      SELECT MIN(add_date) as res 
      FROM `'.PREFIX.'order`'
    );
    if ($row = DB::fetchObject($res)) {
      $result = $row->res;
    }
    return $result;
  }

  /**
   * Возвращает весь список способов доставки в ассоциативном массиве с индексами.
   * @return array
   */
  public function getListDelivery() {
    $result = array();
    $res = DB::query('SELECT  *  FROM `'.PREFIX.'delivery`');
    while ($row = DB::fetchAssoc($res)) {
      $result[$row['id']] = $row['name'];
    }
    return $result;
  }

  /**
   * Получение статуса оплаты.
   *
   * @param int $paidedId
   * @return string
   */
  public function getPaidedStatus($paidedId) {

    if (1 == $paidedId['paided']) {
      return 'оплачен';
    } else {
      return 'не оплачен';
    }
  }

  /**
   * Возвращает общее количество заказов.
   * $where - условие выбора
   */
  public function getOrderCount($where = '') {
    $result = 0;
    $res = DB::query('
      SELECT count(id) as count
      FROM `'.PREFIX.'order`
    '.$where);

    if ($order = DB::fetchAssoc($res)) {
      $result = $order['count'];
    }

    return $result;
  }

  /**
   * Возвращает информацию о соответствии методов оплаты к методам доставки.
   * $where - условие выбора
   */
  private function getCompareMethod($methodSearch, $id) {
    $result = array();
    $res = DB::query('
      SELECT  *  
      FROM `'.PREFIX.'delivery_payment_compare` 
      WHERE `%s` = %d', $methodSearch, $id);
    while ($row = DB::fetchAssoc($res)) {
      $result[] = $row;
    }
    return $result;
  }

  /**
   * Отправляет сообщение  об оплате заказа.
   * @param string $orderNamber - номер заказа.
   * @param string $paySumm - сумма заказа.
   * @param string $pamentId - id способа оплаты.
   */
  public function sendMailOfPayed($orderNamber, $paySumm, $pamentId) {
    $pamentArray = $this->_paymentArray[$pamentId];
    $siteName = MG::getSetting('sitename');
    $adminEmail = MG::getSetting('adminEmail');
    $subj = 'Оплата заказа '.$orderNamber.' на сайте '.$siteName;
    $msg = '
      Вы получили это письмо, так как произведена оплата заказа '.
      $orderNamber.' на сумму '.$paySumm.' '.MG::getSetting('currency').
      '. Оплата произведена при помощи '.$pamentArray['name'].
      '<br/> Статус заказа сменен на "Оплачен"';

    $mails = explode(',', MG::getSetting('adminEmail'));

    foreach ($mails as $mail) {
      if (preg_match('/^[-._a-zA-Z0-9]+@(?:[a-zA-Z0-9][-a-zA-Z0-9]+\.)+[a-zA-Z]{2,6}$/', $mail)) {
        Mailer::sendMimeMail(array(
          'nameFrom' => $siteName,
          'emailFrom' => MG::getSetting('noReplyEmail'),
          'nameTo' => $sitename,
          'emailTo' => $mail,
          'subject' => $subj,
          'body' => $msg,
          'html' => true
        ));
      }
    }
  }

  /**
   * Возвращает ссылки на скачивания электронных товаров.
   * @param int $orderId
   * @return boolean
   */
  public function getFileToOrder($orderId) {
    $linksElectro = array();
    $orderId = (int) $orderId;
    $userInfo = USER::getThis();

    if (empty($userInfo)) {
      return false;
    }

    $orderInfo = $this->getOrder('
      id = '.DB::quote($orderId, true).' AND 
      user_email = '.DB::quote($userInfo->email).' AND
      (status_id = 2 OR status_id = 5)'
    );

    $orderInfo[$orderId]['order_content'] = unserialize(stripslashes($orderInfo[$orderId]['order_content']));
    $product = new Models_Product();
    if (!empty($orderInfo[$orderId]['order_content'])) {
      foreach ($orderInfo[$orderId]['order_content'] as $item) {
        $productInfo = $product->getProduct($item['id']);
        if ($productInfo['link_electro']) {
          $linksElectro[] = array(
            'link' => SITE.'/order?link='.md5($userInfo->email.$productInfo['link_electro']),
            'title' => $productInfo['title'],
            'product' => $productInfo,
          );
        }
      }
    }

    return $linksElectro;
  }

  /**
   * Возвращает файл по хэшу.
   * @param string $md5
   * @return boolean
   */
  public function getFileByMd5($md5) {
    $linksElectro = array();

    $userInfo = USER::getThis();

    if (empty($userInfo)) {
      return false;
    }

    $res = DB::query('
      SELECT `link_electro`
      FROM `'.PREFIX.'product`
      WHERE MD5(concat('.DB::quote($userInfo->email).',`link_electro`)) = '.DB::quote($md5).' 
    ');

    if ($row = DB::fetchAssoc($res)) {
      $realLink = $row['link_electro'];
    }

    $realLink = str_replace('/', DIRECTORY_SEPARATOR, trim($realLink, DIRECTORY_SEPARATOR));
    $realLink = URL::getDocumentRoot().urldecode($realLink);
    
    if ($realLink) {
      header("Content-Length: ".filesize($realLink));
      header("Content-type: application/octed-stream");
      header('Content-Disposition: attachment; filename="'.basename($realLink).'"');
      readfile($realLink);
      exit();
    }
  }

  /**
   * Отправляет письмо со ссылками на приобретенные электронные товары
   * @param string $orderNamber - номер заказа.
   */
  public function sendLinkForElectro($orderId) {
    $linksElectro = array();
    $orderInfo = $this->getOrder(' id = '.DB::quote(intval($orderId), true));
    $orderInfo[$orderId]['order_content'] = unserialize(stripslashes($orderInfo[$orderId]['order_content']));
    $product = new Models_Product();
    foreach ($orderInfo[$orderId]['order_content'] as $item) {
      $productInfo = $product->getProduct($item['id']);
      if ($productInfo['link_electro']) {
        $linksElectro[] = $productInfo['link_electro'];
      }
    }
    // если нет электронных товаров в заказе, то не высылаем письмо
    if (empty($linksElectro)) {
      return false;
    }

    $siteName = MG::getSetting('sitename');
    $adminEmail = MG::getSetting('adminEmail');
    $userEmail = $orderInfo[$orderId]['user_email'];
    $orderNumber = $orderInfo['orderNumber'] != '' ? $orderInfo['orderNumber'] : $orderId;
    $subj = 'Ссылка для скачивания по заказу'.$orderNamber.' на сайте '.$siteName;

    $paramToMail = array(
      'orderNumber' => $orderNumber,
      'getElectro' => SITE.'/order?getFileToOrder='.$orderId
    );

    $emailToUser = MG::layoutManager('email_order_electro', $paramToMail);

    if (preg_match('/^[-._a-zA-Z0-9]+@(?:[a-zA-Z0-9][-a-zA-Z0-9]+\.)+[a-zA-Z]{2,6}$/', $userEmail)) {
      Mailer::sendMimeMail(array(
        'nameFrom' => $siteName,
        'emailFrom' => MG::getSetting('noReplyEmail'),
        'nameTo' => $userEmail,
        'emailTo' => $userEmail,
        'subject' => $subj,
        'body' => $emailToUser,
        'html' => true
      ));
    }

    $mails = explode(',', MG::getSetting('adminEmail'));

    foreach ($mails as $mail) {
      if (preg_match('/^[-._a-zA-Z0-9]+@(?:[a-zA-Z0-9][-a-zA-Z0-9]+\.)+[a-zA-Z]{2,6}$/', $mail)) {
        Mailer::sendMimeMail(array(
          'nameFrom' => $siteName,
          'emailFrom' => MG::getSetting('noReplyEmail'),
          'nameTo' => $sitename,
          'emailTo' => $mail,
          'subject' => $subj,
          'body' => 'Пользователю '.$userEmail.' выслана ссылка на электронные товары',
          'html' => true
        ));
      }
    }
  }

  /**
   * Уведомляет админов о смене статуса заказа пользователем, высылая им письма.
   * @param type $orderId - id заказа.
   * $orderNumber - номер заказа, которые отображается у пользователя
   */
  public function sendMailOfUpdateOrder($orderId) {
    $order = $this->getOrder('id = '.DB::quote(intval($orderId)));
    $orderNumber = $order[$orderId]['number'];
    $siteName = MG::getSetting('sitename');
    $adminEmail = MG::getSetting('adminEmail');
    $subj = 'Пользователь отменил заказ №'.$orderNumber.' на сайте '.$siteName;
    $msg = '
      Вы получили это письмо, так как произведена смена статуса заказа.
     <br/>Статус заказа #'.$orderNumber.' сменен на "Отменен".';

    $mails = explode(',', MG::getSetting('adminEmail'));

    foreach ($mails as $mail) {
      if (preg_match('/^[-._a-zA-Z0-9]+@(?:[a-zA-Z0-9][-a-zA-Z0-9]+\.)+[a-zA-Z]{2,6}$/', $mail)) {
        Mailer::sendMimeMail(array(
          'nameFrom' => $siteName,
          'emailFrom' => MG::getSetting('noReplyEmail'),
          'nameTo' => $sitename,
          'emailTo' => $mail,
          'subject' => $subj,
          'body' => $msg,
          'html' => true
        ));
      }
    }
  }

  /**
   * Возвращает массив параметров оплаты.
   * @param int $pay id способа оплаты.
   * @return array параметры оплаты.
   */
  public function getParamArray($pay, $orderId, $summ) {
    $paramArray = array();
    $jsonPaymentArray = json_decode(MG::nl2br($this->_paymentArray[$pay]['paramArray']), true);
    if (!empty($jsonPaymentArray)) {
      foreach ($jsonPaymentArray as $paramName => $paramValue) {
        $paramArray[] = array('name' => $paramName, 'value' => $paramValue);
      }
      if (5 == $pay) { // Для robokassa добавляем сигнатуру.
        $alg = $paramArray[3]['value'];
        $login = trim($paramArray[0]['value']);
        $pass1 = trim($paramArray[1]['value']);
        $paramArray['sign'] = hash($alg,$login.":".$summ.":".$orderId.":".$pass1);
      }
      if (9 == $pay) { // Для payanyway добавляем сигнатуру.	    
        $summ = sprintf("%01.2f", $summ);
        $currency = (MG::getSetting('currencyShopIso') == "RUR") ? "RUB" : MG::getSetting('currencyShopIso');
        $testmode = 0;
        
        if ($paramArray[2]['value'] == 'true') {
          $testmode = 1;
        }

        $alg = $paramArray[3]['value'];
        $account = trim($paramArray[0]['value']);
        $securityCode = trim($paramArray[1]['value']);
        $paramArray['sign'] = hash($alg, $account.$orderId.$summ.$currency.$testmode.$securityCode);
      }
      if(15 == $pay){
        $model = new Models_Order;
        $summ = sprintf("%01.2f", $summ);
        $order = $model->getOrder(' id = '.DB::quote(intval($orderId), true));
        $payment = 'amt='.$summ.'&ccy=UAH&details=заказ на '.SITE.'&ext_details='.$order[$orderId]['number']
          .'&pay_way=privat24&order='.$orderId.'&merchant='.trim($paramArray[0]['value']);
        $pass = trim($paramArray[1]['value']);
        $paramArray['sign'] = sha1(md5($payment.$pass));
      }
      if(16 == $pay){
        $model = new Models_Order;        
        $order = $model->getOrder(' id = '.DB::quote(intval($orderId), true));                
        $amount = sprintf("%01.2f", $summ);        
        $currency = MG::getSetting('currencyShopIso');
        
        if($currency == 'RUR'){
          $currency = 'RUB';
        }
        
        $params = array(
          'version'     => 3,
          'public_key'  => trim($paramArray[0]['value']),
          'action'      => 'pay',
          'amount'      => $amount,
          'currency'    => $currency,
          'description' => 'Оплата заказа № '.$order[$orderId]['number'],
          'order_id'    => $orderId,
          'server_url'  => SITE.'/payment?id=16&pay=result',
          'result_url'  => SITE.'/payment?id=16&order_id='.$orderId,
        );                

        //Для проведения тестовых платежей
        if(!empty($paramArray[2]['value']) && $paramArray[2]['value'] != "false"){
          $params['sandbox'] = 1;
        }                
        
        $paramArray['data'] = base64_encode(json_encode($params));
        $privateKey = trim($paramArray[1]['value']);
        $paramArray['signature'] = base64_encode(sha1($privateKey.$paramArray['data'].$privateKey, 1));
      }
    }
    
    return $paramArray;
  }

  /**
   * Создает дубль заказа
   * @return $id  -  номер копируемого заказа
   */
  public function cloneOrder($id) {
    // учет остатков товаров в заказе
    $res = DB::query('SELECT `order_content` FROM `'.PREFIX.'order` WHERE `id`= '.DB::quote($id));
    if ($row = DB::fetchArray($res)) {
      $content = unserialize(stripslashes($row['order_content']));
    }
    $allAvailable = true;
    foreach ($content as $item) {
      if ( $this->notSetGoods($item['id'])==false) {
        return false;
      }
      $res = DB::query('SELECT p.`count`, pv.`count` AS  `var_count`, pv.`code` 
        FROM `'.PREFIX.'product` p LEFT JOIN 
        `'.PREFIX.'product_variant` pv ON p.id = pv.product_id WHERE p.id='.DB::quote($item['id']));
      while($row = DB::fetchArray($res)) {
        if (!empty($row['code'])&& $row['code'] == $item['code']) {
          $count = $row['var_count'];
        } elseif(empty($row['code'])) {
          $count = $row['count'];
        }
      }
      if ($count >= 0 && $count < $item['count']) {
         $allAvailable = false;
      }
    }
    if ($allAvailable == false ){
      return false;
    }
    $product = new Models_Product();
    foreach ($content as $item) {
      $product->decreaseCountProduct($item['id'], $item['code'], $item['count']);
    }
    $sql = " INSERT INTO  
      `".PREFIX."order`
        ( 
          `updata_date`, 
          `add_date`, 
          `close_date`, 
          `user_email`, 
          `phone`, 
          `address`, 
          `summ`, 
          `order_content`, 
          `delivery_id`, 
          `delivery_cost`, 
          `payment_id`, 
          `paided`, 
          `status_id`, 
          `comment`, 
          `confirmation`, 
          `yur_info`, 
          `name_buyer`
        ) 
      SELECT 
        `updata_date`, 
         now() as `add_date`,
        `close_date`, 
        `user_email`, 
        `phone`, 
        `address`, 
        `summ`,
        `order_content`,
        `delivery_id`,
        `delivery_cost`,
        `payment_id`,
        `paided`,
        `status_id`,
        `comment`,
        `confirmation`,
        `yur_info`,
        `name_buyer`
      FROM ".PREFIX."order
      WHERE `id`= ".DB::quote($id);
    $res = DB::query($sql);
    $id = DB::insertId();
    $orderNumber = $this->getOrderNumber($id);
    DB::query("UPDATE `".PREFIX."order` SET `number`= ".DB::quote($orderNumber)." WHERE `id`=".DB::quote($id)."");
    return true;
  }

  /**
   * Возвращает общее количества невыполненных заказов.
   * @return int - количество заказов
   */
  public function getNewOrdersCount() {
    $sql = "
  		SELECT `id`
      FROM `".PREFIX."order`
      WHERE `status_id`!=5 AND `status_id`!=4";

    $res = DB::query($sql);
    $count = DB::numRows($res);
    return $count ? $count : 0;
  }

  /**
   * Возвращает статистику заказов за каждый день начиная с открытия магазина.
   * @return array - [время, значение]
   */
  public function getOrderStat() {
    $result = array();
    $res = DB::query('    
      SELECT (UNIX_TIMESTAMP( CAST( o.add_date AS DATE ) ) * 1000) as "date" , COUNT( add_date ) as "count"
      FROM `'.PREFIX.'order` AS o
      GROUP BY CAST( o.add_date AS DATE ),date
    ');

    while ($order = DB::fetchAssoc($res)) {
      $result[] = array($order['date'] * 1, $order['count'] * 1);
    }
    return $result;
  }

  /**
   * Возвращает статистику заказов за выбранный период. 
   * @param $dateFrom дата "от".
   * @param $dateTo дата "До".
   * @return array
   */
  public function getStatisticPeriod($dateFrom, $dateTo) {
    $dateFromRes = $dateFrom;
    $dateToRes = $dateTo;
    $dateFrom = date('Y-m-d', strtotime($dateFrom));
    $dateTo = date('Y-m-d', strtotime($dateTo));
    $period = "AND `add_date` >= ".DB::quote($dateFrom)."
       AND `add_date` <= ".DB::quote($dateTo);

    // Количество закрытых заказов всего.
    $ordersCount = $this->getOrderCount('WHERE status_id = 5 '.$period);

    $noclosed = $this->getOrderCount('WHERE status_id <> 5 '.$period);

    // Сумма заработанная за все время работы магазина.
    $res = DB::query("
      SELECT sum(summ) as 'summ'  FROM `".PREFIX."order`
      WHERE status_id = 5 ".$period
    );

    if ($row = DB::fetchAssoc($res)) {
      $summ = $row['summ'];
    }

    $product = new Models_Product;
    $productsCount = $product->getProductsCount();
    $res = DB::query("SELECT id  FROM `".PREFIX."user`");
    $usersCount = DB::numRows($res);

    $result = array(
      'from_date_stat' => $dateFromRes,
      'to_date_stat' => $dateToRes,
      "orders" => $ordersCount ? $ordersCount : "0",
      "noclosed" => $noclosed ? $noclosed : "0",
      "summ" => $summ ? $summ : "0",
      "users" => $usersCount ? $usersCount : "0",
      "products" => $productsCount ? $productsCount : "0",
    );

    return $result;
  }

  /**
   * Выводит на экран печатную форму для печати заказа в админке.
   * @param int $id - id заказа.
   * @param boolean $sign использовать ли подпись.
   * @param string $type - тип документа
   * @return array 
   */
  public function printOrder($id, $sign = true, $type="order") {    
    $orderInfo = $this->getOrder('id='.DB::quote(intval($id), true));
    if ($type == 'qittance') {
      $summ = $orderInfo[$id]['summ']+$orderInfo[$id]['delivery_cost'];
      $paramArray = $this->getParamArray('7', $id, $summ);
      $data['paramArray'] = $paramArray;
      foreach($data['paramArray'] as $k=>$field){
        $data['paramArray'][$k]['value'] = htmlentities($data['paramArray'][$k]['value'], ENT_QUOTES, "UTF-8");
      }
      $line = "<p class='line'></p>";
      $data['line'] = "<p class='line'></p>";
      $data['line2'] = "<p class='line2'></p>";
      $data['name'] = (!empty($data['paramArray'][0]['value'])) ? $data['paramArray'][0]['value'] : $line;
      $data['inn'] = (!empty($data['paramArray'][1]['value'])) ? $data['paramArray'][1]['value'] : $line;
      $data['nsp'] = (!empty($data['paramArray'][6]['value'])) ? $data['paramArray'][6]['value'] : $line;
      $data['ncsp'] = (!empty($data['paramArray'][7]['value'])) ? $data['paramArray'][7]['value'] : $data['line2'];
      $data['bank'] = (!empty($data['paramArray'][4]['value'])) ? $data['paramArray'][4]['value'] : $line;
      $data['bik'] = (!empty($data['paramArray'][5]['value'])) ? $data['paramArray'][5]['value'] : $data['line2'];
      $data['appointment'] = "Оплата по счету №".($orderInfo[$id]['number']!=''?$orderInfo[$id]['number']:$id);
      $data['nls'] = $line;
      $data['payer'] = $orderInfo[$id]['name_buyer'];
      $data['addrPayer'] = $orderInfo[$id]['address'];
      $data['sRub'] = $orderInfo[$id]['summ']+$orderInfo[$id]['delivery_cost'] ? $orderInfo[$id]['summ']+$orderInfo[$id]['delivery_cost'] : '_______';
      $data['sKop'] = 0;
      $data['uRub'] = '_______';
      $data['uKop'] = 0;
      $data['day'] = date('d');
      $data['month'] = date('m');
      $data['rub'] = '_______';
      $data['kop'] = '_______';
      $html = MG::layoutManager('print_'.$type, $data);
      return $html;
    }
    $order = $orderInfo[$id];
    $lang = MG::get('lang');

    $perOrders = unserialize(stripslashes($order['order_content']));
    $currency = MG::getSetting('currency');
    $totSumm = $order['summ'] + $order['cost'];
    $paymentArray = $this->getPaymentMethod($order['payment_id']);
    $order['name'] = $paymentArray['name'];

    $propertyOrder = MG::getOption('propertyOrder');
    $propertyOrder = stripslashes($propertyOrder);
    $propertyOrder = unserialize($propertyOrder);


    $paramArray = $this->getParamArray(7, $order['id'], $order['summ']);
    foreach ($paramArray as $k => $field) {
      $paramArray[$k]['value'] = htmlentities($paramArray[$k]['value'], ENT_QUOTES, "UTF-8");
    }

    $customer = unserialize(stripslashes($order['yur_info']));

    if ($type == 'packing-list') {
      $customerInfo = $customer['nameyur'].', '
          .'ИНН '. $customer['inn'].', '
          .$customer['adress'].', '
          .'р/с '.$customer['rs'].', '
          .'в банке '.$customer['bank'].', '
          .'БИК '.$customer['bik'].', '
          .'к/с '.$customer['ks'];
    } else {
      $customerInfo = $lang['OREDER_LOCALE_16'].': &nbsp;'. $customer['inn'].'<br/>'.$lang['OREDER_LOCALE_17'].': &nbsp;'.$customer['kpp'].'<br/>'.$lang['OREDER_LOCALE_9'].': &nbsp;'.
        $customer['nameyur'].'<br/>'.$lang['OREDER_LOCALE_15'].': &nbsp;'.$customer['adress'].'<br/>'.$lang['OREDER_LOCALE_18'].': &nbsp;'.
        $customer['bank'].'<br/>'.$lang['OREDER_LOCALE_19'].': &nbsp;'.$customer['bik'].'<br/>'.$lang['OREDER_LOCALE_20'].': &nbsp;'.$customer['ks'].'<br/>'.$lang['OREDER_LOCALE_21'].': &nbsp;'.
        $customer['rs'];
    }

    $ylico = false;
    if (empty($customer['inn']) || empty($customer['bik'])) {
      $fizlico = true;
      $userInfo = USER::getUserInfoByEmail($order['user_email']);
      
      if ($type == 'packing-list') {
        $customerInfo = $order['name_buyer'].', '.$order['address'].', '.$order['phone'];
      } else {
        $customerInfo = $lang['ORDER_BUYER'].': &nbsp;'.$order['name_buyer'].'<br/>'.$lang['ORDER_ADDRESS'].': &nbsp;'.
          $order['address'].'<br/> '.$lang['ORDER_PHONE'].': &nbsp;'.
          $order['phone'].' <br/>'.$lang['ORDER_EMAIL'].': &nbsp;'.$order['user_email'];
      }
    }
    
    if ($type == "invoice" && !empty($customer['nameyur']) && !empty($customer['adress'])) {
      $order['name_buyer'] = $customer['nameyur'];
      $order['address'] = $customer['adress'];
    }

    $customerInfo = htmlspecialchars($customerInfo);
    $propertyOrder['sing'] = $propertyOrder['sing'] ? $propertyOrder['sing'] : 'uploads/sing.jpg';
    $propertyOrder['stamp'] = $propertyOrder['stamp'] ? $propertyOrder['stamp'] : 'uploads/stamp.jpg';
    $data['propertyOrder'] = $propertyOrder;
    $data['order'] = $order;
    $data['customerInfo'] = $customerInfo;
    $data['perOrders'] = $perOrders;
    $data['currency'] = $currency;
    $data['customerInfo'] = htmlspecialchars_decode($data['customerInfo']);

    if(empty($type)){
      $type="order";
    }
    
    $html = MG::layoutManager('print_'.$type, $data);


    return $html;
  }

  /**
   * Отдает pdf файл на скачивание.
   * @param $orderId номер заказа id.
   * @return array 
   */
  public function getPdfOrder($orderId, $type="order") {    
    if(empty($type)){
      $type="order";
    }
    // Подключаем библиотеку tcpdf.php    
    require_once('mg-core/script/tcpdf/tcpdf.php');
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    $pdf->setImageScale(1.53);
    $pdf->SetFont('arial', '', 10);
    $pdf->AddPage();

    $orderInfo = $this->getOrder('id='.DB::quote(intval($orderId), true));

    $access = false;
    if (USER::getThis()->email && (USER::getThis()->email == $orderInfo[$orderId]['user_email'] || USER::getThis()->role != 2)) {
      $access = true;
    }

    if (!$access) {
      MG::redirect('/404');
      return false;
    }

    $html = $this->printOrder($orderId, true, $type);

    $pdf->writeHTML($html, true, false, true, false, '');
    $pdf->Output('Order '.$orderInfo[$orderId]['number'].'.pdf', 'I');
    exit;
  }

  /**
   * Выводит на экран печатную форму для печати квитанции на оплату заказа.
   * @param boolean вывод на печать в публичной части, либо в админке.
   * @return array 
   */
  public function printQittance($public = true) {
    MG::disableTemplate();

    $data['line'] = "<p class='line'></p>";
    $data['line2'] = "<p class='line2'></p>";
    $data['name'] = (!empty($_POST['name'])) ? $_POST['name'] : $line;
    $data['inn'] = (!empty($_POST['inn'])) ? $_POST['inn'] : $line;
    $data['nsp'] = (!empty($_POST['nsp'])) ? $_POST['nsp'] : $line;
    $data['ncsp'] = (!empty($_POST['ncsp'])) ? $_POST['ncsp'] : $data['line2'];
    $data['bank'] = (!empty($_POST['bank'])) ? $_POST['bank'] : $line;
    $data['bik'] = (!empty($_POST['bik'])) ? $_POST['bik'] : $data['line2'];
    $data['appointment'] = (!empty($_POST['appointment'])) ? $_POST['appointment'] : $line;
    $data['nls'] = (!empty($_POST['nls'])) ? $_POST['nls'] : $line;
    $data['payer'] = (!empty($_POST['payer'])) ? $_POST['payer'] : $data['line2'];
    $data['addrPayer'] = (!empty($_POST['addrPayer'])) ? $_POST['addrPayer'] : $data['line2'];
    $data['sRub'] = (!empty($_POST['sRub'])) ? $_POST['sRub'] : '_______';
    $data['sKop'] = (!empty($_POST['sKop'])) ? $_POST['sKop'] : 0;
    $data['uRub'] = (!empty($_POST['uRub'])) ? $_POST['uRub'] : '_______';
    $data['uKop'] = (!empty($_POST['uKop'])) ? $_POST['uKop'] : 0;
    $data['day'] = (!isset($_POST['day']) || $_POST['day'] == '_') ? '____' : $_POST['day'];
    $data['month'] = (!isset($_POST['month']) || $_POST['month'] == '_') ?
      '___________________' : $_POST['month'];

    if (!isset($_POST['sKop'])) {
      $sKop = '___';
    }
    if (!isset($_POST['uKop'])) {
      $uKop = '___';
    }
    $sResult = (!empty($sKop)) ? $sResult = "$sRub.$sKop" : $sRub;
    $uResult = (!empty($uKop)) ? $uResult = "$uRub.$uKop" : $uRub;

    $rubResult = $sResult + $uResult;

    if (empty($rubResult)) {
      settype($rubResult, 'null');
    }

    if (is_double($rubResult)) {
      list($rub, $kop) = explode('.', $rubResult);
    } else if (is_int($rubResult)) {
      $rub = $rubResult;
      $kop = "0";
    }

    if (empty($rub))
      $rub = '_______';
    if (!isset($kop))
      $kop = '___';

    $data['rub'] = $rub;
    $data['kop'] = $kop;
    $data['uKop'] = $uKop;
    $data['sKop'] = $sKop;
    $html = MG::layoutManager('print_qittance', $data);
    if ($public) {
      echo $html;
      exit();
    }
    return $html;
  }

  /**
   * Экспортирует параметры конкретного заказа в CSV файл.
   * @param $orderId - id заказа.
   * @return array
   */
  public function getExportCSV($orderId) {

    header("Content-Type: application/force-download");
    header("Content-Type: application/octet-stream;");
    header("Content-Type: application/download");
    header("Content-Disposition: attachment;filename=data.csv");
    header("Content-Transfer-Encoding: binary ");

    $orderInfo = $this->getOrder('id='.DB::quote(intval($orderId), true));

    $order = $orderInfo[$orderId];

    $order_content =stripslashes($order["order_content"]);
    $order_content = unserialize($order_content);
  
    foreach ($order_content as $item) {
      $csvText .= "\"".$item["id"]."\";\"".$order["add_date"]."\";\"".$order["name_buyer"]."\";\"".$order["user_email"]."\";\"".$order["phone"]."\";\"".$order["address"]."\";\"".$order["comment"]."\";\"".$item["name"]."\";\"".$item["code"]."\";\"".$item["price"]."\";\"".$item["count"]."\";\"".$item["coupon"]."\"\n";
    }

    echo mb_convert_encoding($csvText, "WINDOWS-1251", "UTF-8");
    exit;
  }
  
  private function getOrderNumber($id) {
    $orderNum = MG::getSetting('orderNumber');
    $prefix = PREFIX_ORDER !='PREFIX_ORDER' ? PREFIX_ORDER : '';
    if ($orderNum =='false') {
      $result = $prefix.$id;
    }
    else {
      $str = mt_rand(10000, 9999999999);
      $result = str_pad((string)$str, 10, '0', STR_PAD_LEFT);
      $result = $prefix.$result;
    }
    // Возвращаем номер или префикс заказа.
    $args = func_get_args();
    return MG::createHook(__CLASS__."_".__FUNCTION__, $result, $args);
  }
  
  /** 
   *  Упорядочивает по сортировке.
   */
  public function sort($a, $b) {
    return $a['sort'] - $b['sort'];
  }
  
   /**
   * Пересчитывает количество остатков продуктов при редактировании заказа.
   * @param int $orderId  - id заказа.
   *           $content - новое содержимое содержимое заказа
   * @return bool
   */
  public function refreshCountAfterEdit($orderId, $content) {

    // Если количество товара меняется, то пересчитываем остатки продуктов из заказа.
    $order = $this->getOrder(' id = '.DB::quote(intval($orderId), true));

    $order_content_old = unserialize(stripslashes($order[$orderId]['order_content']));
    $order_content_new = unserialize(stripslashes($content));
    $product = new Models_Product();
    $codes = array();
    foreach ($order_content_old as $item_old) {
      if (!empty($codes[$item_old['id'].'|'.$item_old['code']])) {
        $codes[$item_old['id'].'|'.$item_old['code']]['count'] += $item_old['count'];
      } else {
        $codes[$item_old['id'].'|'.$item_old['code']] = array('id' => $item_old['id'],
        'code' => $item_old['code'],
        'count' => $item_old['count']);
      }      
    }
       
    foreach ($order_content_new as $item_new) {
      $flag = 0;
      foreach ($codes as $key => $info) {
        if (in_array($item_new['code'], $info)&&$item_new['id']==$info['id']) {
          $codes[$key] = array('id' => $item_new['id'],
            'code' => $item_new['code'],
            'count' => $info['count'] - $item_new['count']);
          $flag = 1;
        }
      }
      if ($flag === 0) {
        $codes[] = array('id' => $item_new['id'],
          'code' => $item_new['code'],
          'count' => $item_new['count'] * (-1));
      }
    }
    
    foreach ($codes as $prod) {
      if ($prod['count'] > 0) {
        $product->increaseCountProduct($prod['id'], $prod['code'], $prod['count']);
      } elseif ($prod['count'] < 0) {
        $product->decreaseCountProduct($prod['id'], $prod['code'], abs($prod['count']));
      }
    }
    $result = $flag;
    $args = func_get_args();
    return MG::createHook(__CLASS__."_".__FUNCTION__, $result, $args);
  }

  /**
   * Проверяет есть в заказе комплект или нет при копировании заказа
   * @param array $id - id товара
   */
  public function notSetGoods($id) {
    $result = true;
    $args = func_get_args();
    return MG::createHook(__CLASS__."_".__FUNCTION__, $result, $args);
  }

  /**
   * Выгружает список заказов в CSV файл.
   * $listOrderId выгрузка выбранных заказов
   * @return array
   */
  public function exportToCsvOrder($listOrderId=array(), $full = false) {
  
    header("Content-Type: application/force-download");
    header("Content-Type: application/octet-stream;");
    header("Content-Type: application/download");
    header("Content-Disposition: attachment;filename=data.csv");
    header("Content-Transfer-Encoding: binary ");

    $csvText = '';
    $csvText .= '"id";"Номер";"Дата создания";"Email";"Имя пользователя";"Телефон";"Адрес";"Сумма";"Купон";"Скидка";"Статус заказа";"Способ доставки";"Стоимость доставки";"Оплата";'."\n";
    if ($full) {
      $csvText = '"id заказа";"Номер";"Дата создания";"Сумма заказа";"id товара";"Артикул";"Наименование";"Количество";"Полная цена";"Стоимость";"Купон";"Скидка";"Email";"Имя пользователя";"Телефон";"Адрес";"Статус заказа";"Способ доставки";"Стоимость доставки";"Оплата";'."\n";
    }
    Storage::$noCache = true;
    $page = 1;
    // получаем максимальное количество заказов, если выгрузка всего ассортимента
    if(empty($listOrderId)){
      $res = DB::query('
        SELECT count(id) as count
        FROM `'.PREFIX.'order`
        ');
      if ($order = DB::fetchAssoc($res)) {
        $count = $order['count'];
      }
      $maxCountPage = ceil($count / 500);
    } else {
      $maxCountPage = ceil(count($listOrderId) / 500);
      $listId = implode(',', $listOrderId);
    }    
    for ($page = 1; $page <= $maxCountPage; $page++) {      
      URL::setQueryParametr("page", $page);
      $sql = 'SELECT * FROM `'.PREFIX.'order` ';
      if(!empty($listOrderId)){  
        $sql .= ' WHERE `id` IN ('.DB::quote($listId,1).')';     
      }
      $sql .= ' ORDER BY `add_date`'; 
      $navigator = new Navigator($sql, $page, 500); //определяем класс  
      $orders = $navigator->getRowsSql();
      foreach ($orders as $row) {
        $csvText .= self::addOrderToCsvLine($row, $full);
        }
      }
    
    $csvText = substr($csvText, 0, -2); // удаляем последний символ '\n'
        
    $csvText = mb_convert_encoding($csvText, "WINDOWS-1251", "UTF-8");
    if(empty($listOrderId)){
      echo $csvText;
      exit;
    } else{
      $date = date('m_d_Y_h_i_s');
      file_put_contents('data_csv_'.$date.'.csv', $csvText);
      $msg = 'data_csv_'.$date.'.csv';
    }
    return $msg;
  }

  /**
   * Добавляет пользователя в CSV выгрузку.
   * @param array $row - запись о пользователе.
   * @return string
   */
  public function addOrderToCsvLine($row, $full=false) {
    $csvText = '';
    $row['user_email'] = '"' . str_replace("\"", "\"\"", $row['user_email']) . '"';
    $row['id'] = '"' . str_replace("\"", "\"\"", $row['id']) . '"';
    $row['number'] = '"' . str_replace("\"", "\"\"", $row['number']) . '"';
    $row['address'] = '"' . str_replace("\"", "\"\"", $row['address']) . '"';
    $row['phone'] = '"' . str_replace("\"", "\"\"", $row['phone']) . '"';
    $delivery = $this->getDeliveryMethod(false, $row['delivery_id']);
    $row['delivery_id'] = '"' . str_replace("\"", "\"\"", $delivery['description']) . '"';
    $row['delivery_cost'] = '"' . str_replace("\"", "\"\"", $row['delivery_cost']) . '"';
    $row['payment_id'] = '"' . str_replace("\"", "\"\"", $this->_paymentArray[$row['payment_id']]['name']) . '"';
    $statusArray = self::$status;
    $lang = MG::get('lang');
    $row['status_id'] = '"' . str_replace("\"", "\"\"", $lang[$statusArray[$row['status_id']]]) . '"';
    $row['name_buyer'] = '"' . str_replace("\"", "\"\"", $row['name_buyer']) . '"';
    $row['add_date'] = '"' . str_replace("\"", "\"\"", date('d.m.Y', strtotime($row['add_date']))).'"';
    $row['summ'] = '"' . str_replace("\"", "\"\"",  str_replace('.', ',',$row['summ'])) . '"';
    $content = unserialize(stripslashes($row['order_content'])); 
    foreach ($content as $order) {
      $coupon= '"' . str_replace("\"", "\"\"", (!empty($order['coupon'])&&$order['coupon']!="Не указан"? urldecode($order['coupon']):'')) . '"';
      $discount= '"' . str_replace("\"", "\"\"", (!empty($order['discount'])? $order['discount']:'')) . '"';
      if ($full) {
        $code = '"' . str_replace("\"", "\"\"", $order['code']) . '"';
        $id = '"' . str_replace("\"", "\"\"", $order['id']) . '"';
        $count = '"' . str_replace("\"", "\"\"", $order['count']).'"';
        $name = '"' . str_replace("\"", "\"\"", urldecode($order['name'])).'"';
        $price = '"' . str_replace("\"", "\"\"", str_replace('.', ',',$order['price'])) . '"';
        $priceFull = '"' . str_replace("\"", "\"\"", str_replace('.', ',',$order['fulPrice'])) . '"';
        $csvText .= $row['id'].";".$row['number'].";". $row['add_date'].";".
          $row['summ'].";".$id.";".$code.";".$name.";".$count.";".
          $priceFull.";".$price.";".$coupon.";".$discount.";".
          $row['user_email'].";".$row['name_buyer'].";".$row['phone'].";".
          $row['address'].";".$row['status_id'].";".
          $row['delivery_id'] . ";" .
          $row['delivery_cost'] . ";" .
          $row['payment_id'] . ";". "\n";
      }      
    }      
    if (!$full) {
      $csvText = $row['id'] . ";" .
        $row['number'] . ";" .
        $row['add_date'] . ";" .
        $row['user_email'] . ";" .     
        $row['name_buyer'] . ";" .
        $row['phone'] . ";" .
        $row['address'] . ";" .
        $row['summ'] . ";" .
        $coupon . ";" .
        $discount . ";".
        $row['status_id'] . ";" .
        $row['delivery_id'] . ";" .
        $row['delivery_cost'] . ";" .
        $row['payment_id'] . ";". "\n";
    }
    return $csvText;
  }
  
  /**
   * Поиск скидки, применяемой к заказу по промокоду или в рамках 
   * накопительной/объемной скидки.
   */
  public function getOrderDiscount($params) {
    if ($params['promocode']) {
      $result = DB::query('SHOW TABLES LIKE "'.PREFIX.'promo-code"');
      
      if (DB::numRows($result)) {
        $sql = 'SELECT * FROM `'.PREFIX.'promo-code` 
          WHERE `code` ='.DB::quote($params['promocode']).'
           AND `invisible` = 1 
           AND now() >= `from_datetime`
           AND now() <= `to_datetime`';
        $res = DB::query($sql);

          if ($code = DB::fetchAssoc($res)) {
            $percent = $code['percent'] ? $code['percent'] : 0;
            
        };
      }        
    }
    $percent = (float) $percent;
    
    if ($params['cumulative']=='true' || $params['volume']=='true') {
      $result = DB::query('SHOW TABLES LIKE "'.PREFIX.'discount-system%"');
      if (DB::numRows($result)) {
        
        if ($params['cumulative'] == 'true' && $params['email']) {
          $sql = "SELECT SUM(`summ`) as summ FROM  `".PREFIX."order`       
            WHERE  `user_email` =  ".DB::quote($params['email'])." 
            AND ( `status_id` =  '2'
            OR  `status_id` =  '5')";
          $res = DB::query($sql);
          
          if ($count = DB::fetchAssoc($res)) {
            $sql = "SELECT * FROM `".PREFIX."discount-system-cumulative` 
              WHERE `summ` <= ".DB::quote($count['summ'])." ORDER BY `summ` DESC LIMIT 1";
            $res = DB::query($sql);
            
            if ($discount = DB::fetchAssoc($res)) {
              $percent += (float) $discount['percent'];
            }
          }
        } 
        
        if ($params['volume'] == 'true' && ($params['summ'] > 0)) {
          $sql = 'SELECT * FROM `'.PREFIX.'discount-system-volume` 
            WHERE `summ` <= '.DB::quote($params['summ']).' ORDER BY `summ` DESC LIMIT 1';
          $rez = DB::query($sql);
          
          if ($discount = DB::fetchArray($rez)) {
            $percent += (float) $discount['percent'];
          }
        }
      }
    }
    
    if(!empty($params['paymentId'])){
      $payment = $this->getPaymentMethod($params['paymentId']);
      
      if(!empty($payment['rate'])){   
        $summ = floatval($params['summ']);
        $summDiscount = $summ - ($summ * $percent / 100);
        $percent1 = (float) -1*($payment['rate']*100);
        $summDiscountPay = ($summDiscount * $percent1 / 100);
        $per = $summDiscountPay / $summ * 100;
        $percent += $per;
      }
    }
    
    $productDiscount = array();
    
    foreach ($params['orderItems'] as $orderItem) {
      $productDiscount[] = array(
        'id' => $orderItem['id'],
        'discount' => $percent,
      );
    }
    
    $result = array(
      'percent' => $percent,
      'productDiscount' => $productDiscount,
    );
    
    $args = func_get_args();
    
    return MG::createHook(__CLASS__."_".__FUNCTION__, $result, $args);
  }
}
