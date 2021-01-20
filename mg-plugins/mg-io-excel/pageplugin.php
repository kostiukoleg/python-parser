<!--
Доступны переменные:
  $pluginName - название плагина
  $lang - массив фраз для выбранной локали движка
  $options - набор данного плагина хранимый в записи таблиц mg_setting
  $entity - набор записей сущностей плагина из его таблицы
  $pagination - блок навигациицам 
-->

<div class="section-<?php echo $pluginName?>"><!-- $pluginName - задает название секции для разграничения JS скрипта -->

  <!-- Тут начинается Верстка модального окна -->
<div class="reveal-overlay" style="display:none;">
  <div class="columnComplianceModal hidden-form reveal b-modal">
    <button class="close-button closeModal" type="button"><i class="fa fa-times-circle-o" aria-hidden="true"></i></button>
    <div class="reveal-header">
      <h2 class="pages-table-icon" id="modalTitle"><?php echo $lang['MODAL_TITLE'];?>Соответствие полей импорта</h2>
    </div>
    <div class="widget-table-body">
      <div class="add-product-form-wrapper">
        <table class="main-table complianceHeaders">
          <thead>
            <th width="200px">Поле в системе</th>
            <th width="320px">Поле в файле</th>
          </thead>
          <tbody></tbody>
        </table>
      </div> 
    </div>  
    <div class="product-table-wrapper add-news-form">
      <div class="widget-table-body">
        <div class="add-product-form-wrapper">
          <ul>
            
          </ul>
        </div>
      </div>
    </div>
    <div class="reveal-footer text-right">
      <div class="save">
        <button class="save-button tool-tip-bottom button success" title="<?php echo $lang['T_TIP_SAVE'];?>"><span><?php echo $lang['SAVE'];?></span></button>
      </div>
    </div>
  </div>
