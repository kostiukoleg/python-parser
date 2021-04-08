<?php
/**
 * @var Callback $model
 * @var string $phoneMask
 * @var TbActiveForm $form
 */
?>

<div class="row">
    <div class="col-sm-12">
        <button class="btn button-green" style="margin:0;" data-toggle="modal" data-target="#callbackModal">
            <i class="fa fa-fw fa-phone"></i>
            <?= Yii::t('CallbackModule.callback', 'Request a call back') ?>
        </button>
    </div>
</div>

<div id="callbackModal" class="modal fade" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="<?= Yii::t('CallbackModule.callback', 'Close') ?>">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"><?= Yii::t('CallbackModule.callback', 'Callback') ?></h4>
            </div>
            <p style="padding: 0 20px">Оставьте свои контактные данные и компания с вами свяжется в ближайшее время</p>
            <?php $form = $this->beginWidget(
                'bootstrap.widgets.TbActiveForm',
                [
                    'id' => 'callback-form',
                    'type' => 'vertical',
                    'action' => Yii::app()->createUrl('/callback/callback/send'),
                    'enableClientValidation' => true,
                    'clientOptions' => [
                        'validateOnSubmit' => true,
                        'afterValidate' => 'js:callbackSendForm'
                    ],
                ]
            ); ?>

            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-2"></div>
                    <div class="col-lg-8">
                        <?= $form->errorSummary($model); ?>

                        <div class="row">
                            <div class="col-lg-8">
                                <?= $form->textFieldGroup($model, 'name'); ?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-8">
                                <?= $form->maskedTextFieldGroup($model, 'phone', [
                                    'widgetOptions' => [
                                        'mask' => $phoneMask,
                                    ]
                                ]); ?>
                            </div>

                            <div class="col-lg-4">
                                <?= $form->maskedTextFieldGroup($model, 'time', [
                                    'widgetOptions' => [
                                        'mask' => 'H9:M9',
                                        'charMap' => [
                                            'H' => '[0-2]',
                                            'M' => '[0-5]',
                                            '9' => '[0-9]'
                                        ]
                                    ]
                                ]); ?>
                            </div>
                        </div>
			<?= $form->hiddenField($model,'url',array('value'=>Yii::app()->getBaseUrl(true).Yii::app()->request->url)); ?>
			<?= $form->hiddenField($model,'company_id',array('value'=>$companyId)); ?>
			<?php 
			if($userEmail){
			   echo $form->hiddenField($model,'user_email',array('value'=>$userEmail));
			}	 
			?>
                    </div>
                    <div class="col-lg-2"></div>
                </div>
            </div>
            <div class="modal-footer">
               <button id="callback-link" type="button" class="btn btn-default" data-dismiss="modal" style="visibility:hidden;position:absolute;">
                    <?= Yii::t('CallbackModule.callback', 'Close') ?>
                </button>
                <button type="submit" class="btn btn-success" id="callback-send" style="margin: 0 auto">
                    <?= Yii::t('CallbackModule.callback', 'Send request') ?>
                </button>
            </div>
            <?php $this->endWidget(); ?>
        </div>
    </div>
</div>
<div id="popupmodal" class="modal" role="dialog">
	    <div class="modal-dialog" role="document">
            	<div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button><br>
      </div>
      			<div id="notifications" class="modal-body"></div>
		</div>
	    </div>
</div>
