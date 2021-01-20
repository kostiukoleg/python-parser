<div id="block46">
    <div class="container" id="calc" style="padding:50px 20px;">
        <div class="row">
            <div class="col-md-12 feadback-tittle" title="" style="margin-bottom:10px">
                Калькулятор
            </div>
        </div>
        <div class="row" style="border: 1px solid #1780c3; border-radius: 10px; align-items: center;
        text-align: center; padding:20px">
                <div class="col-md-6">
                    <form>
                        <div class="form-group">
                            <label for="cost">Стоимость авто в Корее</label>
                            <input type="text" class="form-control" id="cost" placeholder="Стоимость в $">
                        </div>
                        <div class="form-group">
                            <label for="dvig">Тип двигателя</label>
                            <select class="form-control" id="dvig">
                                <option value="0">Выберите тип двигателя</option>
                                <option value="b">Бензин/газ</option>
                                <option value="d">Дизель</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="v-dvig">Обьем двигателя куб.</label>
                            <input type="text" class="form-control" id="v-dvig" placeholder="1600">
                        </div>
                        <div class="form-group">
                            <label for="year">Год выпуска</label>
                            <input type="text" name="year" id="year" class="form-control" placeholder="2020">
                        </div>
                    </form>
                </div>
                <div class="col-md-6">
                    <button class="btn-wh calc" style="margin-bottom:20px">Расчитать</button>
                    <div class="res"></div>
                </div>
        </div>
    </div>
</div>
<script data-skip-moving="true">
  $('.calc').click(function (){let cost = $('#cost').val(),
            dvig = $('#dvig').val(),
            ob = $('#v-dvig').val(),
            god = $('#year').val();
        if (!dvig){$('#dvig').css('border-color', 'red');
            return;}if (!ob){$('#v-dvig').css('border-color', 'red');
            return;}if (!god){$('#year').css('border-color', 'red');
            return;}if (!cost){$('#cost').css('border-color', 'red');
            return;}koef = 0;
        if (dvig == 'b'){koef = 0.0542;}else{koef = 0.0813;}rast = (+cost+(+cost*0.022))*0.3;
        if (rast<1600){rast = 1600;}res = rast + ((+ob*koef)*(2020-(+god))) + 3800 + (+cost) + (+cost*0.03);
        $('.res').html('Предварительная стоимость Вашего авто c регистрацией в Украине: <b>' + new Intl.NumberFormat('ru-RU').format(res) + '$.</b> <br>Для более точного расчета свяжитесь с нами!')});
</script>