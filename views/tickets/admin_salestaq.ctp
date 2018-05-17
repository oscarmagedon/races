<script type="text/javascript">
var filt_url = '<?php echo $html->url(array("action"=>"salestaq")) ?>';
$(function(){
	/*$("#since").attr('readonly',true).datepicker({dateFormat:"yy-mm-dd"});
	$("#until").attr('readonly',true).datepicker({dateFormat:"yy-mm-dd"});
	
	$("#filt").click(function(){
		
		var since     = $("#since").val(),
		    until     = $("#until").val();
				
		location = filt_url + "/" + since + "/" + until;
        
	});	
    */
    $("#since").attr('readonly',true).datepicker({dateFormat:"yy-mm-dd"}).change(
        function(){
            var since     = $(this).val();

            location = filt_url + "/" + since + "/" + since;
        });
        
    $("#until").attr('readonly',true).datepicker({dateFormat:"yy-mm-dd"}).change(
        function(){
            var since     = $("#since").val(),
                until     = $(this).val();

            location = filt_url + "/" + since + "/" + until;
        });
    
    $("#htrackid").change(
        function(){
            var since     = $("#since").val(),
                until     = $("#until").val(),
                htrackid  = $(this).find('option:selected').val();

            location = filt_url + "/" + since + "/" + until + '/' + htrackid;
        });
        
    $("#raceid").change(
        function(){
            var since     = $("#since").val(),
                until     = $("#until").val(),
                htrackid  = $("#htrackid").find('option:selected').val();
                raceid  = $(this).find('option:selected').val();

            location = filt_url + "/" + since + "/" + until + '/' + htrackid + '/' + raceid;
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
</style>

<h2>Mis Ventas Totales</h2>

<table class="filter-table" cellpadding="0" cellspacing="0">
    <tr>
        <th>Filtrar Por:</th>
        <td><?php 
            echo $form->input('since',array('value'=>$since,'label'=>"Desde",
                'class'=>'filter_input','style'=>'width:120px'))
        ?></td>
        <td><?php 
            echo $form->input('until',array('value'=>$until,'label'=>"Hasta",
                'class'=>'filter_input','style'=>'width:120px'))
        ?></td>
        <td>
            <?php 
            if ( $since == $until ) {
                echo $form->input('htrackid',array('value'=>$htrackid,'label'=>"Hipodromo",
                    'options' => $htracks ,'class'=>'filter_input','empty'=>'Sel...'));
            }
            ?>
        </td>
        <td>
            <?php 
            if ( $since == $until ) {
                echo $form->input('raceid',array('value'=>$raceid,'label'=>"Carrera",
                    'options' => $races ,'class'=>'filter_input','empty'=>'..'));
            }
            ?>
        </td>
    </tr>
</table>

<table cellpadding="0" cellspacing="0" class="totals-table">
    <tr>
        <th colspan="2" rowspan="2">Tipo</th>
        <th colspan="3">VENTAS</th>
        <th colspan="3">PREMIADOS</th>
        <th colspan="3">PAGADOS</th>
        <th rowspan="2">Utilidad<br/>Ventas - Pagados</th>
        <?php 
        if ($perct > 0) {
            echo "<th rowspan='2'>Pct %</th>";
        }
        ?>
    </tr>
    <tr>
        <th>Tickets</th>
        <th>Unidades</th>
        <th>Total</th>
        <th>Tickets</th>
        <th>Unidades</th>
        <th>Total</th>
        <th>Tickets</th>
        <th>Unidades</th>
        <th>Total</th>
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
        <td> 
            <?php echo number_format($totals['nat']['py']['co'],0,',','.') ?>
        </td>
        <td> 
            <?php echo number_format($totals['nat']['py']['pr'],0,',','.') ?>
        </td>
        <td class="und-nac">
            <?php echo number_format($totals['nat']['py']['to'],0,',','.') ?>
        </td>
        <td class="und-nac">
            <?php echo number_format($totals['nat']['ut'],0,',','.') ?>
        </td>
        <?php
        if ($perct > 0) {
        ?>
            <td rowspan="2">
                <?php echo number_format($perct,1,',','.') ?> %
            </td>
        <?php
        }
        ?>
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
        <td> 
            <?php echo number_format($totals['int']['py']['co'],0,',','.') ?>
        </td>
        <td> 
            <?php echo number_format($totals['int']['py']['pr'],0,',','.') ?>
        </td>
        <td class="und-int">
            <?php echo number_format($totals['int']['py']['to'],0,',','.') ?>
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
        <td colspan="2"> - </td>
        <td>
            <?php echo number_format($totals['tot']['pay'],0,',','.') ?>
        </td>
        <td colspan="2">
            <?php echo number_format($totals['tot']['fin'],0,',','.') ?>
        </td>
    </tr>
</table>
<?php
//pr($totals)
?>