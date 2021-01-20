<?php 
    $sql = "SELECT * FROM `".PREFIX."page` WHERE title='consultation'";
    $res = DB::query($sql);
    while ($row = DB::fetchRow($res)) { ?>
<div id="block37">
    <div class="container-consul-bg">
        <div class="consult-col-1">
            <div class="cont-tittle-cons">
                <div class="tittle-consult"><?php echo html_entity_decode($row[6]); ?></div>
                <div class="desk-consult"><?php echo html_entity_decode($row[8]); ?></div>
            </div>
        </div>
        <div class="consult-col-2">
            <div class="form-b24-bg">
                <div class="feedback-form-wrapper">
                <?php if($data['dislpayForm']){ ?>
                    <form action="" method="post" class="crm-webform-form-container">
                        <fieldset class="crm-webform-fieldset">
                            <div class="row">
                                <div class="col-md-12 col-sm-12 crm-webform-field-string">
                                    <div class="crm-webform-group">
                                        <div class="crm-webform-label-content">
                                        <label class="crm-webform-input-label"><input type="text" placeholder="Ваше Имя" name="fio" value="<?php echo !empty($_POST['fio'])?$_POST['fio']:'' ?>">
                                        <b class="tooltip crm-webform-tooltip-bottom-right">Заполните обязательное поле</b>
                                        </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 col-sm-12 crm-webform-field-phone crm-webform-error">
                                    <div class="crm-webform-group">
                                        <div class="crm-webform-label-content">
                                        <label class="crm-webform-input-label">
                                        <input type="text" placeholder="Ваш телефон" name="phone" value="<?php echo !empty($_POST['phone'])?$_POST['phone']:'' ?>">
                                        <b class="tooltip crm-webform-tooltip-bottom-right">Заполните обязательное поле</b></label>
                                        </div>
                                    </div>
                                </div>  
                            </div>
                            <div class="row">
                                <div class="col-md-12 col-sm-12">
                                    <?php  if(MG::getSetting('useCaptcha')=="true"){ ?>
                        <div>Введите текст с картинки:</div>
                        <div><img src = "captcha.html" width="140" height="36"></div>
                        <div><input type="text" name="capcha" class="captcha"></div>
                        <?php }?>
                        </div>
                        <input type="submit" name="send" class="default-btn crm-webform-submit-button" value="Отправить сообщение">
                                </div>
                            </div>
                        </fieldset>
                    </form>
                    <div class="clear">&nbsp;</div>
                <?php } else { ?>
                    <div class='successSend'> <?php echo $data['message']?> </div>
                <?php }; ?>
                </div>
           </div>
        </div>
    </div>
</div>
   <?php }
?>