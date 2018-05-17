<h2>Config. del Sistema</h2>
<table class="table-total">
        <th>Conf</th>
        <th>Valor</th>
    </tr>

<?php
//pr($confs);
//foreach ($confs as $cnf) {
    echo "<tr>";
    echo "<td>" . $confs['ConfigType']['name'] . "</td>";
    echo "<td>" ;
    
    if ($confs['Config']['actual'] == 1) {
        $txtLink = "ACTIVO";
        $change  = 0;
    } else {
        $txtLink = "DESACTIVO";
        $change  = 1;
    }
    echo $html->link($txtLink,array('action' => 'winter',$change));
    
    echo "</td>";
    echo "</tr>";
//}
?>

</table>