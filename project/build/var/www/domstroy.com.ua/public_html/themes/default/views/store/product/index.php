<?php
$mainAssets = Yii::app()->getTheme()->getAssetsUrl();
Yii::app()->getClientScript()->registerCssFile($mainAssets . '/css/store-frontend.css');
Yii::app()->getClientScript()->registerScriptFile($mainAssets . '/js/store.js');

/* @var $category StoreCategory */
$company = NULL;
$this->breadcrumbs = [Yii::t("site", "Товары")];
$this->pageTitle = Yii::t('CompanyModule.company', 'Купить строительные материалы: всё  для ремонта и отделки');
$this->description = Yii::t('site','Строительные материалы: всё для ремонта и отделки, большой ассортимент. Выбор поставщиков, выгодные цены, отзывы, доставка по Украине | портал ДомСтрой.');
$this->keywords = Yii::t('site','строительные товары');
if (Yii::app()->getRequest()->getParam('company') !== null) {
    $company = Company::model()->findByPk(Yii::app()->getRequest()->getParam('company'));
    if ($company) {
        $city = CompanyLocale::model()->findByPk($company->locale_c);
        $this->pageTitle = 'Все товары компании '.$company->name.(($city) ? ' ('.$city->title.')' : '').' - Стройпортал';
        $this->description = 'Весь ассортимент товаров '.$company->name.(($city) ? ' ('.$city->title.')' : '').' для ремонта и строительства. Выбирайте лучшее!';
        $this->keywords = 'Товары компании ' . $company->name;
        $this->canonicalLink = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].'/product?company='.Yii::app()->getRequest()->getParam('company');
    }
} else {
    $this->canonicalLink = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].'/product';
}
$this->title = $this->pageTitle;
$model = new Company();
$model->unsetAttributes();
if (Yii::app()->getRequest()->getParam('Company') !== null) {
    $model->setAttributes(Yii::app()->getRequest()->getParam('Company'));
}
?>
<section class="materials section-color" xmlns="http://www.w3.org/1999/xhtml">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <header>
                    <h1>
                    <?php
                        if ($company) {
                            echo 'Товары компании ' . $company->name;
                        } else {
                            echo Yii::t("site", "Товары");
                        }
                    ?>
                    </h1>
                </header>
            </div>
        </div>
    <div class="row materials materials-result">
        <div class="col-sm-12">
            <div></div>
        </div>
    </div>
        <div class="row">
            <aside class="col-sm-3">
            <?php if ($company) { ?>
                <form class="form" method="get">
                <input type="hidden" name="company" value="<?php echo $company->id; ?>">
                <div id="add-toggle" class="add-form" style="text-align: center;">
                    <span style="font-size:18px; color:#244A64;font-weight: bold;">Уточнить поиск товаров компании</span></br></br><span style="font-size:18px; color:#244A64;"><?php echo $company->name; ?></span>
                    <?php if($cat == 1){ ?>
                        <div style="margin-top:20px;padding-top: 10px;padding-bottom: 10px;">
                            <div style="background: -webkit-linear-gradient(top, hsl(0, 4%, 78%), hsl(0, 0%, 98%) 65%, hsla(0, 4%, 78%, 1)) !important;padding-top: 10px;padding-bottom: 10px;">
                                <span style="color:#000;">Выберите категорию<span>
                            </div>
                            <select style="margin-top:10px;" name="category" class="form-control" id="Product_sup_ty">
                                <option value="0">--Сделайте выбор--</option>
                                <?php foreach($category_list as $key => $value){ ?>
                                    <?php if($value['sup_type'] == 1){ ?>
                                    <option <?php if($key == $category_search){?>selected<?php }?> value="<?php echo $key; ?>"><?php echo $value['category']; ?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                        </div>
                    <?php } ?>
                </div>

                <div id="add-toggle" class="add-form" style="text-align: center;">
                    <div style="background: -webkit-linear-gradient(top, hsl(0, 4%, 78%), hsl(0, 0%, 98%) 65%, hsla(0, 4%, 78%, 1)) !important;padding-top: 10px;padding-bottom: 10px; margin-bottom: 10px;">
                        <span style="color:#000;">Диапазон цен<span>
                    </div>
                    <div class="col-xs-6">
                        <input class="form-control" placeholder="от" type="number" name="price_from" id="price_from" value="<?php echo $price_min; ?>">
                    </div>
                    <div class="col-xs-6">
                        <input class="form-control" placeholder="до" type="number" name="price_to" id="price_to" value="<?php echo $price_max; ?>">
                    </div>
                </div>

                <button style="margin-top:60px;" class="btn btn-primary" type="submit"><i class="fa fa-search">&nbsp;</i>Уточнить поиск</button>
                </form>
            <?php } else { ?>


                <p class="visible-xs">
                    <a class="btn btn-default btn-sm dropdown-toggle" data-toggle="collapse" data-target="#add-toggle">
                        <i class="glyphicon glyphicon-plus">&nbsp;</i>
                        <!--<?//= Yii::t('StoreModule.store', 'Add') ?>-->
                        Уточнить поиск
                    </a>
                </p>

                <div id="add-toggle" class="collapse in add-form">

                    <?php
                    $form = $this->beginWidget(
                        'bootstrap.widgets.TbActiveForm', [
                            'action'      => Yii::app()->createUrl($this->route),
                            'method'      => 'get',
                            'type'        => 'vertical',
                            'htmlOptions' => ['class' => ''],
                        ]
                    );
                    ?>

                    <div>
                        <?php $this->widget('application.modules.store.widgets.filters.CategoryFilterWidget',['exclude'=>[6,7]]); ?>
                    </div>
                    <div class="hidden">
                        <?php $this->widget('application.modules.store.widgets.filters.QFilterWidget'); ?>
                    </div>
                    <div>
                        <?php $this->widget('application.modules.store.widgets.filters.PriceFilterWidget'); ?>
                    </div>
                    <?php $this->widget('application.modules.company.widgets.CompanyLocaleWidget',['company' => $model, 'form' => $form]); ?>

                    <?php $this->widget(
                        'bootstrap.widgets.TbButton', [
                            'context'     => 'primary',
                            'encodeLabel' => false,
                            'buttonType'  => 'submit',
                            'label'       => '<i class="fa fa-search">&nbsp;</i> ' . Yii::t('CompanyModule.company', 'Искать товары'),
                        ]
                    ); ?>
                    <?php $this->endWidget(); ?>
                    <?php if(Yii::app()->getRequest()->getQuery('category') !== null){
                        $data = StoreCategory::model()->getCategoryById(Yii::app()->getRequest()->getQuery('category'));
                        if($data[0]["parent_id"] !== null ){ ?>
                        <div class="col-md-12 col-sm-6 col-xs-12">
                            <h3>Смотрите также</h3>
                            <div>
                                <?php
                                $value = StoreCategory::model()->getCategoryData($data[0]['parent_id']);
                                foreach ($value as $v) {
                                    if($data[0]['id'] !== $v['id']){
                                        ?>
                                        <a href="/<?php echo $v['slug']; ?>"><?php echo $v['name']; ?></a>
                                    <?php }
                                } ?>
                            </div>
                        </div>
                        <?php }
                    } ?>
		            <div class="add-img">
                        <?php $this->widget('application.modules.banner.widgets.bannerWidget',['position'=>4])?>
                        <?php $this->widget('application.modules.banner.widgets.bannerWidget',['position'=>5])?>
                    </div>
                    <div id="riainfo_cf56593c9455987ce33ad0c850642fa4"></div>
                    <script src="https://cobrand.ria.com/js/ria_informer.js?riacode=cf56593c9455987ce33ad0c850642fa4" ></script>

                </div>

            <?php } ?>
                    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
                    <!-- товар медий -->
                    <ins class="adsbygoogle"
                        style="display:block"
                        data-ad-client="ca-pub-6474835912470070"
                        data-ad-slot="9992023016"
                        data-ad-format="auto"
                        data-full-width-responsive="true">
                    </ins>
                    <script>
                        (adsbygoogle = window.adsbygoogle || []).push({});
                    </script>
            </aside>


            <div class="col-sm-9">
                <section>
                    <div class="grid">
                        <?php $this->widget(
                            'bootstrap.widgets.TbListView',
                            [
                                'dataProvider' => $dataProvider,
                                'itemView' => '_item',
                                'summaryText' => '
                                        <div class="col-sm-2 clearfix hidden-xs hidden-sm">
                                            <div class="pull-left">
                                                <button id="vertical" class="vertical-switch btn glyphicon glyphicon-th-large"></button>
                                                <button id="horizontal" class="horizontal-switch btn glyphicon glyphicon-align-justify active"></button>
                                            </div>
                                        </div>
                                        <div class="col-sm-3 text-left"><p><span>'.Yii::t('CompanyModule.company', 'find').'</span> : '.$dataProvider->totalItemCount.'</p></div>',
                                'enableHistory' => true,
                                'cssFile' => false,
                                'itemsCssClass' => 'row items',
                                'sorterCssClass' => 'sorter row',
                                'sortableAttributes' => [
                                    'name',
                                    'price',
                                    //'is_top'
                                ],
                                'ajaxUpdate' => true,
                                'afterAjaxUpdate' => 'function(){window.scrollTo(400, 0);}'
                            ]
                        ); ?>
                    </div>
                </section>
            </div>
        </div>
    </div>
</section>
<section class="section-color">
<?php 
if($company){
$this->widget('application.modules.company.widgets.AnotherCompanyProductsWidget',
[
  'company'=>$company->id,
  'type' => 2,
  'text' => Yii::t('site','service'),
]);
}
?>
</section>