<?php
/** @var Page $page */
$this->description = 'Строительный портал ДомСтрой: удобный поиск товаров и услуг от проверенных компаний, мастеров Украины в сфере строительства, ремонта и обустройства дома.';
$this->keywords = 'Строительный портал';
$this->title = 'Строительный портал ДомСтрой – все товары для строительства и ремонта';
$this->pageTitle = $this->title;
Yii::app()->clientScript->registerMetaTag(CHtml::encode($this->title),null,null,array('property'=>"og:title"));
Yii::app()->clientScript->registerMetaTag(CHtml::encode($this->description),null,null,array('property'=>"og:description"));
Yii::app()->clientScript->registerMetaTag(CHtml::encode('https://domstroy.com.ua/'),null,null,array('property'=>"og:url"));
Yii::app()->clientScript->registerMetaTag(CHtml::encode('Домстрой - строительный портал №1 в Украине'),null,null,array('property'=>"og:site_name"));
Yii::app()->clientScript->registerMetaTag('/images/logoforSMM.png',null,null,array('property'=>"og:image"));
Yii::app()->getClientScript()->registerCssFile($this->mainAssets. '/css/homepagestyle.css');
?>

<!-- content -->

        <div class="header__main">
            <div class="container">
                <div class="row">
                    <div class="col">
                        <div class="header__main__asided">

                            <a href="" class="hamburger"><i class="icon icon-menu"></i></a>
                            <?php
                                $cat_populaire = StoreCategory::model()->getPopulairCategories();
                            ?>

                            <div class="sidebar">
                                <?php if(isset($cat_populaire)&&!empty($cat_populaire)){ ?>
                                <h3 class="sidebar-heading">ПОПУЛЯРНЫЕ КАТЕГОРИИ</h3>
                                <ul class="sidebar__menu">
                                    <?php foreach ($cat_populaire as $key => $cat) { ?>
                                        <li><a class="sidebar__link" href="<?php echo $cat['slug']; ?>"><?php echo $cat['name']; ?> <i class="icon-angle-right"></i></a></li>
                                    <?php } ?>
                                </ul>
                                <?php } ?>
                            </div><!--/sidebar-->

                            <div class="header__main__content">

                                <div class="header__main__top-content">

                                    <div class="colored-block">
                                        <h1 class="block-heading" style="font-size: 18px; margin-top:5px;">Строительный портал ДомСтрой: всё для ремонта, строительства и обустройства
дома</h1>
                                       <!-- <p style="font-size:14px;">Удобный поиск товаров для клиентов и эффективная
реклама для компаний и мастеров Украины. Каталоги
товаров и услуг, лучшие цены, выбор поставщиков и отзывы
пользователей. Крупнейшая база проверенных
строительных компаний и мастеров.</p>--><p style="font-size:16px;">Удобный поиск товаров и услуг для покупателей. Эффективная реклама для компаний и мастеров по ремонтам.</p>
                                        <a href="<?php echo Yii::app()->createUrl('/page/nash-stroitelnyi-portal'); ?>" target="_blank" class="btn btn-accent" style="padding: 5px 10px;margin-top:15px;">Подробнее</a><!-- Больше о нас-->
                                    </div>

                                    <div class="banner-block pictured-block">
                                        
                                        <?php $this->widget('application.modules.banner.widgets.bannerWidget',['position'=>1])?>
                                    </div>

                                </div><!--/header__main__top-content-->

                                <div class="header__main__features">

                                    <ul>
                                        <li>
                                            <div class="icon"><img src="images/features1.png" alt="icon"></div>

                                            <div class="text-part">
                                                <a style="color:#6c7a89;" href="<?php echo Yii::app()->createUrl('product/'); ?>">
                                                <h4 class="features-heading">Большой асортимент</h4>
                                                <p>более 150 000 товаров</p>
                                                </a>
                                            </div>

                                        </li>
                                        <li>
                                            <div class="icon"><img src="images/features2.png" alt="icon"></div>

                                            <div class="text-part">
                                                <a style="color:#6c7a89;" href="<?php echo Yii::app()->createUrl('service/'); ?>">
                                                <h4 class="features-heading">Широкий спектр услуг</h4>
                                                <p>Более 5000 услуг</p>
                                                </a>
                                            </div>

                                        </li>
                                        <li>
                                            <div class="icon"><img src="images/features3.png" alt="icon"></div>

                                            <div class="text-part">
                                                <a style="color:#6c7a89;" href="<?php echo Yii::app()->createUrl('company/'); ?>">
                                                <h4 class="features-heading">Лучшие компании</h4>
                                                <p>по 500 направлениях</p>
                                                </a>
                                            </div>

                                        </li>
                                        <li>
                                            <div class="icon"><img src="images/features4.png" alt="icon"></div>

                                            <div class="text-part">
                                                <a style="color:#6c7a89;" href="<?php echo Yii::app()->createUrl('master/'); ?>">
                                                <h4 class="features-heading">Проверенные мастера</h4>
                                                <p>с рейтингом по отзывам</p>
                                                </a>
                                            </div>

                                        </li>
                                    </ul>

                                </div><!--/header__main__features-->

                            </div><!--/header__main__content-->

                        </div>
                    </div>
                </div>
            </div>
        </div>

