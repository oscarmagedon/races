<div>
    <h2>Señal en vivo Total Hipico:</h2>

    <div style="clear: both; font-size: 140%">
        <ul>
            <li>
                <?php
                echo $html->link("Canales en Vivo",array(
                        'action'=>'hashsignal','h'),
                    array('target' => '_blank'))
                ?>
            </li>
            <li>
                <?php
                echo $html->link("Hipodromos en Vivo",array(
                        'action'=>'hashsignal','a'),
                    array('target' => '_blank'))
                ?>
            </li>
            <li>
                <?php
                echo $html->link("Retrospectos",array(
                        'action'=>'hashsignal','r'),
                    array('target' => '_blank'))
                ?>
            </li>
        </ul>
    </div>    
</div>