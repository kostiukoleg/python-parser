
<div class="banner_top_up container" style="*width:100%;text-align: center;*background-color: #F0F2FA;">
  <?php if(Yii::app()->request->requestUri == '/'){ ?>
    <?php $this->widget('application.modules.banner.widgets.bannerWidget',['position'=>12]); ?>    
  <?php }else{ ?>
    <?php $this->widget('application.modules.banner.widgets.bannerWidget',['position'=>13]); ?>   
  <?php } ?>

</div>
<script>
  $(".banner-link-trigger").css("height", "90px");
</script>
<header class="header">
  <!-- Здесь выводим меню для редактирования страницы -->
  <?php
  $user = Yii::app()->getUser()->getProfile();
  if (isset($user->id)) {
      $id_user = $user->id;
  }
  else{
      $id_user = 0;
  }
  ?>
    <!-- основна мікророзмітка для всього сайта -->
    <?php
            echo "<script type=\"application/ld+json\"> {";
            echo "\"@context\" : \"http://schema.org\",";
            echo "\"@type\" : \"OfficeEquipmentStore\","; 
            echo "\"image\": \"https://domstroy.com.ua/assets/729f9984/images/logo.png\",";
            echo "\"address\" : {";
            echo "\"@type\": \"PostalAddress\",";
            echo "\"addressLocality\": \"Винница\","; 
            echo "\"addressRegion\": \"Винницкая область\","; 
            echo "\"postalCode\": \"21009\","; 
            echo "\"streetAddress\": \"ул. И. Бевза 34 \" },"; 
            echo "\"name\":\"ДомСтрой\",";
            echo "\"url\":\"https://domstroy.com.ua\",";
            echo "\"email\":\"info@domstroy.com.ua\",";
            echo "\"openingHours\": ["; 
            echo "\"Monday 09:00 - 18:00\", \"Tuesday 09:00 - 18:00\", \"Wednesday 09:00 - 18:00\", \"Thursday 09:00 - 18:00\", \"Friday 09:00 - 18:00\"],";
            echo "\"telephone\": \"+38 067 433 09 48\",";
            echo "\"priceRange\":\"$$\"";
            echo "} </script>"; 
    ?>
    <!-- основна мікророзмітка для всього сайта -->
<!-- Hotjar Tracking Code for domstroy.com.ua -->
<script>
    /*(function(h,o,t,j,a,r){
        h.hj=h.hj||function(){(h.hj.q=h.hj.q||[]).push(arguments)};
        h._hjSettings={hjid:1577922,hjsv:6};
        a=o.getElementsByTagName('head')[0];
        r=o.createElement('script');r.async=1;
        r.src=t+h._hjSettings.hjid+j+h._hjSettings.hjsv;
        a.appendChild(r);
    })(window,document,'https://static.hotjar.com/c/hotjar-','.js?sv=');*/
