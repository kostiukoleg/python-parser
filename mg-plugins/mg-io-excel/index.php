<?php
/*
  Plugin Name: Импорт/Экспорт Excel
  Description: Плагин позволяет работать с каталогом используя файлы Excel.
  Author: Osipov Ivan, Gaydis Mikhail
  Version: 1.2.3
*/

new mgIOExcel;

class mgIOExcel{

  private static $lang = array(); // массив с переводом плагина 
  private static $pluginName = ''; // название плагина (соответствует названию папки)
  private static $path = ''; //путь до файлов плагина 
  private static $startTime = null;
  private static $maxExecTime = null;
  private static $maskArray = array();
  private $currentRowId = null;
  private static $validError = null;   

  public function __construct(){
    mgActivateThisPlugin(__FILE__, array(__CLASS__, 'activate')); //Инициализация  метода выполняющегося при активации  
    mgAddAction(__FILE__, array(__CLASS__, 'pageSettingsPlugin')); //Инициализация  метода выполняющегося при нажатии на кнопку настроек плагина  

    self::$pluginName = PM::getFolderPlugin(__FILE__);
    self::$lang = PM::plugLocales(self::$pluginName);
    self::$path = PLUGIN_DIR.self::$pluginName;
    self::$maxExecTime = min(30, @ini_get("max_execution_time"));
    
    if(empty(self::$maxExecTime)){
      self::$maxExecTime = 30;
    }
    
    self::$maskArray = array(
      'MogutaCMS' => array(
        0 => 'ID товара',              // 0
        1 => 'Артикул',                // 1
        2 => 'Категория',              // 2
        3 => 'URL категории',          // 3
        4 => 'Товар',                  // 4
        5 => 'Вариант',                // 5
        6 => 'Краткое описание',       // 6
        7 => 'Описание',               // 7
        8 => 'Цена',                   // 8
        9 => 'Старая цена',            // 9
        10 => 'URL товара',             // 10
        11 => 'Изображение',            // 11
        12 => 'Количество',             // 12
        13 => 'Активность',             // 13
        14 => 'Заголовок [SEO]',        // 14
        15 => 'Ключевые слова [SEO]',   // 15
        16 => 'Описание [SEO]',         // 16
        17 => 'Рекомендуемый',          // 17
        18 => 'Новый',                  // 18
        19 => 'Сортировка',             // 19
        20 => 'Вес',                    // 20
        21 => 'Связанные артикулы',     // 21
        22 => 'Смежные категории',      // 22
        23 => 'Ссылка на товар',        // 23
        24 => 'Валюта',                 // 24
        // 25 => 'Единицы измерения',      // 25

        // #MG_DEL (MARKET, MINI, FREE)
        // 26 => 'Оптовые цены',           // 26
        // 27 => 'Склады',                 // 27
        // #MG_DEL_END

        // #MG_DEL (MINI, FREE)
        // 28 => 'Свойства начинаются с',  // 28
        // 29 => 'Сложные характеристики', // 29
      ),
      'MogutaCMSUpdate' => array(
        'Артикул',
        'Цена',
        'Старая цена',
        'Количество',
        'Активность',              
      ),
    );
  }
  
    /**
   * Метод выполняющийся при активации палагина 
   */
  static function activate(){
    USER::AccessOnly('1,4','exit()');
    //self::createDataBase();
  }
  
  /**
   * Метод выполняющийся перед генераццией страницы настроек плагина
   */
  private static function preparePageSettings(){
    USER::AccessOnly('1,4','exit()');
    echo '   
      <link rel="stylesheet" href="'.SITE.'/'.self::$path.'/css/style.css" type="text/css" /> 
      <script type="text/javascript">
        includeJS("'.SITE.'/'.self::$path.'/js/script.js");  
      </script> 
    ';
  }
  

  
  /**
   * Выводит страницу настроек плагина в админке
   */
  static function pageSettingsPlugin(){
    USER::AccessOnly('1,4','exit()');
    
    $lang = self::$lang;
    $pluginName = self::$pluginName;
    
    $options = unserialize(stripslashes(MG::getSetting($pluginName.'-option')));    
    
    $listCategories[0] = "Все категории";
    $arrayCategories = MG::get('category')->getHierarchyCategory(0);
    $lc = MG::get('category')->getTitleCategory($arrayCategories, 0, true);
    foreach ($lc as $key => $value) {
      $listCategories[$key] = $value;
    }
    $data['category'] = $listCategories;
    
    $realDocumentRoot = str_replace(DIRECTORY_SEPARATOR.'mg-plugins'.DIRECTORY_SEPARATOR.PM::getFolderPlugin(__FILE__), '', dirname(__FILE__));  
    $file = $realDocumentRoot.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'catalog.xlsx';
    
    if(file_exists($file)){
      $data['file'] = array(
        'link' => SITE.'/uploads/catalog.xlsx',
        'date' => filemtime($file)
      );
    }
    
    $maskArray = self::$maskArray;
    
    self::preparePageSettings();
    include('pageplugin.php');
    
  }
  
