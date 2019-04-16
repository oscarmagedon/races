<h2>Servicios</h2>

<table style="font-size: 150%; width: 70%">
    <tr>
        <th>PROSERVICES</th>
        <th>BOVADA</th>
    </tr>
    <tr>
        <td>
            <?php
            echo $html->link('OLD Races',array(
                'admin'      => true,
                'controller' => 'apidatas',
                'action'     => 'proservtracks',
                date('Y-m-d'),
                1
            ),
            array('target'=>'blank'))
            ?>
        </td>
        <td>
            <?php
                echo $html->link('Cierre',
                    array(
                        'controller'=>'results',
                        'action' => 'closebovadanew',
                        'admin'=> false),
                    array('target'=>'blank'))
                ?>
            <small>
                Cada 5 segundos (sleep)
            </small>
        </td>
    </tr>
    <tr>
        <td>
            <?php
            echo $html->link('Results',array(
                'controller' => 'apidatas',
                'action'     => 'proresults',
                'admin'      => true
            ),
            array('target'=>'blank'))
            ?>
        </td>
        <td>
            <?php
            echo $html->link('Por Carrera',
                array(
                    'controller' =>'apidatas',
                    'action'     => 'bovada',
                    'admin'      => true),
                array('target'=>'blank'))
            ?>
        </td>
    </tr>
    <tr>
        <td>
            <?php
            echo $html->link('Save Races',array(
                'admin'      => true,
                'controller' => 'apidatas',
                'action'     => 'addbytrack',
                date('Y-m-d'),
                1
            ),
            array('target'=>'blank'))
            ?>
        </td>
        <td>
            -
        </td>
    </tr>
</table>

<!--
<ul>
    <li>
        <h4>Proservices Races</h4>
        <h3>
            
        </h3>
    </li>
    <li>
        <h4>Proservices Results</h4>
        <h3>
            
        </h3>
    </li>
    <li>
        <h4>Proservices Races</h4>
        <h3>
            <?php
            echo $html->link('Races',array('controller'=>'results',
                    'action' => 'closebovada','admin'=> false),array('target'=>'blank'))
            ?>
        </h3>
    </li>
    <li>
        <h4>Cada 5 segundos (sleep)</h4>
        <h3>
            <?php
            echo $html->link('Cierre Bovada',array('controller'=>'results',
                    'action' => 'closebovada','admin'=> false),array('target'=>'blank'))
            ?>
        </h3>
    </li>
    <li>
        <h4>Cada minuto</h4>
        <h3>
            <?php
            echo $html->link('Resultados',array('controller'=>'results',
                    'action' => 'checkfromservice','admin'=> false),array('target'=>'blank'))
            ?>
        </h3>
    </li>
    <li>
        <h4>Cada 5 minutos</h4>
        <h3>
            <?php
            echo $html->link('Retirados',array('controller'=>'results',
                    'action' => 'checkretires','admin'=> false),array('target'=>'blank'))
            ?>
        </h3>
    </li>
    <li>
        <h4>Cada 5 minutos</h4>
        <h3>
            <?php
            echo $html->link('Cambio de Hora',array('controller'=>'results',
                    'action' => 'checksrvtime','admin'=> false),array('target'=>'blank'))
            ?>
        </h3>
    </li>
</ul>
-->