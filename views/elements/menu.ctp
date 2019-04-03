<?php
//THE NEW MENU IS HERE IN THE ELEMENT
$menu = array ( 1 => array(
                    'Carreras'    => array(
                            'subactions' => array(
                                            'Administrar' => array(
                                                            'controller' => 'races',
                                                            'action'     => 'index_root'
                                                        ),
                                            'Cargar CSV'   => array(
                                                            'controller' => 'races',
                                                            'action'     => 'loadfile'
                                                        ),
                                            'Consultar'   => array(
                                                            'controller' => 'races',
                                                            'action'     => 'view'
                                                        ),
                                            'Reprogramar' => array(
                                                            'controller' => 'races',
                                                            'action'     => 'reprog'
                                                        ),
                                            'Agregar'   => array(
                                                            'controller' => 'races',
                                                            'action'     => 'add'
                                                        )
                                )
                    )
                    ,
                    'Usuarios'    => array(
                            'controller' => 'profiles',
                            'action'     => 'index'
                    )
                    , 
                    'Hipódromos'  => array(
                                'controller' => 'hipodromes',
                                'action'     => 'index'
                    )
                    , 
                    'Operaciones' => array(
                                'controller' => 'operations',
                                'action'     => 'index'
                    )
                    , 
                    'Config.' => array(
                                'controller' => 'configs',
                                'action'     => 'main'
                    )
                    ,
                    'Otros'  => array(
                            'subactions' => array(
                                            'Proximas Carr.'  => array(
                                                            'controller' => 'races',
                                                            'action'     => 'nextones'
                                                        ),
                                            'Servicios'  => array(
                                                            'controller' => 'results',
                                                            'action'     => 'services'
                                                        )
                                            /*,
                                            'Diff Horas SRV' => array(
                                                            'controller' => 'races',
                                                            'action'     => 'ptimeserv'
                                                        ),
                                            Sericio TVG' => array(
                                                            'controller' => 'races',
                                                            'action'     => 'verifysrvc'
                                                        ),
                                            'Utiles' => array(
                                                            'controller' => 'races',
                                                            'action'     => 'admin_goodques'
                                                        ),*/
                                )
                        
                    )
                ),
        
        2 => array(
                'Mi Centro' => array(
                        'subactions' => array(
                            'Mis Usuarios' => array(
                                        'controller' => 'profiles',
                                        'action'     => 'index_center'
                            )
                            ,
                            'Configs' => array(
                                        'controller' => 'centers',
                                        'action'     => 'my_conf'
                            )
                            ,
                            'Configuraciones' => array(
                                        'controller' => 'centers',
                                        'action'     => 'configs'
                            )
                            ,
                            'Intervalos Nac.' => array(
                                        'controller' => 'intervals',
                                        'action'     => 'index'
                            )
                            ,
                            'Interv. 4 Caballos' => array(
                                        'controller' => 'intervals',
                                        'action'     => 'four'
                            )
                            ,
                            'NEW: Tope Premios' => array(
                                        'controller' => 'top_prizes',
                                        'action'     => 'index'
                            )
                        )
                )
                ,
                'Carreras' => array(
                            'controller' => 'races',
                            'action'     => 'view'
                )
                ,
                'Ventas' => array(
                    'subactions' => array(
                            'Totales' => array(
                                    'controller' => 'tickets',
                                    'action'     => 'sales'
                            )
                            ,
                            'Tickets' => array(
                                    'controller' => 'tickets',
                                    'action'     => 'index'
                            )
                            ,
                            'Cuentas Online' => array(
                                    'controller' => 'accounts',
                                    'action'     => 'index'
                            )
                            ,
                            'Anular' => array(
                                    'controller' => 'tickets',
                                    'action'     => 'anull'
                            )
                    )
                            
                )
                ,
                'Monitoreo' => array(
                    'subactions' => array(
                        'Seguimiento' => array(
                                    'controller' => 'tickets',
                                    'action'     => 'follow'
                        )
                        ,
                        'Nuevo Seguim.' => array(
                                    'controller' => 'tickets',
                                    'action'     => 'newfollow'
                        )
                        ,
                        'Operaciones' => array(
                                    'controller' => 'operations',
                                    'action'     => 'center'
                        )
                    )
                )
            ),
        
        3 => array(
                    'Mi Perfil'    => array(
                            'controller' => 'profiles',
                            'action'     => 'view_taquilla'
                    )
                    , 
                    'Carreras'  => array(
                                'controller' => 'races',
                                'action'     => 'view'
                    )
                    , 
                    'Vender' => array(
                                'controller' => 'tickets',
                                'action'     => 'add'
                    )
                    ,
                    'Ventas' => array(
                        'subactions' => array(
                            'Tickets' => array(
                                'controller' => 'tickets',
                                'action'     => 'taquilla'
                            )
                            , 
                            'Totales' => array(
                                        'controller' => 'tickets',
                                        'action'     => 'salestaq'
                            )
                            , 
                            'Pagar' => array(
                                        'controller' => 'tickets',
                                        'action'     => 'pay'
                            )
                            , 
                            'Pagar Barcode' => array(
                                        'controller' => 'tickets',
                                        'action'     => 'newpaybarc'
                            )
                            ,
                            'Anular' => array(
                                        'controller' => 'tickets',
                                        'action'     => 'anulltaq'
                            )
                            , 
                            'Reimpr. Ult' => array(
                                        'controller' => 'tickets',
                                        'action'     => 'reprint_last'
                            )
                            , 
                            'Pagos/Recargas' => array(
                                        'controller' => 'accounts',
                                        'action'     => 'taquilla'
                            )
                        )
                    )
                ),
        
        4 => array(
                        'Mi Perfil' => array(
                                        'controller' => 'profiles',
                                        'action'     => 'viewonl')
                        , 
                        'Carreras' => array(
                                    'controller' => 'races',
                                    'action'     => 'view')
                        ,
                        'Apostar'  => array(
                                    'controller' => 'tickets',
                                    'action'     => 'add')
                        ,
                        'Tickets'  => array(
                                    'controller' => 'tickets',
                                    'action'     => 'taquilla')
                        ,
                        'Cuentas' => array(
                                    'controller' => 'accounts',
                                    'action'     => 'my_index')
                        ,
                        'Anular Ult.' => array(
                                    'controller' => 'tickets',
                                    'action'     => 'anull_last')
                    ),
        5 => array(
                    'Carreras' => array(
                                'controller' => 'races',
                                'action'     => 'view')
                    ,
                    'Apostar'  => array(
                                'controller' => 'tickets',
                                'action'     => 'add')
                    /*,
                    'Tickets'  => array(
                                'controller' => 'tickets',
                                'action'     => 'autotaq')
                    ,
                    'Anular' => array(
                                'controller' => 'tickets',
                                'action'     => 'anull_auto')
                    
                     */
                )
    ); 