<section class="popular">
    <div class="container">
        <div class="row">
            <div class="col">
                <div class="section-heading">
                    <h2 class="h2">Лучшие компании и магазины</h2>
                    <a class="section-more" href="<?php echo Yii::app()->createUrl('company/'); ?>">Больше компаний</a>
                </div>

                 <ul class="popular__box">
                    <?php $this->widget('application.modules.company.widgets.CompanyWidget',
                        ['assets'=>$this->mainAssets, 'uploadsAsset'=>$this->uploadsAsset]);
                    ?>
                </ul>


                <ul class="popular__banners">
                    <li class="banner__cart">
                        <div class="pictured-block">
                            <?php $this->widget('application.modules.banner.widgets.bannerWidget',['position'=>2])?>
                        </div>

                    </li>
                    <li class="banner__cart">
                        <div class="pictured-block">
                           <?php $this->widget('application.modules.banner.widgets.bannerWidget',['position'=>3])?>
                        </div>

                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>

<?php $this->widget('application.modules.homepage.widgets.BestProposalProductWidget',
    ['assets'=>$this->mainAssets, 'uploadsAsset'=>$this->uploadsAsset]);
?>

<section class="services">
    <div class="container">
        <div class="row">
            <div class="col">
            <?php $this->widget('application.modules.homepage.widgets.HomePageMastersWidget',
                ['assets'=>$this->mainAssets, 'uploadsAsset'=>$this->uploadsAsset]);
            ?>
            <div class="container" style="padding: 20px 0 0 0">
                <ul class="popular__banners">
                    <?php //if($this->widget('application.modules.banner.widgets.bannerWidget',['position'=>22])->data):?>
                    <li class="banner__cart">
                        <div class="pictured-block">
                            <?php $this->widget('application.modules.banner.widgets.bannerWidget',['position'=>22])?>
                        </div>

                    </li>
                    <?php //endif; ?>
                    <?php //var_dump($this->widget('application.modules.banner.widgets.bannerWidget',['position'=>22]));?>
                    <li class="banner__cart">
                        <div class="pictured-block">
                           <?php $this->widget('application.modules.banner.widgets.bannerWidget',['position'=>23]); ?>
                        </div>

                    </li>
                    <?php //endif; ?>
                </ul>
            </div>
            <?php $this->widget('application.modules.homepage.widgets.BestProposalServiceWidget',
                ['assets'=>$this->mainAssets, 'uploadsAsset'=>$this->uploadsAsset]);
            ?>
            </div>
        </div>
    </div>
</section>

<section class="articles" style="padding-bottom: 0px;">
    <div class="container">
        <div class="row">
            <div class="col">
                <div class="section-heading">
                    <h2 class="h2"><?php echo Yii::t('title','Статьи'); ?></h2>
                    <a class="section-more" href="<?php echo Yii::app()->createUrl('blogs/'); ?>">Все статьи</a>
                    <?php
                    $this->widget(
                        'application.modules.blog.widgets.LastPostsWidget',
                        ['view' => 'lastposts','limit'=>3]
                    );
                    ?>
                </div>
            </div>
        </div>
    </div>
