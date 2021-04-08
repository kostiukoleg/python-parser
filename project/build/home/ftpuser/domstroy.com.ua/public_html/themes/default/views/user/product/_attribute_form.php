<?php
/* @var $model Product - передается при рендере из формы редактирования товара */
/* @var $type Type - передается при генерации формы через ajax */
?>
<?php if (!empty($groups)): ?>
    <div class="row">
        <div class="col-sm-12">
            <?php foreach ($groups as $groupName => $items): ?>
                <fieldset style="margin-top: 20px;">
                    <!--<h2><?/*= CHtml::encode($groupName); */?></h2>-->
                    <?php foreach ($items as $attribute): ?>
                        <?php /* @var $attribute Attribute */ ?>
                        <?php $hasError = $model->hasErrors($attribute->name); ?>
                        <div class="row form-group">
                            <div class="col-sm-5">
                                <label for="Attribute_<?= $attribute->name ?>"
                                       class="<?= $hasError ? 'has-error' : null; ?>">
                                    <?= $attribute->title; ?>
                                    <?php if ($attribute->required): ?>
                                        <span class="required">*</span>
                                    <?php endif; ?>
                                    <?php if ($attribute->unit): ?>
                                        <span>(<?= $attribute->unit; ?>)</span>
                                    <?php endif; ?>
                                </label>
                            </div>
                            <div class="col-sm-<?= $attribute->isType(Attribute::TYPE_TEXT) ? 7 : 7; ?> <?= $hasError ? 'has-error' : null; ?>">
                                <?php $htmlOptions = $attribute->isType(Attribute::TYPE_CHECKBOX) || $attribute->isType(Attribute::TYPE_CHECKBOX_LIST) ? [] : ['class' => 'form-control']; ?>
                                <?php if ($attribute->isType(Attribute::TYPE_FILE)): ?>
                                    <?php if ($model->attributeFile($attribute->name)): ?>
                                        <div>
                                            <?= CHtml::link(Yii::t('StoreModule.store', 'Download'),
                                                $model->attributeFile($attribute->name)); ?>
                                            <?= Yii::t('StoreModule.store', 'or'); ?>
                                            <?= CHtml::link(Yii::t('StoreModule.store', 'Delete'), null, [
                                                'class' => 'rm-file-attr',
                                                'data-product' => $model->id,
                                                'data-attribute' => $attribute->id,
                                            ]); ?>
                                        </div>
                                    <?php endif; ?>
                                <?php endif; ?>
                                <?php
                                if ($attribute->isType(Attribute::TYPE_NUMBER)) {
                                    $htmlOptions['step'] = 'any';
                                }
                                if ($attribute->required) {
                                    $htmlOptions['required'] = true;
                                }
                                echo AttributeRender::renderField($attribute, $model->attribute($attribute), null,
                                     $htmlOptions); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </fieldset>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            $('.rm-file-attr').on('click', function (event) {
                event.preventDefault();
                var $this = $(this);
                var product = parseInt($(this).data('product'));
                var attribute = parseInt($(this).data('attribute'));
                $.post('<?= Yii::app()->createUrl('/store/attributeBackend/deleteFile');?>', {
                    'product': product,
                    'attribute': attribute,
                    '<?= Yii::app()->getRequest()->csrfTokenName;?>': '<?= Yii::app()->getRequest()->csrfToken;?>'
                }, function (response) {
                    if (response.result) {
                        $this.parent('div').fadeOut();
                    }
                }, 'json');
            });
        });
    </script>
<?php endif; ?>
