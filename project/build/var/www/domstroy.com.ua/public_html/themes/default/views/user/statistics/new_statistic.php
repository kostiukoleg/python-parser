<?php
/**
 *
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
<link rel="stylesheet" href="https://cdn.jsdelivr.net/chartist.js/latest/chartist.min.css">
<script src="https://cdn.jsdelivr.net/chartist.js/latest/chartist.min.js"></script>
<script src="/js/chartist-plugin-legend.js"></script>
<style>
       .ct-chart {
           position: relative;
       }
       .ct-legend {
           position: relative;
           z-index: 10;
           list-style: none;
           text-align: center;
       }
       .ct-legend li {
           position: relative;
           padding-left: 23px;
           margin-right: 10px;
           margin-bottom: 3px;
           cursor: pointer;
           display: inline-block;
       }
       .ct-legend li:before {
           width: 12px;
           height: 12px;
           position: absolute;
           left: 0;
           content: '';
           border: 3px solid transparent;
           border-radius: 2px;
       }
       .ct-legend li.inactive:before {
           background: transparent;
       }
       .ct-legend.ct-legend-inside {
           position: absolute;
           top: 0;
           right: 0;
       }
       .ct-legend.ct-legend-inside li{
           display: block;
           margin: 0;
       }
       .ct-legend .ct-series-0:before {
           background-color: #271DFF;
           border-color: #271DFF;
       }
        .ct-series-a .ct-bar, .ct-series-a .ct-line, .ct-series-a .ct-point, .ct-series-a .ct-slice-donut {
            stroke: #271DFF;
        }
       .ct-legend .ct-series-1:before {
           background-color: #d70206;
           border-color: #d70206;
       }
       .ct-legend .ct-series-2:before {
           background-color: #f4c63d;
           border-color: #f4c63d;
       }
       .ct-legend .ct-series-3:before {
           background-color: #d17905;
           border-color: #d17905;
       }
       .ct-legend .ct-series-4:before {
           background-color: #453d3f;
           border-color: #453d3f;
       }

       .ct-chart-line-multipleseries .ct-legend .ct-series-0:before {
           background-color: #d70206;
           border-color: #d70206;
       }

       .ct-chart-line-multipleseries .ct-legend .ct-series-1:before {
           background-color: #f4c63d;
           border-color: #f4c63d;
       }

       .ct-chart-line-multipleseries .ct-legend li.inactive:before {
           background: transparent;
       }

       .crazyPink li.ct-series-0:before {
          background-color: #C2185B;
          border-color: #C2185B;
       }

       .crazyPink li.ct-series-1:before {
          background-color: #E91E63;
          border-color: #E91E63;
       }

       .crazyPink li.ct-series-2:before {
          background-color: #F06292;
          border-color: #F06292;
       }
       .crazyPink li.inactive:before {
          background-color: transparent;
       }

       .crazyPink ~ svg .ct-series-a .ct-line, .crazyPink ~ svg .ct-series-a .ct-point {
          stroke: #C2185B;
       }

       .crazyPink ~ svg .ct-series-b .ct-line, .crazyPink ~ svg .ct-series-b .ct-point {
          stroke: #E91E63;
       }

       .crazyPink ~ svg .ct-series-c .ct-line, .crazyPink ~ svg .ct-series-c .ct-point {
          stroke: #F06292;
       }
      .ct-series-a .ct-line,
      .ct-series-a .ct-point {
        stroke:#271DFF;

      }
       /* Page styling */
       h1, h2, h3{
          color: #5b4421;
          text-transform: uppercase;
       }

       h1, h2{
          text-align: center;
       }

       h3 > * {
          text-transform: none;
       }

       .codeblock-hidden{
          display: none;
       }

      .javascript.hljs {
             background-color: #453D3F;
             padding: 1.3333333333rem;
             color: #f7f2ea;
             font-family: "Source Code Pro","Courier New",monospace!important;
             line-height: 1.4;
             word-wrap: break-word;
             height: auto;
             margin-bottom: 1.3333333333rem
       }

       .ct-hidden {
          opacity: 0;
       }

       .ct-dimmed {
          opacity: 0.5;
       }

       .javascript.hljs span::selection, .javascript.hljs::selection {
         background: #2a2526!important
       }

       .javascript.hljs .hljs-comment {
             color: #7b6d70
       }

       .javascript.hljs .hljs-atom,.javascript.hljs .hljs-number {
             color: #F4C63D
       }

       .cm-s-3024-day .hljs-attribute,.javascript.hljs .hljs-property {
             color: #f7f2ea
       }

       .javascript.hljs .hljs-keyword {
             color: #F05B4F;
             font-weight: 700
       }

       .javascript.hljs .hljs-string {
             color: #F4C63D
       }

       .javascript.hljs .hljs-variable {
             color: #f7f2ea
       }

       .javascript.hljs .hljs-def,.javascript.hljs .hljs-variable-2 {
             color: #f8b3ad
       }

       .javascript.hljs .hljs-bracket {
             color: #3a3432
       }

       .javascript.hljs .hljs-tag {
             color: #F05B4F;
             font-weight: 700
       }

       .javascript.hljs .hljs-link {
             color: #F4C63D
       }

       .javascript.hljs .hljs-error{
             background-color: #F05B4F;
             color: #453D3F
       }

       .javascript.hljs .hljs-literal{
             color: #F05B4F;
       }

       .javascript.hljs .CodeMirror-activeline-background {
             background: #e8f2ff!important
       }

       .javascript.hljs .CodeMirror-matchingbracket {
             text-decoration: underline;
             color: #fff!important
       }
       .button, button {
          border-radius: 0;
          border-style: solid;
          border-width: 0;
          cursor: pointer;
          font-weight: 400;
          line-height: normal;
          margin: 5px auto;
          text-align: center;
          display: block;
          padding: 1rem 2rem 1.0625rem;
          font-size: 1rem;
          background-color: #F4C63D;
          border-color: #e7b00d;
          color: #453D3F;
       }

    .no_active{
        cursor: pointer;
    }
    .no_active > span:hover{
        background-color: #f93306;
        color: #fff;
    }
    .no_active > p:hover{
        background-color: #f93306;
        color: #fff;
    }
    .active > p{
        background-color: #ccc;
        color: #fff;
        font-weight: bold;
    }
    .active > span{
        background-color: #ccc;
        color: #fff;
        font-weight: bold;
    }
    .ct-label{
        color:#000;
    }
    .ct-line {
        stroke-width: 2px;
    }
    .ct-point {
        stroke-width: 5px;
        stroke-linecap: round;
    }
    /*For tables still need to write 'cellspacing="0"' in code*/
    table {
    border-collapse:collapse;
    border-spacing:0;
    }
    caption, th, td {
    text-align:left;
    font-weight:normal;
    }
    blockquote:before, blockquote:after,
    q:before, q:after {
    content:"";
    }
    blockquote, q {
    quotes:"" "";
    }
