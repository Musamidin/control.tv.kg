<?php

//use dosamigos\fileupload\FileUploadUI;
// with UI
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

use dosamigos\fileupload\FileUpload;
// without UI

$this->title = 'Размещение бегущей строки на все ТВ и Радио! «онлайн сервис»'; 
?>
<script type="text/javascript">
    var valuta = 'сом';
    var tv_packet = {};
    var mlang = {
        lang: 'ru'
    };
</script>
<br/>
<div class="site-add" ng-controller="AppAddCtrl">
    <div class="row">
        <div class="col-md-12">
        <?
            $form = ActiveForm::begin([
                'enableClientScript' => false,
                'action' => ['/add'],
                'options' => ['class' => 'form-horizontal'],
            ]) 
        ?>
            <div class="pdRL10 pdb-40">
                <div class="row">
                    <div class="col-lg-7 col-md-7">
                        <div class="text-title tgradient">
                            <div class="row">
                            <div class="col-sm-8 col-xs-12">
                                <h3>Введите текст вашего объявления</h3>
                            </div>
                            <div class="col-sm-4  col-xs-12 text-right">Символов: <span id="sym_count">0</span></div>
                            </div>
                        </div>
                        <textarea class="text-enter" name="text" id="msg_text" placeholder=""></textarea>
                    </div>
                    <div class="col-lg-5 col-md-5 cont in hidden-xs">
                        <h3 class="rcol">Правила заполнения текста</h3>
                        <p>Уважаемый Рекламодатель!</p>
                        <ul>
                            <li>При заполнении объявления после каждого слова должен обязательно стоять пробел;</li>
                            <li>Размещенный текст не&nbsp;должен побуждать граждан к&nbsp;насилию, агрессии и&nbsp;опасным действиям, создающим угрозу жизни и&nbsp;здоровью, а&nbsp;также призывающему к&nbsp;беспорядку;</li>
                            <li>Рекламодатель самостоятельно несет ответственность за&nbsp;соответствие рекламы действующему законодательству Кыргызской Республики о&nbsp;рекламе;</li>
                            <li>Если рекламируемый товар/услуга подлежат лицензированию укажите номера лицензий и&nbsp;наименование органов, выдавшего их&nbsp;и/или укажите «товар сертифицирован», если рекламируемый товар подлежит обязательной сертификации;</li>
                        </ul>
                        <hr class="lichr"/>
                        <input name="licdoc" value="" placeholder="Поле для ввода лицензий" class="form-control"/>
                    </div>
                </div>
            </div>
            
			<div class="pdRL10 bg-white channel-list-cont">
                <h2>Выбор каналов</h2>
                <div class="channel-list">
                    <div class="title hidden-xs">
                        <div class="row">
                            <div class="col-md-6 col-sm-6"><label id="choicech">выберите каналы</label></div>
                            <div class="col-md-4 col-sm-3"><label>укажите даты</label></div>
                            <div class="col-md-2 col-sm-3"><label>количество дней</label></div>
                        </div>
                    </div>
                    
                    <div id="channels">
                        <? $i = 0; ?>
                        <? foreach($tvlist as $item): ?>
                        <? if($i > 3) { $clas = 'tv_hidden'; }else{ $clas = ''; } ?>
                        
                            <div class="one" data-id="<?=$item['id'];?>" data-price="<?=$item['price'];?>">
                                <div class="row">
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <div class="img"><img src="/img/tvlog/<?=$item['id'];?>.<?=$item['img'];?>" alt="<?=$item['tvname'];?>" title="<?=$item['tvname'];?>"></div>
                                        <span class="one-title"><?=$item['tvname'];?></span>
                                    </div>
                                    <div class="col-md-4 col-sm-3  col-xs-12">
                                        <div class="cal_cont">
                                        <span class="show_dates form-control"></span>
                                        <input type="hidden" class="multidate" name="dates[<?=$item['id'];?>]">
                                        <span class="input-group-addon showcalend"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-sm-3 price col-xs-12"><!--span class="price_real">0.0 сом</span--><span class="countdays_real"></span><span class="price_old"></span></div>
                                </div>
                            </div>
                            <? $i++; ?>
                        <? endforeach; ?>
                    </div>
                <div class="row total-block">
                    <!--div class="col-md-10 col-sm-9 text-right">Общая сумма:</div>
                    <div class="col-md-2 col-sm-3 total-summ" id="total">0 сом</div-->
                </div>
            </div>
            <div class="pdRL10 action-btn">
            <?= Html::button('Сохранить объявление', ['class' => 'btn btn-success','id'=>'save']) ?>
            </div>
        </div>
        <?php ActiveForm::end() ?>
		</div>
	</div>


<!--modal_date Modal -->
<div class="modal fade" id="modal_date" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">
                                ×
                            </span>
            </button>

            <div class="modal-body">
                <div class="one_tv_modal">
                    <div class="row" id="skidka" style="display: block;">
                        <div class="cont">
                       При заказе на данном телеканале действует система скидок:
                        </div>
                        <div class="items">
                        </div>
                    </div>
                    <div class="row">
                        <div id="calend_rows" style="display: block;">
                            <div class="one_inp"><input type="checkbox" value="" disabled="disabled"></div>
                            <div class="one_inp"><input type="checkbox" value=""></div>
                            <div class="one_inp"><input type="checkbox" value=""></div>
                            <div class="one_inp"><input type="checkbox" value=""></div>
                            <div class="one_inp"><input type="checkbox" value=""></div>
                            <div class="one_inp" style="display: block;"><input type="checkbox" value=""></div>
                        </div>
                        <div id="calendar" unselectable="on"></div>
                    </div>
                    <div class="row">
                        <div class="col-xs-6"><input type="button" value="Отмена" class="btn btn-default" id="cancel_date"></div>
                        <div class="col-xs-6 text-right"><input type="button" id="ok_date" class="btn btn-success savedate" value="Сохранить"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!--Loading Modal -->
<div id="loading-modal" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <img id="img-load" src="/img/loading.gif"/>
    </div>
  </div>
</div>

<!--Error Modal -->
<div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        <button type="button"  id="resultClose" class="close" data-dismiss="modal">
			    <span aria-hidden="true">&times;</span><span class="sr-only">Close</span>
			  </button>
            <div class="modal-header">
                <h3 class="modal-title"></h3>
            </div>
            <div class="modal-body"></div>
        </div>
    </div>
</div>

<!-- Modal Result-->
<div class="modal fade" id="resultModal" tabindex="-1" role="dialog" aria-labelledby="resultModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header tgradient">
			<button type="button"  id="resultClose" class="close" data-dismiss="modal">
			<span aria-hidden="true">&times;</span><span class="sr-only">Close</span>
			</button>
					<div>
				<div class="modal-title h4"  id="resultModalLabel">
				<span id="resultIcon" aria-hidden="true"></span>
				<span id="resutTitleText">Резульат оформления заявки </span></div>
					</div>
		</div>
      <div id="getResult" class="modal-body">
	  <div id="tt"></div>
	    <p class="bold">Ваша Заявка зарегистрирована.</p>
      </div>
    </div>
  </div>
</div>

</div>