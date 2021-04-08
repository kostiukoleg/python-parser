<?php
$this->breadcrumbs = [
    $this->getModule()->getCategory() => [],
    Yii::t('CompanyModule.company', 'Компании') => ['/company/company/index'],
    Yii::t('CompanyModule.company', 'Управление'),
];
$this->pageTitle = Yii::t('CompanyModule.company', 'Компании - управление');
$this->menu = [
    ['icon' => 'fa fa-fw fa-list-alt', 'label' => Yii::t('CompanyModule.company', 'Управление компаниями'), 'url' => ['/company/company/index']],
    ['icon' => 'fa fa-fw fa-plus-square', 'label' => Yii::t('CompanyModule.company', 'Добавить компанию'), 'url' => ['/company/company/create']],
];
?>
<div class="page-header">
    <h1>
        <?=  Yii::t('CompanyModule.company', 'Компании'); ?>
        <small><?=  Yii::t('CompanyModule.company', 'управление'); ?></small>
    </h1>
</div>

<p>
    <a class="btn btn-default btn-sm dropdown-toggle" data-toggle="collapse" data-target="#search-toggle">
        <i class="fa fa-search">&nbsp;</i>
        <?=  Yii::t('CompanyModule.company', 'Поиск компании');?>
        <span class="caret">&nbsp;</span>
    </a>
</p>

<div id="search-toggle" class="collapse out search-form">
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

<br/>

<p> <?=  Yii::t('CompanyModule.company', 'В данном разделе представлены средства управления компаниями'); ?>
</p>

<?php
 $this->widget(
    'yupe\widgets\CustomGridView',
    [
        'id'           => 'company-grid',
        'type'         => 'striped condensed',
        'dataProvider' => $model->search(),
        'filter'       => $model,
        'columns'      => [
            'id',
            'owner_id',
            'is_active',
            'type',
            'experience',
            'product_comments',
            'name',
//            'activities',
//            'delivery',
//            'terms',
//            'additional',
//            'logo',
            [
                'class' => 'yupe\widgets\CustomButtonColumn',
            ],
        ],
    ]
); ?>
