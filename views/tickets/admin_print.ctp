<?php 
function appears_box($boxes){
	
    $count  = array();
	
    foreach ($boxes as $box) {
		
        foreach ($box as $k => $b) {
            
            if(!empty($count[$k][$b])) {
                $count[$k][$b] ++;
            } else {
                $count[$k][$b] = 1;
            }
				
		}
        
	}
    
	$count_each = 0;
	$is_box     = true;
    
	foreach($count as $c){
		foreach($c as $n){
			if($n != $count_each && $count_each != 0){
				$is_box = false;	
			}	
		}
		$count_each = $n;
	}
	
	return $is_box;
}
if ($ticket['barcode'] == 1) {
    //tkid, confirm
    $id_encode = $ticket['id']; // $ticket['number']."-".$ticket['confirm']."-".;
    $conf_encode = $ticket['confirm']; // $ticket['number']."-".$ticket['confirm']."-".;

    // Generate Barcode data
    $barcode->barcode();
    $barcode->setType('C128');
    $barcode->setSize(50,200);

    //ID PART
    $barcode->setCode($id_encode);
    // Generate filename            
    $idfile = 'img/barcode/code_'.$id_encode.'.png';
    // Generates image file on server            
    $barcode->writeBarcodeFile($idfile);

    //COINFIRM PART
    $barcode->setCode($conf_encode);
    $name = 'code_'.$id_encode."_".$conf_encode;
    // Generate filename            
    $confile = "img/barcode/$name.png";
    // Generates image file on server            
    $barcode->writeBarcodeFile($confile);
}
?>
<script type="text/javascript">
var url_loc = '<?php echo $html->url(array("action"=>"add")) ?>';
$(function() {
    
    print();
    
    $("#accept").click(function(){ refrescador() });
});
function refrescador(){
	location = url_loc;
}

setInterval("refrescador()",3000);
</script>
<style>
@media print {
	.for_screen{
   		display:none;	
   	}
	.printing{
		width:300px; 
  		height: 150px;
  		margin-left:5px;  		
   		color: #000000;
		font: "Arial" 14pt normal;
	}
}
@media screen {
  	.printing{
 		display:none;
    }
   	.for_screen {
   		font-size: 16pt; 
		color: #000000; 
		font-family: "Arial"; 
		font-weight: bolder;
	}
}
.printing{
	width: 300px;
	padding-left:5px;
}
#title_center{
	float: left; 
	clear: both; 
	width: 290px; 
	margin-left:5px;
	text-align: center;
	font-size:14pt;
	font-weight:bold;
}
#rif_lic{
	width: 300px;
	float: left;
}
#rif{
	float: left; width: 155px;
	margin-bottom: 5px;
}
#lic{
	float: right; width: 135px;
	text-align: right; margin-bottom: 5px;
}
#number_serial{
	float: left; 
	clear: none; 
	width: 190px;
	font-size:12pt;
}
	
#create_time{
	float: right; 
	clear: none; 
	width: 110px; 
	text-align:right;
	font-size:12pt;
}
	
#race_data{
	float: left; 
	clear: both; 
	width: 300px; 
	text-align: center;
	font-size:16pt;
	margin-top:10px;
}
	#race_number{
		float:left; 
		width:40px;
		font-size:12pt;
		font-weight:bold;
	}
	#hipodrome{
		float:left;
		width:150px; 
		font-size:12pt;
		font-weight:bold;
		text-align:center;
	}
	#race_date{
		float:right;
		text-align:right;
		font-size:12pt;
		width: 110px;
		font-weight:bold;
	}

#play_data{
	float: left; 
	clear: both; 
	width: 285px; 
	margin-top:5px;
}
	#horses{
		width:100%;
		margin-left:5px;
		margin-bottom:10px;
		background-color:blue;
		clear:both;
	}
	#play_type{
		float:left; 
		width:90px;
		padding-left: 5px; 
		font-weight:bold; 
		font-size:14pt;
	}
	#units{
		float:right;
		text-align:right;
		width: 100px;
		font-size:14pt;
	}
	.barcode{
		border-top: 1px dotted #000;
		border-bottom: 1px dotted #000;
		padding: 3px;
	}
