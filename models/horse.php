<?php
class Horse extends AppModel {

    var $name      = 'Horse';

    var $belongsTo = array('Race');

    //determina cantidad necesaria segun tipo para ser ganador		 
    var $quants    = array(
                            10 => 2, 11 => 2,                   //exactas
                            12 => 3, 13 => 3, 14 => 3,          //trifectas
                            15 => 4, 16 => 4, 17 => 4, 18 => 4  //superfcts
                     );

	
    function getAllHorses($raceId)
    {		
        $allHorses = $this->find(
                                'all',
                                array(
                                    'conditions' => array('race_id' => $raceId),
                                    'fields'     => array('id','number','name','enable'),
                                    'recursive'  => -1,
                                )
                         );

        $horses = array();

        foreach ($allHorses as $hrs) {
                array_push($horses, 
                            array(
                                'id'     => $hrs['Horse']['id'],
                                'number' => $hrs['Horse']['number'],
                                'name'   => $hrs['Horse']['name'],
                                'enable' => $hrs['Horse']['enable']
                            ));
        }

        return $horses;
    }
    
    function getRiders($raceId)
    {		
        $allHorses = $this->find(
                                'list',
                                array(
                                    'conditions' => array('race_id' => $raceId),
                                    'fields'     => array('id'),
                                    'recursive'  => -1,
                                )
                         );

        $horses = array_keys($allHorses);
        
        return $horses;
    }
    
    //get horses son
    function getHorseSon($horseId,$raceSon)
    {
        $horseSel = $this->find('first',array(
                        'conditions' => "id = " . $horseId,
                        'fields'     => 'number',
                        'recursive'  => -1 
                    ));
                
        $horseSon = $this->find('first',array(
                        'conditions' => array(
                            'number'  => $horseSel['Horse']['number'],
                            'race_id' => $raceSon
                        ),
                        'fields'     => 'id',
                        'recursive'  => -1 
                    ));


        return $horseSon['Horse']['id'];
                
    }
	
	// SET RETIRED HORSES

    function setRetired($retires)
    {
        if (!empty($retires)) {
            $this->updateAll(
                array('enable' => 0 ),
                array('Horse.id' => $retires )
            );	
        }
    }
	
    //SAVE HORSES 
    
    function saveHorses($raceId,$num)
    {
        //$this->create();
        
        for ($i = 1; $i <= $num; $i ++) {
            $this->save(array('race_id' => $raceId, 'number' => $i,'name'=>''));
            unset($this->id);
        }
        
    }
    
    function horsesFromCsv($raceId,$horses)
    {
        $this->create();
        
        foreach ($horses as $horse) {
            
            $toSave = array(
                        'race_id' => $raceId,
                        'number'  => $horse['number'],
                        'name'    => $horse['hsname'],
                        'jockey'  => $horse['jockey'],
                        'mlodds'  => $horse['mlodds']
                    );
            
            $this->save($toSave);
            unset($this->id);
        }   
    }
    
    function orderThem($horses,$list = false)
    {
        $letters = array('A', 'B', 'C', 'X', 'Y');
        $horsObj = array();
        foreach ($horses as $horse) {
            
            $horseInd = $horse;
            if ( $list == false ) {
                $horseInd = $horse['Horse'];
            }
            
            $toPush = $horseInd;
            
            foreach($letters as $l) {
                if (strpos($horseInd['number'],$l)) {
                    $toPush['letter'] = $l;
                    $onlyNum          = str_replace($l,'',$horseInd['number']);
                    $toPush['numcmp'] = $onlyNum . '.5';
                    //echo "* $onlyNum *";
                    
                } else {
                    $toPush['letter'] = '';
                    $toPush['numcmp'] = $horseInd['number'];
                }
                
            }
            
            //echo $toPush['numcmp'] . ", ";
            
            if ( $list == false ) {
                array_push($horsObj,array('Horse' => $toPush));
            } else {
                array_push($horsObj,$toPush);
            }
            
        }
        usort($horsObj, array('Horse','numcmp'));
        
        return $horsObj;
    }
    
