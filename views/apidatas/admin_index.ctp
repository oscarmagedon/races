<ul>
    <li>
        <h2>
            <?php
            echo $html->link('All Proservice',array(
                'controller' => 'apidatas',
                'admin'      => true,
                'action'     => 'nextones'
            ),
            array('target'=>'blank'))
            ?>    
        </h2>
    </li>
    <li>
        <h2>
            <?php
            echo $html->link('Save Proservice',array(
                'admin'      => true,
                'controller' => 'apidatas',
                'action'     => 'addbytrack'
            ),
            array('target'=>'blank'))
            ?>
        </h2>
    </li>
    <li>
        <h2>
            <?php
            echo $html->link('Results, retires and close',array(
                'controller' => 'apidatas',
                'action'     => 'proresults',
                'admin'      => true
            ),
            array('target'=>'blank'))
            ?>    
        </h2>
    </li>
</ul>
<h1>Public API's to crons</h1>
<ul>    
    <li>
        <h2>
            <?php
            echo $html->link('Races',array(
                'controller' => 'apidatas',
                'action'     => 'checkraces',
                'admin'      => false
            ),
            array('target'=>'blank'))
            ?>    
        </h2>
    </li>
    <li>
        <h2>
            <?php
            echo $html->link('Results',array(
                'controller' => 'apidatas',
                'action'     => 'checkresults',
                'admin'      => false
            ),
            array('target'=>'blank'))
            ?>    
        </h2>
    </li>
    <li>
        <h2>
            <?php
            echo $html->link('Bovada',array(
                'controller' => 'apidatas',
                'action'     => 'checkbovada',
                'admin'      => false
            ),
            array('target'=>'blank'))
            ?>    
        </h2>
    </li>
</ul>