</script>
  <!-- Здесь выводим меню для редактирования страницы -->
  <?php if (($id_user == 1761)||($id_user == 10)||($id_user == 2562)): ?>
  <div style="position: absolute;">
  <nav id="sidebar-menu">
    <ul style="margin:0px;">
      <li>
        <i class="fa fa-cog" style="margin-left: -25px;"></i>
        <b class="fa fa-caret-left"></b>
        <ul style="margin-left: -30px;width:300px;">
          <li onclick="EditMetaTag()">Редактирование метатегов</li>
          <li onclick="EditDescription()">Редактирование описания страницы</li>
        </ul>
      </li>
    </ul>
  </nav>
  </div>
  <?php endif ?>
  <!-- Здесь выводим меню для редактирования страницы -->
    <div class="header__top">
      <div class="container">
        <div class="row">
          <div class="col">
            <div class="header__top__box">
              <?php if(Yii::app()->request->requestUri == '/'){ ?>
                <span class="logo"><?= CHtml::image($this->mainAssets.'/images/logo.png', 'Domstroy.com.ua'); ?></span>
                <span class="logo-name">СТРОИТЕЛЬНАЯ <br>ИНТЕРНЕТ ПЛАТФОРМА <span>№1</span></span>                
              <?php }else{ ?>
                <a href="<?=Yii::app()->createUrl('/homepage/hp/index');?>" class="logo"><?= CHtml::image($this->mainAssets.'/images/logo.png', 'Domstroy.com.ua'); ?></a>
                <a href="<?=Yii::app()->createUrl('/homepage/hp/index');?>" class="logo-name">СТРОИТЕЛЬНАЯ <br>ИНТЕРНЕТ ПЛАТФОРМА <span>№1</span></a>
              <?php } ?>
              <?php $this->widget('application.modules.store.widgets.SearchProductWidget', ['view' => 'home-page']);?>
              <div class="lang" style="display: none;">
                <a href="">РУС</a>
                <a href="" class="active">УКР</a>
              </div>
              <?php
              $today = date('d.m.Y');
              $addedToday = Product::model()->count(['condition'=>'create_time >= STR_TO_DATE(\''.$today.' 00:00:00\', \'%d.%m.%Y %H:%i:%s\')']);
              if($addedToday%1000 < 100){
                  $addedToday = '0'.$addedToday%1000;
              }else{
                  $addedToday= $addedToday%1000;
              }
              ?>
	      <div>
              	<ul class="header__top__menu" style="list-style: none;">
                  	<?php if (Yii::app()->getUser()->isAuthenticated()) { ?>
                      		<li><a class="enter active" href="<?=Yii::app()->createUrl('cabinet');?>"><?=Yii::t('site','Профиль');?></a></li>
                      		<li><a href="<?=Yii::app()->createUrl('logout');?>"><?=Yii::t('site','Выйти');?></a></li>
                  	<?php } else {?>
                      		<li><a class="enter active" href="<?=Yii::app()->createUrl('login');?>"><?=Yii::t('site','Войти');?></a></li>
                      		<li><a href="<?=Yii::app()->createUrl('registration');?>"><?=Yii::t('site','Регистрация');?></a></li>
                  	<?php } ?>
                  	<?php if (Yii::app()->getUser()->isAuthenticated()) { ?>
                  		<li><a style="padding:0; margin:0;padding-bottom: 20px" rel="nofollow" href="/scart/popup" id="cart-popup"><?= CHtml::image($this->mainAssets.'/images/cart.png', 'cart'); ?> <span><?php if(Cart::getInstance()->getCount() > 0){echo '('.Cart::getInstance()->getCount().')';} ?></span></a></li>
                  	<?php } else {?>
                    		<li><a style="padding:0; margin:0;" rel="nofollow" href="/scart/popup" id="cart-popup"><?= CHtml::image($this->mainAssets.'/images/cart.png', 'cart'); ?><span><?php if(Cart::getInstance()->getCount() > 0){echo '('.Cart::getInstance()->getCount().')';} ?></span></a></li>
                  	<?php } ?>
                </ul>
              	<div class="language">
<script>
function showSearch() {
  if(document.getElementById("f-search").style.display !== "block"){
  document.getElementById("f-search").style.display = "block";
  }else{
  document.getElementById("f-search").style.display = "none";
  }
}
</script>
			<a class="language__img" href="javascript:void(0)" title="Українська!" data-google-lang="uk"><span class="headerLanguage__text">UA</span></a>
			<span>&nbsp;|&nbsp;</span>
			<a class="language__img" href="javascript:void(0)" title="Російська" data-google-lang="ru"><span class="headerLanguage__text">RU</span></a>
		<!-- <?= CHtml::image($this->mainAssets.'/images/flags/uk.png', 'uk', array('data-google-lang'=>'uk', 'class'=>'language__img')); ?>
		<?= CHtml::image($this->mainAssets.'/images/flags/ru.png', 'ru', array('data-google-lang'=>'ru', 'class'=>'language__img')); ?>-->
    		<!-- <img src="/images/flags/en.png" alt="en" data-google-lang="en" class="language__img"> -->
	      	</div>
	      <div>
            </div><!--/header__top__box-->
          </div>
        </div>
      </div>
    </div>
