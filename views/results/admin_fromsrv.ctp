<style>
.res-cnt{
    height: auto;
    padding: 10px;
    border: 1px solid #080;
}
.load-res{
    border: 1px solid #000;
    padding: 6px 8px;
    margin: 6px;
}
</style>
<h2>Servicio Results</h2>

<?php
foreach ($nextNicks as $nick) {
    
    $txtLink = $nick['Race']['number'] . ' de ' . $nick['Hipodrome']['name'].
                " (" . $nick['Race']['local_time'] . ")";
    
    $lastNick = $nick['Hipodrome']['nick'];
    
    if ($nick['Hipodrome']['tvgnick'] != '') {
        $lastNick = $nick['Hipodrome']['tvgnick'];
    }
    
    echo "<div class='res-cnt'>";
    echo $html->link($txtLink, array('action' => 'getsrv',
                                     $nick['Race']['id'],
                                     $lastNick,
                                     $nick['Race']['number']
                                ),
                               array(
                                   'class' => 'serv-link'
                                ));
    
    echo "<div class='load-res'>Here loads...</div>";
    echo "</div>";
}
pr($nextNicks);
?>
<script>
$(function (){
    $('.serv-link').click(function(){
        
        $link   = $(this).attr('href');
        $toload = $(this).parent().find('.load-res');
        
        $toload.html('loading...').load($link);
        
        return false;
    });
});
</script>