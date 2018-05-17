<?php
echo $form->input('Ticket.race_id',array('options' => $races,
    'empty' => array(0 => 'No.'),'label'=>''))
?>