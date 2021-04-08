<?php
/**
 * @var $this StatisticsController
 * @var $model CompanyViewed
 */

$this->layout = '//layouts/user';

$this->breadcrumbs = [
    Yii::t('UserModule.user', 'Кабинет пользователя') => ['/cabinet'],
    'Статистика компании "' . $company->name . '"',
];

$this->pageTitle = 'Статистика компании "' . $company->name . '"';

Yii::app()->getClientScript()->registerCssFile(Yii::app()->createUrl('/css/user_product.css'));

$plan = Company::model()->find(['condition' => 'owner_id = '.Yii::app()->getUser()->id])->plan;

?>

<div class="page-header">
    <h1>Статистика компании "<?php echo $company->name; ?>"</h1>
</div>
<div class="search-form" style="padding: 20px; background-color: rgba(240, 242, 250, 0.8);">
    <div class="cabinet-product-search">
        <?php $this->renderPartial('_search', [
            'company' => $company,
            'statSearch' => $statSearch,
        ]); ?>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <div class="sorter">
            Выбрать: <ul>
                <li><a href="/user/statistics?StatSearch%5Bdate_from%5D=<?php echo date('d-m-Y'); ?>&StatSearch%5Bdate_to%5D=<?php echo date('d-m-Y'); ?>&yt0=">За день | </a></li>
                <li><a href="/user/statistics?StatSearch%5Bdate_from%5D=<?php echo date('d-m-Y', strtotime('monday this week')); ?>&StatSearch%5Bdate_to%5D=<?php echo date('d-m-Y', strtotime('sunday this week')); ?>&yt0=">За неделю | </a></li>
                <li><a href="/user/statistics?StatSearch%5Bdate_from%5D=<?php echo date('d-m-Y', strtotime('first day of this month')); ?>&StatSearch%5Bdate_to%5D=<?php echo date('d-m-Y', strtotime('last day of this month')); ?>&yt0=">За месяц</a></li>
            </ul>
        </div>
    </div>
</div>
<?php $this->widget('zii.widgets.grid.CGridView', [
    'id' => 'user-orders',
    'dataProvider' => $dataProvider,
    'columns' => [
        [
            'name' => 'Дата',
            'value' => function ($data) {
                return $data['date'];
            },
            'footer' => $dataProvider->getTotalItemCount() > 0 ? 'ВСЕГО' : '',
        ],
        [
            'name' => 'Просмотров страницы компании',
            'value' => function ($data) {
                return Viewed::countDayCompanyPageViews($data['company_id'], $data['date']);
            },
            'footer' => $dataProvider->getTotalItemCount() > 0 ? Viewed::countDayCompanyPageViews($company->id, [$statSearch->date_from, $statSearch->date_to]) : '',
        ],
        [
            'name' => 'Просмотров телефонов компании (функционал работает с 12:00 05.02.2018)',
            'value' => function ($data) {
                return Viewed::countDayCompanyPhoneViews($data['company_id'], $data['date']);
            },
            'footer' => $dataProvider->getTotalItemCount() > 0 ? Viewed::countDayCompanyPhoneViews($company->id, [$statSearch->date_from, $statSearch->date_to]) : '',
        ],
        [
            'name' => 'Просмотров объявлений',
            'value' => function ($data) {
                return Viewed::countDayCompanyViews($data['company_id'], $data['date']);
            },
            'footer' => $dataProvider->getTotalItemCount() > 0 ? Viewed::countDayCompanyViews($company->id, [$statSearch->date_from, $statSearch->date_to]) : '',
        ],
        [
            'name' => 'Заказов',
            'value' => function ($data) {
                return StoreSorder::countBySeller($data['company_id'], $data['date']);
            },
            'footer' => $dataProvider->getTotalItemCount() > 0 ? StoreSorder::countBySeller($company->id, [$statSearch->date_from, $statSearch->date_to]) : '',
        ],
        [
            'name' => 'Переходов на сайт',
            'value' => function ($data) {
                return CompanyViewed::countDayHits($data['company_id'], $data['date']);
            },
            'footer' => $dataProvider->getTotalItemCount() > 0 ? CompanyViewed::countDayHits($company->id, [$statSearch->date_from, $statSearch->date_to]) : '',
        ],
    ],
]); ?>
<style>
    #user-orders td {
        text-align: center;
    }
    #user-orders tfoot {
        display: table-row-group;
        background: #F8F8F8;
    }
    .sorter {
        margin-top: 20px;
        text-align: right;
    }
    .sorter ul,
    .sorter li{
        display: inline-block;
    }
</style>