<?php
/**
 *  Файл представления Registration - выводит сгенерированную движком информацию на странице регистрации нового пользователя.
 *  В этом файле доступны следующие данные:
 *   <code>     
 *    $data['error'] => Сообщение об ошибке.
 *    $data['message'] => Информационное сообщение.
 *    $data['form'] =>  Отображение формы,
 *    $data['meta_title'] => 'Значение meta тега для страницы '
 *    $data['meta_keywords'] => 'Значение meta_keywords тега для страницы '
 *    $data['meta_desc'] => 'Значение meta_desc тега для страницы '
 *   </code>
 *   
 *   Получить подробную информацию о каждом элементе массива $data, можно вставив следующую строку кода в верстку файла.
 *   <code>     
 *    <php viewData($data['message']); ?>  
 *   </code>
 * 
 *   Вывести содержание элементов массива $data, можно вставив следующую строку кода в верстку файла.
 *   <code>     
 *    <php echo $data['message']; ?>  
 *   </code>
 * 
 *   <b>Внимание!</b> Файл предназначен только для форматированного вывода данных на страницу магазина. Категорически не рекомендуется выполнять в нем запросы к БД сайта или реализовывать сложную программную логику логику.
 *   @author Авдеев Марк <mark-avdeev@mail.ru>
 *   @package moguta.cms
 *   @subpackage Views
 */
// Установка значений в метатеги title, keywords, description.
mgSEO($data);
?>
<script src="<?php echo SITE ?>/mg-core/script/jquery.maskedinput.min.js"></script>
<div class="container">
  <div class="row">
      <div class="col-sm-6">
        <h1 class="new-products-title">Регистрация пользователя</h1>
          <?php if($data['message']): ?>
            <span class="mg-success"><?php echo $data['message'] ?></span>
          <?php endif; ?>
          <?php if($data['error']): ?>
          <span class="msgError"><?php echo $data['error'] ?></span>
          <?php endif; ?>
        <?php if($data['form']): ?>
        <div class="create-user-account-form">
              <div class="title">Новый пользователь</div>
            <p class="custom-text">Заполните форму ниже, чтобы получить дополнительные возможности в нашем интерент-магазине.</p>
            <form action="<?php echo SITE ?>/registration" method="POST">
              <ul class="form-list">
                <li>Email:<span class="red-star">*</span></li>
                <li><input type = "text" name = "email" value = "<?php echo $_POST['email'] ?>"></li>
                <li>Пароль:<span class="red-star">*</span></li>
                <li><input type="password" name="pass"></li>
                <li>Подтвердите пароль:<span class="red-star">*</span></li>
                <li><input type="password" name="pass2"></li>
                <li>Имя:</li>
                <li><input type="text" name="name" value = "<?php echo $_POST['name'] ?>"></li>
                <li><input type="hidden" name="ip" value = "<?php echo $_SERVER['REMOTE_ADDR'] ?>"></li>      
                <?php if(MG::getSetting('useCaptcha')=="true"){ ?>
                  <li>Введите текст с картинки:</li>
                  <li><img alt="Cap" style="margin-top: 5px; border: 1px solid gray; background: url('<?php echo PATH_TEMPLATE ?>/images/cap.png');" src = "captcha.html" width="140" height="36"></li>
                  <li><input type="text" name="capcha" class="captcha"></li>
                <?php } ?>
              </ul>
              <button type = "submit" name="registration" class="register-btn default-btn">Зарегистрироваться</button>
            </form>
            <div class="clear">&nbsp;</div>
        </div>
        <?php endif ?>
      </div>
      <div class="col-sm-6">
      <h1 class="new-products-title">Авторизация пользователя</h1>
      <?php echo!empty($data['msgError'])?$data['msgError']:'' ?>
      <div class="user-login">
        <div class="title">Зарегистрированный пользователь</div>
        <p class="custom-text">Если Вы уже зарегистрированы у нас в интернет-магазине, пожалуйста авторизуйтесь.</p>
        <form action="<?php echo SITE ?>/enter" method="POST">
          <ul class="form-list">
            <li>Email:<span class="red-star">*</span></li>
            <li><input type = "text" name = "email" value = "<?php echo!empty($_POST['email'])?$_POST['email']:'' ?>"></li>
            <li>Пароль:<span class="red-star">*</span></li>
            <li><input type="password" name="pass"></li>
            <?php echo!empty($data['checkCapcha'])?$data['checkCapcha']:'' ?>
            <?php if (!empty($_REQUEST['location'])) :?>
            <input type="hidden" name="location" value="<?php echo $_REQUEST['location'];?>" />
            <?php endif;?>
          </ul>
          <a href="<?php echo SITE ?>/forgotpass" class="forgot-link">Забыли пароль?</a>
          <button type="submit" class="enter-btn default-btn">Войти</button>
        </form>
      </div>
      </div>
  </div>
</div>