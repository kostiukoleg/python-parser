<?php

/*
  Plugin Name: SMS оповещения расширенная версия
  Description: Позволяет отправлять бесплатные СМС оповещения администратору сайта о новых заказах, а также любых других событиях. Возможна отправка SMS покупателям (платно). Шорт код [sms]Текст ообщения[/sms]. Внимание! Только для версий более 6.9.0: Отправляет сообщение пользователю о смене статуса заказа.
  Author: Могута
  Version: 1.0.5
 */

new SMSAlertsExt;

class SMSAlertsExt {
  private static $pluginName = ''; // название плагина (соответствует названию папки)
  private static $path = ''; //путь до файлов плагина 
  public function __construct() {
    mgActivateThisPlugin(__FILE__, array(__CLASS__, 'createDateBase'));
    mgAddAction(__FILE__, array(__CLASS__, 'pageSettingsPlugin'));
    mgAddAction('models_Order_sendMailOfPayed', array(__CLASS__, 'informUserSMSPaid'), 1);
    mgAddAction('models_Order_sendStatusToEmail', array(__CLASS__, 'informUserSMS'), 1);

    if (!URL::isSection('mg-admin')) {
      mgAddShortcode('sms', array(__CLASS__, 'sendsms'));
    }
    self::$pluginName = PM::getFolderPlugin(__FILE__);
    self::$path = PLUGIN_DIR.self::$pluginName;
  }

