<h2>FORMAT: #RACE-HIP HORSE TYPE UNDS</h2>
<?php
$defTxt = "2PRX 5 W 10";
$defNum = "+584129945734";
echo $form->create('Ticket',array('action' => 'betsms'));
echo $form->input('number',array('value' => $defNum));
echo $form->input('message',array('type' => 'textarea', 'value' => $defTxt,
    'style' => 'height: 70px; width: 200px'));
echo $form->end('SEND');

if ( !empty($results) ) {
    pr($results);
}
?>