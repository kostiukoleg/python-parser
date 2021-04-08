<?php
/**
 * Отображение для _form:
 *
 *   @category YupeView
 *   @package  yupe
 *   @author   CyberSpidersStudio team <team@asevenstudio.com>
 *   @license  https://github.com/yupe/yupe/blob/master/LICENSE BSD
 *   @link     http://asevenstudio.com
 *
 *   @var $model Banner
 *   @var $form TbActiveForm
 *   @var $this BannerController
 **/
$form = $this->beginWidget(
    'bootstrap.widgets.TbActiveForm', [
        'id'                     => 'banner-form',
        'enableAjaxValidation'   => false,
        'enableClientValidation' => true,
        'htmlOptions'            => ['enctype' => 'multipart/form-data','class' => 'well'],
    ]
);
?>

<div class="alert alert-info">
    <?=  Yii::t('BannerModule.banner', 'Поля, отмеченные'); ?>
    <span class="required">*</span>
    <?=  Yii::t('BannerModule.banner', 'обязательны.'); ?>
</div>

<?=  $form->errorSummary($model); ?>

    <div class='row'>
        <div class="col-sm-7">
            <div class="preview-image-wrapper<?= !$model->isNewRecord && $model->image? '' : ' hidden' ?>">


                <?php
                echo CHtml::image(
                    !$model->isNewRecord && $model->image? $model->getImageUrl(200,200,true) : '#',
                    $model->name,
                    [
                        'class' => 'preview-image img-thumbnail',
                        'style' => !$model->isNewRecord && $model->image? '' : 'display:none'
                    ]
                ); ?>
            </div>
            <?= $form->fileFieldGroup(
                $model,
                'image',
                [
                    'widgetOptions' => [
                        'htmlOptions' => [
                            'onchange' => 'readURL(this);',
                        ],
                    ],
                ]
            ); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-7">
            <?=  $form->dropDownListGroup($model, 'status', [
                'widgetOptions' => [
                    'data' => [
                        1 => 'Включен',
                        0 => 'Выключен'
                    ],
                    'htmlOptions' => [
                        'class' => 'popover-help',
                        'data-original-title' => $model->getAttributeLabel('status'),
                        'data-content' => $model->getAttributeDescription('status')
                    ]
                ]
            ]); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-7">
            <?=  $form->dropDownListGroup($model, 'position', [
                'widgetOptions' => [
                    'data' => [
                        1=>'Позиция 1 - 360x270',
                        2=>'Позиция 2 - 555х200',
                        3=>'Позиция 3 - 555х200',
                        4=>'Банер товара 4 - 260х260',
                        5=>'Банер товара 5 - 260х260',
                        6=>'Позиция 6 - 360x360',
                        7=>'Позиция 7 - 360x360',
                        8=>'Позиция 8 - 600x160',
                        9=>'Позиция 9 - 720x270',
                        10=>'Позиция 10 - 720x270',
                        11=>'Позиция 11 - 720x270',
                        12=>'Позиция 12 - 1000x90',
                        13=>'Позиция 13 - 1000x90',
                        14=>'Банер послуги 14 - 260х260',
                        15=>'Банер послуги 15 - 260х260',
                        16=>'Банер мастера 16 - 260х260',
                        17=>'Банер мастера 17 - 260х260',
                        18=>'Банер компінії 18 - 260х260',
                        19=>'Банер компінії 19 - 260х260',
                        20=>'Баннер головна мобільний 20 - 1000x90',
                        21=>'Баннер другорядні мобільний 21 - 1000x90',
                        22=>'Позиция 22 - 555х200',
                        23=>'Позиция 23 - 555х200',
                    ],
                    'htmlOptions' => [
                        'class' => 'popover-help',
                        'data-original-title' => $model->getAttributeLabel('position'),
                        'data-content' => $model->getAttributeDescription('position')
                    ]
                ]
            ]); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-7">
            <?=  $form->textFieldGroup($model, 'name', [
                'widgetOptions' => [
                    'htmlOptions' => [
                        'class' => 'popover-help',
                        'data-original-title' => $model->getAttributeLabel('name'),
                        'data-content' => $model->getAttributeDescription('name')
                    ]
                ]
            ]); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-7">
            <?=  $form->textAreaGroup($model, 'descritpion', [
                'widgetOptions' => [
                    'htmlOptions' => [
                        'class' => 'popover-help',
                        'data-original-title' => $model->getAttributeLabel('descritpion'),
                        'data-content' => $model->getAttributeDescription('descritpion')
                    ]
                ]
            ]); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-7">
            <?=  $form->textFieldGroup($model, 'url', [
                'widgetOptions' => [
                    'htmlOptions' => [
                        'class' => 'popover-help',
                        'data-original-title' => $model->getAttributeLabel('url'),
                        'data-content' => $model->getAttributeDescription('url')
                    ]
                ]
            ]); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-7">
            <?=  $form->dropDownListGroup($model, 'check_type', [
                'widgetOptions' => [
                    'data' => [
                        1 => 'Время',
                        2 => 'Показы',
                        3 => 'Переходы'
                    ],
                    'htmlOptions' => [
                        'class' => 'popover-help',
                        'data-original-title' => $model->getAttributeLabel('check_type'),
                        'data-content' => $model->getAttributeDescription('check_type')
                    ]
                ]
            ]); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-7">
            <?=  $form->select2Group($model, 'company_id', [
                'widgetOptions' => [
                    'data' => Company::model()->getFormattedById(),
                    'htmlOptions' => [
                        'empty' => '---',
                        'class' => 'popover-help',
                        'data-original-title' => $model->getAttributeLabel('company_id'),
                        'data-content' => $model->getAttributeDescription('company_id')
                    ]
                ]
            ]); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-7">
            <?=  $form->textFieldGroup($model, 'priority', [
                'widgetOptions' => [
                    'htmlOptions' => [
                        'class' => 'popover-help',
                        'data-original-title' => $model->getAttributeLabel('priority'),
                        'data-content' => $model->getAttributeDescription('priority')
                    ]
                ]
            ]); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-7">
            <?=  $form->dateTimePickerGroup($model,'start_time', [
            'widgetOptions' => [
                'options' => [],
                'htmlOptions'=>[]
            ],
            'prepend'=>'<i class="fa fa-calendar"></i>'
        ]); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-7">
            <?=  $form->dateTimePickerGroup($model,'end_time', [
            'widgetOptions' => [
                'options' => [],
                'htmlOptions'=>[]
            ],
            'prepend'=>'<i class="fa fa-calendar"></i>'
        ]); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-7">
            <?=  $form->textFieldGroup($model, 'to_show', [
                'widgetOptions' => [
                    'htmlOptions' => [
                        'class' => 'popover-help',
                        'data-original-title' => $model->getAttributeLabel('to_show'),
                        'data-content' => $model->getAttributeDescription('to_show')
                    ]
                ]
            ]); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-7">
            <?=  $form->textFieldGroup($model, 'to_click', [
                'widgetOptions' => [
                    'htmlOptions' => [
                        'class' => 'popover-help',
                        'data-original-title' => $model->getAttributeLabel('to_click'),
                        'data-content' => $model->getAttributeDescription('to_click')
                    ]
                ]
            ]); ?>
        </div>
    </div>
    <?php  if ($model->id): ?>
        <div class="row">
            <div class="col-sm-2">
                <?php
                    echo '<div  class="text-center">';
                    echo $form->labelEx($model, 'showed',[
                        'class' => 'inline-label'
                    ]);
                    echo '</div>';
                    $showed = Yii::app()->db->createCommand('SELECT SUM(showed) FROM main_banner_views WHERE banner_id='.$model->id)->queryRow();
                    $showed = $showed['SUM(showed)'];
                    if ($showed) {
                        echo '<p class="text-center">'.$showed.'</p>';
                    } else {
                        echo '<p class="text-center">'. 0 .'</p>';
                    }
                ?>
            </div>

            <div class="col-sm-2">
                <?php
                    echo '<div class="text-center">';
                    echo $form->labelEx($model, 'redirected',[
                        'class' => 'inline-label"'
                    ]);
                    echo '</div>';
                    $redirected = Yii::app()->db->createCommand('SELECT SUM(redirected) FROM main_banner_views WHERE banner_id='.$model->id)->queryRow();
                    $redirected = $redirected['SUM(redirected)'];
                    if ($redirected) {
                        echo '<p class="text-center">'.$redirected.'</p>';
                    } else {
                        echo '<p class="text-center">'. 0 .'</p>';
                    }
                ?>
            </div>
        </div>
    <?php  endif; ?>
    <?php $this->widget(
        'bootstrap.widgets.TbButton', [
            'buttonType' => 'submit',
            'context'    => 'primary',
            'label'      => Yii::t('BannerModule.banner', 'Сохранить Banner и продолжить'),
        ]
    ); ?>
    <?php $this->widget(
        'bootstrap.widgets.TbButton', [
            'buttonType' => 'submit',
            'htmlOptions'=> ['name' => 'submit-type', 'value' => 'index'],
            'label'      => Yii::t('BannerModule.banner', 'Сохранить Banner и закрыть'),
        ]
    ); ?>
<?php $this->endWidget(); ?>