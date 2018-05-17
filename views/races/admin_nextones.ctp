<h2>Serv. Proximas Carreras</h2>
    
<?php
//pr($racesObj);
/*
 * [0] => Array
    (
        [id] => 654
        [race] => 1
        [time] => 1:25 PM
        [diff] => 4h 1m
        [htrack] => Belmont Park
        [ptime] => 0
    )
 */
?>
<table class="table-total">
    <tr>
        <th>ID</th>
        <th>Htrack</th>
        <th>Num</th>
        <th>Time</th>
        <th>Diff</th>
        <th>PostTime</th>
    </tr>

<?php
foreach ($racesObj as $race) {
?>
    <tr>
        <td><?php echo $race['id'] ?></td>
        <td><?php echo $race['htrack'] ?></td>
        <td><?php echo $race['race'] ?></td>
        <td><?php echo $race['time'] ?></td>
        <td><?php echo $race['diff'] ?></td>
        <td><?php echo $race['ptime'] ?></td>
    </tr>
<?php
}
//pr($racesObj);
?>
</table>