  public static function getMaskArray($type){
    return self::$maskArray[$type];
  }
  
  public static function setCompliance($file, $importType = 'MogutaCMS'){  
    $cpmArray = array();
    $colTitles = mgIOExcel::$maskArray[$importType];
    $titleList = mgIOExcel::getTitleList($file);
    
    foreach($colTitles as $id=>$title){
      if($key = array_search($title, $titleList)){
        $cpmArray[$id] = $key;
      }
    }
    
    MG::setOption(array('option' => self::$pluginName.'-auto'.$importType.'ColComp', 'value' => addslashes(serialize($cpmArray))));
  }
  
  /**
   * 
   * @param string $type - тип соответствия:
   *    last - последнее использовавшееся
   *    auto - созданное автоматически после загрузки файла
   * @return array
   */
  public static function getCompliance($type, $importType = 'MogutaCMS'){
    
    if($type != 'default'){
      $data = MG::getOption(self::$pluginName.'-'.$type.$importType.'ColComp');
      $data = unserialize(stripslashes($data));
    } else {
      foreach(self::$maskArray[$importType] as $id=>$title){
        $data[$id] = $id;
      }
    }
    
    return $data;
  }
  
  public static function getTitleList($file){
    $titleList = array();
    
    include_once 'classes/PHPExcel/IOFactory.php';
    include_once 'classes/chunkReadFilter.php';
    
    $chunkFilter = new chunkReadFilter();    
    $chunkFilter->setRows(0,1);
    
    $objReader = PHPExcel_IOFactory::createReaderForFile($file);    
    $objReader->setReadFilter($chunkFilter);
    $objReader->setReadDataOnly(true);    
    
    $objPHPExcel = $objReader->load($file);
    $sheet = $objPHPExcel->getActiveSheet();
    
    $colNumber = PHPExcel_Cell::columnIndexFromString($sheet->getHighestColumn());
    
    for($i=0; $i<$colNumber; $i++){
      $titleList[$i] = $sheet->getCellByColumnAndRow($i, 1)->getValue();
    }

    unset($objReader); 
    unset($objPHPExcel);   
    return $titleList;
  }   
  
