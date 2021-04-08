<?php
/**
 * @var $this ProductBackendController
 * @var $model Product
 */

$this->layout = '//layouts/user';

$this->breadcrumbs = [
    Yii::t('UserModule.user', 'Кабинет пользователя') => ['/cabinet'],
    Yii::t('StoreModule.store', 'Products') => ['/user/product'],
    $model->name,
    Yii::t('StoreModule.store', 'Edition'),
];

$this->pageTitle = Yii::t('StoreModule.store', 'Products - edition');
Yii::app()->getClientScript()->registerCssFile('/css/custom.css');
?>
<div class="page-header">

</div>

<?= $this->renderPartial('_form', [
    'model' => $model,
    'searchModel' => $searchModel,
    'imageGroup' => $imageGroup,
    'measures' => $measures,
    'categories_list1' => $categories_list1,
    'categories_major' => $categories_major,
    'categories_finish' => $categories_finish,
    'count_categories' => $count_categories,
    'categories1' => $categories1,
    'categories2' => $categories2,
    'categories3' => $categories3,
    'categories_list2' => $categories_list2,
    'categories_list3' => $categories_list3,
    'categories_list4' => $categories_list4,
    'attribute' => $attribute,
    'attr_f' => $attr_f,
]); ?>
