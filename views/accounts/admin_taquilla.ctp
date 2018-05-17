<div class="sports index">
<h2>Pagos y Recargas ONLINE</h2>
<div style="float: left; width:auto; clear:left">
	<table id="filtable">
	<tr>
		<td><?php 
			echo $form->input('profile_id',array('options'=>$profiles,
                'value'=>$profileId,'label'=>"Taquilla",
				'empty'=>array(0=>"Seleccione"),'class'=>'filter_input'))
		?></td>
		<td><?php 
			echo $form->input('since',array('value'=>$since,'label'=>"Desde",
                'class'=>'filter_input'))
		?></td>
		<td><?php 
			echo $form->input('until',array('value'=>$until,'label'=>"Hasta",
                'class'=>'filter_input'))
		?></td>
        <td><?php 
			echo $form->input("title",array('options'=>$titles,'value' => $atitle,
                'empty'=>'Todos', 'class'=>'filter_input','label'=>'Tipo')) 
		?></td>
	</tr>	
	</table>	
</div>
<?php
if ( $profileId != 0 ) {
?>	
	<div id="balance">
		<div class="account-row acc-titles">
            <b>SALDO: </b>
			<span class="acc-row-money">
                Bs. <?php echo number_format($totals['baln'],0,',','.') ?>
            </span>
		</div>
		<div class="account-row acc-income">
			<span class="acc-row-title">
				Recargas:
			</span>
	        <span class="acc-row-units">
				<?php echo number_format($totals['rels'],0,',','.') ?>
			</span>
            
		</div>
        <div class="account-row acc-income">
			<span class="acc-row-title">
				Premiados:
			</span>
            <span class="acc-row-units">
				<?php echo number_format($totals['wins'],0,',','.') ?>
			</span>
		</div>
		<div class="account-row acc-income" style="color:#005">
			<span class="acc-row-title">
				Anulados:
			</span>
            <span class="acc-row-units">
				<?php echo number_format($totals['anul'],0,',','.') ?>
			</span>
		</div>
        <div class="account-row acc-outcome">
			<span class="acc-row-title">
				Apuestas:
			</span>
			<span class="acc-row-units">
				<?php echo number_format($totals['bets'],0,',','.') ?>
			</span>
		</div>
        <div class="account-row acc-outcome">
			<span class="acc-row-title">
				Retiros:
			</span>
            <span class="acc-row-units">
				<?php 
                echo number_format($totals['rets'],0,',','.') ?>
			</span>
		</div>
	</div>
	
	<button id="addacc" href="<?php echo $html->url(array("action"=>"addtaq", $profileId)) ?>"> 
        Agregar Movimiento</button>
	
	<div style="clear:both"><p><?php echo $paginator->counter(array(
	'format' => "Pagina %page% de %pages%, mostrando %current% registros de %count% totales, empezando en %start%, terminando en %end%")) ?></p></div>
    <table cellpadding="0" cellspacing="0" class="mov-table">
	<tr>
		<th>Fecha</th>
		<th>Hora</th>
		<th>Bal. Ant.</th>
		<th>Monto</th>
        <th>Titulo</th>
		<th>Concepto</th>
	</tr>
	<?php
	$i = 0;
	foreach ($accounts as $account):
		$clsalt = '';
        $sty    = "acc-income";
        if ( $i++ % 2 == 0 ) {
			$clsalt = ' altrow';
		}
        if ( $account['Account']['add'] == 0 ) {
            $sty = "acc-outcome";
        }
	?>
		<tr class="<?php echo $sty. $clsalt ;?>">
            <td>
				<?php echo $dtime->date_from_created($account['Account']['created']) ?>
			</td>
			<td>
				<?php echo $dtime->hour_from_created($account['Account']['created']) ?>
			</td>
            <td style="text-align: right; font-size: 95%;">
                <?php echo number_format($account['Account']['balance'],0,',','.') ?>
            </td>
			<td style="text-align: right">
				<?php 
				if($account['Account']['add'] == 0) echo "- ";
                    echo number_format($account['Account']['amount'],0,',','.') ?>
			</td>
            <td style="text-align: left">
                <?php echo $account['Account']['title'] ?>
            </td>
			<td style="text-align: left">
				<?php echo $account['Account']['metainf'] ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
</div>
<div class="paging">
	<?php echo $paginator->prev('<< anterior', array(
            'url' => array('action' => 'index', $profileId,$since,$until)), 
            null, array('class'=>'disabled')) ?>
    | 	
    <?php echo $paginator->numbers(array(
            'url' => array('action' => 'index', $profileId,$since,$until))) ?>
    | 
	<?php echo $paginator->next('siguiente >>', array(
            'url' => array('action' => 'index', $profileId,$since,$until)), 
            null, array('class' => 'disabled')) ?>
<?php
}
?>
</div>
<script>
$(function(){
	var filt_url = '<?php echo $html->url(array("controller"=>"accounts",
	"action"=>"taquilla")) ?>';
		
    $("#panel_look").dialog({
		autoOpen: false,
		bgiframe: true,		
		modal: true,
		height: 350,
		width:  450,
		resizable: true
	});
	
    $("#since").attr('readonly',true).datepicker({dateFormat:"yy-mm-dd"});
	$("#until").attr('readonly',true).datepicker({dateFormat:"yy-mm-dd"});

       
    $('.filter_input').change(function () {
    	location = filt_url + "/" + $("#profile_id").val() + "/" + $("#since").val() + 
                "/" + $("#until").val() + "/" + $("#title").val();
	});    
    
	$("#addacc").button({ icons: { primary: "ui-icon-plus" }}).click(
		function(){
			$('#panel_look').html('<?php echo $html->image("loading.gif")?>');
			$('#panel_look').dialog({title:$(this).text()});
			$('#panel_look').load($(this).attr('href'));
			$('#panel_look').dialog('open');
		}
	);
	
});
</script>
<style>
#balance{
	width: 220px; 
	height: auto;
	float: left; 
	margin: 0 10px 5px 10px; 
	padding: 5px;
	border: 1px solid #555;
	border-radius: 8px;
    font-size: 11pt;
}
	.account-row{
		margin: 3px 1px;
		padding: 1px;
		height: 14px;
    }
		.acc-titles{
			border-bottom: 1px solid #555; 
			padding-bottom: 4px;
		}
		.acc-reload{
			color: #00A;
		}
		.acc-bets{
			color: #A00;
		}
        
		.acc-income{
			color: #0A0;
		} 
        .acc-outcome{
			color: #A00;
		}
		.acc-row-title{
			font-style: italic;
		}
		.acc-row-units{
			float: right;
			margin-right: 10px;
		}
		.acc-row-money{
			float: right;
			font-weight: bold;
			width: 150px;
			text-align: right;
		}
        .mov-table {
            width: auto;
        }
        .mov-table th{
            padding: 6px 8px;
            font-size: 12pt;
        }
        .mov-table td{
            padding: 4px 8px;
            font-size: 11pt;
        }
</style>