  public static function importFromExcel($file, $schemeType = 'last', $importType = 'MogutaCMS'){
    USER::AccessOnly('1,4','exit()');
    self::$startTime = microtime(true);
    ini_set('memory_limit', '1024M');
    
    if(empty(self::$maxExecTime)){
      self::$maxExecTime = min(30, @ini_get("max_execution_time"));
    }
    
    $phpMemoryLimit = @ini_get('memory_limit');
    $phpMemoryLimit = intval($phpMemoryLimit);
    $data['importSuccess'] = false;
    
    $complianceArray = self::getCompliance($schemeType, $importType);        
    
    include_once 'classes/PHPExcel/IOFactory.php';
    include_once 'classes/chunkReadFilter.php';  
    
    $chunkSize = 100;
    $chunkFilter = new chunkReadFilter();        
    
    $objReader = PHPExcel_IOFactory::createReaderForFile($file);      
        
    $currentPosition = (empty($_POST['nextRow'])) ? 0 : intval($_POST['nextRow']);
    $endFile = false;        
    
    while(!$endFile){
      $chunkFilter->setRows($currentPosition, $chunkSize);
      $objReader->setReadFilter($chunkFilter);
      //$objReader->setReadDataOnly(true);
      $objPHPExcel = $objReader->load($file);
      $sheetData = $objPHPExcel->getActiveSheet()->toArray(null,false,true,false);      
      
      if($currentPosition >= count($sheetData)){
        $endFile = true;
        continue;
      }
      
      $stepItemCount = $currentPosition+$chunkSize;
      
      for($i=$currentPosition; $i<$stepItemCount; $i++){                                
        
        if($i == 0 && $schemeType == 'default'){
          $validFormat = self::validateFormate(
            $sheetData[$i],
            self::$maskArray[$importType],
            $complianceArray
          );       

          if(!$validFormat){
            $data['error_message'] = self::$validError;
            return $data;
          }

          continue;
        }

        $rowData = $sheetData[$i];   
        $cData = array();               

        foreach(self::$maskArray[$importType] as $key=>$title){
          $v = trim($rowData[$complianceArray[$key]]);

          if(!empty($v)){
            // Функция str_replace(' ',' ',...); Заменяет невидимый символ alt+255 на пробелы
            $v = str_replace(' ',' ',$v);  
            $v = str_replace('  ',' ',$v);  //заменяем двойные пробелы на один
            if($importType == 'MogutaCMS' && $k == 4){
              $v = nl2br($v, true);  //заменяем символы перевода строки на тэг <br />
            } else {
              $v = str_replace("\n",' ',$v);
            }
            $cData[$key] = $v;
          } else {
            $cData[$key] = '';
          }

        }  
        
        if($importType == 'MogutaCMS'){
          if(empty($cData[1])){
            $cData[1] = MG::translitIt(str_replace(array('«', '»'), '', $cData[0]), 1);
          }    

          $cData[24] = $rowData[24];  
        }                
        
        if(isset(Import::$fullProduct)){
          Import::$fullProduct = $cData;
        }    
        
        switch($importType){
          case "MogutaCMS": 
            $import = new Import("MogutaCMS");
            
            if(method_exists($import, 'setNotUpdateFields')){
              $import->setNotUpdateFields(array());
            } 
            
            if(!$import->formateMogutaCMS($cData)){

              if(method_exists($import,'getValidError')){
                $data['error_message'] = $import->getValidError();
              } else {
                $data['error_message'] = self::$lang['INVALID_FILE'];
              }

              return $data;
            }         
            break;
          case "MogutaCMSUpdate":  
            $import = new Import("MogutaCMSUpdate");
            
            if(method_exists($import, 'setNotUpdateFields')){
              $import->setNotUpdateFields(array());
            }     
            
            if(method_exists($import, 'setNotUpdateFields')){
              $import->setTypeCatalog('MogutaCMSUpdate');
            }                        
            
            if(!$import->formateMogutaCMSUpdate($cData)){
              if(method_exists($import,'getValidError')){
                $data['error_message'] = $import->getValidError();
              } else {
                $data['error_message'] = self::$lang['INVALID_FILE'];
              }
              
              return $data;
            } 
            
            break;

          default:
            $import = new Import("MogutaCMS");
            
            if(!$import->formateMogutaCMS($cData)){              

              if(method_exists($import,'getValidError')){
                $data['error_message'] = $import->getValidError();
              } else {
                $data['error_message'] = self::$lang['INVALID_FILE'];
              }

              return $data;
            }
        }    
        
        unset($cData);
        unset($rowData);
        $currentPosition = $i;
        $execTime = microtime(true) - self::$startTime;
        $phpMemoryUsage = memory_get_usage()/1024/1024;
        
        if($execTime + 10 > self::$maxExecTime){
          unset($objReader);
          //$percent = $currentPosition * 100 / $rowInFile;
          $currentPosition++;
          return array('importSuccess'=>false, 'status'=>'success', 'nextRow'=>$currentPosition);
        }
      }
      
      if($phpMemoryUsage > $phpMemoryLimit/2){
        unset($objReader);
        //$percent = $currentPosition * 100 / $rowInFile;
        //$currentPosition++;
        return array('importSuccess'=>false, 'status'=>'success', 'nextRow'=>$currentPosition);
      }

      //unset($objReader); 
      unset($objPHPExcel);
      unset($sheetData);
    }        
    
    $data['importSuccess'] = true;
    return $data;
  }
  