</div>
  <!-- Тут заканчивается Верстка модального окна -->
  
  <!-- Тут начинается верстка видимой части станицы настроек плагина-->
  <div class="widget-body">
    <div class="wrapper-entity-setting">
      <div id="settings-tabs" style="padding: 0 15px 0 15px;">
        <!-- Заголовки -->
        <ul class="tabs-list template-tabs-menu">
          <li class="template-tabs button is-active" style="margin:0;">
            <a href="javascript:void(0);" class="tool-tip-top" id="setting-import-tab" title="Импорт каталога из Excel файла"><span>Импорт</span></a>
          </li>
          <li class="template-tabs button" style="margin:0;">
            <a href="javascript:void(0);" class="tool-tip-top" id="setting-export-tab" title="Экспорт каталога в Excel файл"><span>Экспорт</span></a>
          </li>
        </ul>
      </div>
      <!-- Тут начинается  Верстка базовых настроек  плагина (опций из таблицы  setting)-->
      <div class="widget-table-action base-settings">
        <div class="setting-block setting-import-block">
          <div class="row">
            <div class="large-6 medium-9 small-12 columns">
              <div class="row">
                <div class="small-12 columns">
                  <div class="section"><?php echo $lang['SECTION_SELECT_FILE']?></div>
                </div>
              </div>
              <div class="row">
                <div class="small-6 columns">
                  <label class="dashed"><?php echo $lang['IMPORT_FILE_TYPE'];?>:</label>
                </div>
                <div class="small-6 columns">
                  <select name="importType">
                    <option value="0"><?php echo $lang['SELECT_FILE_TYPE'];?></option>
                    <option value="MogutaCMS"><?php echo $lang['CATALOG_FILE_TYPE'];?></option>
                    <option value="MogutaCMSUpdate"><?php echo $lang['UPDATE_FILE_TYPE'];?></option>
                  </select>
                </div>
              </div>
              <div class="row">
                <div class="small-6 columns">
                  <label class="dashed"><?php echo $lang['UPLOAD_FILE']?>:</label>
                </div>
                <div class="small-6 columns">
                  <form method="post" noengine="true" enctype="multipart/form-data" class="excel-upload-form">
                    <input type="file" name="upload_data_file" class="tool-tip-right" id="ExcelChoser" title="<?php echo $lang['T_TIP_UPLOAD_FILE']?>" disabled="disabled" style="display:none;" />
                    <label class="button removeDisabled" disabled="disabled" for="ExcelChoser">Загрузить</label>
                    <span class="upload_file_success" style="display:none;"><?php echo $lang['UPLOAD_FILE_SUCCESS']?></span>
                  </form>
                </div>
              </div>
              <div class="row">
                <div class="small-6 columns">
                  <label class="dashed"><?php echo $lang['SET_COLUMN_COMPLIANCE'];?>:</label>
                </div>
                <div class="small-6 columns">
                  <select name="importScheme" disabled="disabled" class="importScheme">
                    <option value="default"><?php echo $lang['DEFAULT_IMPORT_SCHEME'];?></option>
                    <option value="last"><?php echo $lang['LAST_IMPORT_SCHEME'];?></option>
                    <option value="new"><?php echo $lang['NEW_IMPORT_SCHEME'];?></option>
                  </select>
                </div>
              </div>
              <div class="row">
                <div class="small-6 columns">
                  <label class="dashed"><?php echo $lang['CLEAR_CATALOG_MODE'];?>:</label>
                </div>
                <div class="small-6 columns">
                  <div class="checkbox margin">
                    <input id="tconsentData" type="checkbox" name="clearCatalog" value="" class="tool-tip-right" title="<?php echo $lang['T_TIP_CLEAR_CATALOG_IMPORT']?>">
                    <label for="tconsentData"></label>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="small-12 columns">
                  <div class="block-console">
                    <textarea style="height:200px;" disabled="disabled"> </textarea>
                  </div>
                </div>
              </div>
              <button class="tool-tip-bottom base-setting-save save-button button success" data-id="" title="<?php echo $lang['START_IMPORT']?>">
                <span><?php echo $lang['START_IMPORT']?></span> <!-- кнопка применения настроек -->
              </button>
            </div>
          </div>
        </div>
        <div class="setting-block setting-export-block list-option input" style="display: none;">
          <div class="row">
            <div class="large-6 medium-9 small-12 columns">
              <div class="row">
                <div class="row">
                  <div class="small-12 columns">
                    <div class="section"><?php echo $lang['SECTION_EXPORT_SETTINGS']?></div>
                  </div>
                </div>
                <div class="row">
                  <div class="small-6 columns">
                    <label class="dashed"><?php echo $lang['EXPORT_ONLY_ACTIVE'];?>:</label>
                  </div>
                  <div class="small-6 columns">
                    <div class="checkbox margin">
                      <input id="tconsentData-1" type="checkbox" name="only_active" value="" class="tool-tip-right" title="<?php echo $lang['T_TIP_EXPORT_ONLY_ACTIVE']?>">
                      <label for="tconsentData-1"></label>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="small-6 columns">
                    <label class="dashed"><?php echo $lang['EXPORT_ONLY_ON_COUNT'];?>:</label>
                  </div>
                  <div class="small-6 columns">
                    <div class="checkbox margin">
                      <input id="tconsentData-2" type="checkbox" name="only_in_count" value="" class="tool-tip-right" title="<?php echo $lang['T_TIP_EXPORT_ONLY_ON_COUNT']?>">
                      <label for="tconsentData-2"></label>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="small-6 columns">
                    <label class="dashed"><?php echo $lang['EXPORT_CATEGORY'];?>:</label>
                  </div>
                  <div class="small-6 columns">
                    <select name="export_category_list" multiple="multiple" size="10">
                      <?php foreach ($data['category'] as $key => $value):?>
                          <option value="<?php echo $key?>"><?php echo $value?></option>
                        <?php endforeach;?>
                    </select>
                  </div>
                </div>
              </div>
              <div class="download-export-file">
                <?php if($data['file']):?>
                <a class="link" href="<?php echo $data['file']['link']?>">Скачать файл экспорта от <?php echo date('d.m.Y H:i', $data['file']['date'])?></a>
                <?php endif;?>
              </div>
              <button class="tool-tip-bottom export-start save-button button success fl-right" data-id="" title="<?php echo $lang['START_EXPORT']?>">
                <span><?php echo $lang['START_EXPORT']?></span> <!-- кнопка применения настроек -->
              </button>
            </div>
          </div>
          
        </div>
      </div>
    </div>
  </div>