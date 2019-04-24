<?php
//pr($authUser)
?>
<!-- Smartsupp Live Chat script -->
<script type="text/javascript">
var _smartsupp = _smartsupp || {};
_smartsupp.key = 'ed61800b774ab98e8ee687382bfaf3706198d063';
window.smartsupp||(function(d) {
  var s,c,o=smartsupp=function(){ o._.push(arguments)};o._=[];
  s=d.getElementsByTagName('script')[0];c=d.createElement('script');
  c.type='text/javascript';c.charset='utf-8';c.async=true;
  c.src='https://www.smartsuppchat.com/loader.js?';s.parentNode.insertBefore(c,s);
})(document);
</script>
<script>
var load_img    = '<?= $html->image("loading_small.gif",array("alt"=>"Esperando..."))?>',
    load_races  = '<?= $html->url(array("controller"=>"races","action"=>"list_ajax"))?>',
    load_horses = '<?= $html->url(array("controller"=>"horses","action"=>"list_ajax"))?>',
    dat         = '<?= $theDate ?>',
    race        = <?= $raceId ?>,
    co          = 0,
    ind         = 0,
    the_box     = Object(),
    role        = <?php echo $authUser['role_id'] ?>;
    
$(function(){
	$("#boxing").button({icons: {primary:'ui-icon-shuffle'}, text: false}).click(function(){
		box_all();
		return false;
	});
	
	$("#next").button({icons: {primary:'ui-icon-seek-next'}, text: false}).click(function(){
		pass_next();
		return false;
	});
	
	$("#mess_bet").hide();	
    $(".loadwait").hide();
	
    //ATTENTION box
	//message = "Las jugadas EXA, TRI y SUP implican un orden en su eleccion";
	var message = 'Próxima Carrera Seleccionada';
	if ( race != 0 ) {
		message = 'Ultimo Racetrack Seleccionado';
        $("#TicketRaceId").val(race);
        change_race(race);
    } else {
        $("#TicketRaceId").val($("#TicketRaceId option:first").next().val());
        change_race($("#TicketRaceId option:first").next().val());
    }
	
    err_line(1,message,$("#mess_bet"));
	
    $("#TicketRaceId").change(function(){
		change_race($(this).attr('value'));
	});

	$("#play_types button").button().click(function(){
		$("#play_types button").removeClass('button-clicked');
		$(this).addClass('button-clicked');
		
        $("#TicketPlayTypeId").val($(this).attr('id'));
		$("#play_name").html($(this).attr('title'));
		to_set_details($(this).attr('id'));
		
		$(".ui_check").removeClass("ui_checked");
		$(".horse_check").each(function(){
			$(this).attr('checked',false);
		});
		reset_positions();
		co = 0;

        //console.log('play_type_id');
        //console.log($("#TicketPlayTypeId").val());

		return false;
	});
    
    
    
    //AUTOTAQ LOAD ACTIONS
    if (role == 5) {
        $('#montapos').hide();
    } else {
        $('.autotaq-pin').hide();
    }
    
    $('.autotaq-confirm').button({icons: {primary: "ui-icon-notice"}}).click(
        function(){
            $('#montapos').show();
            return false;
        });
    
    $('.number-key').click(function(){
        var thsbtn = $(this),
            inpin  = $('.autopin'),
            nowpin = inpin.val(),
            nowkey = thsbtn.text();
            
            if (thsbtn.hasClass('clear-pin')) {
                $('.autopin').val('');
            } else {
                $('.autopin').val(nowpin + nowkey);
            }
        //console.log($(this).text());
        
    });
    
	$("#betting").button({icons: {primary: "ui-icon-circle-check"}}).css('width','120px').click(
		function(){
			var val      = true,
                playType = $("#TicketPlayTypeId").val();
			
			if($("#TicketEach").val() == ""){
				err_line(2,"Debe introducir Unidades",$("#mess_bet"));
				val = false;
			}
			
			if(co == 0){
				err_line(2,"Debe escoger al menos un caballo",$("#mess_bet"));
				val = false;
			}
            
            if ( role == 5 && $('#TicketAutopin').val() == '') {
                err_line(2,"El PIN del ONLINE es obligatorio.",$("#mess_bet"));
				val = false;
            }

            //exacta boxes 
            if ( playType == 7 ) {
                if ($("#1_positioner").find('div.added').length == 0 
                    ||
                    $("#2_positioner").find('div.added').length == 0) {

                    err_line(2,"EXACTA requiere caballos en todas las boxes.",$("#mess_bet"));
                    val = false;       
                }
            }

            //trifecta boxes 
            if ( playType == 8 ) {
                if ($("#1_positioner").find('div.added').length == 0 
                    ||
                    $("#2_positioner").find('div.added').length == 0
                    ||
                    $("#3_positioner").find('div.added').length == 0) {

                    err_line(2,"TRIFECTA requiere caballos en todas las boxes.",$("#mess_bet"));
                    val = false;       
                }
            }

            //supfecta boxes 
            if ( playType == 9 ) {
                if ($("#1_positioner").find('div.added').length == 0 
                    ||
                    $("#2_positioner").find('div.added').length == 0
                    ||
                    $("#3_positioner").find('div.added').length == 0
                    ||
                    $("#4_positioner").find('div.added').length == 0) {

                    err_line(2,"SUPERF. requiere caballos en todas las boxes.",$("#mess_bet"));
                    val = false;       
                }
            }
            
            
			if(val == true){
				$(this).parents('form').submit();
                $(".loadwait").show();
                $(this).hide();
			}else{
				return false;
			}
		}
	);
	
	$("#calc").button({icons: {primary: "ui-icon-calculator"}}).css('width','120px').click(function(){ 
		var amo = $("#TicketEach").val();
		var val = true;
		var type = $(".button-clicked").text();
		
		if(type == null && val == true){
			err_line(2,"Para calcular debe escoger un tipo de apuesta",$("#mess_bet"));
			val = false;
		}
		
		if(amo == "" && val == true){
			err_line(2,"Para calcular las unidades no pueden ser vacias",$("#mess_bet"));
			val = false;
		}
		
		if(co == 0 && val == true){
			err_line(2,"Para calcular debe escoger al menos un caballo",$("#mess_bet"));
			val = false;
		}
				
		if(val){
			calculate(amo,type,co);	
		}
		
		return false;
	});
  
    
    
    $('.each-list').change( function () {
        var toVal = $(this).find(':selected').text();
       
        if (toVal != 'def') {
            $('.each-field').val(toVal);
        }
       
    });
    
});
</script>
<style>	
    .autotaq-pin {
        margin: 4px;
    }
    .autotaq-pin label{
        font-weight: bold;
        font-size: 8pt;
    }
    .autotaq-pin input{
        font-weight: bold;
        font-size: 14pt;
        width: 90px;
        padding: 3px;
        margin: 3px;
        text-align: center;
    }
    .confirm-sect{
        margin: 2px;
        padding: 10px 5px 5px 5px
    }
    .number-keyboard{
        width: 108px; 
        height:145px; 
    }
    .number-key{
        float: left;
        clear: none;
        width: 24px;
        height: 24px;
        margin: 2px;
        padding: 4px;
        font-size: 14pt;
        text-align: center;
        vertical-align: middle;
        font-weight: bold;
        background-color: #DDD; 
        border-radius: 8px;
        cursor: pointer;
    }
    .clear-pin{
        width: 60px;
    }
    .number-key:hover{
        background-color: #BBB;
      
    }
  .reportetkt {
    display: none;
    width: 320px;
    margin: 0, 0, 0, 0;
   }
	@media only screen
