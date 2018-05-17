<style>
    .red-row td{
        color: #B00;
    }
</style>
<h2>Servicio TVG</h2>
<table class="table-total">
    <tr>
        <th>BrisCode</th>
        <th>TrackType</th>
        <th>RaceNum</th>
        <th>MTP</th>
        <th>PostTime</th>
        <th>First PostTime</th>
        <th>Race Status</th>
        <th>Status</th>
        <th>Display Name</th>
        <th>Track Cancelled</th>
    </tr>
<?php
/*
 *  [BrisCode] => aus
    [TrackType] => Thoroughbred
    [RaceNum] => 1
    [Mtp] => 99
    [PostTime] => 2015-10-01T22:33:00-04:00
    [FirstPostTime] => 2015-10-01T22:33:00-04:00
    [RaceStatus] => Open
    [Status] => Open
    [DisplayName] => Australia A Benalla
    [TrackCanceled] 
 */
foreach ($races as $race) {
    
    $clsRed = "";
    
    if ( $race['RaceStatus'] == "Closed" ) {
        $clsRed = " class='red-row'";
    }
        
        
    
    echo "<tr$clsRed>";
    echo "<td>" .$race['BrisCode'] . "</td>";
    echo "<td>" .$race['TrackType'] . "</td>";
    echo "<td>" .$race['RaceNum'] . "</td>";
    echo "<td>" .$race['Mtp'] . "</td>";
    echo "<td>" .$race['PostTime'] . "</td>";
    echo "<td>" .$race['FirstPostTime'] . "</td>";
    echo "<td>" .$race['RaceStatus'] . "</td>";
    echo "<td>" .$race['Status'] . "</td>";
    echo "<td>" .$race['DisplayName'] . "</td>";
    echo "<td>" .$race['TrackCanceled'] . "</td>";
    echo "</tr>";
    
    
    if (!empty($race['MyRace'])) {
        echo "<tr class='my-race-row'>";
        echo "<td colspan='10'>" ;
        
        pr($race['MyRace']);
        
        echo "</td>";
        echo "</tr>";

    }
}
?>
</table>
<?php
pr($races);
?>