<?php
//pr($results);
//pr($allHorses);

$raceId = $race['Race']['id'];

$horses = $dtime->getListedEnabled($allHorses);

echo $form->create('Result',array('action' => 'rootset'));

echo $form->input("Race.id",array('value' => $raceId,'type'=>'hidden'));

//verificar por data
if (!empty($this->data)) {
	echo $form->input("Result.0.id");
    echo $form->input("Result.1.id",array('type'=>'hidden'));
    echo $form->input("Result.2.id",array('type'=>'hidden'));
    echo $form->input("Result.3.id",array('type'=>'hidden'));
    
    if (isset($this->data['Result'][4])) {
        echo $form->input("Result.4.id",array('type'=>'hidden'));
    }
}
?>
<div class="horses index">
<h2>
	RESULTADOS ROOT :: 
	<?php echo $dtime->date_spa_mon_abr($race['Race']['race_date']) ?> &gt;&gt;
	<?php echo $race['Hipodrome']['name']?> &gt;&gt;
	<?php echo $race['Race']['number']?>&ordf; carr.  
</h2>

<table style="width: auto">
	<tr>
		<th>Resultados</th><th>Retirados</th>
	</tr>
	<tr>
		<td rowspan="3">
			<table cellpadding="0" cellspacing="0">
				<tr>
					<th>Pos.</th>
					<th>Caballo</th>
					<th>Win</th>
					<th>Place</th>
					<th>Show</th>
				</tr>
				<tr>
					<td>1&ordm; </td>
					<td>
						<?php echo $form->input("Result.0.horse_id.",array(
                                'options' => $horses,'empty' => array(0 => 'Co.'),
                                'class' => 'horse_sel')) ?>
					</td>
					<td>
						<?php echo $form->input("Result.0.win",array(
                                'class'=>'number-race',
                                'label'=>false,'div'=>false)) ?>
					</td>
					<td>
						<?php echo $form->input("Result.0.place",array(
                                'class'=>'number-race',
                                'label'=>false,'div'=>false)) ?>
					</td>
					<td>
						<?php echo $form->input("Result.0.show",array(
                                'class'=>'number-race',
                                'label'=>false,'div'=>false)) ?>
					</td>
				</tr>
				<tr id="draw_first">
					<td>1&ordm; (emp)
					</td>
					<td>
						<?php echo $form->input("Result.4.horse_id.",array(
                                'options' => $horses,'empty' => array(0 => 'Co.'),
                                'class' => 'horse_sel','title'=>'drawer',
                                'label'=>false,'div'=>false)) ?>
					</td>
					<td>
						<?php echo $form->input("Result.4.win",array(
                                'class'=>'number-race',
                                'title'=>'drawer','label'=>false,'div'=>false)) ?>
					</td>
					<td>
						<?php echo $form->input("Result.4.place",array(
                                'class'=>'number-race',
                                'title'=>'drawer','label'=>false,'div'=>false)) ?>
					</td>
					<td>
						<?php echo $form->input("Result.4.show",array(
                            'class'=>'number-race',
                            'title'=>'drawer','label'=>false,'div'=>false)) ?>
					</td>
				</tr>
				<tr>
					<td>2&ordm; </td>
					<td>
						<?php echo $form->input("Result.1.horse_id.",array(
                            'options' => $horses,'empty' => array(0 => 'Co.'),
                            'class' => 'horse_sel',
                            'label'=>false,'div'=>false)) ?>
					</td>
					<td>
						-
					</td>
					<td>
						<?php echo $form->input("Result.1.place",array(
                            'class'=>'number-race',
                            'label'=>false,'div'=>false)) ?>
					</td>
					<td>
						<?php echo $form->input("Result.1.show",array(
                            'class'=>'number-race',
                            'label'=>false,'div'=>false)) ?>
					</td>
				</tr>
				<tr>
					<td>3&ordm; </td>
					<td>
						<?php echo $form->input("Result.2.horse_id.",array(
                            'options' => $horses,'empty' => array(0 => 'Co.'),
                            'class' => 'horse_sel',
                            'label'=>false,'div'=>false)) ?>
					</td>
					<td>
						-
					</td>
					<td>
						-
					</td>
					<td>
						<?php echo $form->input("Result.2.show",array(
                            'class'=>'number-race',
                            'label'=>false,'div'=>false)) ?>
					</td>
				</tr>
				<?php 
				if(count($horses) > 3){
				?>
				<tr>
					<td>4&ordm;	</td>
					<td>
						<?php echo $form->input("Result.3.horse_id.",array(
                            'options' => $horses,'empty' => array(0 => 'Co.'),
                            'class' => 'horse_sel',
                            'label'=>false,'div'=>false)) ?>
					</td>
					<td>
						-
					</td>
					<td>
						-
					</td>
					<td>
						-
					</td>
				</tr>
				<?php 
				}
				?>
				<tr class="row-validation">
					<th colspan="5">
					</th>
				</tr>
			</table>
			
			<button id="hasdraw">Empate en 1o
			
			<button id="hasnotdraw">SIN Empate</button>
			
		</td>
		
		<td class="retired-horses">
			<?php
            echo $form->input("RetHorses.selection",array(
                    'options' => $horses,
                    'empty'   => array(0 => 'Seleccione...'),
                    'class'   => 'retire-horse',
                    'label'   => false));
            
            //ya retirados
            foreach ($allHorses as $allh) {
                if($allh['enable'] == 0) {
                    echo "<div class='retired-elem'>";
                    echo "<input type='hidden' value='1' ";
                    echo "name='data[Retired][" . $allh['id'] . "]' />";
                    echo $allh['number'] . "-" . $allh['name'];
                    echo "<a href='#' class='del-ret'";
                    echo "onclick='delRet(this); return false'>x</a>";
                    echo "</div>";
                }
            }
            ?>
		</td>
	</tr>
	<tr>
		<th>Premios Especiales</th>
	</tr>
	<tr>
		<td class='special-res'>
            <?php 
                echo $form->input("Special.exacta",
                    array('class'=>'specials',
                        'label'=>'EXAC.','div'=>false));
                
                echo $form->input("Special.trifecta",
							array('class'=>'specials',
                            'label'=>'TRIF.','div'=>false));
                
                echo $form->input("Special.superfecta",
                        array('class'=>'specials',
                            'label'=>'SUPRF.','div'=>false));

				
            ?>
		</td>
	</tr>
