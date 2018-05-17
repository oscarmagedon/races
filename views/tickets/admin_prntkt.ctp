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
    $id_encode   = $ticket['id']; // $ticket['number']."-".$ticket['confirm']."-".;
    $num_encode  = $ticket['number']; // $ticket['number']."-".$ticket['confirm']."-".;
    $conf_encode = $ticket['confirm']; // $ticket['number']."-".$ticket['confirm']."-".;

    // Generate Barcode data
    $barcode->barcode();
    $barcode->setType('C128');
    $barcode->setSize(60,220);
    /*
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
     */
    // NEW FULL PART
    $fullName = $id_encode . '-' . $conf_encode; //'-' . $num_encode .
    $barcode->setCode($fullName);
    // Generate filename            
    $fullidfile = 'img/barcode/code_'.$fullName.'.png';
    // Generates image file on server            
    $barcode->writeBarcodeFile($fullidfile);

}

?>

<div class="wait-screen">
	Ticket por impresora.<br />
	<?php echo $html->image('loading.gif',array('class'=>'imgload')) ?>
    <input value="Aceptar" type="button" id="accept">
</div>

<!-- NEW TICKET -->
<div class="ticket-printer">
    <table class="ticket-table" border="1">
        <tr>
            <td colspan="2" class="title-big">
                <?php echo $ticket['center'] ?>
            </td>
        </tr>
        <?php
        if ($ticketnw['Center']['rif'] != '' && $ticketnw['Center']['nro_lic'] != '') {
        ?>
            <tr>
                <td>
                    RIF: <?php echo $ticketnw['Center']['rif'] ?></td>
                <td class="right-paneled">
                    LIC: <?php echo $ticketnw['Center']['nro_lic'] ?></td>
            </tr>
        <?php
        }
        
        if ( $ticket['copies'] > 1) {
            echo "<tr><td colspan='2'>" . $ticket['copies'] . "a COPIA</td></tr>";
        }
        
        ?>  
        
        <tr>
            <td>
                NO: <?php echo $ticketnw['Ticket']['number'] ?></td>
            <td class="right-paneled">
                Creacion:</td>
        </tr>
        
        <tr>
            <td>
                SC: <?php echo $ticketnw['Ticket']['confirm'] ?></td>
            <td class="right-paneled">
                <?php echo $dtime->date_from_created($ticketnw['Ticket']['created']) ?></td>
        </tr>
        
        <tr>
            <td>
                TQ: <?php echo $ticketnw['Profile']['name'] ?></td>
            <td class="right-paneled">
                <?php echo $dtime->hour_exact_created($ticketnw['Ticket']['created']) ?></td>
        </tr>
        
        <tr>
            <td class="middle-title">
                <?php 
                if($ticketnw['Ticket']['play_type_id'] != 19) 
                    echo $race['Race']["number"]."&ordf; ";
                
                echo $race['Hipodrome']['name'];
                ?>
            </td>
            <td class="right-paneled middle-title">
                <?php 
                    echo $dtime->date_spa_mon_abr($race['Race']['race_date']) 
                ?>
            </td>
        </tr>
        
        <tr>
            <td colspan="2">
                <table class="print-dets">
                <?php
                // ==> DETAILS ==> 
                
                //BASICS W P S
                if ( $ticketnw['Ticket']['type'] == 'BASIC') {
                    
                    echo "<tr><th>Caballo</th><th>Unds.</th></tr>";
                    foreach($details as $d){
                    ?>
                        <tr>
                            <td><?php echo $d['horse'] ?></td>
                            <td><?php echo $d['und'] ?></td>
                        </tr>
                    <?php				
                    }
                }
                
                //EXOTICS
                if ( $ticketnw['Ticket']['type'] == 'EXOTIC' ) {
                    echo "<tr><th>Apuesta</th><th>Unds.</th></tr>";
                
                    
                    foreach ($details['Boxes'] as $numBox => $boxall) {
                    
                    ?>
                        
                    <tr>
                        <td style="padding-right: 15px;">
                        <?php
                            $it = 0;
                            
                            foreach ($boxall as $tit => $num) {
                                if ($it > 0) 
                                    echo ",";
                                
                                echo $num;
                                $it ++;
                            }
                            ?>
                        </td>
                        <td class="right-paneled" style="padding-right: 10px;">
                            <?php echo $details['Each'] ?></td>
                    </tr>
                
                <?php  
                    
                    }
                }
                
                //PICKS
                if ( $ticketnw['Ticket']['type'] == 'PICK') {
                    
                }
                ?>
                </table>
            </td>
        </tr>
        <tr>
            <td class="middle-title">
                <?php 
                echo $ticketnw['PlayType']['name'];
                ?>
            </td>
            <td class="middle-title">
                Unidades: <?php echo number_format($ticket['units'],0) ?>
            </td>
        </tr>
        <tr>
            <td colspan="2" class="middle-title" style="text-align: center">
                Valido por <?php echo $ticket['valid'] ?> dias.
            </td>
        </tr>
        <?php
        if ($ticket['barcode'] == 1) {
        ?>
            <tr class="barcode">
                <td colspan="2">
                    <?php
                    echo $html->image('barcode/code_'.$fullName.'.png',array(
                        'width' => '280px'
                    )); 
                    ?>
                </td>
            </tr>
        <?php
        }
        ?>
        
    </table>
</div>
<script type="text/javascript">
    var url_loc = '<?php echo $html->url(array("action"=>"add",$ticket['race_id'])) ?>';
    
    $(function() {
        
        $('.imgload').hide();
        $("#accept").show();
        
        <?php if ($test != 'show') echo "print();";  ?>

        $("#accept").click(function(){ refrescador(); });
        
    });
    
    function refrescador(){
        location = url_loc;
    }
    
    <?php if ($test != 'show') echo "setInterval('refrescador()',3000);";  ?>
</script>
<?php
/*
echo "DETAILS:: ";
pr($details);
echo "TICKET:: ";
pr($ticketnw);
echo "RACE:: ";
pr($race);
echo "TICKETFINAL:: ";
pr($ticket);
*/
?>