<?php
//pr($byRace);
?>
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
        if (isset($byRace['Totals'][$hid])) {
            echo $byRace['Totals'][$hid]['co'] . ' tks, ';
            echo $byRace['Totals'][$hid]['un'] . ' unds. Prem: ';
            echo $byRace['Totals'][$hid]['pr'];
        }
        echo "</td>";
        
        foreach  ($profiles as $pid => $pname){
            echo "<td>";
            if (isset($byRace['byHorses'][$hid][$pid])) {
                echo $byRace['byHorses'][$hid][$pid]['co'] . ' tks, ';
                echo $byRace['byHorses'][$hid][$pid]['un'] . ' unds. Prem: ';
                echo $byRace['byHorses'][$hid][$pid]['pr'];
            }
            echo "</td>";
        
        }
        
        echo "</tr>";
    }
    ?>
</table>

