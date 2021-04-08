<?php
$this->breadcrumbs = [
    $this->getModule()->getCategory() => [],
    Yii::t('CompanyModule.company', 'Компании') => ['/company/company/index'],
    'Статистика просмотров страниц компании'
];
$this->pageTitle = 'Статистика компании - ' . $company->name;
$this->title = $this->pageTitle;
?>
<div class="page-header">
    <h1>Статистика компании "<?php echo $company->name; ?>"</h1>
</div>

<?php $form = $this->beginWidget(
'bootstrap.widgets.TbActiveForm', [
'method'      => 'get',
'type'        => 'vertical',
'htmlOptions' => ['class' => ''],
]
);

?>

<fieldset>
    <div class="row">
        <div class="col-sm-4">
            <?=  $form->datePickerGroup(
                $statSearch,
                'date_from',
                [
                    'widgetOptions' => [
                        'options'     => [
                            'format'    => 'dd-mm-yyyy',
                            'weekStart' => 1,
                            'startDate' => '24-12-2017',      // minimum date
                            // 'endDate' => $endDate,          // maximum date
                            'autoclose' => true,
                        ],
                        'htmlOptions' => [
                            'class'               => 'popover-help',
                            'data-original-title' => 'Период от',
                            'data-content'        => 'Период от',
                        ],
                    ],
                    'prepend'       => '<i class="fa fa-calendar"></i>',
                ]
            ); ?>
        </div>
        <div class="col-sm-4">
            <?=  $form->datePickerGroup(
                $statSearch,
                'date_to',
                [
                    'widgetOptions' => [
                        'options'     => [
                            'format'    => 'dd-mm-yyyy',
                            'weekStart' => 1,
                            // 'startDate' => $startDate,      // minimum date
                            // 'endDate' => $endDate,          // maximum date
                            'autoclose' => true,
                        ],
                        'htmlOptions' => [
                            'class'               => 'popover-help',
                            'data-original-title' => 'Период до',
                            'data-content'        => 'Период до',
                        ],
                    ],
                    'prepend'       => '<i class="fa fa-calendar"></i>',
                ]
            ); ?>
        </div>
        <div class="col-sm-4" style="padding-top: 25px;">
            <?php $this->widget(
                'bootstrap.widgets.TbButton', [
                    'context'     => 'primary',
                    'encodeLabel' => false,
                    'buttonType'  => 'submit',
                    'label'       => '<i class="fa fa-search">&nbsp;</i> ' . Yii::t('CompanyModule.company', 'Искать'),
                ]
            ); ?>
        </div>
    </div>
</fieldset>

<?php $this->endWidget(); ?>

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
            'name' => 'Просмотров телефонов',
            'value' => function ($data) {
                return Viewed::countDayCompanyPhoneViews($data['company_id'], $data['date']);
            },
            'footer' => $dataProvider->getTotalItemCount() > 0 ? Viewed::countDayCompanyPhoneViews($company->id, [$statSearch->date_from, $statSearch->date_to]) : '',
        ],
        [
            'name' => 'Удачных звонков',
            'value' => function ($data) {
                return Viewed::countDayCompanyPhoneSuccesViews($data['company_id'], $data['date']);
            },
            'footer' => $dataProvider->getTotalItemCount() > 0 ? Viewed::countDayCompanyPhoneSuccesViews($company->id, [$statSearch->date_from, $statSearch->date_to]) : '',
        ],
        [
            'name' => 'Неудачных звонков',
            'value' => function ($data) {
                return Viewed::countDayCompanyPhoneUnsuccesViews($data['company_id'], $data['date']);
            },
            'footer' => $dataProvider->getTotalItemCount() > 0 ? Viewed::countDayCompanyPhoneUnsuccesViews($company->id, [$statSearch->date_from, $statSearch->date_to]) : '',
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
            'name' => 'Обратний звонок',
            'value' => function ($data) {
                return CompanyViewed::countDayCallback($data['company_id'], $data['date']);
            },
            'footer' => $dataProvider->getTotalItemCount() > 0 ? CompanyViewed::countDayCallback($company->id, [$statSearch->date_from, $statSearch->date_to]) : '',
        ],
        [
            'name' => 'Переходов на сайт',
            'value' => function ($data) {
                return CompanyViewed::countDayHits($data['company_id'], $data['date']);
            },
            'footer' => $dataProvider->getTotalItemCount() > 0 ? CompanyViewed::countDayHits($company->id, [$statSearch->date_from, $statSearch->date_to]) : '',
        ],
        [
            'name' => 'Переходов на Viber',
            'value' => function ($data) {
                return CompanyViewed::countDayViber($data['company_id'], $data['date']);
            },
            'footer' => $dataProvider->getTotalItemCount() > 0 ? CompanyViewed::countDayViber($company->id, [$statSearch->date_from, $statSearch->date_to]) : '',
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
<script>
    $('.col-sm-12.col-md-9.col-lg-10').removeClass('col-md-9').removeClass('col-lg-10');
</script>