    function numcmp($a, $b)
    {
        if (isset($a['Horse']['numcmp'])) {
            return ($a['Horse']['numcmp'] > $b['Horse']['numcmp']);
        } else {
            return ($a['numcmp'] > $b['numcmp']);
        }
    }
    
    
    //METHODS TO HORSES TICKETS

    // ===========>>>				


    //		              G  E  T  T  E  R  S    ------------>>>>>

    //get listed prizes by BASIC
    function getPrizeByTickets($horses)
    {
        $hrsTicketsModel = ClassRegistry::init('HorsesTicket');

        $prizes = $hrsTicketsModel->find(
                            'all',
                            array(
                                    'conditions' => array('prize >' => 0, 'horse_id' => $horses),
                                    'fields'     => array('ticket_id','prize'), 
                                    'recursive'  => -1
                            ));

        $byTicket = array();	
        foreach($prizes as $pr){
            if(!empty($byTicket[$pr['HorsesTicket']['ticket_id']]))
                $byTicket[$pr['HorsesTicket']['ticket_id']] += $pr['HorsesTicket']['prize'];
            else
                $byTicket[$pr['HorsesTicket']['ticket_id']] = $pr['HorsesTicket']['prize'];
        }

        return $byTicket;
    }
	
    //Get listed prizes SPECIALS by ticket 
    function getPrizeSpecialTickets($horses,$prizes,$topPrizes = array())
    {
        $hrsTicketsModel = ClassRegistry::init('HorsesTicket');

        $specials = $hrsTicketsModel->find(
                            'all',
                            array(
                                'conditions' => array(
                                    'horse_id'                     => $horses,
                                    'horses_tickets_status_id'     => 2,
                                    'play_type_id BETWEEN ? AND ?' => array(10,18),
                                ),
                                'fields'     => array(  
                                    'ticket_id','horses_tickets_status_id',
                                    'units','play_type_id','box_number','horse_id'
                                ), 
                                'recursive'  => -1
                         ));

        //determina el betamax del premio
        $priceSet = array( 2 => $prizes['exacta'],
                           3 => $prizes['trifecta'],
                           4 => $prizes['superfecta'] );

        if ( ! empty ($topPrizes ) ) {
            if ( $priceSet[2] > $topPrizes['EX']) {
                $priceSet[2] = $topPrizes['EX'];
            }
            if ( $priceSet[3] > $topPrizes['TR']) {
                $priceSet[3] = $topPrizes['TR'];
            }
            if ( $priceSet[4] > $topPrizes['SU']) {
                $priceSet[4] = $topPrizes['SU'];
            }
        }
        
        $detsFinal = array();
        foreach($specials as $d){
            $tkid = $d['HorsesTicket']['ticket_id'];
            $boxn = $d['HorsesTicket']['box_number'];
            $hsid = $d['HorsesTicket']['horse_id'];

            $detsFinal[$tkid]['units'] = $d['HorsesTicket']['units'];
            $detsFinal[$tkid]['towin'] = $this->quants[$d['HorsesTicket']['play_type_id']];

            if (!empty($detsFinal[$tkid]['boxes'][$boxn]))
                array_push($detsFinal[$tkid]['boxes'][$boxn],$hsid);
            else 
                $detsFinal[$tkid]['boxes'][$boxn][0] = $hsid;
        }

        $byTicket = array();
        foreach($detsFinal as $tkid => $det){
            foreach($det['boxes'] as $box){
                if(count($box) == $det['towin']){
                    $byTicket[$tkid] = $det['units'] * $priceSet[$det['towin']];
                }
            }
        }

        return $byTicket;		
    }
	
    //		<<<<<----     G  E  T  T  E  R  S   


