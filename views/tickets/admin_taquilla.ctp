<script type="text/javascript">
var load_img = 'Cargando... <?php echo $html->image("loading_small.gif",array("alt"=>"Esperando..."))?>';
var url_details = '<?php echo $html->url(array("controller"=>"tickets","action"=>"horses_details"))?>';

$(function(){
	$("#date").attr('readonly',true);
	$("#date").datepicker({dateFormat: "yy-mm-dd"});

	$("#filt").click(function(){
		var filt_url = '<?php echo $html->url(array("action"=>"taquilla","/")) ?>'
		var date = $("#date").val();
				
		location = filt_url + "/" + date;
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
</style>
<div class="tickets index">
<h2>Mis Tickets</h2>

<h3 class="und-nac">Und. NACIONAL <?= $unitNac ?> </h3>
<h3 class="und-int">Und. INTERNAC. <?= $unitInt ?> </h3>

<table style="width:80%">
<tr>
	<th>Filtrar Por:</th>
	<td><?php 
		echo $form->input('date',array('value'=>$date,'label'=>"Fecha",'class'=>'filter_input','style'=>'width:120px'))
	?></td>
	<td><?php echo $form->button('Filtrar',array('id'=>'filt')) ?></td>
</tr>
</table>
<p>
<?php
echo $paginator->counter(array(
'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true)
));
?></p>
<table cellpadding="0" cellspacing="0">
<tr>
	<th>Numero</th>
	<th>Fecha</th>
	<th>Hora</th>
  <th>Hipod.</th>
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
    
    $tktUnitsCurrency = $ticket['Ticket']['units'] * $myUnit;
    $tktPrizeCurrency = $ticket['Ticket']['prize'] * $myUnit;
    
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
        <td class="<?= $unitCls ?>">
			<?php echo $hipods[$ticket['Race']['hipodrome_id']] ?>
		</td>
		<td>
			<?php echo $ticket['Ticket']['units'] ?>
		</td>
		<td class="currency">
            <span class="unity <?= $unitCls ?>"><?= $myUnit ?></span>
            <span class="unit-amo"><?php echo $tktUnitsCurrency ?></span>
		</td>
		<td>
			<?php echo $ticket['Ticket']['prize'] ?>
		</td>
		<td class="currency">
            <span class="unity <?= $unitCls ?>"><?= $myUnit ?></span>
            <span class="unit-amo"><?= $tktPrizeCurrency ?></span>
		</td>
		<td>
			<?php 
            if ( $ticket['Ticket']['prize'] > 0 ) {
                echo $ticket['PayedStatus']['name'];
            }
			
			if($ticket['Ticket']['enable'] == 0){
				echo "<span style='color:Red; font-weight:bold'> ANULADO</span>";
			}
			?>
		</td>
		<td class="actions">
			<?php 
			echo $html->link("Detalles", array('action'=>'#'),array('class'=>'detail','id'=>$ticket['Ticket']['id'])); 
			?>
		</td>
	</tr>
<?php endforeach; ?>
</table>
</div>
<div class="paging">
	<?php echo $paginator->prev('<< '.__('previous', true), array('url'=>array('action'=>'taquilla',$date)), null, array('class'=>'disabled'));?>
 | 	<?php echo $paginator->numbers(array('url'=>array('action'=>'taquilla',$date))); ?>
	<?php echo $paginator->next(__('next', true).' >>', array('url'=>array('action'=>'taquilla',$date)), null, array('class'=>'disabled'));?>
</div>