</header>
<div class="main-banner-bg">
    <div class="container ">
        <div class="navigation-buttons hidden-md hidden-sm hidden-xs" style="position: relative;">
            <a title="<?=Yii::t('site','Форум');?>" href="<?=Yii::app()->createUrl('/forums')?>" class="button button-green" id="header_forum">Форум</a>
            <a title="<?=Yii::t('site','+ Объявление');?>" href="<?=Yii::app()->createUrl('/user/product/create')?>" class="button button-green"  id="header_create"><p>+ Добавить</p><p> Объявление</p></a>
            <a title="<?=Yii::t('site','Оставить заявку на ремонт');?>" href="<?=Yii::app()->createUrl('/repairorder/create')?>" class="button button-red" id="header_create_product"><p>Заявка</p><p>на ремонт</p></a>
        </div>
        <div class="navigation-content pull-left">
            <div class="tab">
                <ul class="menu desctop_menu" style="height: 74px;">
                    <li class="mega_punkt" style="height: 74px;padding-top: 3.5%;"><a style="font-size: 17px;text-transform: uppercase;" href="/product">Товары</a>
                      <?php
                        $cat_product = StoreCategory::model()->getCatToProducts();
                      ?>
                      <?php if(!empty($cat_product)){ ?>
                        <div class="body_menu">
                        <ul class="sky-mega-menul sky-mega-menu-pos-left anim" style="max-height: 320px;">
                          <li>
                            <ul>
                                <?php
                                $i = 0;
                                ?>
                                <?php foreach ($cat_product as $c_p) { ?>
                                  <?php if($i < 9){ ?>
                                    <li aria-haspopup="true"  class="parent">
                                      <a href="/<?php echo $c_p['slug']; ?>" title="" style="text-transform: uppercase;font-weight: bold;font-size:14px;"><?php echo $c_p['name']; ?></a>
                                      <?php
                                          $cat_product_2 = StoreCategory::model()->getSubCatForNav($c_p['id']);
                                      ?>
                                      <?php if(!empty($cat_product_2)){ ?>
                                      <div class="grid-container3" style="margin-left: -60px;">
                                        <ul>
                                          <?php
                                          $i2 = 0;
                                          ?>
                                          <?php foreach($cat_product_2 as $c_p2){ ?>
                                            <?php 
                                      		$cp2 = Yii::app()->getDb()->createCommand()
								            ->select('id')
								            ->from('{{store_product}}')
								            ->where('category_id = :id', [':id' => $c_p2['id']])
								            ->queryColumn();
								            $cat_product_3 = StoreCategory::model()->getSubCatForNav($c_p2['id']);
                                            if($i2 < 9 && !empty($cp2) || !empty($cat_product_3)){ ?>
                                              <li  class="parent" style="width:100%;"><a style="width:100%;" href="/<?php echo $c_p2['slug']; ?>" title=""><?php echo $c_p2['name']; ?></a>
                                                  <?php
                                                    //$cat_product_3 = StoreCategory::model()->getSubCatForNav($c_p2['id']);
                                                  ?>
                                                  <?php if(!empty($cat_product_3)){ ?>
                                                  <div class="grid-container3" style="margin-left: -60px;">
                                                    <ul>
                                                      <?php $i3 = 0; ?>
                                                        <?php foreach($cat_product_3 as $c_p3){ ?>
                                                          <?php 
                                                              		$cp3 = Yii::app()->getDb()->createCommand()
														            ->select('id')
														            ->from('{{store_product}}')
														            ->where('category_id = :id', [':id' => $c_p3['id']])
														            ->queryColumn();  
														            $cat_product_4 = StoreCategory::model()->getSubCatForNav($c_p3['id']);
                                                          if($i3 < 9 && !empty($cp3) || !empty($cat_product_4)){ ?>
                                                          <li class="sub_menu"><a href="/<?php echo $c_p3['slug'] ?>" title=""><?php echo $c_p3['name'] ?></a>
                                                              <?php
                                                              	// $cat_product_4 = StoreCategory::model()->getSubCatForNav($c_p3['id']);
                                                              ?>
                                                              <?php if(!empty($cat_product_4)){ ?>
                                                              <div class="grid-container3" style="margin-left: -60px;">
                                                                <ul>
                                                                  <?php $i4 = 0; ?>
                                                                  <?php foreach($cat_product_4 as $c_p4){ ?>
                                                                    <?php 
                                                              		$cp4 = Yii::app()->getDb()->createCommand()
														            ->select('id')
														            ->from('{{store_product}}')
														            ->where('category_id = :id', [':id' => $c_p4['id']])
														            ->queryColumn();
                                                                    if ($i4 < 9 && !empty($cp4)){ ?>
                                                                    <li class="sub_menu">
                                                                       <a href="/<?php echo $c_p4['slug']; ?>" title=""><?php echo $c_p4['name']; ?></a>
                                                                     </li>
                                                                    <?php }
                                                                    $i4++;
                                                                    ?>
                                                                  <?php } ?>
                                                                  <?php
                                                                    if(count($cat_product_4) > 9 && !empty($cp4)){ ?>
                                                                      <li><a href="/<?php echo $c_p3['slug']; ?>" style="border: none;font-weight: bold;color: red;">посмотреть все категории</a></li>
                                                                    <?php } ?>
                                                                </ul>
                                                              </div>
                                                              <?php } ?>
                                                          </li>
                                                          <?php } ?>
                                                        <?php $i3++; ?>
                                                      <?php } ?>
                                                      <?php if (count($cat_product_3) > 9 && !empty($cp4)): ?>
                                                        <li><a href="/<?php echo $c_p2['slug']; ?>" style="border: none;font-weight: bold;color: red;">посмотреть все категории</a></li>
                                                      <?php endif ?>
                                                    </ul>
                                                  </div>
                                                  <?php } ?>
                                              </li>
                                            <?php }
                                            $i2++;
                                            ?>
                                          <?php } ?>
                                          <?php if(count($cat_product_2) > 9 && !empty($cp2)){ ?>
                                            <li><a href="/<?php echo $c_p['slug']; ?>" style="border: none;font-weight: bold;color: red;">посмотреть все категории</a></li>
                                          <?php } ?>
                                        </ul>
                                      </div>
                                      <?php } ?>
                                    </li>
                                  <?php } ?>
                                <?php
                                $i++;
                                } ?>
                                <?php if(count($cat_product) > 6){ ?>
                                  <li><a href="/vse-dlya-doma" style="border: none;font-weight: bold;color: #5c94a7;">посмотреть все категории</a></li>
                                <?php } ?>
                            </ul>
                          </li>
                        </ul>
                      </div>
                      <?php } ?>
                    </li>
                    <li class="mega_punkt" style="height: 74px;padding-top: 3.5%;"><a style="font-size: 17px;text-transform: uppercase;" href="/service">Услуги</a>
                      <?php
                        $cat_product = StoreCategory::model()->getCatToService();
                      ?>
                      <?php if(!empty($cat_product)){ ?>
                        <div class="body_menu">
                        <ul class="sky-mega-menul sky-mega-menu-pos-left anim" style="max-height: 320px;">
                          <li>
                            <ul>
                                <?php
                                $i = 0;
                                ?>
                                <?php foreach ($cat_product as $c_p) { ?>
                                  <?php if($i < 5){ ?>
                                    <li aria-haspopup="true"  class="parent">
                                      <a href="/<?php echo $c_p['slug']; ?>" title="" style="text-transform: uppercase;font-weight: bold;font-size:14px;"><?php echo $c_p['name']; ?></a>
                                      <?php
                                          $cat_product_2 = StoreCategory::model()->getSubCatForNav($c_p['id']);
                                      ?>
                                      <?php if(!empty($cat_product_2)){ ?>
                                      <div class="grid-container3" style="margin-left: -60px;">
                                        <ul>
                                          <?php
                                          $i2 = 0;
                                          ?>
                                          <?php foreach($cat_product_2 as $c_p2){ ?>
                                            <!--во всех остальных колонках показываем только первые 15 дабы избежать портянок-->
                                            <?php 
                                      		$cp2 = Yii::app()->getDb()->createCommand()
								            ->select('id')
								            ->from('{{store_product}}')
								            ->where('category_id = :id', [':id' => $c_p2['id']])
								            ->queryColumn();
											$cat_product_3 = StoreCategory::model()->getSubCatForNav($c_p2['id']);
                                            if($i2 < 9 && !empty($cp2) || !empty($cat_product_3)){ ?>
                                              <li  class="parent" style="width:100%;"><a style="width:100%;" href="/<?php echo $c_p2['slug']; ?>" title=""><?php echo $c_p2['name']; ?></a>
                                                  <?php
                                                    // $cat_product_3 = StoreCategory::model()->getSubCatForNav($c_p2['id']);
                                                  ?>
                                                  <?php if(!empty($cat_product_3)){ ?>
                                                  <div class="grid-container3" style="margin-left: -60px;">
                                                    <ul>
                                                      <?php $i3 = 0; ?>
                                                        <?php foreach($cat_product_3 as $c_p3){ ?>
                                                          <?php 
				                                      		$cp3 = Yii::app()->getDb()->createCommand()
												            ->select('id')
												            ->from('{{store_product}}')
												            ->where('category_id = :id', [':id' => $c_p3['id']])
												            ->queryColumn();
												            $cat_product_4 = StoreCategory::model()->getSubCatForNav($c_p3['id']);
                                                          if($i3 < 9 && !empty($cp3) || !empty($cat_product_4)){ ?>
                                                          <li class="sub_menu"><a href="/<?php echo $c_p3['slug'] ?>" title=""><?php echo $c_p3['name'] ?></a>
                                                              <?php
                                                                // $cat_product_4 = StoreCategory::model()->getSubCatForNav($c_p3['id']);
                                                              ?>
                                                              <?php if(!empty($cat_product_4)){ ?>
                                                              <div class="grid-container3" style="margin-left: -60px;">
                                                                <ul>
                                                                  <?php $i4 = 0; ?>
                                                                  <?php foreach($cat_product_4 as $c_p4){ ?>
                                                                    <?php 
						                                      		$cp4 = Yii::app()->getDb()->createCommand()
														            ->select('id')
														            ->from('{{store_product}}')
														            ->where('category_id = :id', [':id' => $c_p4['id']])
														            ->queryColumn();
                                                                    if ($i4 < 9 && !empty($cp4)){ ?>
                                                                    <li class="sub_menu">
                                                                       <a href="/<?php echo $c_p4['slug']; ?>" title=""><?php echo $c_p4['name']; ?></a>
                                                                     </li>
                                                                    <?php }
                                                                    $i4++;
                                                                    ?>
                                                                  <?php } ?>
                                                                  <?php
                                                                    if(count($cat_product_4) > 9 && !empty($cp4)){ ?>
                                                                      <li><a href="/<?php echo $c_p3['slug']; ?>" style="border: none;font-weight: bold;color: red;">посмотреть все категории</a></li>
                                                                    <?php } ?>
                                                                </ul>
                                                              </div>
                                                              <?php } ?>
                                                          </li>
                                                          <?php } ?>
                                                        <?php $i3++; ?>
                                                      <?php } ?>
                                                      <?php if (count($cat_product_3) > 9 && !empty($cp3) || !empty($cat_product_4)): ?>
                                                        <li><a href="/<?php echo $c_p2['slug']; ?>" style="border: none;font-weight: bold;color: red;">посмотреть все категории</a></li>
                                                      <?php endif ?>
                                                    </ul>
                                                  </div>
                                                  <?php } ?>
                                              </li>
                                            <?php }
                                            $i2++;
                                            ?>
                                          <?php } ?>
                                          <?php if(count($cat_product_2) > 9 && !empty($cp2) || !empty($cat_product_3)){ ?>
                                            <li><a href="/<?php echo $c_p['slug']; ?>" style="border: none;font-weight: bold;color: red;">посмотреть все категории</a></li>
                                          <?php } ?>
                                        </ul>
                                      </div>
                                      <?php } ?>
                                    </li>
                                  <?php } ?>
                                <?php
                                $i++;
                                } ?>
                                <?php if(count($cat_product) > 6){ ?>
                                  <li><a href="/uslugi" style="border: none;font-weight: bold;color: red;">посмотреть все категории</a></li>
                                <?php } ?>
                            </ul>
                          </li>
                        </ul>
                      </div>
                      <?php } ?>
                    </li>
                    <li class="mega_punkt" style="height: 74px;padding-top: 3.5%;"><a href="/company" style="font-size: 17px;text-transform: uppercase;">Компании</a></li>
                    <li class="mega_punkt" style="height: 74px;padding-top: 3.5%;"><a href="/master" style="font-size: 17px;text-transform: uppercase;">Мастера</a></li>
                </ul>
                <!-- Мобильное меню-->
                <div class="box-menu mobile_menu">
		  <span style="width: 15%;float: left;" id="search" onclick="showSearch()">&nbsp;</span>
                  <div style="width: 30%;float: left;font-weight: bold;"><a style="color: #fff;text-transform: uppercase;" href="/product">Товары</a></div>
                  <div style="width: 30%;float: left;font-weight: bold;"><a style="color: #fff;text-transform: uppercase;" href="/service">Услуги</a></div>
                  <div style="cursor: pointer;width:20%;margin-left: 90%;" onclick="OpenMobileMenu()"><img style="width: 28px;margin-top: -5px;" src="/images/menu-512.png" alt="Фон меню"><span style="display:none;font-size: 16px;font-weight: bold;margin-left:10px;color:#fff;">Меню</span>
                  </div>
                  <div class="nav_mobile">
                      <p>
                        <a href="/company">Компании</a>
                      </p>
                      <p>
                        <a href="/master">Мастера</a>
                      </p>
                      <p style="padding-top: 7px;padding-bottom: 7px;cursor: pointer;" onclick="OpenPidpunkt1()">
                        <span style="padding: 10px 3px;color: #fff;line-height: 1.5;transition: color 0.3s ease;">Категории</span>
                        <img class="img_level1" style="width: 20px;float: right;" src="/images/noup.png" alt="Открывающее меню">
                      </p>
                      <div class="level_1" style="display:none;margin-top: 10px;padding: 0;">
                          <!--Вытягиваем все категории второго уровня-->
                          <?php
                            $allcat = StoreCategory::model()->getAllCatToMobileMenu();
                          ?>
                          <?php foreach ($allcat as $ac) { ?>
                            <div style="background-color: #226382;border-bottom: 1px solid #113140;padding-top: 5px;min-height: 36px;">
                            <?php
                              $sub_cat1 = StoreCategory::model()->getSubCatForNav($ac['id']);
                            ?>
                              <div class="row" style="margin:0;padding: 0;">
                                <?php if (!empty($sub_cat1)){ ?>
                                <div class="col-xs-9">
                                  <a  style="padding: 15px 3px;" href="/<?php echo $ac['slug']?>"><?php echo $ac['name']?></a>
                                </div>
                                <div class="col-xs-3" data-category="<?php echo $ac['id'];?>" style="padding-right:0px;padding-top: 10px;padding-bottom: 10px;display: inline-block;vertical-align: middle;cursor: pointer;" onclick="OpenPidpunkt2(this)">
                                  <img class="img_level1" style="width: 20px;float: right;" src="/images/noup.png"  alt="Открывающее меню">
                                </div>
                                <?php } else{ ?>
                                  <div class="col-xs-12">
                                    <a style="padding: 15px 3px;" href="/<?php echo $ac['slug']?>"><?php echo $ac['name']?></a>
                                  </div>
                                <?php } ?>
                              </div>
                            </div>
                            <?php if (!empty($sub_cat1)){ ?>
                                                <div class="level_2_<?php echo $ac['id']; ?>" style="display: none;padding:0;background-color: #068ab1;">
                                  <?php foreach ($sub_cat1 as $sc1) { ?>
                                      <?php
                                        $sub_cat2 = StoreCategory::model()->getSubCatForNav($sc1['id']);
                                      ?>
                                      <div class="row" style="margin:0;padding: 0;border-bottom: 1px solid #236788;padding-top: 5px;padding-bottom: 5px;">
                                        <?php if (!empty($sub_cat2)){ ?>
                                        <div class="col-xs-9">
                                          <a  style="padding: 15px 3px;" href="/<?php echo $sc1['slug']?>"><?php echo $sc1['name']?></a>
                                        </div>
                                        <div class="col-xs-3" data-category="<?php echo $sc1['id'];?>" style="padding-right:0px;padding-top: 10px;padding-bottom: 10px;display: inline-block;vertical-align: middle;cursor: pointer;" onclick="OpenPidpunkt2(this)">
                                          <img class="img_level1" style="width: 20px;float: right;" src="/images/noup.png" alt="Открывающее меню">
                                        </div>
                                        <?php } else{ ?>
                                          <div class="col-xs-12">
                                            <a style="padding: 15px 3px;" href="/<?php echo $sc1['slug']?>"><?php echo $sc1['name']?></a>
                                          </div>
                                        <?php } ?>
                                      </div>

                                      <!--3-й уровень-->
                                      <?php if (!empty($sub_cat2)){ ?>
                                          <div class="level_2_<?php echo $sc1['id']; ?>" style="display: none;padding:0;background-color: rgb(16, 164, 208);">
                                            <?php foreach ($sub_cat2 as $sc2) { ?>
                                              <?php
                                                $sub_cat3 = StoreCategory::model()->getSubCatForNav($sc2['id']);
                                              ?>
                                              <div class="row" style="margin:0;padding: 0;border-bottom: 1px solid #236788;padding-top: 5px;padding-bottom: 5px;">
                                                <?php if (!empty($sub_cat3)){ ?>
                                                <div class="col-xs-9">
                                                  <a  style="padding: 15px 3px;" href="/<?php echo $sc2['slug']?>"><?php echo $sc2['name']?></a>
                                                </div>
                                                <div class="col-xs-3" data-category="<?php echo $sc2['id'];?>" style="padding-right:0px;padding-top: 10px;padding-bottom: 10px;display: inline-block;vertical-align: middle;cursor: pointer;" onclick="OpenPidpunkt2(this)">
                                                  <img class="img_level1" style="width: 20px;float: right;" src="/images/noup.png" alt="Открывающее меню">
                                                </div>
                                                <?php } else{ ?>
                                                  <div class="col-xs-12">
                                                    <a style="padding: 15px 3px;" href="/<?php echo $sc2['slug']?>"><?php echo $sc2['name']?></a>
                                                  </div>
                                                <?php } ?>
                                              </div>
                                              <!-- 4-й уровень -->
                                                <?php if (!empty($sub_cat3)){ ?>
                                                    <div class="level_2_<?php echo $sc2['id']; ?>" style="display: none;padding:0;background-color: rgb(18, 190, 241);">
                                                      <?php foreach ($sub_cat3 as $sc3) { ?>
                                                        <div class="row" style="margin:0;padding: 0;border-bottom: 1px solid #236788;padding-top: 5px;padding-bottom: 5px;">
                                                            <div class="col-xs-12">
                                                              <a style="padding: 15px 3px;" href="/<?php echo $sc3['slug']?>"><?php echo $sc3['name']?></a>
                                                            </div>
                                                        </div>
                                                      <?php } ?>
                                                    </div>
                                                <?php } ?>
                                            <?php } ?>
                                          </div>
                                      <?php } ?>

                                                    <?php } ?>
                                                </div>
                            <?php } ?>
                                    <?php } ?>
                                    </div>


                  </div>

                        </div>
                        </div>
                    </div>
                </div>
            </div>

    </div>
  
