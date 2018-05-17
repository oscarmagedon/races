<table class="follow-table racestbl">
    <tr>
        <th rowspan="2">
            Carreras
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
    foreach ( $races['Listed'] as $rid => $rnum ) {
        echo "<tr><td>";
        //echo $html->link($rnum. 'a ',array('action'=>'newfollow',$date,$htkid,$rid));
        echo $html->link($rnum. 'a ',array('action'=>'followhorses',$rid));
        echo "</td>";
        
        echo "<td>";
        if ( isset($races['Totals'][$rid] ) ) {
            echo $races['Totals'][$rid]['co'] . ' tks, ';
            echo $races['Totals'][$rid]['un'] . ' unds. Prem: ';
            echo $races['Totals'][$rid]['pr'];
        }
        echo "</td>";
        
        foreach  ($profiles as $pid => $pname){
            echo "<td>";
            if (isset($races['Sales'][$rid][$pid])) {
                echo $races['Sales'][$rid][$pid]['co'] . ' tks, ';
                echo $races['Sales'][$rid][$pid]['un'] . ' unds. Prem: ';
                echo $races['Sales'][$rid][$pid]['pr'];
            }
            echo "</td>";
        
        }
        echo "</tr>";
    }
    ?>
</table>
<?php //pr($races) ?>
<script>
$(function(){
    $('.racestbl a').click(function(){
        //console.log($(this).attr('href'));
        $('.show-horses').html('wait...').load($(this).attr('href'));
        $('.title-horses').html('Caballos de ' +  $(this).text() ).show();
        return false; 
    });
});
</script>

