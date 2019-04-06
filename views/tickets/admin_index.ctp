<style>
    .und-nac {
        color :#080;
    }
    .und-int {
        color :#008;
    }
    .unity{
        font-size: 90%;
        float:left;
        padding: 2px 4px;
    }
    .unit-amo {
        float: right;
        padding: 2px 6px;
    }
    .table-tks {
        width: auto;
    }
    .table-tks th{
        padding: 4px 8px;
        font-size: 95%;
    }
    .table-tks td{
        padding: 4px;
    }
</style>
<div class="tickets index">
<h2><?php __('Tickets');?></h2>
<h3 class="und-nac">Und. NACIONAL <?= $unitNac ?> </h3>
<h3 class="und-int">Und. INTERNAC. <?= $unitInt ?> </h3>
<table style="width:auto;">
<tr>
	<td>
	<?php echo $form->input('since',array('value'=>$since,'label'=>"Desde",'style'=>'width:90px')) ?>
	</td>
	<td>
	<?php echo $form->input('until',array('value'=>$until,'label'=>"Hasta",'style'=>'width:90px')) ?>
	</td>
	<td>
	<?php echo $form->input('profile',array('value'=>$profileId,'label'=>"Taquilla",'options'=>$profiles,'empty'=>array(0=>'Todos'))) ?>
	</td>
	<td>
	<?php echo $form->input('winner',array('value'=>$winner,'label'=>"Resultado",'options'=>array(0=>'Todos',1=>'Ganadores',2=>'Perdedores'))) ?>
	</td>
	<td>
	<?php echo $form->input('payed',array('value'=>$payed,'label'=>"Pagado",'options'=>array(0=>'Todos',1=>'Pagados',2=>'No Pagados',3=>'Anulados'))) ?>
	</td>
	<td>
	<?php echo $form->button('Filtrar',array('id'=>'filt')) ?>
	</td>
</tr>
</table>
<p>
<?php
//echo $unitNac. "-".$unitInt;
echo $paginator->counter(array(
'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true)
));
?></p>
<table cellpadding="0" cellspacing="0" class='table-tks'>
<tr>
	<th>Numero</th>
	<th>Fecha</th>
	<th>Hora</th>
	<th>Taquilla</th>
    <th>Via</th>
    <th>Hipod</th>
	<th>No Car.</th>
	<th>Unidades</th>
	<th>Monto Bs</th>
	<th>Premio</th>	
	<th>Premio Bs</th>
	<th>Estado</th>
	<th>Detalles</th>
</tr>
<?php
$i = 0;
foreach ($tickets as $ticket):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
    
    $myUnit  = $unitNac;
    $unitCls = 'und-nac';
    if (in_array($ticket['Race']['hipodrome_id'],$intls)) {
        $myUnit  = $unitInt;
        $unitCls = 'und-int'; 
    }

    $tktUnitsCurrency = ($ticket['Ticket']['units'] * 1 ) * ( $myUnit * 1 ) ;
    $tktPrizeCurrency = ($ticket['Ticket']['prize'] * 1 ) * ( $myUnit * 1 ) ;

    //echo $ticket['Ticket']['prize'] . ' by ' . $myUnit . ' is ' . $tktPrizeCurrency;

    //$unitNac = number_format($unitNac,0);
	//$unitInt = number_format($unitInt,0);
	?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $ticket['Ticket']['number'] ?>
		</td>
		<td>
			<?php echo $dtime->date_from_created($ticket['Ticket']['created']) ?>
		</td>
		<td>
			<?php echo $dtime->hour_from_created($ticket['Ticket']['created']) ?>
		</td>
		<td>
			<?php echo $ticket['Profile']['name'] ?>
		</td>
        <td>
            <?php echo $ticket['Ticket']['via'] ?>
        </td>
        <td class="<?= $unitCls ?>">
			<?php 

			if (isset($hipods[$ticket['Race']['hipodrome_id']])) {
				echo $hipods[$ticket['Race']['hipodrome_id']];
			} else {
				echo $ticket['Race']['hipodrome_id']. '- unset'; 
			}

			?>
		</td>
        <td>
			<?php echo $ticket['Race']['number'] ?>a.
		</td>
		<td class="currency">
            <span class="unit-amo">
                <?php echo number_format($ticket['Ticket']['units'],0,',','.') ?>
            </span>
		</td>
        <td class="currency">
            <span class="unity <?= $unitCls ?>"><?= $myUnit ?></span>
            <span class="unit-amo"><?php echo number_format($tktUnitsCurrency,0,',','.') ?></span>
		</td>
		<td class="currency">
            <span class="unit-amo">
                <?php echo number_format($ticket['Ticket']['prize'],0,',','.') ?>
            </span>
		</td>
		<td class="currency">
            <span class="unity <?= $unitCls ?>"><?= $myUnit ?></span>
            <span class="unit-amo"><?php echo number_format($tktPrizeCurrency,0,',','.') ?></span>
		</td>
		<td>
			<?php 
			echo $ticket['PayedStatus']['name'];
			
			if ( $ticket['Ticket']['enable'] == 0 ) {
				echo "<br /><span style='color:Red; font-weight:bold'>(ANULADO)</span>";
			}

			if ( $ticket ['Ticket']['payed_status_id'] == 2 
					&& $ticket['Ticket']['payed_at'] != ''
				) { 
				
				echo "<br /><small>";	
				echo $dtime->date_from_created($ticket['Ticket']['payed_at']);
				echo ', ';
				echo $dtime->hour_from_created($ticket['Ticket']['payed_at']);
				echo "</small>";
			}
			?>
		</td>
		<td>
			<?php echo $html->link("Detalles", array('action'=>'#'),array('class'=>'detail','id'=>$ticket['Ticket']['id'])) ?>
		</td>
	</tr>
<?php endforeach; 

?>
</table>
</div>
<?php
/*
echo $unitNac . " - " . $unitInt; 
pr($intls);

pr($tickets);
*/
//pr($hipods);
?>

<div class="paging">
	<?php echo $paginator->prev('<< '.__('previous', true), array('url'=>array('action'=>'index',$since,$until,$profileId,$winner,$payed)), null, array('class'=>'disabled'));?>
 | 	<?php echo $paginator->numbers(array('url'=>array('action'=>'index',$since,$until,$profileId,$winner,$payed))); ?>
	<?php echo $paginator->next(__('next', true).' >>', array('url'=>array('action'=>'index',$since,$until,$profileId,$winner,$payed)), null, array('class'=>'disabled'));?>
</div>
<script type="text/javascript">
var load_img = 'Cargando... <?php echo $html->image("loading_small.gif",array("alt"=>"Esperando..."))?>';
var url_details = '<?php echo $html->url(array("controller"=>"tickets","action"=>"horses_details"))?>';

$(function(){
	$("#since").attr('readonly',true).datepicker({dateFormat:"yy-mm-dd"});
	$("#until").attr('readonly',true).datepicker({dateFormat:"yy-mm-dd"});

	$("#filt").click(function(){
		var filt_url = '<?php echo $html->url(array("action"=>"index","/")) ?>'
		var since = $("#since").val();
		var until = $("#until").val();
		var profile = $("#profile option:selected").val();
		var winner = $("#winner option:selected").val();
		var payed = $("#payed option:selected").val();
				
		location = filt_url + "/" + since + "/" + until + "/" + profile + "/" + winner + "/" + payed;
	});	
	
	$(".detail").click(function(){
		var tik_id = $(this).attr('id');
		var tdelem = $(this).parents("td");	
		tdelem.html(load_img);
		tdelem.load(url_details + "/" + tik_id);
		return false;
	});
});
</script>
