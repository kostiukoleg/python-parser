<?php

/**
 * Контроллер: Catalog
 *
 * Класс Controllers_Catalog обрабатывает действия пользователей в каталоге интернет магазина.
 * - Формирует список товаров для конкретной страницы;
 * - Добавляет товар в корзину.
 *
 * @author Авдеев Марк <mark-avdeev@mail.ru>
 * @package moguta.cms
 * @subpackage Controller
 */
class Controllers_Index extends BaseController {

  function __construct() {
    $settings = MG::get('settings');
    // Если нажата кнопка купить.
    $_REQUEST['category_id'] = URL::getQueryParametr('category_id');
    $_REQUEST['inCartProductId'] = intval($_REQUEST['inCartProductId']);

    if (!empty($_REQUEST['inCartProductId'])) {
      $cart = new Models_Cart;
      $property = $cart->createProperty($_POST);
      $cart->addToCart($_REQUEST['inCartProductId'], $_REQUEST['amount_input'], $property);
      SmalCart::setCartData();
      MG::redirect('/cart');
    }

    $countСatalogProduct = $settings['countСatalogProduct'];
    // Показать первую страницу выбранного раздела.
    $page = 1;

    // Запрашиваемая страница.
    if (isset($_REQUEST['p'])) {
      $page = $_REQUEST['p'];
    }
    
    $model = new Models_Catalog;

    // Получаем список вложенных категорий, для вывода всех продуктов, на страницах текущей категории.
    $model->categoryId = MG::get('category')->getCategoryList($_REQUEST['category_id']);

    // В конец списка, добавляем корневую текущую категорию.
    $model->categoryId[] = $_REQUEST['category_id'];

    // Передаем номер требуемой страницы, и количество выводимых объектов.
    $countСatalogProduct = 100;
    if (MG::getSetting('mainPageIsCatalog') == 'true'){
    $printCompareButton = MG::getSetting('printCompareButton');
    $actionButton = MG::getSetting('actionInCatalog') === "true" ? 'actionBuy' : 'actionView';
    $dataGroupProducts = Storage::get(md5('dataGroupProductsIndexConroller'));
    
    $currencyRate = MG::getSetting('currencyRate');      
    $currencyShopIso = MG::getSetting('currencyShopIso'); 
    $randomProdBlock = MG::getSetting('randomProdBlock')=="true"? true: false;  
    
    if ($dataGroupProducts == null) {
      $onlyInCount = '';
      
      if(MG::getSetting('printProdNullRem') == "true"){
        $onlyInCount = 'AND p.count != 0'; // ищем только среди тех которые есть в наличии
      }
      DB::query('SELECT `system_set` FROM `'.PREFIX.'product`');
      // Формируем список товаров для блока рекомендуемой продукции.
      $sort = $randomProdBlock ? "RAND()" : "sort";    
      $recommendProducts = $model->getListByUserFilter(MG::getSetting('countRecomProduct'), ' p.recommend = 1 and p.activity=1 '.$onlyInCount.' ORDER BY '.$sort.' ASC');
      foreach ($recommendProducts['catalogItems'] as &$item) {
        $imagesUrl = explode("|", $item['image_url']);
        $item["image_url"] = "";
        if (!empty($imagesUrl[0])) {
          $item["image_url"] = $imagesUrl[0];
        }
         $item['currency_iso'] = $item['currency_iso']?$item['currency_iso']:$currencyShopIso;
         $item['old_price'] = $item['old_price']? MG::priceCourse($item['old_price']):0;
         $item['price'] =  MG::priceCourse($item['price_course']); 
         if($printCompareButton!='true'){
          $item['actionCompare'] = '';         
        }    
        if($actionButton=='actionBuy' && $item['count']==0){
          $item['actionBuy'] = $item['actionView'];         
        }
      }

      // Формируем список товаров для блока новинок.
      $newProducts = $model->getListByUserFilter(MG::getSetting('countNewProduct'), ' p.new = 1 and p.activity=1 '.$onlyInCount.' ORDER BY '.$sort.' ASC');

      foreach ($newProducts['catalogItems'] as &$item) {
        $imagesUrl = explode("|", $item['image_url']);
        $item["image_url"] = "";
        if (!empty($imagesUrl[0])) {
          $item["image_url"] = $imagesUrl[0];
        }
        $item['currency_iso'] = $item['currency_iso']?$item['currency_iso']:$currencyShopIso;
        $item['old_price'] = $item['old_price']? MG::priceCourse($item['old_price']):0;
        $item['price'] =  MG::priceCourse($item['price_course']); 
        if($printCompareButton!='true'){
          $item['actionCompare'] = '';         
        }    
        if($actionButton=='actionBuy' && $item['count']==0){
          $item['actionBuy'] = $item['actionView'];         
        }
      }

      // Формируем список товаров со старой ценой.
      $saleProducts = $model->getListByUserFilter(MG::getSetting('countSaleProduct'), ' (p.old_price>0 || pv.old_price>0) and p.activity=1 '.$onlyInCount.' ORDER BY '.$sort.' ASC');

      foreach ($saleProducts['catalogItems'] as &$item) {
        $imagesUrl = explode("|", $item['image_url']);
        $item["image_url"] = "";
        if (!empty($imagesUrl[0])) {
          $item["image_url"] = $imagesUrl[0];
        }
        $item['currency_iso'] = $item['currency_iso']?$item['currency_iso']:$currencyShopIso;
        $item['old_price'] = $item['old_price']? MG::priceCourse($item['old_price']):0;
        $item['price'] =  MG::priceCourse($item['price_course']); 
        if($printCompareButton!='true'){
          $item['actionCompare'] = '';         
        }    
        if($actionButton=='actionBuy' && $item['count']==0){
          $item['actionBuy'] = $item['actionView'];         
        }
       
      }

      $dataGroupProducts['recommendProducts'] = $recommendProducts;
      $dataGroupProducts['newProducts'] = $newProducts;
      $dataGroupProducts['saleProducts'] = $saleProducts;
      Storage::save(md5('dataGroupProductsIndexConroller'), $dataGroupProducts);
    }
    
    $recommendProducts = $dataGroupProducts['recommendProducts'];
    $newProducts = $dataGroupProducts['newProducts'];
    $saleProducts = $dataGroupProducts['saleProducts'];
    }
    // Если пришли данные с формы.
    if(isset($_POST['send'])){

      // Создает модель отправки сообщения.
      $feedBack = new Models_Feedback;

      // Проверяет на корректность вода.
      $error = $feedBack->isValidData($_POST);
      $data['error'] = $error;

      // Если есть ошибки заносит их в переменную.
      if(!$error){
        //$_POST['message'] = MG::nl2br($_POST['message']);     
        //Отправляем админам.
        $sitename = MG::getSetting('sitename');       
        //$message = str_replace('№', '#', $feedBack->getMessage());        
        $body = MG::layoutManager('email_feedback', array('phone'=>$feedBack->getPhone(), 'name'=>$feedBack->getFio()));
     
        $mails = explode(',', MG::getSetting('adminEmail'));    
        foreach($mails as $mail){
          if(preg_match('/^[-._a-zA-Z0-9]+@(?:[a-zA-Z0-9][-a-zA-Z0-9]+\.)+[a-zA-Z]{2,6}$/', $mail)){
            Mailer::sendMimeMail(array(
              'nameFrom' => $feedBack->getFio(),
              'emailFrom' => MG::getSetting('noReplyEmail'),
              'nameTo' => $sitename,
              'emailTo' => $mail,
              'subject' => 'Сообщение с формы обратной связи',
              'body' => $body,
              'html' => true
            ));
          }
        }

        MG::redirect('/index');
      }
    }
    //Конец форми отправки

    // Если пришли данные с формы2.
    if(isset($_POST['send2'])){

      // Создает модель отправки сообщения.
      $feedBack = new Models_Feedback2;

      // Проверяет на корректность вода.
      $error = $feedBack->isValidData($_POST);
      $data['error'] = $error;

      // Если есть ошибки заносит их в переменную.
      if(!$error){
        //$_POST['message'] = MG::nl2br($_POST['message']);     
        //Отправляем админам.
        $sitename = MG::getSetting('sitename');       
        //$message = str_replace('№', '#', $feedBack->getMessage());        
        $body = MG::layoutManager('email_feedback2', array('phone2'=>$feedBack->getPhone(), 'name2'=>$feedBack->getFio(), 'email2'=>$feedBack->getEmail(), 'text2'=>$feedBack->getMessage()));
     
        $mails = explode(',', MG::getSetting('adminEmail'));    
        foreach($mails as $mail){
          if(preg_match('/^[-._a-zA-Z0-9]+@(?:[a-zA-Z0-9][-a-zA-Z0-9]+\.)+[a-zA-Z]{2,6}$/', $mail)){
            Mailer::sendMimeMail(array(
              'nameFrom' => $feedBack->getFio(),
              'emailFrom' => MG::getSetting('noReplyEmail'),
              'nameTo' => $sitename,
              'emailTo' => $mail,
              'subject' => 'Сообщение с формы обратной связи',
              'body' => $body,
              'html' => true
            ));
          }
        }

        MG::redirect('/index');
      }
    }
    //Конец форми отправки

    $html = MG::get('pages')->getPageByUrl('index');
    
    if(!empty($html)){
      $html['html_content'] = MG::inlineEditor(PREFIX.'page', "html_content", $html['id'], $html['html_content']);
    }else{
      $html['html_content'] = '';    
    }
    $this->data = array(
      'dislpayForm' => true,
      'recommendProducts' => !empty($recommendProducts['catalogItems'])&&MG::getSetting('countRecomProduct') ? $recommendProducts['catalogItems'] : array(),
      'newProducts' => !empty($newProducts['catalogItems'])&&MG::getSetting('countNewProduct') ? $newProducts['catalogItems'] : array(),
      'saleProducts' => !empty($saleProducts['catalogItems'])&&MG::getSetting('countSaleProduct') ? $saleProducts['catalogItems'] : array(),
      'titeCategory' => $html['meta_title'],
      'cat_desc' => $html['html_content'],
      'meta_title' => $html['meta_title'],
      'meta_keywords' => $html['meta_keywords'],
      'meta_desc' => $html['meta_desc'],
      'currency' => $settings['currency'],
      'actionButton' => $actionButton
    );
  }

}
