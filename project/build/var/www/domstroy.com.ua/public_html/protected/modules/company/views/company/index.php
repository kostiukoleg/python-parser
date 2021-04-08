<?php
$this->breadcrumbs = [
    $this->getModule()->getCategory() => [],
    Yii::t('CompanyModule.company', 'Компании') => ['/company/company/index'],
    Yii::t('CompanyModule.company', 'Поиск'),
];
$this->pageTitle = Yii::t('CompanyModule.company', 'Строительные компании: большой каталог на портале ДомСтрой');
//$this->pageTitle = Yii::t('Строительные компании: большой каталог на портале ДомСтрой');
$this->description = Yii::t('site','Строительные компании: крупнейшая база проверенных производителей и поставщиков. Удобный поиск для клиентов и эффективная реклама для компаний | ДомСтрой.');
$this->keywords = Yii::t('site','строительные компании');
$this->title = $this->pageTitle;
$this->canonicalLink = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].'/company';
Yii::app()->getClientScript()->registerCssFile(Yii::app()->createUrl('/css/custom.css'));

?>
<section class="section-trans">
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <h1>Каталог строительных компаний и магазинов</h1>
            </div>
        </div>
        <div class="row materials materials-result">
            <div class="col-sm-3">
                <p><span><?=Yii::t('CompanyModule.company', 'find')?></span>  компаний: <?=$dataProvider->totalItemCount;?></p>
            </div>
            <div class="col-sm-9 clearfix">
                <div class="pull-right">
                    <?=CHtml::htmlButton('',[
                        'id'=>'vertical',
                        'class'=>'btn glyphicon glyphicon-th-large'
                    ])?>
                    <?=CHtml::htmlButton('',[
                        'id'=>'horizontal',
                        'class'=>'btn glyphicon glyphicon-align-justify active'
                    ])?>
                </div>
            </div>
            <div class="col-sm-12">
                <div></div>
            </div>
        </div>
        <div class="row">
            <aside class="col-sm-3">
                <p class="visible-xs">
                    <a class="btn btn-default btn-sm dropdown-toggle" data-toggle="collapse" data-target="#search-toggle">
                        <i class="fa fa-search">&nbsp;</i>
                        <?=  Yii::t('CompanyModule.company', 'Поиск компании');?>
                        <span class="caret">&nbsp;</span>
                    </a>
                </p>
                <div id="search-toggle" class="collapse in search-form">
                    <?php Yii::app()->clientScript->registerScript('search', "
                        $('.search-form form').submit(function () {
                            $.fn.yiiGridView.update('company-grid', {
                                data: $(this).serialize()
                            });

                            return false;
                        });
                    ");
                    $this->renderPartial('_search', ['model' => $model]);
                    ?>
                </div>
                <div class="add-img">
		    <?php $this->widget('application.modules.banner.widgets.bannerWidget',['position'=>18])?>
                    <?php $this->widget('application.modules.banner.widgets.bannerWidget',['position'=>19])?>
                    <!--<a target="_blank" href="https://isoplaat.org.ua/"><img class="img-responsive" src="/images/aviso.jpg" alt=" "></a>
                    <a href="<?=Yii::app()->createUrl('/registration')?>"><img class="img-responsive" src="<?=Yii::app()->getTheme()->getAssetsUrl()?>/images/ban-reg.png" alt=" "></a>-->
                </div>
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

                <div class="services company clearfix">
                    <?php $this->widget(
                        'bootstrap.widgets.TbListView',
                        [
                            'dataProvider' => $dataProvider,
                            'itemView' => '_item',
                            'summaryText' => '',
                            'cssFile' => false,
                            'enableHistory' => true,
                            'ajaxUpdate' => true,
                            'afterAjaxUpdate' => 'function(){window.scrollTo(400, 0);}'
                        ]
                    ); ?>
                </div>
            </div>
        </div>
    </div>
</section>