<div class="banner_top_up container" id="mob-banner" style="*width:100%;text-align: center;*background-color: #F0F2FA;">
  <?php if(Yii::app()->request->requestUri !== '/'){ ?>
    <?php $this->widget('application.modules.banner.widgets.bannerWidget',['position'=>21]); ?>    
  <?php } ?>
</div>
  <style>
      @media screen and (max-width: 586px){
        .products__box li{
          min-height: auto;
        }
        .popular__banners .banner__cart{
          margin-bottom: 0;
        }
      section{
        padding-bottom: 0; 
      }
      .pictured-block{
        height: auto !important;
        margin-bottom: 0;
      }
      .popular{
        padding-bottom: 0;
      }
       .tab #description{
     font-size: 12px;
     }
    }
#name_product_s{
    font-size: x-large;
    text-align: center;
    white-space: normal;
}

#open_block_one{
  background-color: #2a4864;
  color: #fff;
  padding-left: 15px;
  padding-right: 15px;
  padding-top: 5px;
  padding-bottom: 5px;
  border-radius: 5px;
  float: right;
  margin-right:30px;
  cursor:pointer;
  display: none;
}
#mobile_descr{
  display: none;
  font-size: 12px;
}

@media screen and (max-width: 586px){
#open_block_one{
  display: block;
}
#mobile_descr{
  display: block;
}
#mobile-descr p{
  font-size: 12px;
}
.tab-content #description{
  display: none;
}
}
.section-trans {
  padding-top: 50px;
}

