<h2>Cargar Archivo CSV</h2>
<?php

echo $form->create('Race',array('action' => 'loadfile', 'type' => 'file'));

echo $form->file('csvfile',array('style' => 'width: 400px', 'label' => 'Seleccione archivo...'));

echo $form->end('Guardar');
