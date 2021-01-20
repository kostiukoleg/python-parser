<?php 

/**
 * Модель: Feedback2
 *
 * Класс Models_Feedback реализует логику взаимодействия с формой обратной связи.
 * - Проверяет корректность ввода данных;
 * - Отправляет сообщения на электронные адреса пользователя и администраторов.
 *
 * @author Авдеев Марк <mark-avdeev@mail.ru>
 * @package moguta.cms
 * @subpackage Model
 */
class Models_Feedback2 {

  // Электронный адрес пользователя.
  private $email2;
  // Фамилия имя пользователя.
  private $fio2;
  // Сообщение пользователя.
  private $text2;
  //Телефон пользователя
  private $phone;

  /**
   * Проверяет корректность ввода данных.
   *
   * @param array $arrayData массив с данными введенными пользователем.
   * @return bool|string $error сообщение с ошибкой в случае некорректных данных.
   */
  public function isValidData($arrayData) {
   
    $result = false;
    if (!preg_match('/^[-._a-zA-Z0-9]+@(?:[a-zA-Z0-9][-a-zA-Z0-9]{0,61}+\.)+[a-zA-Z]{2,6}$/', $arrayData['email2'])) {
      $error = '<span class="error-email">E-mail не существует!</span>';
    } elseif (!trim($arrayData['text2'])) {
      $error = 'Введите текст сообщения!';
    }

    if(MG::getSetting('useCaptcha')=="true"){
      if (strtolower($arrayData['capcha']) != strtolower($_SESSION['capcha'])) {
        $error .= "<span class='error-captcha-text'>Текст с картинки введен неверно!</span>";
      }    
    }

    // Если нет ошибок, то заносит информацию в поля класса.
    if ($error) {
      $result = $error;
    } else {

      $this->fio2 = trim($arrayData['fio2']);
      $this->phone = trim($arrayData['phone']);
      $this->email2 = trim($arrayData['email2']);
      $this->text2 = trim($arrayData['text2']);
      $result = false;
    }

    $args = func_get_args();

    return MG::createHook(__CLASS__."_".__FUNCTION__, $result, $args);
  }
  
  /**
   * Получает сообщение из закрытых полей класса.
   * @return type
   */
  public function getMessage() {
    return $this->fio.": ".$this->text2;
  }

   /**
   * Получает email из закрытых полей класса.
   * @return type
   */
  public function getEmail() {
    return $this->email2;
  }

  /**
   * Получает имя отправителя из закрытых полей класса.
   * @return type
   */
  public function getFio() {
    return $this->fio2;
  }
  /**
   * Получает телефона отправителя из закрытых полей класса.
   * @return type
   */
  public function getPhone() {
    return $this->phone;
  }
}