  /**
   * Проверка валидности файла
   */
  private static function validateFormate($data, $maskArray, $complianceArray) {
    $result = true;   
    
    // проверим на соответствие заголовки столбцов  
    foreach($maskArray as $k => $v){  
      
      if(!empty($data[$complianceArray[$k]])){
        $title = $data[$complianceArray[$k]];
        // Функция str_replace(' ',' ',...); Заменяет невидимый символ alt+255 на пробелы
        $title = str_replace(' ',' ',$title);
        $title = trim(str_replace("\n",'',$title));
        
        if($title != $v) {          
          $result = false;        
          self::$validError = 'Столбец "'.$maskArray[$k].'" не обнаружен!';          
          break;
        }              
      } else {
        $result = false;        
        self::$validError = 'Столбец "'.$maskArray[$k].'" не обнаружен!';
        break;
      }
    }
    
    return $result;    
  }
  
  private function getChildCategoryIds($id = 0){
    $cIds = MG::get('category')->getChildCategoryIds($id);
    
    foreach($cIds as $cId){
      $child = self::getChildCategoryIds($cId);
      $cIds = array_merge($cIds, $child);
    }
    
    return $cIds;
  }
  
  public static function exportToExcel($nextRow = 2, $nextPage = 1, $arParams = array(), $catIds = ''){
    USER::AccessOnly('1,4','exit()');
    
    self::$startTime = microtime(true);
    $userFilter = '';
    $prodCount = 0;
    
    $realDocumentRoot = str_replace(DIRECTORY_SEPARATOR.'mg-plugins'.DIRECTORY_SEPARATOR.PM::getFolderPlugin(__FILE__), '', dirname(__FILE__));  
    $file = $realDocumentRoot.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'catalog.xlsx';
    
    $data = array(
      'exportSuccess' => false,
    );
    //Массив заголовков столбцов для выгрузки
    $colTitle = array(
      'ID товара',              // 0
      'Артикул',                // 1
      'Категория',              // 2
      'URL категории',          // 3
      'Товар',                  // 4
      'Вариант',                // 5
      'Краткое описание',       // 6
      'Описание',               // 7
      'Цена',                   // 8
      'Старая цена',            // 9
      'URL товара',             // 10
      'Изображение',            // 11
      'Количество',             // 12
      'Активность',             // 13
      'Заголовок [SEO]',        // 14
      'Ключевые слова [SEO]',   // 15
      'Описание [SEO]',         // 16
      'Рекомендуемый',          // 17
      'Новый',                  // 18
      'Сортировка',             // 19
      'Вес',                    // 20
      'Связанные артикулы',     // 21
      'Смежные категории',      // 22
      'Ссылка на товар',        // 23
      'Валюта',                 // 24
      'Единицы измерения',      // 25
    );

    $propNameToCsv = Property::getEasyPropNameToCsv();

    // добавляем заголовки для оптовых цен
    $res = DB::query('SELECT DISTINCT count FROM '.PREFIX.'wholesales_sys');
    while ($row = DB::fetchAssoc($res)) {
      $_SESSION['export']['wholeColumns'][] = $row['count'];
      $colTitle[] = 'Количество от '.$row['count'].' [оптовая цена]';
    }

    if(MG::enabledStorage()) {
      unset($_SESSION['export']['storageColumns']);
      $storages = unserialize(stripcslashes(MG::getSetting('storages')));
      foreach ($storages as $item) {
        $colTitle[] = $item['name'].' [склад='.$item['id'].']';
        $_SESSION['export']['storageColumns'][] = $item['id'];
      } 
    }
    foreach ($propNameToCsv as $item) {
      $colTitle[] = $item;
    }
    $colTitle[] = "Сложные характеристики";

    include_once 'classes/PHPExcel.php';
    
    if($nextPage > 1 && file_exists($file)){
      $objPHPExcel = PHPExcel_IOFactory::load($file);
    } else {
      $objPHPExcel = new PHPExcel();
    }
        
    $objPHPExcel->getProperties()->setCreator(SITE)
                                 ->setTitle(SITE." catalog");
    $objPHPExcel->getActiveSheet()->setTitle('Страница 1');
    $objPHPExcel->getActiveSheet()->fromArray($colTitle);
    
    $product = new Models_Product();
    $catalog = new Models_Catalog();
    
    if(!empty($arParams['only_active'])){
      $userFilter .= ' p.activity = 1 AND ';
    }
    
    if(!empty($arParams['only_in_count'])){
      $userFilter .= ' p.count <> 0 AND ';
    }
    
    if(!empty($catIds)){
      $arCatIds = explode(',', $catIds);
      
      foreach($arCatIds as $catId){
        $chlidIds = self::getChildCategoryIds($catId);
        $arCatIds = array_merge($arCatIds, $chlidIds);
      }
      
      $catIds = implode(',', $arCatIds);
      
      $userFilter .= ' p.cat_id IN ('.DB::quote($catIds,1).') AND ';
    }    
    
    if(!empty($userFilter)){      
      $userFilter = substr($userFilter, 0, -4);
      $dbRes = DB::query('SELECT count(p.id) as count FROM '.PREFIX.'product p WHERE '.$userFilter);
      if($res = DB::fetchAssoc($dbRes)){
        $prodCount = $res['count'];
      }
    }   
    
    if(empty($prodCount)){
      $maxCountPage = ceil($product->getProductsCount() / 100);    
    } else {
      $maxCountPage = ceil($prodCount / 100);    
    }
    
    $cell = $nextRow;
    
    for($page = $nextPage; $page <= $maxCountPage; $page++){
      
      URL::setQueryParametr("page", $page);
      
      if(empty($userFilter)){
        $catalog->getList(100, true);
      } else {
        $catalog->getListByUserFilter(100, $userFilter);
      }
      
      foreach ($catalog->products as $row) {
        $parent = $row['category_url'];

        // Подставляем всесто URL названия разделов.
        $resultPath = '';
        $resultPathUrl = '';
        while ($parent) {
          $url = URL::parsePageUrl($parent);
          $parent = URL::parseParentUrl($parent);
          $parent = $parent != '/' ? $parent : '';
          $alreadyParentCat = MG::get('category')->getCategoryByUrl(
            $url, $parent
          );

          $resultPath = $alreadyParentCat['title'] . '/' . $resultPath;
          $resultPathUrl = $alreadyParentCat['url'] . '/' . $resultPathUrl;
        }

        $resultPath = trim($resultPath, '/');
        $resultPathUrl = trim($resultPathUrl, '/');

        $variants = $product->getVariants($row['id']);

        if (!empty($variants)) {
          foreach ($variants as $key => $variant) {
            foreach ($variant as $k => $v) {
              if( $k != 'sort' && $k != 'id'){
                $row[$k] = $v;
              }
            }
            
            $row['image'] = $variant['image'];
            $row['category_url'] = $resultPath;
            $row['category_full_url'] = $resultPathUrl;
            $row['real_price'] = $row['price'];

            $objPHPExcel->getActiveSheet()->fromArray(self::addRowToSheet($row, 1), '', 'A'.$cell);
            // $objPHPExcel->setActiveSheetIndex()->setCellValueExplicitByColumnAndRow(8, $cell, $row['code'], PHPExcel_Cell_DataType::TYPE_STRING);
            $cell++;
          }
        } else {
          $row['category_url'] = $resultPath;
          $row['category_full_url'] = $resultPathUrl;

          $objPHPExcel->getActiveSheet()->fromArray(self::addRowToSheet($row), '', 'A'.$cell);
          // $objPHPExcel->setActiveSheetIndex()->setCellValueExplicitByColumnAndRow(8, $cell, $row['code'], PHPExcel_Cell_DataType::TYPE_STRING);
          $cell++;
        }

      }
      
      $execTime = microtime(true) - self::$startTime;
      
      if($execTime + 15 > self::$maxExecTime){
        $objPHPExcel->setActiveSheetIndex(0);            

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save($file);
        
        $percent = $page * 100 / $maxCountPage;
        
        return array('exportSuccess'=>false, 'percent'=>round($percent), 'status'=>'success', 'nextRow'=>$cell, 'nextStep'=>$page+1);
      }
      
    }
    
    $objPHPExcel->setActiveSheetIndex(0);       
    
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->save($file);
    
    $data['exportSuccess'] = true;
    $data['file'] = SITE.'/uploads/catalog.xlsx';
    return $data;
  }
  
