<script>
$(function(){
    $('#date').datepicker({dateFormat:"yy-mm-dd"});
    $('.fwfilt').change(function(){
        location = '<?php echo $html->url(array('action'=>'newfollow')) ?>' +'/' + 
                    $('#date').val() 
                    //+ '/' + $('#htrack').val() + '/' + $('#race').val();
    });
    
    $('.htrackstbl a').click(function(){
        //console.log('foo');
        $('.show-races').html('wait...').load($(this).attr('href'));
        $('.title-races').html('Carreras de ' +  $(this).text() ).show();
        //
        ; 
        return false;
    });
});
</script>
<style>
    #date {
        font-size: 12pt;
        width: 120px;
    }
    .follow-table{
        width:auto
    }
    .follow-table th{
        padding: 4px 8px;
        font-size: 12pt;
    }
    .follow-table tr:nth-child(even) td{
        background-color: #EAF4E4;
    }
    .follow-table td{
        padding: 4px;
        font-size: 11pt;
        text-align: right;
    }
    .show-races {
        border: 1px solid #333;
        padding: 4px;
    }
    .show-horses {
        border: 1px solid #333;
        padding: 4px;
    }
    .title-races{
        display: none;
        font-size: 13pt;
    }
    .title-horses{
        display: none;
        font-size: 13pt;
    }
    .txt-def {
        color: #999;
        font-size: 14pt;
    }
</style>
<h2>Seguimiento NUEVO</h2>
<?php 
echo $form->input('date',array('value' => $date, 'class' => 'fwfilt','label'=>false)); 

?>
<table class="follow-table htrackstbl">
    <tr>
        <th rowspan="2">
            Hipodromos
        </th>
        <th colspan="<?php echo (count($profiles) + 1) ?>">
            Ventas
        </th>
    </tr>
    <tr>
        <th>TOTALES</th>
        <?php
        foreach  ($profiles as $pid => $pname){
            echo "<th>$pname</th>";
        }
        ?>
    </tr>
    <?php
    foreach ( $htracks['Listed'] as $htk => $hname ) {
        echo "<tr><td>";
        //echo $html->link($hname,array('action'=>'newfollow',$date,$htk));
        echo $html->link($hname,array('action'=>'followraces',$date,$htk));
        echo "</td>";

        echo "<td>";
        if ( isset ( $htracks['Totals'][$htk] ) ) {
            echo $htracks['Totals'][$htk]['co'] . ' tks, ';
            echo $htracks['Totals'][$htk]['un'] . ' unds. Prem: ';
            echo $htracks['Totals'][$htk]['pr'];
        }
        echo "</td>";
        
        foreach  ($profiles as $pid => $pname){
            echo "<td>";
            if (isset($htracks['Sales'][$htk][$pid])) {
                echo $htracks['Sales'][$htk][$pid]['co'] . ' tks, ';
                echo $htracks['Sales'][$htk][$pid]['un'] . ' unds. Prem: ';
                echo $htracks['Sales'][$htk][$pid]['pr'];
            }
            echo "</td>";
        }
        echo "</tr>";
    }
    //pr($htracks);
    ?>
</table>
<h3 class='title-races'></h3>
<div class="show-races">
    <span class="txt-def">Seleccione Hipodromo</span>
</div>

<h3 class='title-horses'></h3>
<div class="show-horses">
    <span class="txt-def">Seleccione Carrera</span>
</div>
<?php
if (!empty ($race)){
?>
<h3>Caballos de la <?php echo $byHipo['Listed'][$race] ?>a 
    de <?php echo $htracks['Listed'][$htkid]?></h3>
<table class='follow-table horses-tbl'>
    <tr>
        <th rowspan="2">
            Caballos
        </th>
        <th colspan="<?php echo (count($profiles) + 1) ?>">
            Ventas
        </th>
    </tr>
    <tr>
        <th>TOTALES</th>
        <?php
        foreach  ($profiles as $pid => $pname){
            echo "<th>$pname</th>";
        }
        ?>
    </tr>
    <?php
    foreach ( $byRace['Listed'] as $hid => $horse ) {
        echo "<tr>";
        echo "<td>$horse</td>";
        echo "<td>";
        
        echo "</td>";
        echo "</tr>";
    }
    ?>
</table>
<ul>
    <li>OK::Crear nuevos querys bellos</li>
    <li>LLevar cada link a una nueva vista AJAX Hipos -> Carreras -> Caballos</li>
    <li>En proc...Hacer el nuevo por caballos</li>
    <li>OK:: Mostrar los totales filtrados</li>
    <li>Llevar a</li>
    <li>Cada resumen muestra el total mas el detalle por profile</li>
    
</ul>
<?php    
    echo $race;
    pr($byRace);
}
?>