<h2>Premios Segun Caballos</h2>

<div class="add-divi">
    <?php
    echo $form->create('Interval');
    
    echo $form->input('hipodrome_id',array('empty' => 'Sel...','label' => 'Hipod.'));
    
    echo $form->input('byHorses',array('options' => array(4=>4,5=>5,6=>6),
        'label' => 'Caballos.'));
    
    echo $form->input('val_from',array('class' => 'numb-inp','label' => 'Desde'));
    
    echo $form->input('val_to',array('class' => 'numb-inp', 'label' => 'Hasta'));
    
    
    echo $form->input('div_add',array('options' => array(
            0 => 'Colocar', 1 => 'Sumar'),'label' => 'Accion'));
    
    echo $form->input('amount',array('class' => 'numb-inp','label' => 'Monto'));
    
    echo $form->end('Agregar');
    ?>
</div>
<div class="dividendos">
    <?php
    echo $form->create('Interval',array('action' => 'four'));
    ?>
    <table>
        <?php
        $i = 0;
        foreach ( $byHorses as $hipId => $horses ) { // $hipId => $intvs
            
            echo "<tr><th colspan='5' style='font-size:12pt'>".$hipodromes[$hipId]."</th></tr>";
            
            foreach ( $horses as $hk => $intvs ) { 
                echo "<tr><th colspan='5'>" . $hk . " caballos </th></tr>";
                echo "<tr><th>Desde</th><th>Hasta</th><th>Tipo</th>";
                echo "<th>Monto</th><th>Borrar</th></tr>";
             
                foreach ( $intvs as $int ) {
                    echo "<tr>";
                    echo "<td>";
                    echo $form->input("Interval.$i.id",array('type' => 'hidden',
                            'value' => $int['id']));
                    echo $form->input("Interval.$i.val_from",array('class' => 'numb-inp',
                            'value' => $int['vfrom'],'label' => false));
                    echo "</td>";
                    echo "<td>";
                    echo $form->input("Interval.$i.val_to",array('class' => 'numb-inp',
                            'value' => $int['vto'],'label' => false));
                    echo "</td>";
                    echo "<td>";
                    echo $form->input("Interval.$i."
                        . "div_add",array('options' => array(
                        0 => 'Colocar', 1 => 'Sumar'),'label' => false, 'value' => $int['add']));

                    echo "</td>";
                    echo "<td>";
                    echo $form->input("Interval.$i.amount",array('class' => 'numb-inp',
                            'value' => $int['amo'],'label' => false));
                    echo "</td>";
                    echo "<td>";
                    echo $html->link('x',array('action'=>'delete',$int['id']),
                        array('title' => 'BORRAR','class'=>'del-cnf'));

                    echo "</td>";
                    echo "</tr>";
                    $i ++;
                }
            }
            echo "<tr><th colspan='5'> - </th></tr>";
        }
        ?>        
    </table>
    <?php
    echo $form->end('GUARDAR');
    //pr($fourhrs);
    ?>
</div>
<style>
    .add-divi{
        border: 1px solid blue;
        padding: 5px;
        margin: 10px;
        height: 100px;
        float: left;
        clear: both;
    }    
    .add-divi div {
        float: left;
        clear: none;
    }
    .dividendos {
        float: left;
        clear: left;
        width: auto;
        margin: 5px;
        border: 1px solid blue;
        padding: 5px;
    }
    .dividendos table {
        width: auto;
    }
    .numb-inp {
        width: 60px;
        text-align: right;
    }
    .del-cnf {
        color: #900;
        padding: 1px 8px;
        font-weight: bolder;
        font-size: 130%;
        float: right;
    }
    .four-hrs {
        float: left;
        clear: right;
        width: auto;
        margin: 5px;
        border: 1px solid blue;
        padding: 5px;
    }
</style>   