and (min-device-width : 320px)
and (max-device-width : 768px) {
/* Layout */

.oculto {display:none}
.boton { width: 120px; padding-top: 10px; padding-bottom: 10px; margin-top: -10px;	}
	
	}
</style> 
   --ATENCION NUEVA UNIDAD INTERNACIONAL BsS. 500 PARA USUARIOS EN VENEZUELA--
    Hoy Martes estamos realizando pruebas a todos los servicios por favor no realizar apuestas..
<div class="tickets_form">
 	<div id="titl">Fecha<?php echo ": ".$dtime->date_spa_mon_abr(date("Y-m-d")); ?></div>
 	<div id="mess_bet"></div>
 	<?php echo $form->create('Ticket',array('action'=>'add'));?>
 	<div id="div_bet">
 		<div id="left_bet">
 			<div id="nextraces">
 				<!--  Pr&oacute;ximas Carreras:   -->
	 			<?php 
	 			echo $form->input('Ticket.race_id',
	 				array('options' => $nextones,'style'=>'width:200px;',
                        'empty' => array(0 => 'Select Racetrack'),'label'=>''))
	 			?>
 			</div>
            
 			<div class="divs_right plays">
            
 				<div id="play_types">
          <h4 class="oculto" style="color: blue;">Apuestas Directas</h4>
 					<button id="1" title="WIN">W</button>
 					<button id="2" title="PLACE">P</button>
 					<button id="3" title="SHOW">S</button>
 					<button id="4" title="WIN PLACE">WP</button>
 					<button id="5" class="oculto" title="WIN SHOW">WS</button>
 					<button id="6" title="WIN PLACE SHOW" >WPS</button>
          <h4 class="oculto" style="color: blue;">Apuestas Exóticas</h4>
 					<button id="7" class="oculto" title="EXACTA">EXA</button>
 					<button id="8" class="oculto" title="TRIFECTA">TRI</button>
 					<button id="9" class="oculto" title="SUPERFECTA">SUP</button>
 				</div>
		 		<?php echo $form->input('play_type_id',array('type'=>'hidden')) ?>
 			</div>
 			<div class="divs_right resumen">
				<span style="float:left">Tipo de Apuesta:&nbsp;</span>
 				<span id="play_name" style="float: left; color: blue; font-size:110%">NINGUNA</span>
 				<div id="horse_set" style="width:220px;">
 				<h4 style="color: red;">Seleccione Apuesta Exótica</h4>
 				</div>
 			</div>
 			<div id="lil_butns">
        
				<button id="next">SIGUIENTE POSICION</button>
        <h6 style="color: blue;">PROXIMA POSICION</h6>
				<button id="boxing">COMBINAR TODOS</button>
        <h6 style="color: blue;">BOX CON TODOS</h6>
			</div>
  		</div>
 		<div id="div_horses">
			<span style="color: #CCC; font-size:14pt; margin: 5px">Seleccione carrera.</span>
		</div>
 		<div id="right_bet">
            <?php
            if ($authUser['role_id'] == 4) {
            ?>
                <div class="balance-part">
                    <span class='title-balance'>Balance:</span> 
                    <span style="color: blue;" class='amo-balance'>
                        <?php echo number_format($balance,0,',','.') ?>
                    </span>
                </div>
 			<?php
            }
            ?>
            <div class="units-section">
 				 
 				<?php
                echo $form->input('each',array('label'=>'Inserte o Seleccione','div' => false,
                        'class'=>'each-field','autocomplete'=>'off','default' => '1'));
                
                echo $form->input('eachlist',array('label'=>false,'div' => false,
                        'class'=>'each-list','options' => $eachUnits,'empty' => 'Unids.'));
                ?>
 			</div>
            
            <?php
            //AUTOTAQ CONFIRM WINDOW
            if ($authUser['role_id'] == 5) {
            ?>
            
            <div class="confirm-sect">
                <button class="autotaq-confirm" title="Confirmar Apuesta">CONFIRMAR</button>
            </div>
            <?php
            }
            ?>
            <div id="montapos">
 				 <?php
                //AUTOTAQ CONFIRM WINDOW
                /*if ($authUser['role_id'] == 5) {
                    echo "Su ticket es de Bs. XXX. Escriba su PIN para confirmar el ticket.";                    
                }*/
                ?>
                <div class="autotaq-pin">
                    <?php
                    echo $form->input('autopin',array('label'=>'**SECURE PIN**','div' => false,
                            'class'=>'autopin','autocomplete'=>'off','maxlength'=> 4,
                            'type' => 'password' ) );
                    ?>
                    
                    <div class="number-keyboard">
                        <?php
                        for ( $i = 1; $i <= 10; $i ++ ) {
                            if ($i == 10) $j = 0; else $j = $i;
                            
                            echo "<div class='number-key'>$j</div>";
                            
                        }
                        ?>
                        
                        <div class='number-key clear-pin'>CLR</div>
                    </div>
                    
                </div>
                <?php
            //AUTOTAQ CONFIRM WINDOW
            if ($authUser['role_id'] == 5) {
            ?>
            
            <div class="confirm-sect">
                <button id="betting" title="Apostar" onclick="javascript:document.getElementById('betting').style.visibility = 'hidden';">Crear Ticket </button> 
            	<!--  onclick="javascript:imprSelec('reportetkt')"   -->  
            </div>
            <?php
            } else {
            ?>
                <button id="betting"  title="Apostar" class="boton" onclick="javascript:document.getElementById('betting').style.visibility = 'hidden';">Crear Ticket</button>
            <?php
            }
            ?>
                <div class="loadwait">
                    Espere...
                    <?php echo $html->image('loading_small.gif') ?>
                </div>
                <!--                
                <button id="calc" title="Calcular" style="margin-top:40px">CALCULAR</button>
                -->
 			</div>
 			
 		</div>
 	</div>
<?php echo $form->end(); ?>
</div>

 