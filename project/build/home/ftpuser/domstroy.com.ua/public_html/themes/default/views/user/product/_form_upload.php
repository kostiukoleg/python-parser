<?php
$form = $this->beginWidget(
    '\yupe\widgets\ActiveForm',
    [
        'id' => 'import-form',
        'enableAjaxValidation' => false,
        'enableClientValidation' => true,
        'type' => 'vertical',
        'htmlOptions' => ['enctype' => 'multipart/form-data', 'class' => ''],
        'action' => Yii::app()->createUrl('/user/product/importxls'),
        'clientOptions' => [
            'validateOnSubmit' => true,
        ],
    ]
); ?>

<input name="importfile" id="importfile" type="file" />
<?php $this->widget(
    'bootstrap.widgets.TbButton',
    [
        'id' => 'product-mass-import',
        'buttonType' => 'submit',
        'label' => Yii::t(
            'StoreModule.store',
            'Import XLSX'
        ),
    ]
); ?>

<?php $this->endWidget(); ?>