  public function addRowToSheet($row, $variant = false){

    $row['category_url'] = htmlspecialchars_decode($row['category_url']);
    $row['category_full_url'] = $row['category_full_url'];
    $row['title'] = htmlspecialchars_decode($row['title']);
    
    if($variant){
      $var_image = '[:param:][src='.$row['image'].']';
      $row['title_variant'] .= $var_image;
    }
    
    $row['title_variant'] = htmlspecialchars_decode($row['title_variant']);
        
    $row['description'] = $row['description'];
    $row['price'] = MG::numberDeFormat($row['real_price']);
    $row['price'] = str_replace(".", "," ,$row['price']);    
    $row['image_url'] = '';
    
    if(!empty($row['images_product'])){
      foreach ($row['images_product'] as $key => $url ) {
        $param = '';
        
        if (!empty($row['images_alt'][$key])||!empty($row['images_title'][$key])) {
          $param = '[:param:][alt='.(!empty($row['images_alt'][$key]) ? $row['images_alt'][$key] : '').'][title='.(!empty($row['images_title'][$key]) ? $row['images_title'][$key] : '').']';
        }
        
        $arUrl = explode('/', $url);
        $row['image_url'] .= $arUrl[count($arUrl)-1].$param.'|';
      }
      $row['image_url'] = substr($row['image_url'], 0, -1);
    }
    
    $row['meta_title'] = htmlspecialchars_decode($row['meta_title']);
    $row['meta_keywords'] = htmlspecialchars_decode($row['meta_keywords']);
    $row['meta_desc'] = htmlspecialchars_decode($row['meta_desc']);
    // $row['old_price'] = MG::numberDeFormat($row['real_old_price']);
    $row['old_price'] = ($row['old_price']!='"0"')?str_replace(".", "," ,$row['old_price']):'""';    
    $row['description'] = str_replace("\r", "", $row['description']);
    $row['description'] = str_replace("\n", "", $row['description']);
    $row['meta_desc'] = str_replace("\r", "", $row['meta_desc']);
    $row['meta_desc'] = str_replace("\n", "", $row['meta_desc']);    
    $row['weight'] = str_replace(".", "," ,$row['weight']);
    //получаем строку со связанными продуктами
    $row['property'] = Property::getHardPropToCsv($row['id']);
    $row['property'] = str_replace("\r", "", $row['property']);
    $row['property'] = str_replace("\n", "", $row['property']);
    $row['property'] = str_replace("\"", "\"\"",$row['property']); 

    // mg::loger($row);  

    if(MG::enabledStorage()) {
      foreach ($_SESSION['export']['storageColumns'] as $item) {
        $variant = 0;
        foreach ($row['variants'] as $var) {
          $tmp = explode('[', $row['title_variant']);
          $tmp = $tmp[0];
          if($var['title_variant'] == $tmp) {
            $variant = $var['id'];
          }
        }
        $res = DB::query('SELECT count, storage FROM '.PREFIX.'product_on_storage WHERE 
          product_id = '.DB::quoteInt($row['id']).' AND variant_id = '.DB::quoteInt($variant).'
          AND storage = '.DB::quote($item));
        while ($subRow = DB::fetchAssoc($res)) {
          $result = $subRow['count'];
        }
        if(empty($result)) $result = 0;
        $row['storages'][] = $result;
      }
    }

    $row['wholesales'] = MG::getWholesalesToCSV($row['id'], $row);
    $row['easy_prop'] = Property::getEasyPropToCsv($row['id'], $row['color'], $row['size']);

    if ($variant) { 
      $var_image = '[:param:][src='.$row['image'].']';
      $row['title_variant'] .= $var_image;
      $variantsCol = str_replace("\"", "\"\"", htmlspecialchars_decode($row['title_variant']));
    } else {
      $variantsCol = "";
    }

    $sortRow = array(
      $row['id'],
      $row['code'],
      $row['category_url'],
      $row['category_full_url'],
      $row['title'],
      $row['title_variant'],
      $row['short_description'],
      $row['description'],
      $row['price'],
      $row['old_price'],
      $row['url'],
      $row['image_url'],
      $row['count'],
      $row['activity'],
      $row['meta_title'],
      $row['meta_keywords'],
      $row['meta_desc'],
      $row['recommend'],
      $row['new'],
      $row['sort'],
      $row['weight'],
      $row['related'],
      $row['inside_cat'],
      $row['link_electro'],
      $row['currency_iso'],
      $row['category_unit'],
    );

    foreach ($row['wholesales'] as $item) {
      $sortRow[] = $item;
    }
    foreach ($row['storages'] as $item) {
      $sortRow[] = $item;
    }

    foreach ($row['easy_prop'] as $item) {
      $sortRow[] = $item;
    }
    $sortRow[] = $row['property'];    
    
    return $sortRow;
  }
  
}