    //		              S  E  T  T  E  R  S    ------------->>>>>


    function setStatuses($riders,$retired,$results)
    {
        //die('here!');
    	
        $winners = $this->_utilWinners($results);
		
        //$prizes  = $this->_utilPrizes($results);
		
        //RESETEO DE RESULTADOS
        $this->_executeStatuses(1,array('horse_id' => $riders));
		
        //Retirados
        $this->_executeStatuses(4,array('horse_id' => $retired));
		
        //seteo BASICO de premios
        $this->setBasicStatuses($winners);

        //seteo de premios ESPECIAL
        $this->setSpecialStatuses($winners);

        //Perdedores todos los corredores q sobraron
        $this->_executeStatuses(3,array('horse_id' => $riders, 
            'horses_tickets_status_id' => 1));
		
    }
	
    //seteo de statuses basicos
    function setBasicStatuses($winners)
    {
        //Ganadores al primero (TODAS LAS APUESTAS)
        $this->_executeStatuses(2, array('horse_id' => $winners[1]));
        //Ganadores al segundo (DE SEGUNDO O DE PRIMERO)
        $this->_executeStatuses(2, array('horse_id' => $winners[2], 'play_type_id' => array(2,3)));
        //Ganadores al tercero ( SOLO DE TERCERO PAGA)
        $this->_executeStatuses(2, array('horse_id' => $winners[3], 'play_type_id' => 3));
    }
	
    //seteo de statuses especiales exa-tri-sup
    function setSpecialStatuses($winners)
    {

        //Ganadores 1er lugar EXACTA,TRIFECTA y SUPERFECTA
        $this->_executeStatuses(2, array('horse_id' => $winners[1], 
                                        'play_type_id' => array(10,12,15)));

        //Ganadores 2do lugar EXACTA,TRIFECTA y SUPERFECTA 
        //   11: EXA2, 13: TRI2, 16: SUP2        
        $this->_executeStatuses(2, array('horse_id' => $winners[2], 
                                        'play_type_id' => array(11,13,16)));

        //Ganadores 3er lugar TRIFECTA y SUPERFECTA 
        //   14: TRI3, 17: SUP3
        $this->_executeStatuses(2, array('horse_id' => $winners[3], 
                                        'play_type_id' => array(14,17)));

        //Ganadores 4to lugar SUPERFECTA
        //   18: SUP4 
        $this->_executeStatuses(2, array('horse_id' => $winners[4], 
                                                    'play_type_id' => 18));

    }
	
    //seteo de premios = monto en retirados
    function setRetiredPrizes($retires)
    {
        $this->_executePrize("units",array('horse_id' => $retires));
    }
	
