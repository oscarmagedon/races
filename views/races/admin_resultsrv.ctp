<h2>Servicio Results</h2>

<?php
foreach ($nextNicks as $nick) {
    $txtLink = $nick['Race']['number'] . ' of ' . $nick['Hipodrome']['nick'].
                " (" . $nick['Race']['local_time'] . ")";
    
    echo "<h2>";
    echo $html->link($txtLink, array('action' => 'getsrvres',
                                     $nick['Hipodrome']['nick'],
                                     $nick['Race']['number']
        ),
        array('target' => '_blank'));
    echo "</h2><br />";
    
}
//pr($nextNicks);
?>