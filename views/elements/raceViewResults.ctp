<?php
//pr($this->race['Horse']);
//pr($results[$this->race['Race']['id']]);
/*
function getDetails($horses,$id)
{
    $horseDets = array();
    
    foreach ($horses as $horse) {
        if ($horse['id'] == $id) {
            $horseDets = array(
                            'number' => $horse['number'],
                            'name'   => $horse['name']
                         );
        }
    }
    return $horseDets;
}*/
$retires = array();
foreach ($this->race['Horse'] as $horse) {
    if ($horse['enable'] == 0) {
        array_push($retires, $horse['number']. "-" . $horse['name']);
    }
}

if (!empty ($results[$this->race['Race']['id']])) {
?>
<table cellpadding="0" cellspacing="0" border='1' class="table-total" style="font-size: 90%">
    <tr>
        <th>Pos.</th>
        <th>No.</th>
        <th>Nombre</th>
        <th>W</th><th>P</th><th>S</th>
    </tr>
    <?php
    foreach ($results[$this->race['Race']['id']] as $posit => $res) :
    
        
        $horse = $dtime->getDetails($this->race['Horse'],$res['horse_id']);
        
        ?>
        <tr>
            <td style="text-align: right">
                <?php echo $posit ?>
            </td>
            <td style="text-align: right">
                <?php 
                echo isset($horse['number'])?$horse['number']:'-';
                ?>
            </td>
            <td style="text-align: left">
                <?php echo isset($horse['name'])?$horse['name']:'-'; ?>
            </td>
            <td>
                <?php
                if ($res['win'] > 0) {
                    echo $res['win'];
                } else {
                    echo "-";
                }
                ?>
            </td>
            <td>
                <?php
                if ($res['place'] > 0) {
                    echo $res['place'];
                } else {
                    echo "-";
                }
                ?>
            </td>
            <td>
                <?php
                if ($res['show'] > 0) {
                    echo $res['show'];
                } else {
                    echo "-";
                }
                ?>
            </td>
        </tr>
    <?php
    endforeach;
    ?>
    <tr>
        <th colspan='6'>
            EXA : <?= number_format($this->race['Race']['exacta'],0) ?> ,
            TRI : <?= number_format($this->race['Race']['trifecta'],0) ?> ,
            SUP : <?= number_format($this->race['Race']['superfecta'],0) ?>.
        </th>
    </tr>
    <?php
    if (!empty($retires)) {
         //pr($retires);
    ?>
    <tr>
        <th colspan='6' style="color: #B00">
            RETIRADOS : <br /> 
            <?php
            foreach ($retires as $ret){
                $retexp = explode('-',$ret);
                echo "<span title='" . $retexp[1] ."'>" . $retexp[0] ."</span>, ";
            }
            ?>
        </th>
    </tr>
    <?php
    }
    ?>
</table>
<?php
}
?>