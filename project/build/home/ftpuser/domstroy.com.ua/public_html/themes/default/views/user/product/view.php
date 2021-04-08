<?php
/**
 * @var $this ProductBackendController
 * @var $model Product
 */

$this->layout = '//layouts/user';

$this->breadcrumbs = [
    Yii::t('StoreModule.store', 'Products') => ['/user/product/index'],
    $model->name,
];

$this->pageTitle = Yii::t('StoreModule.store', 'Products - view');

/*$this->menu = [

    ['label' => Yii::t('StoreModule.store', 'Product') . ' «' . mb_substr($model->name, 0, 32) . '»'],
    [
        'icon' => 'fa fa-fw fa-pencil',
        'label' => Yii::t('StoreModule.store', 'Update product'),
        'url' => [
            '/user/product/update',
            'id' => $model->id
        ]
    ],
    [
        'icon' => 'fa fa-fw fa-eye',
        'label' => Yii::t('StoreModule.store', 'View product'),
        'url' => [
            '/user/product/view',
            'id' => $model->id
        ]
    ],
    [
        'icon' => 'fa fa-fw fa-trash-o',
        'label' => Yii::t('StoreModule.store', 'Delete product'),
        'url' => '#',
        'linkOptions' => [
            'submit' => ['/user/product/delete', 'id' => $model->id],
            'params' => [Yii::app()->getRequest()->csrfTokenName => Yii::app()->getRequest()->csrfToken],
            'confirm' => Yii::t('StoreModule.store', 'Do you really want to remove product?'),
            'csrf' => true,
        ]
    ],
];*/
?>
<div class="page-header">
    <h1>
        <?= Yii::t('StoreModule.store', 'Viewing product'); ?><br/>
        <small>&laquo;<?= $model->name; ?>&raquo;</small>
    </h1>
</div>
<?php $this->widget(
    'bootstrap.widgets.TbDetailView',
    [
        'data' => $model,
        'attributes' => [
            'id',
            [
                'name' => 'type_id',
                'value' => function($model) {
                        return is_null($model->type) ? '---' : $model->type->name;
                    },
            ],
            [
                'name' => 'producer_id',
                'value' => function($model) {
                        return is_null($model->producer) ? '---' : $model->producer->name;
                    },
            ],
            'name',
            'price',
            'sku',
            [
                'name' => 'description',
                'type' => 'raw'
            ],
            'slug',
            [
                'name' => 'data',
                'type' => 'raw'
            ],
            [
                'name' => 'is_special',
                'value' => $model->getSpecial(),
            ],
            [
                'name' => 'status',
                'value' => $model->getStatusTitle(),
            ],
            [
                'name' => 'create_time',
                'value' => Yii::app()->getDateFormatter()->formatDateTime($model->create_time, "short", "short"),
            ],
            [
                'name' => 'update_time',
                'value' => Yii::app()->getDateFormatter()->formatDateTime($model->update_time, "short", "short"),
            ],
            'length',
            'width',
            'height',
            'weight',
            'quantity',
        ],
    ]
); ?>