<?php
$serviceHeaders = "<tr>
                    <th>Display Name</th>
                    <th>RaceNum</th>
                    <th>PostTime</th>
                    <th>MTP</th>
                    <th>TIME</th>
                    <th>Verif</th>
                    </tr>";
                /*
                <th>DATE</th>
                <th>First PostTime</th>
                <th>BrisCode</th>
                <th>TrackType</th>
                <th>Race Status</th>
                <th>Status</th>
                <th>Track Cancelled</th>
                 */
?>

<style>
    .red-row td{
        color: #B00;
    }
    .table-total th{
        font-size: 8pt;
    }
</style>
<h2>Servicio TVG</h2>

<h3>Prox <?php echo $minutes?> mins.</h3>
<?php
//if (!empty())
?>
<table class="table-total">
    
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
//foreach ($raceStats as $status => $races) {
echo $serviceHeaders;
if (isset($raceStats["Next$minutes"])){
    foreach ($raceStats["Next$minutes"] as $status => $race) {

        $clsRed = "";

        if ( $race['RaceStatus'] == "Closed" ) {
            $clsRed = " class='red-row'";
        }

        $ptimeParts  = explode('T', $race['PostTime']);
        
        $theDate = $ptimeParts[0];
        $timePts = $ptimeParts[1];
        $theTime = explode('-', $timePts);
        $theGmt  = explode(':', $theTime[1]);
        echo "<tr$clsRed>";
        echo "<td class='title-col'>" .$race['DisplayName'] . "</td>";
        echo "<td>" .$race['RaceNum'] . "</td>";
        echo "<td>" .$race['PostTime'] . "</td>";
        //echo "<td>" .$race['FirstPostTime'] . "</td>";
        echo "<td>" .$race['Mtp'] . "</td>";
        //echo "<td>$theDate</td>";
        echo "<td>" .$theTime[0] . "</td>";
        echo "<td>" .$html->link('Verif.',
                    array('action' => 'verify_ours',$race['BrisCode'],
                                $race['RaceNum'],$theTime[0],$theGmt[0]),
                    array('class' => 'veriftime')
            ) . "</td>";
        //echo "<td>" .$fptTime[0] . "</td>";

        /*
        echo "<td>" .$race['BrisCode'] . "</td>";
        echo "<td>" .$race['TrackType'] . "</td>";
        echo "<td>" .$race['RaceStatus'] . "</td>";
        echo "<td>" .$race['Status'] . "</td>";
        echo "<td>" .$race['TrackCanceled'] . "</td>";
        */
        echo "</tr>";

    }
}


?>
</table>

<h3>Cerradas OFF</h3>

<table class="table-total">    
<?php
echo $serviceHeaders;

if (isset($raceStats["Off"])){
    foreach ($raceStats["Off"] as $race) {

        $ptimeParts = explode('T', $race['PostTime']);
        $timePts    = $ptimeParts[1];
        $theTime    = explode('-', $timePts);

        echo "<tr>";
        echo "<td class='title-col'>" .$race['DisplayName'] . "</td>";
        echo "<td>" .$race['RaceNum'] . "</td>";
        echo "<td>" .$race['PostTime'] . "</td>";
        echo "<td>" .$race['Mtp'] . "</td>";
        echo "<td>" .$theTime[0] . "</td>";
        echo "</tr>";
    }
}
?>
</table>

<h3>Proximas Abiertas</h3>

<table class="table-total">    
<?php
echo $serviceHeaders;
foreach ($raceStats["Open"] as $race) {
    
    $ptimeParts = explode('T', $race['PostTime']);
    $theDate    = $ptimeParts[0];
    $timePts    = $ptimeParts[1];
    $theTime    = explode('-', $timePts);

    echo "<tr>";
    echo "<td class='title-col'>" .$race['DisplayName'] . "</td>";
    echo "<td>" .$race['RaceNum'] . "</td>";
    echo "<td>" .$race['PostTime'] . "</td>";
    echo "<td>" .$race['Mtp'] . "</td>";
    echo "<td>" .$theTime[0] . "</td>";
    echo "<td>" .$html->link('Verif.',
                    array('action' => 'verify_ours',$race['BrisCode'],$race['RaceNum']),
                    array('class' => 'veriftime')
            ) . "</td>";
    echo "</tr>";
}
?>
</table>


<h3>Cerradas CLOSED</h3>
   
<table class="table-total">    
<?php
echo $serviceHeaders;
foreach ($raceStats["Closed"] as $race) {
    
    $ptimeParts  = explode('T', $race['PostTime']);
    $timePts     = $ptimeParts[1];
    $theTime     = explode('-', $timePts);

    echo "<tr class='red-row'>";
    echo "<td class='title-col'>" .$race['DisplayName'] . "</td>";
    echo "<td>" .$race['RaceNum'] . "</td>";
    echo "<td>" .$race['PostTime'] . "</td>";
    echo "<td>" .$race['Mtp'] . "</td>";
    echo "<td>" .$theTime[0] . "</td>";
    echo "</tr>";
}
?>
</table>

<?php
//pr($raceStats);
?>
<script>
$(function (){
    $('.veriftime').click(function () {
        $partd   = $(this).parent();
        verifUrl = $(this).attr('href');
        $partd.html('loading...').load(verifUrl);
        
        return false; 
    });
});
</script>