.simple-little-table {
    font-family:Arial, Helvetica, sans-serif;
    color:#666;
    font-size:14px;
    text-shadow: 1px 1px 0px #fff;
    background:#eaebec;
    margin:20px;
    border:#ccc 1px solid;
    border-collapse:separate;

    -moz-border-radius:3px;
    -webkit-border-radius:3px;
    border-radius:3px;

    -moz-box-shadow: 0 1px 2px #d1d1d1;
    -webkit-box-shadow: 0 1px 2px #d1d1d1;
    box-shadow: 0 1px 2px #d1d1d1;
    width: 90%;
    margin: auto;
}

.simple-little-table th {
    font-weight:bold;
    padding:21px 25px 22px 25px;
    border-top:1px solid #fafafa;
    border-bottom:1px solid #e0e0e0;

    background: #ededed;
    background: -webkit-gradient(linear, left top, left bottom, from(#ededed), to(#ebebeb));
    background: -moz-linear-gradient(top,  #ededed,  #ebebeb);
}
.simple-little-table th:first-child{
    text-align: left;
    padding-left:20px;
}
.simple-little-table tr:first-child th:first-child{
    -moz-border-radius-topleft:3px;
    -webkit-border-top-left-radius:3px;
    border-top-left-radius:3px;
}
.simple-little-table tr:first-child th:last-child{
    -moz-border-radius-topright:3px;
    -webkit-border-top-right-radius:3px;
    border-top-right-radius:3px;
}
.simple-little-table tr{
    text-align: center;
    padding-left:20px;
}
.simple-little-table tr td:first-child{
    text-align: left;
    padding-left:20px;
    border-left: 0;
}
.simple-little-table tr td {
    padding:18px;
    border-top: 1px solid #ffffff;
    border-bottom:1px solid #e0e0e0;
    border-left: 1px solid #e0e0e0;

    background: #fafafa;
    background: -webkit-gradient(linear, left top, left bottom, from(#fbfbfb), to(#fafafa));
    background: -moz-linear-gradient(top,  #fbfbfb,  #fafafa);
}
.simple-little-table tr:nth-child(even) td{
    background: #f6f6f6;
    background: -webkit-gradient(linear, left top, left bottom, from(#f8f8f8), to(#f6f6f6));
    background: -moz-linear-gradient(top,  #f8f8f8,  #f6f6f6);
}
.simple-little-table tr:last-child td{
    border-bottom:0;
}
.simple-little-table tr:last-child td:first-child{
    -moz-border-radius-bottomleft:3px;
    -webkit-border-bottom-left-radius:3px;
    border-bottom-left-radius:3px;
}
.simple-little-table tr:last-child td:last-child{
    -moz-border-radius-bottomright:3px;
    -webkit-border-bottom-right-radius:3px;
    border-bottom-right-radius:3px;
}
.simple-little-table tr:hover td{
    background: #f2f2f2;
    background: -webkit-gradient(linear, left top, left bottom, from(#f2f2f2), to(#f0f0f0));
    background: -moz-linear-gradient(top,  #f2f2f2,  #f0f0f0);
}

.simple-little-table a:link {
    color: #666;
    font-weight: bold;
    text-decoration:none;
}
.simple-little-table a:visited {
    color: #999999;
    font-weight:bold;
    text-decoration:none;
}
.simple-little-table a:active,
.simple-little-table a:hover {
    color: #bd5a35;
}
.header_content{
    width: 100%;
    height: 50px;
}
</style>
<div class="page-header">
    <h4>Статистика компании <b>"<?php echo $company->name; ?>"</b> (работает в тестовом режиме)</h4>
</div>
<?php if(!empty($banner)){ ?>
    <span style="color: #000635;font-size: 16px;font-weight: bold;">Банерная реклама</span>
    <?php foreach ($banner as $b) { ?>
        <div class="row" style="padding: 10px;border-bottom:1px solid #ccc;">
            <div class="col-sm-3">
                <img style="width:100%;" src="https://domstroy.com.ua/uploads/straights/<?php echo $b['image']; ?>">
            </div>
            <div class="col-sm-4">
                <p>Начало показа: <b><?php echo date('d.m.Y', strtotime($b['start_time'])); ?></b></p>
                <p>Конец показа:&nbsp;&nbsp;&nbsp;<b><?php echo date('d.m.Y', strtotime($b['end_time'])); ?></b></p>
            </div>
            <div class="col-sm-4">
                <span>Всего показов - <b><?php echo $b['all_baner_vieved']; ?></b></span></br>
                <span>Всего переходов - <b><?php echo $b['all_baner_red']; ?></b></span>
            </div>
        </div>
    <?php } ?>
<?php } else { ?>
    <!-- <span>Нажаль у Вас немає банерної реклами на нашому порталі. Для того, щоб підвищити якість реклами ознайомтеся з інформацією про <a href="https://domstroy.com.ua/page/reklama-na-sayte">рекламу на нашому порталі</a>, та звяжіться зі своїм менеджером</span> -->
<?php } ?>

<div class="row" style="margin-top: 20px;margin-bottom: 20px;">
    <input type="hidden" id="type_statistic" value="t">
    <div class="col-sm-4"><span>Вид:</span></div>
    <div class="col-sm-2 no_active" id="sel_graphic"><p style="border:1px solid #c24627;text-align: -webkit-center;padding: 3px;" onclick="SelectGraphic()">График</p></div>
    <div class="col-sm-2 active" id="sel_table"><p style="border:1px solid #c24627;text-align: -webkit-center;padding: 3px;" onclick="SelectTable()">Таблица</p></div>
</div>
<div class="row" id="period_block" style="margin-top: 20px;margin-bottom: 20px;display: none;">
    <input type="hidden" id="type_period" value="p_1">
    <div class="col-sm-4"><span>Периоды:</span></div>
    <div class="col-sm-2 no_active" id="p_2"><p style="border:1px solid #c24627;text-align: -webkit-center;padding: 3px;" data-type="p_2" onclick="SelectPeriod(this)">По месяцам</p></div>
    <div class="col-sm-2 no_active" id="p_3"><p style="border:1px solid #c24627;text-align: -webkit-center;padding: 3px;" data-type="p_3" onclick="SelectPeriod(this)">По годам</p></div>
</div>
<div class="row">
    <input type="hidden" id="type_select" value="s_2">
    <div class="col-sm-2 no_active" id="s_1"><p  style="border:1px solid #c24627;padding: 3px;text-align: -webkit-center;" data-type="s_1" onclick="SelectTypeSelect(this)">Общая статистика</p></div>
    <div class="col-sm-2 active" id="s_2"><p  style="border:1px solid #c24627;padding: 3px;text-align: -webkit-center;" data-type="s_2" onclick="SelectTypeSelect(this)">Просмотры компании</p></div>
    <div class="col-sm-2 no_active" id="s_3"><p  style="border:1px solid #c24627;padding: 3px;text-align: -webkit-center;" data-type="s_3" onclick="SelectTypeSelect(this)">Просмотры обьявлений</p></div>
    <div class="col-sm-2 no_active" id="s_4"><p  style="border:1px solid #c24627;padding: 3px;text-align: -webkit-center;" data-type="s_4" onclick="SelectTypeSelect(this)">Номера телефонов</p></div>
    <div class="col-sm-2 no_active" id="s_5"><p  style="border:1px solid #c24627;padding: 3px;text-align: -webkit-center;" data-type="s_5" onclick="SelectTypeSelect(this)">Переходы на сайт</p></div>
    <div class="col-sm-2 no_active" id="s_6"><p  style="border:1px solid #c24627;padding: 3px;text-align: -webkit-center;" data-type="s_6" onclick="SelectTypeSelect(this)">Переходы на Viber</p></div>
    <div class="col-sm-2 no_active" id="s_7"><p  style="border:1px solid #c24627;padding: 3px;text-align: -webkit-center;" data-type="s_7" onclick="SelectTypeSelect(this)">Переходы на Обратный звонок</p></div>
    <div class="col-sm-2 no_active"></div>
</div>
<p id="text_legend" style="color: #000635;font-size: 16px;font-weight: bold;text-align: center;">Общая статистика за последние 30 дней</p>
<div class="row">
    <div class="ct-chart ct-golden-section" id="chart1"></div>
    <div class="ct-chart-dynamic ct-golden-section" style="display: none;" id="chart2"></div>
    <table class="simple-little-table" cellspacing='0' style="display:none;">
        <tr>
            <th>Дата</th>
            <th>Просмотры компании</th>
            <th>Просмотры обьявлений</th>
            <th>Просмотры телефонов</th>
	          <th>Viber</th>
            <th>Обратный звонок</th>
        </tr>
        <?php foreach ($vieved_company_phone as $key => $value) { ?>
          <tr>
              <td><?php echo $value['date']; ?></td>
              <td><?php echo $value['views']; ?></td>
              <?php if(isset($viewed_products[$key])){ ?>
                <td><?php echo $viewed_products[$key]['viewed_product']; ?></td>
              <?php } else { ?>
                <td>0</td>
              <?php } ?>
              <td><?php echo $value['views_phone']; ?></td>
	            <td><?php echo $value['viber_click']; ?></td>
              <td><?php echo $value['callback']; ?></td>
          </tr>
        <?php } ?>
    </table>
    <table class="simple-little-table" id="simple-little-table-dynamic" cellspacing='0' style="display:none;">

    </table>
</div>

<script>

new Chartist.Line('#chart1', {
    labels: <?php echo $data_stroka; ?>,
    series: [
        <?php echo $data_viewed_company; ?>,
        <?php echo $data_viewed_product; ?>,
        <?php echo $data_viewed_phone; ?>,
        <?php echo $data_viber_click; ?>,
        <?php echo $data_callback; ?>
     ]
    }, {
    fullWidth: true,
    chartPadding: {
        right: 40
    },
    axisY: {
      onlyInteger: true,
    },
    plugins: [
        Chartist.plugins.legend({
            legendNames: ['Просмотры компании', 'Просмотры обьявлений', 'Просмотры телефонов', 'Viber', 'Обратный звонок'],
        })
    ]
});
function SelectGraphic(){
  var type = $("#type_statistic").val();
  if (type != 'g') {
    $("#sel_graphic").removeClass('no_active');
    $("#sel_graphic").addClass('active');
    $("#sel_table").removeClass('active');
    $("#sel_table").addClass('no_active');
    $("#type_statistic").val('g');
  }
  FormationGraphic();
}
function SelectTable(){
  var type = $("#type_statistic").val();
  if (type != 't') {
    $("#sel_table").removeClass('no_active');
    $("#sel_table").addClass('active');
    $("#sel_graphic").removeClass('active');
    $("#sel_graphic").addClass('no_active');
    $("#type_statistic").val('t');
  }
  FormationTable();
}
function SelectPeriod(e){
  var type = $(e).data('type');
  var type_now = $("#type_period").val();
    if (type != type_now) {
      switch (type) {
        case 'p_2':
          $("#"+type).removeClass('no_active');
          $("#"+type).addClass('active');
          $("#p_3").removeClass('active');
          $("#p_3").addClass('no_active');
          $("#type_period").val(type);
          break;
        case 'p_3':
          $("#"+type).removeClass('no_active');
          $("#"+type).addClass('active');
          $("#p_2").removeClass('active');
          $("#p_2").addClass('no_active');
          $("#type_period").val(type);
          break;
      }
    }
    if ($("#type_statistic").val() == 'g') {
      FormationGraphic();
    } else{
      FormationTable();
    }
}
function SelectTypeSelect(e){
  var type = $(e).data('type');
  var type_now = $("#type_select").val();
  var type_period_now = $("#type_period").val();
  if (type != type_now) {
    switch (type) {
      case 's_1':
        $("#"+type).removeClass('no_active');
        $("#"+type).addClass('active');
        $("#s_2").removeClass('active');
        $("#s_2").addClass('no_active');
        $("#s_3").removeClass('active');
        $("#s_3").addClass('no_active');
        $("#s_4").removeClass('active');
        $("#s_4").addClass('no_active');
        $("#s_5").removeClass('active');
        $("#s_5").addClass('no_active');
        $("#s_6").removeClass('active');
        $("#s_6").addClass('no_active');
        $("#s_7").removeClass('active');
        $("#s_7").addClass('no_active');
        $("#type_select").val('s_1');
        $("#type_period").val('p_1');
        $("#p_2").removeClass('active');
        $("#p_2").addClass('no_active');
        $("#p_3").removeClass('active');
        $("#p_3").addClass('no_active');
        $("#period_block").css('display', 'none');
        $("#simple-little-table-dynamic").css('display', 'none');
        break;
      case 's_2':
        $("#"+type).removeClass('no_active');
        $("#"+type).addClass('active');
        $("#s_1").removeClass('active');
        $("#s_1").addClass('no_active');
        $("#s_3").removeClass('active');
        $("#s_3").addClass('no_active');
        $("#s_4").removeClass('active');
        $("#s_4").addClass('no_active');
        $("#s_5").removeClass('active');
        $("#s_5").addClass('no_active');
        $("#s_6").removeClass('active');
        $("#s_6").addClass('no_active');
        $("#s_7").removeClass('active');
        $("#s_7").addClass('no_active');
        $("#type_select").val('s_2');
        if (type_period_now == 'p_1') {
          $("#type_period").val('p_2');
          $("#p_2").removeClass('no_active');
          $("#p_2").addClass('active');
        }
        $("#period_block").css('display', 'block');
        break;
      case 's_3':
        $("#"+type).removeClass('no_active');
        $("#"+type).addClass('active');
        $("#s_1").removeClass('active');
        $("#s_1").addClass('no_active');
        $("#s_2").removeClass('active');
        $("#s_2").addClass('no_active');
        $("#s_4").removeClass('active');
        $("#s_4").addClass('no_active');
        $("#s_5").removeClass('active');
        $("#s_5").addClass('no_active');
        $("#s_6").removeClass('active');
        $("#s_6").addClass('no_active');
        $("#s_7").removeClass('active');
        $("#s_7").addClass('no_active');
        $("#type_select").val('s_3');
        if (type_period_now == 'p_1') {
          $("#type_period").val('p_2');
          $("#p_2").removeClass('no_active');
          $("#p_2").addClass('active');
        }
        $("#period_block").css('display', 'block');
        break;
      case 's_4':
        $("#"+type).removeClass('no_active');
        $("#"+type).addClass('active');
        $("#s_1").removeClass('active');
        $("#s_1").addClass('no_active');
        $("#s_2").removeClass('active');
        $("#s_2").addClass('no_active');
        $("#s_3").removeClass('active');
        $("#s_3").addClass('no_active');
        $("#s_5").removeClass('active');
        $("#s_5").addClass('no_active');
        $("#s_6").removeClass('active');
        $("#s_6").addClass('no_active');
        $("#s_7").removeClass('active');
        $("#s_7").addClass('no_active');
        if (type_period_now == 'p_1') {
          $("#type_period").val('p_2');
          $("#p_2").removeClass('no_active');
          $("#p_2").addClass('active');
        }
        $("#type_select").val('s_4');
        $("#period_block").css('display', 'block');
        break;
      case 's_5':
        $("#"+type).removeClass('no_active');
        $("#"+type).addClass('active');
        $("#s_1").removeClass('active');
        $("#s_1").addClass('no_active');
        $("#s_2").removeClass('active');
        $("#s_2").addClass('no_active');
        $("#s_3").removeClass('active');
        $("#s_3").addClass('no_active');
        $("#s_4").removeClass('active');
        $("#s_4").addClass('no_active');
        $("#s_6").removeClass('active');
        $("#s_6").addClass('no_active');
        $("#s_7").removeClass('active');
        $("#s_7").addClass('no_active');
        if (type_period_now == 'p_1') {
          $("#type_period").val('p_2');
          $("#p_2").removeClass('no_active');
          $("#p_2").addClass('active');
        }
        $("#type_select").val('s_5');
        $("#period_block").css('display', 'block');
        break;
      case 's_6':
        $("#"+type).removeClass('no_active');
        $("#"+type).addClass('active');
        $("#s_1").removeClass('active');
        $("#s_1").addClass('no_active');
        $("#s_2").removeClass('active');
        $("#s_2").addClass('no_active');
        $("#s_3").removeClass('active');
        $("#s_3").addClass('no_active');
        $("#s_4").removeClass('active');
        $("#s_4").addClass('no_active');
        $("#s_5").removeClass('active');
        $("#s_5").addClass('no_active');
        $("#s_7").removeClass('active');
        $("#s_7").addClass('no_active');
        if (type_period_now == 'p_1') {
          $("#type_period").val('p_2');
          $("#p_2").removeClass('no_active');
          $("#p_2").addClass('active');
        }
        $("#type_select").val('s_6');
        $("#period_block").css('display', 'block');
        break;
      case 's_7':
        $("#"+type).removeClass('no_active');
        $("#"+type).addClass('active');
        $("#s_1").removeClass('active');
        $("#s_1").addClass('no_active');
        $("#s_2").removeClass('active');
        $("#s_2").addClass('no_active');
        $("#s_3").removeClass('active');
        $("#s_3").addClass('no_active');
        $("#s_4").removeClass('active');
        $("#s_4").addClass('no_active');
        $("#s_5").removeClass('active');
        $("#s_5").addClass('no_active');
        $("#s_6").removeClass('active');
        $("#s_6").addClass('no_active');
        if (type_period_now == 'p_1') {
          $("#type_period").val('p_2');
          $("#p_2").removeClass('no_active');
          $("#p_2").addClass('active');
        }
        $("#type_select").val('s_7');
        $("#period_block").css('display', 'block');
        break;
    }
  }
  if ($("#type_statistic").val() == 'g'){
    FormationGraphic();
  } else{
    FormationTable();
  }
}
function FormationGraphic(){
  var period = $("#type_period").val();
  var typeselect = $("#type_select").val();
  var arr_data = period+'-'+typeselect;
  //запит на отримання даних
  if (typeselect == 's_1') {
    $("#text_legend").html('');
    $("#text_legend").html('Загальна статистика за останні 30 днів');
    $(".ct-chart-dynamic").css('display', 'none');
    $(".simple-little-table").css('display', 'none');
    $("#simple-little-table-dynamic").css('display', 'none');
    $(".ct-chart").css('display', 'block');
  } else{
     $.get('<?= Yii::app()->createUrl('user/statistics/getdatagraphic');?>/' + arr_data, function (data) {
       if (data !='false') {
        $(".ct-chart").css('display', 'none');
        $(".simple-little-table").css('display', 'none');
        $("#simple-little-table-dynamic").css('display', 'none');
        $(".ct-chart-dynamic").html('');
        $(".ct-chart-dynamic").css('display', 'block');
        res = JSON.parse(data);
          $("#text_legend").html('');
          $("#text_legend").html(res['text_legend']);
          if (typeselect == 's_4') {
            new Chartist.Bar('#chart2', {
              labels: res['str_date'],
              series: [
                res['viewed'],
                res['successfully_phone'],
                res['unsuccessfully_phone']
              ]
            }, {
              seriesBarDistance: 10,
              axisX: {

                offset: 60
              },
              axisY: {
                offset: 80,
                onlyInteger: true,
                scaleMinSpace: 15
              },
              plugins: [
                    Chartist.plugins.legend({
                        legendNames: ['Всего звонков', 'Удачно', 'Неудачно'],
                    })
                ]
            });



            // new Chartist.Line('#chart2', {
            //     labels: res['str_date'],
            //     series: [
            //         res['viewed'],
            //         res['successfully_phone'],
            //         res['unsuccessfully_phone']
            //      ]
            // }, {
            //     fullWidth: true,
            //     chartPadding: {
            //         right: 40
            //     },
            //     axisY: {
            //       onlyInteger: true,
            //     },
                // plugins: [
                //     Chartist.plugins.legend({
                //         legendNames: ['Всього дзвінків', 'Успішні дзвінки', 'Неспішні дзвінки'],
                //     })
                // ]
            // });
          } else{
            new Chartist.Bar('#chart2', {
              labels: res['str_date'],
              series: [
                res['viewed']
              ]
            }, {
              seriesBarDistance: 10,
              axisX: {

                offset: 60
              },
              axisY: {
                offset: 80,
                onlyInteger: true,
                scaleMinSpace: 15
              },
            });

            // new Chartist.Line('#chart2', {
            //     labels: res['str_date'],
            //     series: [
            //         res['viewed']
            //         ]
            // }, {
            //     fullWidth: true,
            //     chartPadding: {
            //         right: 40
            //     },
            //     axisY: {
            //       onlyInteger: true,
            //     },
            // });
          }
       }
     });
  }
}

function FormationTable(){
  var period = $("#type_period").val();
  var typeselect = $("#type_select").val();
  if (typeselect == 's_1') {
    $("#text_legend").html('');
    $("#text_legend").html('Загальна статистика за останні 30 днів');
    $(".ct-chart").css('display', 'none');
    $(".simple-little-table").css('display', 'block');
    $("#simple-little-table-dynamic").css('display', 'none');
  } else{
    $("#simple-little-table-dynamic").html('');
    $(".ct-chart").css('display', 'none');
    $(".ct-chart-dynamic").css('display', 'none');
    $(".simple-little-table").css('display', 'none');
    $("#simple-little-table-dynamic").css('display', 'block');
    var arr_data = period+'-'+typeselect;
    $.get('<?= Yii::app()->createUrl('user/statistics/getdatagraphic');?>/' + arr_data, function (data) {
      if (data !='false') {
        res = JSON.parse(data);
        $("#text_legend").html('');
        $("#text_legend").html(res['text_legend']);
        if (typeselect == 's_4') {
          d = res['str_date'];
          v = res['viewed'];
          s = res['successfully_phone'];
          u = res['unsuccessfully_phone'];
          text_html = '<tr><th>Дата</th><th>Всего просмотров</th><th>Удачно</th><th>Неудачно</th></tr>';
          d.forEach(function(item, i, d) {
            text_html = text_html + '<tr><td>'+item+'</td><td>'+v[i]+'</td><td>'+s[i]+'</td><td>'+u[i]+'</td></tr>';
            console.log(text_html);
          });
          $("#simple-little-table-dynamic").html(text_html);
        } else{
          d = res['str_date'];
          v = res['viewed'];
          text_html = '<tr><th>Дата</th><th>Кількість переглядів</th></tr>';
          d.forEach(function(item, i, d) {
            text_html = text_html + '<tr><td>'+item+'</td><td>'+v[i]+'</td></tr>';
            console.log(text_html);
          });
          $("#simple-little-table-dynamic").html(text_html);
        }
      }
    });
  }
}
</script>