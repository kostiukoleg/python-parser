<?php
/**
 *  Файл представления Catalog - выводит сгенерированную движком информацию на странице сайта с каталогом товаров.
 *  В этом  файле доступны следующие данные:
 *   <code>
 *    $data['items'] => Массив товаров
 *    $data['titeCategory'] => Название открытой категории
 *    $data['cat_desc'] => Описание открытой категории
 *    $data['pager'] => html верстка  для навигации страниц
 *    $data['searchData'] =>  результат поисковой выдачи
 *    $data['meta_title'] => Значение meta тега для страницы
 *    $data['meta_keywords'] => Значение meta_keywords тега для страницы
 *    $data['meta_desc'] => Значение meta_desc тега для страницы
 *    $data['currency'] => Текущая валюта магазина
 *    $data['actionButton'] => тип кнопки в мини карточке товара
 *   </code>
 *
 *   Получить подробную информацию о каждом элементе массива $data, можно вставив следующую строку кода в верстку файла.
 *   <code>
 *    <?php viewData($data['items']); ?>
 *   </code>
 *
 *   Вывести содержание элементов массива $data, можно вставив следующую строку кода в верстку файла.
 *   <code>
 *    <php echo $data['items']; ?>
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
<!-- Верстка каталога -->
<?php if(empty($data['searchData'])): ?>  
  <?php if(class_exists('BreadCrumbs')): ?>
    [brcr]
  <?php endif; ?>

  <?php if(MG::getSetting('picturesCategory') == 'true'):?>   
    <?php echo mgSubCategory($data['cat_id']); ?>
  <?php endif;?>  

    <div class="products-wrapper catalog">
      
    <?php layout("apply_filter", $data['applyFilter']); ?> 
      <div class="form-group">    
        <?php echo $data['pager']; ?>
        <div class="view-switcher">
          <span class="form-title">Вид каталога:</span>
          <div class="btn-group" data-toggle="buttons-radio">
            <button class="view-btn grid" title="Плиткой" data-type="grid"></button>
            <button class="view-btn list" title="Списком" data-type="list"></button>
          </div>
        </div>
        <div class="count-viewed"></div>
        <div class="clear"></div>
      </div>
      <?php foreach($data['items'] as $item): ?>	
        <div itemscope itemtype="https://schema.org/Product" class="product-wrapper">
          <!--<div class="product-stickers">
            <?php
            echo $item['recommend']?'<span class="sticker-recommend">Хит!</span>':'';
            echo $item['new']?'<span class="sticker-new">Новинка</span>':'';
            ?>
          </div>-->
          <div class="product-image">
            <a onclick="ga('send', 'event', 'products', 'click', 'view_product');" href="<?php echo $item["link"]?>">
              <?php echo mgImageProduct($item); ?>
            </a>
          </div>
          <?php if(!class_exists('Rating')): ?>
            <div class="mg-rating">
              [rating id = "<?php echo $item['id'] ?>"]
            </div>
          <?php endif; ?>
          <!--<div class="product-code">Код: <span class="code"><?php echo $item["code"] ?></span></div>-->   
          <?php if(class_exists('MyDesiresPlugin')): ?>[addtowishlist product=<?php echo $item['id']; ?>]<?php endif; ?>
          <div class="product-name">
            <a itemprop="url" onclick="ga('send', 'event', 'products', 'click', 'view_product');" href="<?php echo $item["link"]?>"><h2 itemprop="name"><?php echo $item["title"] ?></h2></a>
          </div>
          <?php if(!class_exists('QuickView')): ?>
              [quick-view id="<?php echo $item['id']?>"]
          <?php endif; ?>
          <div class="product-description">
            <?php echo MG::textMore($item["description"], 240) ?>
          </div>
          <div class="product-footer">
            <div itemprop="offers" itemscope itemtype="http://schema.org/Offer" class="product-price">
              <ul class="product-status-list">
                <li <?php echo (!$item['old_price'])?'style="display:none"':'style="display:block"' ?>>
                  <span class="product-old-price old-price"><?php echo MG::priceCourse($item['old_price']); ?> <?php echo $data['currency']; ?></span>
                </li>
              </ul>
              <span itemprop="price" class="product-default-price">
                <?php echo $item["price"] ?> </span><span itemprop="priceCurrency" class="currency" content='UAH'><?php echo $data['currency']; ?>
              </span>
            </div>
            <div>
              <p><b>Марка/Модель:</b> <?php echo $item["thisUserFields"]['157']['value']; ?></p>
              <p><b>Пробег:</b> <?php echo $item["thisUserFields"]['162']['value']; ?> км</p>
              <p><b>Оценка:</b> <?php echo $item["thisUserFields"]['178']['value']; ?></p>
            </div>
            <div class="product-buttons">
              <!--Кнопка, которая меняет свое значение с "В корзину" на "Подробнее"-->
              <?php echo $item['actionButton']; ?>
              <?php echo $item['buyButton']; ?>
            </div>
          </div>
          <div class="clear"></div>
        </div>
      <?php endforeach; ?>
      <div class="clear"></div>
      <?php echo $data['pager']; ?>
      <div class="clear"></div>
    </div>
<?php else: ?>

  <h1 class="new-products-title">При поиске по фразе: <strong>"<?php echo $data['searchData']['keyword'] ?>"</strong> найдено
    <strong><?php echo mgDeclensionNum($data['searchData']['count'], array('товар', 'товара', 'товаров')); ?></strong>
  </h1>

  <div class="search-results products-wrapper list">
    <?php foreach($data['items'] as $item): ?>
      <div itemscope itemtype="https://schema.org/Product" class="product-wrapper">
        <!--<div class="product-stickers">
          <?php
          echo $item['recommend']?'<span class="sticker-recommend">Хит!</span>':'';
          echo $item['new']?'<span class="sticker-new">Новинка</span>':'';
          ?>
        </div>-->
        <div class="product-image">
          <a onclick="ga('send', 'event', 'products', 'click', 'view_product');" href="<?php echo $item["link"]?>">
            <?php echo mgImageProduct($item); ?>
          </a>
        </div>
        <?php if(!class_exists('Rating')): ?>
          <div class="mg-rating">
            [rating id = "<?php echo $item['id'] ?>"]
          </div>
        <?php endif; ?>
        <!--<div class="product-code">Код: <span class="code"><?php echo $item["code"] ?></span></div>-->
        <div class="product-name">
          <a itemprop="url" onclick="ga('send', 'event', 'products', 'click', 'view_product');" href="<?php echo $item["link"]?>"><h2 itemprop="name"><?php echo $item["title"] ?></h2></a>
        </div>
        <div class="product-description">
          <?php echo MG::textMore($item["description"], 240) ?>
        </div>
        <div class="product-footer">
          <div itemprop="offers" itemscope itemtype="http://schema.org/Offer" class="product-price">
            <span class="product-old-price" <?php echo (!$item['old_price'])?'style="display:none"':'style="display:block"' ?>>
              <?php echo MG::priceCourse($item['old_price']); ?> <?php echo $data['currency']; ?>
            </span>
           <span itemprop="price" class="product-default-price"><?php echo $item["price"] ?> </span><span itemprop="priceCurrency" class="currency" content='UAH'><?php echo $data['currency']; ?></span>
          </div>

          <div class="product-buttons">
            <!--Кнопка, которая меняет свое значение с "В корзину" на "Подробнее"-->
            <?php echo $item['buyButton']; ?>
          </div>
        </div>
        <div class="clear"></div>
      </div>
    <?php endforeach; ?>
    <div class="clear"></div>
  </div>

  <?php
  echo $data['pager'];
endif;
?>
<!-- / Верстка поиска -->
<?php if (!URL::isSection('catalog')||(((MG::getSetting('catalogIndex')=='true') && (URL::isSection('index') || URL::isSection(''))))): ?> 
  <?php if($cd = str_replace("&nbsp;", "", $data['cat_desc'])): ?>
    <h1 class="new-products-title"><?php echo $data['titeCategory'] ?></h1>
    <div class="cat-desc">
      <?php if($data['cat_img']): ?>
        <div class="cat-desc-img">
          <img src="<?php echo SITE.$data['cat_img'] ?>" alt="<?php echo $data['titeCategory'] ?>" title="<?php echo $data['titeCategory'] ?>" >
        </div>
      <?php endif; ?>
        <?php if (URL::isSection('catalog')||(((MG::getSetting('catalogIndex')=='true') && (URL::isSection('index') || URL::isSection(''))))): ?> 
          <!-- Здесь можно добавить описание каталога - информация для пользователей (выводится только на странице каталог (не в категории)) -->
          <?php else :?>
          <div class="cat-desc-text"><?php echo $data['cat_desc'] ?></div>
        <?php endif;?>
      <div class="clear"></div>
    </div>
  <?php endif; ?>
<?php endif; ?>