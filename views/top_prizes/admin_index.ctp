<h2>Tope Premios del Centro</h2>
<?php
echo $form->create('TopPrize',array('action' => 'index'));
?>
<table style="width: 350px">
    <tr>
        <th>Clase</th>
        <th>EXA</th>
        <th>TRI</th>
        <th>SUP</th>
    </tr>
    <?php
    foreach ( $topHclass as $thc ) {
        echo "<tr>";
        echo "<th>$thc</th>";
        
        foreach ( $topTypes as $typ ) {
            echo "<td>";
            $val = ""; $sty = '';
            if ( isset ( $centerTops[$thc][$typ])) {
                $val = $centerTops[$thc][$typ]['top'];
                echo $form->input('TopPrize.'.$thc.'.'.$typ.'.id',array('type'=>'hidden',
                    'value' => $centerTops[$thc][$typ]['id']));
            } else {
                $sty = 'background-color: yellow';
                $val = $rootTops[$thc][$typ]['top'];
            }
            
            echo $form->input('TopPrize.'.$thc.'.'.$typ.'.top',array(
                'value'=>$val,'label'=>false,'style' => $sty));

            echo "</td>";

        }
        echo "</tr>";
    }
    ?>
</table>
<?php
//echo $form->input('foo');
echo $form->end('GUARDAR');
//pr($topPrizes);
?>