  /**
   * Создает таблицу настроек в БД при активации плагина
   */
  static function createDateBase() {
    DB::query("
      CREATE TABLE IF NOT EXISTS `".PREFIX."sms_setting` (
       `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Порядковый номер настройки',
       `option` varchar(255) NOT NULL COMMENT 'Имя опции',
       `value` longtext NOT NULL COMMENT 'Значение опции',
       `name` varchar(255) NOT NULL COMMENT 'Название опции',
       PRIMARY KEY (`id`)
      ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 COMMENT='Настройки плагина SMS' AUTO_INCREMENT=1 ");

    DB::query("INSERT IGNORE INTO `".PREFIX."sms_setting` (`id`, `option`, `value`, `name`) VALUES
      (1, 'nomer', '79991234567', 'NOMER'),
      (2, 'token', 'api_id', 'api_id'),
      (3, 'inform', 'false', 'Отправлять смс пользователю при изменении статуса заказа'),
      (4, 'log', 'false', 'Сохранять отчет в файле log_sms_send_дд.мм.г по отправленным смс')
      ");
  }

  /**
   * Получает текущий номер телефона
   */
  static function getNomer() {
    $nomer = "";
    $res = DB::query(" SELECT `value` FROM `".PREFIX."sms_setting` WHERE `id`= 1");
    if ($row = DB::fetchAssoc($res)) {
      $nomer = $row['value'];
    }
    return $nomer;
  }

  /**
   * Получает текущий токен
   */
  static function getToken() {
    $token = "";
    $res = DB::query(" SELECT `value` FROM `".PREFIX."sms_setting` WHERE `id`= 2");
    if ($row = DB::fetchAssoc($res)) {
      $token = $row['value'];
    }
    return $token;
  }
    /**
   * Получает значения оповещения пользователей
   */
  static function getSettings($option=false) {
    $where = '';
    $setting = array();
    if ($option) {
      $where = 'WHERE `option` = '.DB::quote($option);
    }
    $res = DB::query("SELECT `value`, `option` FROM `".PREFIX."sms_setting` ".$where);
    while ($row = DB::fetchAssoc($res)) {
      $setting[$row['option']] = $row['value'];
    }
    if ($option) {
      return !empty($setting[$option]) ? $setting[$option] : false;
    }
    return $setting;
  }

  /**
   * Страница настроек плагина
   */
  static function pageSettingsPlugin() {
    echo '   
      <link rel="stylesheet" href="'.SITE.'/'.self::$path.'/css/pageSettings.css" type="text/css" />  
    ';
    if (isset($_POST['nomer']) && isset($_POST['token'])) {
      $nomer = $_POST['nomer'];
      $token = $_POST['token'];
      $inform = $_POST['inform'];
      $log = $_POST['log'];
      DB::query("
        UPDATE `".PREFIX."sms_setting`
        SET `value` = ".DB::quote($nomer)."
        WHERE id=1
      ");
      DB::query("
        UPDATE `".PREFIX."sms_setting`
        SET `value` = ".DB::quote($token)."
        WHERE id=2
      ");
      DB::query("
        UPDATE `".PREFIX."sms_setting`
        SET `value` = ".DB::quote($inform)."
        WHERE id=3
      ");
      DB::query("
        UPDATE `".PREFIX."sms_setting`
        SET `value` = ".DB::quote($log)."
        WHERE id=4
      ");
      echo "<br/><div class=\"sms-setting-update\">Установлен номер для SMS информирования: ".$nomer.". Токен: ".$token."</div>";
    } else {      
      $settings = self::getSettings();
      $nomer = $settings['nomer'];
      $token = $settings['token'];
      $inform = $settings['inform'];
      $log = $settings['log'];
    }

    echo '<div class="sms-padding">
      <div class="sms-setting-block">  
        <form methot="post" action="">
          Номер телефона: <input class="nomer" type="text" name="nomer" value='.$nomer.'><br/>
          Токен: <input class="token" type="text" name="token" value='.$token.'><br/>
          <label> 
          <input type="checkbox" name="inform" value='.$inform.' '.($inform=='true' ? 'checked' : '').'>
          <span> информировать пользователей при смене статруса заказа</span>  
          </lable><br/>
          <label> 
          <input type="checkbox" name="log" value='.$log.' '.($log=='true' ? 'checked' : '').'>
          <span>сохранять отчет в файле log_sms_send_дд.мм.г по отправленным смс</span>  
          </lable><br/>
          <input type="hidden" name="pluginTitle" value="'.$_POST['pluginTitle'].'"/>
          <input type="submit" class="button success" value="&#xf0c7; Применить"/>
        </form>
      </div>
      <br/>
      <div class="sms-help-block">
        <i><b>Инструкция:</b></i><br/>
        <br/>
        Для получения токена пройдите по ссылке <a href="http://mogutacms.sms.ru/?panel=register">Регистрация</a>;<br/>
        Пройдите бесплатную регистрацию, подтвердите номер телефона кодом из SMS сообщения (бесплатно);<br/>
        Откройте <a href="http://mogutacms.sms.ru/?panel=my">Панель</a> и скопируйте Ваш <b>api_id</b> - это и есть токен.<br/>
        <br/>        
        <b>Для того, чтобы получать SMS уведомления о новом заказе</b>, скопируйте шорткод <b>[sms content="Принята заявка #&lt?php echo $data[\'orderNumber\']; ?&gt Сумма &lt?php echo $data[\'summ\']; ?&gt &lt?php echo $data[\'currency\']; ?&gt"][/sms]</b>, перейдите в раздел "Настройки" - "Шаблоны" - "Шаблоны страниц" и откройте файл order.php. Вставьте шорткод после <b>&lt?php echo lang("orderPaymentForm3"); ?&gt</b>, или текста "На Ваш электронный адрес выслано письмо для подтверждения заказа."(Если в вашем шаблон не поддерживает мультиязычность) и сохраните. <a target="_blank" href="http://joxi.ru/eAOM569FxvvpYm.png">Подробнее</a>
        <br/>        
        <br/>      
          
        Для отправки SMS достаточно добавить в нужном месте шорт код <b>[sms]Текст сообщения[/sms]</b>.<br/>
        По-умолчанию сообщение будет отправлено на Ваш номер.<br/>
        Чтобы отправить смс на другой номер укажите его в шорткоде. Например так <b>[sms nomer="79990001122"]Hellow World![/sms]</b>. Перед отправкой SMS на другие номера ознакомьтесь с тарифами и правилами на сайте <a href="http://mogutacms.sms.ru/?panel=settings&subpanel=plan">sms.ru</a>.<br/>
        Бесплатная отправка SMS возможна только на свой номер!<br/>
        <br/>
        Полный формат шорт кода: <b>[sms nomer="79180001122" token="a123b45c-d6e7-8h90-g123-k45m6no78901"]Текст сообщения[/sms]</b>.<br/>
        Обязателен только текст сообщения. Номер и токен можно не указывать (они должны быть указаны в настройках плагина)<br/>
        <br/>
      </div></div>
	';
  }

  /**
   * Отправляет СМС через сервис sms.ru
   */
  static function sendsms_smsru($nomer, $msg, $token) {
    $body = file_get_contents("http://sms.ru/sms/send?api_id=$token&to=$nomer&text=".urlencode($msg)."&partner_id=120860");
    if (self::getSettings('log')) {
      $fileName = 'log_sms_send.txt';
      $string = date('d.m.Y H:i:s').' =>'.$body.'. Сообщение:'.$msg.'. Получатель: '.$nomer."\r\n";
      $f = fopen($fileName, 'a+');
      fwrite($f, $string);
      fclose($f);
    }
    return $body;
  }

  /**
   * Обработчик шотркода вида [sms nomer="79123456789" token="abcde123-qwerty-9876"]Текст сообщения[/sms].
   * Отправляет СМС на указанный номер. Если номер не указан, использует номер по-умолчанию.
   * Если токен не указан, использует токен по-умолчанию.
   * Если не указан текст СМС, то сообщение отправлено не будет
   */
  static function sendsms($arg) {
    $msg = $arg['content'];
    if (isset($arg['content'])) {
      $msg = $arg['content'];

      if (isset($arg['nomer'])) {
        $nomer = $arg['nomer'];
      } else {
        $nomer = self::getNomer();
      };

      if (isset($arg['token'])) {
        $token = $arg['token'];
      } else {
        $token = self::getToken();
      };

      $res = self::sendsms_smsru($nomer, $msg, $token);
    }
    return "";
  }
  /*
   * функция отправляет сообщение пользователю, если был изменен статус заказа и 
   * был отмечен чекбокс об информировании покупателя в админке
   */
  static function informUserSMS($args){
    $inform = self::getSettings('inform');
    if ($inform == true) {    
      if ($args['result']) {
        $data = $args['result'];  
        $phone = preg_replace('/[^0-9]/', '', $data['orderInfo']['phone']);    
        if ($phone) {          
          ob_start();
          include ('layout_status_order.php');
          $html = ob_get_contents();
          ob_clean();
          $msg = trim($html);
          $token = self::getToken();
          self::sendsms_smsru($phone, $msg, $token);          
        }
      }
    }
    return $args['result'];       
  }
   /*
   * функция отправляет сообщение пользователю, при оплате заказа
   */
  static function informUserSMSPaid($args){
    $inform = self::getSettings('inform');    
    if ($inform == true) { 
      $lang = mg::get('lang');
      $data['orderInfo']['number'] = $args['args'][0];
      if (class_exists('statusOrder')) {
      $dbQuery = DB::query('SELECT `status` FROM `'.PREFIX.'mg-status-order` '
        . 'WHERE `id_status`=2');
      if ($dbRes = DB::fetchArray($dbQuery)) {
        $data['statusName'] = $dbRes['status'];
      }
    } else {
      $lang = MG::get('lang');
      $data['statusName'] = $lang['PAID'];
    }
    $dbQuery = DB::query('SELECT `phone` FROM `'.PREFIX.'order` WHERE `number`='.DB::quote($data['orderInfo']['number']));
    if ($dbRes = DB::fetchArray($dbQuery)) {
      $phone = preg_replace('/[^0-9]/', '', $dbRes['phone']);    
    }   
        if ($phone) {          
          ob_start();
          include ('layout_status_order.php');
          $html = ob_get_contents();
          ob_clean();
          $msg = trim($html);
          $token = self::getToken();
          self::sendsms_smsru($phone, $msg, $token);          
        }
      }    
    return $args['result'];       
  }
}