    //seteo de PREMIOS a CABALLOS
    function setAllPrizes($result,$currency,$isIntl,$intervals = array())
    {
        //DEPENDIENDO EN NAC-INTL RETORNA EL ARRAY DE CALCULO
        $winners = $this->_utilPrizes($result,$isIntl);
        
        //VALORES PARA EL PREMIO SI ES INTL -NAC
        if ( $isIntl == true ) {
            $factor = 2;
        } else {
            $factor = $currency;
        }
        
        if ($factor == 0) {
            $factor = 1;
        }
        
        $winnerPla = $winners[1]['place'] / $factor;
        $winnerSho = $winners[1]['show']  / $factor; 
        $placePla  = $winners[2]['place'] / $factor;
        $placeSho  = $winners[2]['show']  / $factor;
        $thirdSho  = $winners[3]['show']  / $factor;
        $toSetWin  = $winners[1]['win'];
        $changed   = false;
        //pr($intervals);
        //die();
        foreach ( $intervals as $intv ) {
            if ( $toSetWin >= $intv['Interval']['val_from'] && 
                 $toSetWin <= $intv['Interval']['val_to'] ) {

                //echo "passed on intv: " . $intv['Interval']['val_from'];
                
                if ( $intv['Interval']['div_add'] == 0 ) {
                    $toSetWin = $intv['Interval']['amount'];
                } else {
                    if ( ! $changed ){
                        $toSetWin = $toSetWin + $intv['Interval']['amount'];
                        $changed  = true;
                    }
                }
            }
            /*
            if ( $changed ) {
                break;
            }*/
        }
        
        $winnerWin = $toSetWin / $factor;
        /*
        echo "finalVal: $toSetWin - $winnerWin <br>";
        pr($result);
        echo $factor;
        pr($winners);
        pr($intervals);
        echo "hrs mod: ";
        die($winnerWin); 
        //*/
        
        //  ---- P  R  I  M  E  R    C  A  B  A  L  L  O   -------

        //apuestas WIN AL GANADOR
        $this->_executePrize(
                "(units * $winnerWin)", 
                array('horse_id' => $winners[1]['horse_id'], 'play_type_id' => 1));

        //APUESTAS PLACE AL GANADOR
        $this->_executePrize(
                "(units * $winnerPla)", 
                array('horse_id' => $winners[1]['horse_id'], 'play_type_id' => 2));

        //APUESTAS SHOW AL GANADOR
        $this->_executePrize(
                "(units * $winnerSho)",
                array('horse_id' => $winners[1]['horse_id'], 'play_type_id' => 3));


        //  ---- S  E  G  U  N  D  O    C  A  B  A  L  L  O   -------

        //APUESTAS PLACE AL SEGUNDO
        $this->_executePrize(
                "(units * $placePla)",
                array('horse_id' => $winners[2]['horse_id'], 'play_type_id' => 2));

        //APUESTAS SHOW AL SEGUNDO
        $this->_executePrize(
                "(units * $placeSho)",
                array('horse_id' => $winners[2]['horse_id'], 'play_type_id' => 3));


        //  ---- T  E  R  C  E  R    C  A  B  A  L  L  O   -------

        //APUESTAS SHOW AL TERCERO
        $this->_executePrize(
                "(units * $thirdSho)",
                array('horse_id' => $winners[3]['horse_id'], 'play_type_id' => 3));

    }	

    //		<<<<<----     S  E  T  T  E  R  S   

    //utilidad que setea el status
    function _executeStatuses($stat, $conditions)
    {
        $updarr = array('horses_tickets_status_id' => $stat);
        if ( $stat == 1) {
            $updarr['prize'] = 0;
        }
        $hrsTicketsModel = ClassRegistry::init('HorsesTicket');
        $hrsTicketsModel->updateAll($updarr, $conditions);	
    }

    //utilidad que setea el premio
    function _executePrize($prize, $conditions)
    {
        $hrsTicketsModel = ClassRegistry::init('HorsesTicket');
        $hrsTicketsModel->updateAll(array('prize' => $prize), $conditions);	
    }

    //utilidad que devuelve array util
    function _utilWinners($result)
    {
        $winners = array();
        foreach ($result as $tw){
            for ($i = 1; $i <= 4; $i ++) {	
                if($tw['position'] == $i)
                    $winners[$i] = $tw['horse_id'];
            }
    }

        return $winners;
    }

    //utilidad que devuelve array util (Arreglar mejor)
    function _utilPrizes($result)
    {
        $winners = array();

        foreach ($result as $tw){
            
            for ($i = 1; $i <= 3; $i ++) {
                                
                if($tw['position'] == $i) {

                    $pSho = $tw['show'];

                    $winners[$i]['horse_id'] =  $tw['horse_id'];
                    $winners[$i]['show']     =  $pSho;
                    
                    if (isset($tw['win'])) {
                        $winners[$i]['win'] = $tw['win'];
                    }	

                    if (isset($tw['place'])) {
                        $winners[$i]['place'] = $tw['place'];
                    }
				}
                
            }
        }

        return $winners;
    }

	
}
?>