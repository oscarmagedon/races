<script type="text/javascript">
var filt_url = '<?php echo $html->url(array("action"=>"salesnew")) ?>';
$(function(){
	$("#since").attr('readonly',true).datepicker({dateFormat:"yy-mm-dd"});
	$("#until").attr('readonly',true).datepicker({dateFormat:"yy-mm-dd"});
	
	$("#filt").click(function(){
		
		var since     = $("#since").val(),
		    until     = $("#until").val();
				
		location = filt_url + "/" + since + "/" + until;
        
	});	
});
</script>

<style>
.filter-table{
	width: 500px;
}
.und-nac {
    color :#080;
}
.und-int {
    color :#008;
}
.totals-table{
	width: auto;
}
.totals-table th{
	padding: 3px 12px 1px 4px;
}
.totals-table td{
	font-weight: bold;
	font-size: 115%;
	text-align: right;
	padding: 2px 10px 1px 20px;
    border: 1px solid #000;
}

.totals-row td {
    font-style: italic;
    padding-top: 10px;
    font-size: 120%;
}

.profiles-totals {
    width: auto;
}

.profiles-totals th{
	padding: 4px 8px;
}
.profiles-totals td{
	text-align: right;
	padding: 4px 8px;
}
.profiles-totals .profname{
	text-align: left;
	padding-left: 10px;
    font-size: 100%;
}
</style>

<h2>NUEVA Pantalla de VENTAS</h2>

<table class="filter-table" cellpadding="0" cellspacing="0">
    <tr>
        <th>Filtrar Por:</th>
        <td><?php 
            echo $form->input('since',array('value'=>$since,'label'=>"Desde",'class'=>'filter_input','style'=>'width:120px'))
        ?></td>
        <td><?php 
            echo $form->input('until',array('value'=>$until,'label'=>"Hasta",'class'=>'filter_input','style'=>'width:120px'))
        ?></td>
        <td><?php echo $form->button('Filtrar',array('id'=>'filt')) ?></td>
    </tr>
</table>

<h3>TOTALES</h3>    
<?php
//pr($totals)
?>

<table cellpadding="0" cellspacing="0" class="totals-table">
    <tr>
        <th colspan="2">Tipo</th>
        <th>Tickets</th>
        <th>Unds</th>
        <th>Vendido</th>
        <th>Tks. Prem.</th>
        <th>Premios Und</th>
        <th>Total Premios</th>
        <th>Utilidad</th>
    </tr>
    <tr>
        <td class="und-nac">NACIONALES</td>
        <td class="und-nac">
            <?php echo number_format($totals['nat']['uv'],0,',','.') ?>
        </td>
        <td>
            <?php echo number_format($totals['nat']['sa']['co'],0,',','.') ?>
        </td>
        <td>
            <?php echo number_format($totals['nat']['sa']['un'],0,',','.') ?>
        </td>
        <td class="und-nac">
            <?php echo number_format($totals['nat']['sa']['to'],0,',','.') ?>
        </td>
        <td> 
            <?php echo number_format($totals['nat']['pr']['co'],0,',','.') ?>
        </td>
        <td> 
            <?php echo number_format($totals['nat']['pr']['pr'],0,',','.') ?>
        </td>
        <td class="und-nac">
            <?php echo number_format($totals['nat']['pr']['to'],0,',','.') ?>
        </td>
        <td class="und-nac">
            <?php echo number_format($totals['nat']['ut'],0,',','.') ?>
        </td>
    </tr>
    <tr>
        <td class="und-int">INTERNAC.</td>
        <td class="und-int">
            <?php echo number_format($totals['int']['uv'],0,',','.') ?>
        </td>
        <td>
            <?php echo number_format($totals['int']['sa']['co'],0,',','.') ?>
        </td>
        <td>
            <?php echo number_format($totals['int']['sa']['un'],0,',','.') ?>
        </td>
        <td class="und-int">
            <?php echo number_format($totals['int']['sa']['to'],0,',','.') ?>
        </td>
        <td> 
            <?php echo number_format($totals['int']['pr']['co'],0,',','.') ?>
        </td>
        <td> 
            <?php echo number_format($totals['int']['pr']['pr'],0,',','.') ?>
        </td>
        <td class="und-int">
            <?php echo number_format($totals['int']['pr']['to'],0,',','.') ?>
        </td>
        <td class="und-int">
            <?php echo number_format($totals['int']['ut'],0,',','.') ?>
        </td>
    </tr>
    <tr class='totals-row'>
        <td colspan="2">TOTALES</td>
        <td>
            <?php echo number_format($totals['tot']['tks'],0,',','.') ?>
        </td>
        <td> - </td>
        <td>
            <?php echo number_format($totals['tot']['amo'],0,',','.') ?>
        </td>
        <td colspan="2"> - </td>
        <td>
            <?php echo number_format($totals['tot']['pri'],0,',','.') ?>
        </td>
        <td>
            <?php echo number_format($totals['tot']['fin'],0,',','.') ?>
        </td>
    </tr>
</table>

<h3>Por Usuarios</h3>

<table border='1' class="profiles-totals">
    <tr>
        <th>Usuario</th>
        <th>Tickets</th>
        <th>Unds</th>
        <th>Vendido</th>
        <th>Tks Prem.</th>
        <th>Prem. Unds.</th>
        <th>Total Prem.</th>
        <th>SubTotal</th>
        <th> % Pct.</th>
        <th>Total</th>
    </tr>
    
    <?php
    foreach ($profSales as $pid => $sale) :
    ?>
        <tr>
            <td rowspan="2" class='profname'>
                <?php echo $profiles[$pid] ?>
            </td>
            <td>
                <?php 
                echo number_format($sale['nat']['sa']['co'],0,',','.') ?>
            </td>
            <td>
                <?php 
                echo number_format($sale['nat']['sa']['un'],0,',','.') ?>
            </td>
            <td class="und-nac">
                <?php 
                echo number_format($sale['nat']['sa']['to'],0,',','.') ?>  
            </td>
            
            <td>
                <?php 
                echo number_format($sale['nat']['pr']['co'],0,',','.') ?>
            </td>
            <td>
                <?php 
                echo number_format($sale['nat']['pr']['pr'],0,',','.') ?>
            </td>
            <td class="und-nac">
                <?php 
                echo number_format($sale['nat']['sa']['to'],0,',','.') ?>  
            </td>
            <td class="und-nac">
                <?php 
                echo number_format($sale['nat']['st'],0,',','.') ?>  
            </td>
            
            
            <td rowspan="2" >
                <?php echo number_format($sale['pct'],1,',','.') ?> %
            </td>
            
            <td rowspan="2" >
               <?php echo number_format($sale['total'],2,',','.') ?>
            </td>
            
        </tr>
        <tr>
            <td>
                <?php echo number_format($sale['int']['sa']['co'],0,',','.') ?>
            </td>
            <td>
                <?php echo number_format($sale['int']['sa']['un'],0,',','.') ?>
            </td>
            <td class="und-int">
                <?php echo number_format($sale['int']['sa']['to'],0,',','.') ?>  
            </td>
            
            <td>
                <?php echo number_format($sale['int']['pr']['co'],0,',','.') ?>
            </td>
            <td>
                <?php echo number_format($sale['int']['pr']['pr'],0,',','.') ?>
            </td>
            <td class="und-int">
                <?php echo number_format($sale['int']['pr']['to'],0,',','.') ?>  
            </td>
            <td class="und-int">
                <?php echo number_format($sale['int']['st'],0,',','.') ?>  
            </td>
        </tr>
    <?php 
    endforeach;
    ?>
</table>
<?php
//pr($profSales);
?>