.reviews {
  background-color: #f0f2fa;
  padding: 30px 0!important; 
}
.rew_auto {
  margin: 0 auto;
  margin-top: 30px;
}
.reviews__title {
  text-align: center;
}
.reviews__title h2 {
  font-size: 30px;
  font-weight: 400;
  color: #2a4864;
  margin-bottom: 7px;
}
.reviews__title span {
  font-size: 14px;
  display: block;
  margin-bottom: 20px;
}
.banner_top_up {
  padding: 15px 0;
}
.banner_top_up img {
  width: 100%!important;
  height: auto!important;
}
.my-text {
  max-height: 300px;
}
.testimony-wrap {
    display: block;
    position: relative;
}
.pb-5, .py-5 {
    padding-bottom: 3rem !important;
}
.p-4 {
    padding: 1.5rem !important;
}
.testimony-wrap .user-img {
    width: 200px;
    height: 200px;
    border-radius: 50%;
    position: relative;
    margin-top: -52px;
    margin: 0 auto;
    float: none;
}
.user-img {
    background-size: contain;
    background-repeat: no-repeat;
    background-position: center center;
}
.testimony-wrap .user-img .quote {
    position: absolute;
    bottom: -20px;
    left: 50%;
    width: 40px;
    height: 40px;
    -webkit-transform: translateX(-50%);
    -ms-transform: translateX(-50%);
    transform: translateX(-50%);
    background: #2a4864;
    -webkit-border-radius: 50%;
    -moz-border-radius: 50%;
    -ms-border-radius: 50%;
    border-radius: 50%;
}
.text-center {
    text-align: center !important;
}
.mb-5, .my-5 {
    margin-bottom: 3rem !important;
}
.testimony-wrap .name {
    font-weight: 500;
    font-size: 16px;
    margin-bottom: 0;
    color: #000;
}
.testimony-wrap .position {
    font-size: 13px;
}
.reviews__slider .owl-pagination .owl-page.active span, .reviews__slider .owl-pagination .owl-page:focus span, .reviews__slider .owl-pagination .owl-page:hover span {
    background: #2a4864;
    border: 1px solid #2a4864;
    outline: none !important;
}
.reviews__slider .owl-pagination .owl-page span{
    width: 10px;
    height: 10px;
    margin: 5px;
    -webkit-transform: rotate(45deg);
    -ms-transform: rotate(45deg);
    transform: rotate(45deg);
    border: 1px solid #ccc;
    -webkit-border-radius: 0; 
    -moz-border-radius: 0;
     border-radius: 0;
}
.reviews__slider .owl-pagination {
    text-align: center;
}
.reviews__slider .owl-controls .owl-buttons {
    position: absolute;
    top: 50%;
    width: 100%;
}
.reviews__slider:hover .owl-prev, .reviews__slider:focus .owl-prev {
    left: -15px!important;
}
.reviews__slider:hover .owl-next, .reviews__slider:focus .owl-next {
    right: -15px!important;
}
.reviews__slider:hover .owl-prev, .reviews__slider:hover .owl-next, .reviews__slider:focus .owl-prev, .reviews__slider:focus .owl-next {
    opacity: 1!important;
}
.reviews__slider button:focus {
  outline: none;
}
.reviews__slider .owl-prev {
    left: 0;
}
.reviews__slider .owl-next {
    right: 0;
}
.reviews__slider:hover .owl-prev span:before, .reviews__slider:hover .owl-next span:before, .reviews__slider:focus .owl-prev span:before, .reviews__slider:focus span:before {
    color: #d9d9d9!important;
}
.reviews__slider .owl-prev span:before, .reviews__slider .owl-next span:before {
    font-size: 40px;
    color: #e6e6e6;
}
.reviews__slider .owl-controls .owl-buttons div {
  opacity: 0;
  background: transparent;
  border-radius: 0;
  margin: 0;
  padding: 0;
}
.reviews__slider .owl-prev, .reviews__slider .owl-next {
    position: absolute;
    -webkit-transform: translateY(-50%);
    -ms-transform: translateY(-50%);
    transform: translateY(-50%);
    margin-top: -10px;
    -moz-transition: all 0.7s ease;
    -o-transition: all 0.7s ease;
    -webkit-transition: all 0.7s ease;
    -ms-transition: all 0.7s ease;
    transition: all 0.7s ease;
    opacity: 0;
}
.testimony-wrap .user-img .quote i {
    color: #fff;
}
    </style>