</section>
<style>
.popular__box .products__box li {
    min-height: 240px;
}    
.popular__box .products__box li .products__cart .cart-bottom {
    height: 75px;
}
.popular__box .products__box li .products__cart .cart-bottom .name {
    height: calc(100% - 36px);
    overflow: hidden;
}
.why-we.section-trans {
    padding-top: 0;
}
.reviews {
    background-color: #f0f2fa;
}    
.rew_auto {
  margin: 0 auto;
  margin-top: 30px;
}
.reviews__title {
  text-align: center;
}
.reviews__title span {
  font-size: 14px;
  display: block;
  margin-bottom: 20px;
}
.testimony-wrap {
    display: -webkit-flex;
    display: -moz-flex;
    display: -ms-flex;
    display: -o-flex;
    display: flex;
    -webkit-flex-direction: column;
    -moz-flex-direction: column;
    -ms-flex-direction: column;
    -o-flex-direction: column;
    flex-direction: column;
    position: relative;
}
.pb-5, .py-5 {
    padding-bottom: 3rem !important;
}
.p-4 {
    padding: 1.5rem !important;
}
.testimony-wrap .user-img {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    position: relative;
    margin-top: -52px;
    margin: 0 auto;
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
.reviews__slider .owl-pagination .owl-page span {
    display: none;
}
.reviews__slider .owl-pagination .owl-page.active , .reviews__slider .owl-pagination .owl-page:focus , .owl-carousel .owl-pagination .owl-page:hover  {
    background: #2a4864;
    border: 1px solid #2a4864;
    outline: none !important;
}
.reviews__slider .owl-pagination .owl-page  {
    width: 10px;
    height: 10px;
    margin: 5px;
    -webkit-transform: rotate(45deg);
    -ms-transform: rotate(45deg);
    transform: rotate(45deg);
    border: 1px solid #ccc;
}
.reviews__slider .owl-pagination {
    text-align: center;
}
.reviews__slider.owl-theme .owl-controls .owl-buttons {
    position: static;
}

.reviews__slider.owl-theme:hover .owl-controls .owl-buttons .owl-prev,
.reviews__slider.owl-theme:focus .owl-controls .owl-buttons .owl-prev {
    left: -20px;
}
.reviews__slider.owl-theme:hover .owl-controls .owl-buttons .owl-next,
.reviews__slider.owl-theme:focus .owl-controls .owl-buttons .owl-next {
    right: -20px;
}
.reviews__slider.owl-theme:hover .owl-controls .owl-buttons .owl-prev,
.reviews__slider.owl-theme:hover .owl-controls .owl-buttons .owl-next,
.reviews__slider.owl-theme:focus .owl-controls .owl-buttons .owl-prev,
.reviews__slider.owl-theme:focus .owl-controls .owl-buttons .owl-next {
    opacity: 1;
}
.reviews__slider button:focus {
  outline: none;
}
.reviews__slider.owl-theme .owl-controls .owl-buttons .owl-prev {
    left: 0;
}
.reviews__slider.owl-theme .owl-controls .owl-buttons .owl-next {
    right: 0;
}
.reviews__slider.owl-theme:hover .owl-controls .owl-buttons .owl-prev span:before,
.reviews__slider.owl-theme:hover .owl-controls .owl-buttons .owl-next span:before,
.reviews__slider.owl-theme:focus .owl-controls .owl-buttons .owl-prev span:before,
.reviews__slider.owl-theme:focus .owl-controls .owl-buttons .owl-next span:before {
    color: #d9d9d9;
}
.reviews__slider.owl-theme .owl-controls .owl-buttons .owl-prev span:before,
.reviews__slider.owl-theme .owl-controls .owl-buttons .owl-next span:before {
    font-size: 40px;
    color: #e6e6e6;
}
.reviews__slider.owl-theme .owl-controls .owl-buttons .owl-prev, 
.reviews__slider.owl-theme .owl-controls .owl-buttons .owl-next {
    position: absolute;
    top: 50%;
    -webkit-transform: translateY(-50%);
    -ms-transform: translateY(-50%);
    transform: translateY(-50%);
    margin-top: -10px;
    background-color: transparent;
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
<section class="reviews">
  <div class="container">
    <div class="row">
      <div class="col-12">
        <div class="reviews__title">
          <h2 class="h2">Отзывы о нашем интернет портале</h2>
          <p>Что говорят наши клиенты</p>
        </div>
      </div>
    </div>  
    <div class="row justify-content-center">
      <div class="col-12 rew">
        <div class="reviews__slider owl-carousel">
          <div class="item">
            <div class="testimony-wrap p-4 pb-5">
                <div class="user-img mb-5" style="background-image: url(images/company2.jpg)">
                  <span class="quote d-flex align-items-center justify-content-center">
                    <i class="fa fa-quote-left"></i>
                  </span>
                </div>
                <div class="text text-center">
                  <p class="mb-5">
                    С компанией "ДомСтрой" сотрудничаем уже не первый год и очень довольны работой их менеджеров. Размещаем более 1000 товарных единиц запорной арматуры. 
                    Работа с ресурсом довольна простая, легко обновить цену и изменить описание товара при необходимости. 
                    Портал с каждым днем развивается и набирает обороты по посещаемости.
                    Рекомендуем как площадку размещения Вашей рекламы!
                  </p>
                  <p class="name">Компания UKSPAR</p>
                  <span class="position">Интернет маркетолог Вячеслав</span>
                </div>
            </div>
          </div>
          <div class="item">
            <div class="testimony-wrap p-4 pb-5">
                <div class="user-img mb-5" style="background-image: url(images/company1.jpg)">
                    <span class="quote d-flex align-items-center justify-content-center">
                        <i class="fa fa-quote-left"></i>
                    </span>
                </div>
                <div class="text text-center my-text">
                  <p class="mb-5">
                   Очень коммуникабельная команда, индивидуальный подход к потребностям заказчика в плане подготовки и изменения дизайна, места  размещения наших баннеров и статей. 
                   Менеджеры постоянно следят за актуальностью наших прайсовых позиций.
                   Особенная программа лояльности к каждому клиенту. 
                   Рекомендуем данный ресурс для качественной рекламы.!
                  </p>
                  <p class="name">Компания Логистикбуд</p>
                  <span class="position">Руководитель компании Анатолий</span>
                </div>
            </div>
          </div>
          <div class="item">
            <div class="testimony-wrap p-4 pb-5">
                <div class="user-img mb-5" style="background-image: url(images/company3.jpg)">
                    <span class="quote d-flex align-items-center justify-content-center">
                      <i class="fa fa-quote-left"></i>
                    </span>
                </div>
                <div class="text text-center">
                    <p class="mb-5">
                      Домстрой стремительно вошел в ряд ведущих строительных порталов Украины и для этого были веские причины.Современный дизайн ,ясное и понятное юзабилити,сайт не перенасыщен информацией,развитие и совершенствование программных возможностей портала .Сотрудники постоянно в контакте и реагируют на наши пожелания и замечания.Взаимодействие в случаях необходимости с программистами.
                      Наше сотрудничество приносит свои плоды. Домстрой это серьезная, перспективная площадка для сотрудничества.Рекомендуем
                    </p>
                    <p class="name">Компания IsoplaatUA</p>
                    <span class="position">Руководитель компании Виктор Чубук</span>
                </div>
            </div>
        </div>
            <div class="item">
            <div class="testimony-wrap p-4 pb-5">
                <div class="user-img mb-5" style="background-image: url(images/company4.jpg)">
                    <span class="quote d-flex align-items-center justify-content-center">
                        <i class="fa fa-quote-left"></i>
                    </span>
                </div>
                <div class="text text-center">
                  <p class="mb-5">
                    Наша компания  выражает благодарность Строительному порталу ДомСтрой за
                    профессиональный подход к продвижению нашей рекламы.Компания разработала современный и удобный сайт ,результатом чего стало увеличение числа клиентов для нас и повышения уровня продаж.За период нашего сотрудничества ДомСтрой зарекомендовал себя как надежный ,стабильный партнер .Желаем компании и ее сотрудникам дальнейшего развития и процветания.
                  </p>
                  <p class="name">Компания ЧП “ШАТО</p>
                  <span class="position">Представитель компании Анна</span>
                </div>
            </div>
          </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<script>

$('.reviews__slider').owlCarousel({
            loop: true,
            items: 2,
            margin: 30,
            stagePadding: 0,
            navigation: true,
            navigationText: ['<span class="fa fa-angle-left">', '<span class="fa fa-angle-right">'],
            autoHeight : true,
            itemsCustom : [
              [0, 1],
              [600, 1],
              [800, 2]
            ]
        });
</script>
<?php $this->widget(
    "application.modules.contentblock.widgets.ContentBlockWidget",
    array("code" => "pochemu-my-blok_Ru"));
?>
<!-- content end-->
<!--dzjvf-->
<div>