#valid{
	clear:both;
	font-size:14pt;
	font-weight:bold;
	padding-top: 10px;
}
</style>
<?php //pr($ticket['Details']) ?>
<div class="printing"> 
	<?php
    if ($ticket['barcode'] == 1) {
    ?>
        <div class="barcode">
            <?php
            echo $html->image('barcode/code_'.$id_encode.'.png'); 
            ?>
        </div>
	<?php
    }
    ?>
    
    <div id="title_center">
		<?php echo $ticket['center'] ?>
	</div>
	<div id="rif_lic">
		<div id="rif">RIF: <?php echo $ticket['rif'] ?></div>
		<div id="lic">LIC: <?php echo $ticket['lic'] ?></div>
	</div>
	<div id="number_serial">
		NO.: <?php echo $ticket['number'] ?><br />
		SC.: <?php echo $ticket['confirm'] ?><br />
		TQ.: <?php echo $ticket['profile'] ?>	
	</div>
	<div id="create_time">
		Creaci&oacute;n:<br />
		<?php echo $dtime->date_from_created($ticket['created']) ?><br />
		<?php echo $dtime->hour_exact_created($ticket['created']) ?>
	</div>
	<div id="race_data">
		<div id="race_number">
			<?php if($ticket['play_type_id'] != 19) echo $ticket["race_number"]."&ordf;" ?>
		</div>
		<div id="hipodrome">
			<?php echo $ticket['hipodrome'] ?>
		</div>
		<div id="race_date">
			<?php 
			if($ticket['race_date'] != "")
				echo $dtime->date_spa_mon_abr($ticket['race_date']) 
			?>
		</div>
	</div>
	
	<div id="play_data">
		<div id="horses">
			<table cellpadding="0" cellspacing="0" border="0" style="border:none;margin-bottom: 0px; width:100%">
				<?php 
				
                if (empty($ticket['Details']['Each'])) { // TICKET W P S normal				
					if($ticket['play_type_id'] == 19){
					?>
						<?php 
						foreach($ticket['Details'] as $r => $horses){
						?>
							<tr><td style="padding: 0px; border-right:none; font-size:12pt; text-align:center">
							<?php  
							echo "<b style='font-size:110%'>Carr. $r: </b>";
							$i = 1;
							foreach ($horses as $h) {
								if($i != 1)
									echo ", ";
								
								echo $h;
								$i ++;	
							}
							?> 
							</td></tr>
						<?php				
						}	
					}else{
					?>
					<tr>
						<td style="padding: 0px; border-right:none; font-size:12pt;">Caballo</td>
						<td style="padding: 0px; border-right:none; font-size:12pt;">Und.</td>
					</tr>
				
						<?php 
						foreach($ticket['Details'] as $d){
						?>
							<tr>
								<td style="padding: 0px; border-right:none; font-size:12pt; text-align:left">
									<?php echo $d['horse'] ?> 
								</td>
								<td style="padding: 0px; border-right:none; font-size:12pt;"><?php echo $d['und'] ?></td>
							</tr>
						<?php				
						}
					} 	
				} else { // ticket ex, tri, sup
				
                ?>
					<tr>
						<td style="padding: 0px; border-right:none; font-size:12pt;">Bet</td>
						<td style="padding: 0px; border-right:none; font-size:12pt;">Und.</td>
					</tr>
				<?php 
					//$dtime->date_spa_mon_abr($ticket['race_date'])
					if(appears_box($ticket['Details']['Boxes'])){
					?>
						<tr>
							<td style="padding: 0px; border-right:none; font-size:12pt;">
								
                                <?php 
                                
                                $totals = count($ticket['Details']['Boxes']); 
                                //aqui cuantas combinations hay
                                
                                if ($totals > 1) {
                                    echo "<b>BOX </b>";
                                }
                                
                                $it = 0;
                                foreach($ticket['Details']['Boxes'][1] as $b){
                                    if ($it != 0) {
                                        echo ", ";
                                    }
                                    echo $b;
                                    
                                    $it ++;
                                }
								?>
							</td>
							<td style="padding: 0px; border-right:none; font-size:12pt;">
								<?php echo $ticket['Details']['Each'] ?></td>
						</tr>
					<?php 
					}else{
						foreach($ticket['Details']['Boxes'] as $box){
						?>
							<tr>
								<td style="padding: 0px; border-right:none; font-size:12pt;">
									<?php 
									foreach($box as $b){
										echo $b.", ";
									}
									?>
								</td>
								<td style="padding: 0px; border-right:none; font-size:12pt;">
									<?php echo $ticket['Details']['Each'] ?></td>
							</tr>
						<?php 	
						}
					}
				}	
				?>
			</table>
		</div>
		<div id="play_type">
			<?php 
			$ptype = $ticket['play_type']; 
			if($ticket['play_type_id'] == 19){
				if($ticket['pick'] == 2)
					$ptype = "DBL";
				else
					$ptype .= " ".$ticket['pick'];
			} 
			echo $ptype;
			?>
		</div>
		<div id="units">
			U: <?php echo number_format($ticket['units'],0) ?>
		</div>
		<div id="valid">
			Valido por <?php echo $ticket['valid'] ?> dias.
		</div>
		<?php
        if ($ticket['barcode'] == 1) {
        ?>
            <div class="barcode">
                <?php
                echo $html->image('barcode/'.$name.'.png'); 
                ?>
            </div>
        <?php
        }
        ?>
	</div>	
</div>
<div class="for_screen">
	Ticket enviandose a impresora.<br />
	<input value="Aceptar" type="button" style="width:100px; text-align:center;" id="accept">
</div>