<?php

/**
 * Класс Pactioner наследник стандарного Actioner
 * Предназначен для выполнения действий,  AJAX запросов плагина 
 *
 * @author Avdeev Mark <mark-avdeev@mail.ru>
 */
class Pactioner extends Actioner {

  private $pluginName = 'buy-click';

  /**
   * Сохраняет  опции плагина
   * @return boolean
   */
  public function saveBaseOption() {
    //доступно только модераторам и админам.
    USER::AccessOnly('1,4', 'exit()');

    $this->messageSucces = $this->lang['SAVE_BASE'];
    $this->messageError = $this->lang['NOT_SAVE_BASE'];
    if (!empty($_POST['data'])) {
      MG::setOption(array('option' => 'buyClickOption', 'value' => addslashes(serialize($_POST['data']))));
    }
    return true;
  }

  /**
   * Проверяет правильно ли введены email, телефон и капча 
   * @return boolean
   */
  public function sendOrderBuyClick() {
    $this->messageSucces = $this->lang['ENTITY_SAVE'];
    $this->messageError = $this->lang['ENTITY_DEL_NOT'];

    $option = MG::getSetting('buyClickOption');
    $option = stripslashes($option);
    $options = unserialize($option);
    if ($options['email'] == 'true') {
     if (!preg_match('/^[-._a-zA-Z0-9]+@(?:[a-zA-Z0-9][-a-zA-Z0-9]{0,61}+\.)+[a-zA-Z]{2,6}$/', $_POST['email'])) {
        $error = "<span class='error'>E-mail введен некорректно!</span>";
        $this->data['msg'] = $error;
        return false;
      }
    }
    if ($options['phone'] == 'true') {
      if (empty($_POST['phone'])) {
        $error = "<span class='error'>Введите номер телефона!</span>";
        $this->data['msg'] = $error;
        return false;
      }
    }
    if ($options['capcha'] == 'true') {
      if (method_exists('MG', 'checkReCaptcha') && MG::getSetting('useReCaptcha') == 'true' && MG::getSetting('reCaptchaSecret') && MG::getSetting('reCaptchaKey')) {
        $_POST['g-recaptcha-response'] = $_POST['capcha'];
        if (!MG::checkReCaptcha()) {
          $error = "<span class='error'>reCAPTCHA не пройдена!</span>";
          $this->data['msg'] = $error;
          return false;
        }
        unset($_POST['g-recaptcha-response']);
      }
      else{
        if (strtolower($_POST['capcha']) != strtolower($_SESSION['capcha'])) {
          $error = "<span class='error'>Текст с картинки введен неверно!</span>";
          $this->data['msg'] = $error;
          return false;
        }
      }
    }
    unset($_POST['capcha']);
    unset($_POST['pluginHandler']);
    $_SESSION['infoClient'] = $_POST;
    $this->data['infoclient'] = $_POST;
    return true;
  }
  /**
   *  Функция создания модального окна для формы заказа
   */
  public function buildOrderForm() {
     
    $option = MG::getSetting('buyClickOption');
    $option = stripslashes($option);
    $options = unserialize($option);
    $productModel = new Models_Product();
    $prodData = $productModel->getProduct($_POST['id']);
    $var_name = '';

    if ($_POST['var_id']) {
      $variants = $productModel->getVariants($_POST['id']);
      if (!empty($variants[$_POST['var_id']])) {
        $var_name = $variants[$_POST['var_id']]['title_variant'];
      }
    }

    $user = array(
      'name' => isset($_SESSION['user']) ? $_SESSION['user']->name : '',
      'phone' => isset($_SESSION['user']) ? $_SESSION['user']->phone :'',
      'email' => isset($_SESSION['user']) ? $_SESSION['user']->email :'',
      'address' => isset($_SESSION['user']) ? $_SESSION['user']->address :'',
      );

    $options['recaptcha'] = 'false';
    if (method_exists('MG', 'checkReCaptcha') && MG::getSetting('useReCaptcha') == 'true' && MG::getSetting('reCaptchaSecret') && MG::getSetting('reCaptchaKey')) {
      $options['recaptcha'] = 'true';
      $this->data['recaptchahtml'] = MG::printReCaptcha(false);
    }



    $this->data['options'] = $options;
    $this->data['product_image'] = $prodData['image_url'];
    $this->data['product_title'] = $prodData['title'];
    $this->data['variant_title'] = $var_name;
    $this->data['user'] = $user;
    return true;
    
  }

}