// == > PATCHES

// online 
if ( $authUser['profile_id'] == 11 ) {
    $menu[4]['Carreras']['action'] = 'viewnew';
    $menu[4]['Apostar']['action']  = 'bet';
}

// admonline 
if ( $authUser['profile_id'] == 10 ) {
    $menu[2]['Carreras']['action'] = 'viewnew';
    //$menu[4]['Apostar']['action']  = 'bet';
}

// == > PATCHES 

?>
<nav>
    <ul>
        <?php
        foreach ($menu[$authUser['role_id']] as $title => $elem) {
            $classTitle = null;
            if (isset($elem['subactions'])) {
                $classTitle = array('class' => 'only-tit');
                $route      = '#';
                $title     .= " ";
            } else {
                $route = array(
                        'controller' => $elem['controller'],
                        'action'     => $elem['action'],
                        'admin'      => true
                    );
            }
            
            
            echo "<li>"; 
            
            echo $html->link($title,$route,$classTitle);
            
            if (isset($elem['subactions'])) {
                
                echo "<ul class='subs'>";

                foreach ($elem['subactions'] as $subTitle => $subelem) {

                    echo "<li>";

                    echo $html->link($subTitle, array(
                        'controller' => $subelem['controller'],
                        'action'     => $subelem['action'],
                        'admin'      => true)
                    );

                    echo "</li>";

                }

                echo "</ul>";
            }

            
            echo "</li>";
                
        }

        //pr($menu);
        //die();
        ?>
    </ul>    
</nav>