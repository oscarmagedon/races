<style>
    .time-show{
        font-weight: bolder;
    } 
    .local-srv{
        color: #007E40;
    }
    .local-th{
        color: #761c19;
    }
</style>
<h2>
    Diferencia Horas Servicio-TotalHipico
</h2>
<?php
echo $form->create('Race',array('action' => 'ptimeserv'));
?>
<table class="table-total">
    <tr>
        <th colspan="5"> SERVICIO </th>
        <th rowspan="2"> | </th>
        <th colspan="4"> TOTALHIPICO </th>
    </tr>
    <tr>
        <th>Display Name</th>
        <th>RaceNum</th>
        <th>Srv PostTime</th>
        <th>MTP</th>
        <th>LocalTime</th>
        
        <th>My Htrack</th>
        <th>Race Time</th>
        <th>Local Time</th>
        <th> -Sel- </th>
    </tr>
    <?php
    foreach ($raceStats as $race) {
        
        $rid = $race['MyRace']['Race']['id'];
        
        echo "<tr>";
        echo "<td>" . $race['DisplayName'] . "</td>";
        echo "<td>" . $race['RaceNum'] . "</td>";
        echo "<td>" . $race['RaceTime'] . "</td>";
        echo "<td>" . $race['Mtp'] . "</td>";
        echo "<td class='time-show local-srv'>" . $race['Local'] . "</td>";
        
        echo "<td> - </td>";
        
        echo "<td>" . $race['MyRace']['Race']['number'] . "-" . $race['MyRace']['Hipodrome']['name'] . "</td>";
        
        echo "<td>" . $race['MyRace']['Race']['race_time'] . "</td>";
        
        echo "<td class='time-show local-th'>" . $race['MyRace']['Race']['local_time'] . "</td>";
        
        echo "<td style='text-align: left'>";
        
        if (!empty($race['MyRace']) && 
            $race['MyRace']['Race']['local_time'] != $race['Local']) {
                
                echo $form->input("Race.$rid.sel",array('type' => 'checkbox',
                    'label'=>'Seleccionar'));
                
                echo $form->input("Race.$rid.newtime",array('type' => 'text',
                    'value' => $race['Local'],'label'=>'Nueva Hora Local'));
            }
        
        
        
        echo "</td>";
        
        echo "</tr>";
    }
    ?>
</table>
    <?php
echo $form->end('CAMBIAR');
//pr($raceStats);

/*
 *  [BrisCode] => aqu
    [RaceNum] => 9
    [Mtp] => 18
    [PostTime] => 2015-12-02T16:20:00-05:00
    [DisplayName] => Aqueduct
    [RaceTime] => 16:20:00-05:00
    [Local] => 16:50:00
    [MyRace] => Array
        (
            [Race] => Array
                (
                    [id] => 49124
                    [enable] => 1
                    [number] => 9
                    [race_time] => 16:20:00
                    [local_time] => 16:50:00
                    [post_time] => 0
                )

            [Hipodrome] => Array
                (
                    [name] => Aqueduct
                )

        )
 */