</table>
</div>
<?php 
	echo $form->end('GUARDAR');
?>

<script>
var draw_flag = false;

var messages  = {
					'empty'  : 'Ningun campo puede estar vacio.',
					'horse'  : 'Debe seleccionar caballos correctos.'
				};

$(function(){
	
	$("#hasdraw").button({icons: { primary: "ui-icon-newwin" }}).click(function(){
		$("#draw_first").show('slow');
		draw_flag = true;
		$(this).hide();
		$("#hasnotdraw").show();
		return false;
	});

	$("#hasnotdraw").button({icons: { primary: "ui-icon-close" }}).click(function(){
		$("#draw_first").hide('slow');
		draw_flag = false;
		$(this).hide();
		$("#hasdraw").show();
		return false;
	});
	
	<?php
	if(!empty($results[5])){
	?>
		$("#hasnotdraw").show();
		$("#hasdraw").hide();
	<?php
	}else{
	?>
		$("#hasnotdraw").hide();
		$("#draw_first").hide();
	<?php
	}
	?>
	//retired horses
	//$(".retired-horses").buttonset();
	
	//validation messages
	$(".row-validation").hide();
	
	// only number! take it to js libs
	$(".number-race").keydown(function (e) {
        // Allow: backspace, delete, tab, escape, enter and .
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
             // Allow: Ctrl+A
            (e.keyCode == 65 && e.ctrlKey === true) || 
             // Allow: home, end, left, right, down, up
            (e.keyCode >= 35 && e.keyCode <= 40)) {
                 // let it happen, don't do anything
                 return;
        }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
    });
	
	//validation before sending
	$("#ResultSetForm").submit(function(){
		var tosub = true;

		if(draw_flag == false){
			$("#draw_first").remove();
		}
		
		//validate number empty 
		$(".number-race").each(function(i){
			
			if($(this).val() == ""){
				$(".row-validation").find('th').html(messages['empty'])
				$(".row-validation").show('slow');
				tosub = false;
			}
		});

		//
		$(".horse_sel").each(function(i){
			if($(this).val() == 0){
				$(".row-validation").find('th').html(messages['horse'])
				$(".row-validation").show('slow');
				tosub = false;
			}
		});
        
		
		return tosub;
	});
	
	//RETIRE HORSES
    $(".retire-horse").change(function(){
        var $thisSel  = $(this),
            $selected = $thisSel.find(':selected'),
            $selTxt   = $selected.text(),
            $selVal   = $selected.val(),
            $tdParent = $thisSel.parents('td'),
            $delBtn   = "<a href='#' class='del-ret'" +
                            "onclick='delRet(this); return false'>x</a>",
            $inpHidn  = "<input type='hidden' value='1' " +
                        "name='data[Retired][" + $selVal + "]' />";
            
        //alert($(this).val());
        $tdParent.append("<div class='retired-elem'>" + 
                           $inpHidn + $selTxt + $delBtn + "</div>");
        
    });
});

function delRet(link){
    var $link = $(link);
    
    $link.parent().remove();
    
    //return false;
}
</script>
