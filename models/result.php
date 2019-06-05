<?php
class Result extends AppModel {

    var $name      = 'Result';

    var $belongsTo = array('Race');

    //		              G  E  T  T  E  R  S    ------------->>>>>

    //tomar los resultados como array util
    function get_results($raceId)
    {
        $theResults   = $this->find('all',array(
                                'conditions' => array('Result.race_id' => $raceId),
                                'recursive' => -1
                        ));

        $results      = array();

        foreach($theResults as $r){
            
            $pos = $r['Result']['position'];
            
            $results[$r['Result']['race_id']][$pos]['id']       = $r['Result']['id'];
            $results[$r['Result']['race_id']][$pos]['horse_id'] = $r['Result']['horse_id'];
            $results[$r['Result']['race_id']][$pos]['win']      = $r['Result']['win'];
            $results[$r['Result']['race_id']][$pos]['place']    = $r['Result']['place'];
            $results[$r['Result']['race_id']][$pos]['show']     = $r['Result']['show'];	

        }
        
        return $results;
    }
    
    //tomar los resultados para tipo data
    function getDataResults($raceId)
    {
        $theResults   = $this->find('all',array(
                                'conditions' => array('Result.race_id' => $raceId),
                                'recursive' => -1
                        ));

        $results      = array();

        foreach($theResults as $rk => $rval){
            $results['Result'][$rk]['id']        = $rval['Result']['id'];
            $results['Result'][$rk]['horse_id']  = $rval['Result']['horse_id'];
            $results['Result'][$rk]['win']       = $rval['Result']['win'];
            $results['Result'][$rk]['place']     = $rval['Result']['place'];
            $results['Result'][$rk]['show']      = $rval['Result']['show'];

        }
        
        return $results;
    }
	
    //		<<<<<----     G  E  T  T  E  R  S  


    //		              S  E  T  T  E  R  S    ------------->>>>>


    /**
        === NEW RESULTS SETTER ===>>>
    */
    public function saveAndTickets($data)
    {
        $horseModel  = ClassRegistry::init('Horse');
        
        //resetear todos los caballos a enable = 1
        $horses = $horseModel->getRiders($data['Race']['id']);
        
        //guardo solo datos de los resultados root
        $this->setAll($data['Result']);
        
        $horseModel->updateAll(
            array('enable'   => 1),
            array('Horse.id' => $horses)
        );
        
        //retirados
        if (isset($data['Retired'])) {
            $horseModel->setRetired(array_keys($data['Retired']));
        }
        
        //carrera ended y premios superf
        $this->Race->setRaceEnded($data['Race']['id'],$data['Special']);

        // !!
        $this->ticketsCenters($data, $horses, $data['Race']['national']);
    }

    public function ticketsCenters($data, $horses)
    {
        $centerModel = ClassRegistry::init('Center');
        $configModel = ClassRegistry::init('Config');
        $ticketModel = ClassRegistry::init('Ticket');
        $intervModel = ClassRegistry::init('Interval');
        $horseModel  = ClassRegistry::init('Horse');
        
        $centers = $centerModel->find('list');
        $isIntl  = ($data['Race']['national']==0);
        $retired = (isset($data['Retired'])) ? array_keys($data['Retired']) : [];

        // ==> SETEAR LOS PREMIOS [NAC-INTL] EN HORSES-TICKETS
        $lastRiders = count($horses) - count($retired);

        unset($centers[1]);

        //patch
        $centers = [5=>'Horses Online'];

        foreach ($centers as $centerId => $centerName) {
            //echo $centerName. ':<br>';
            //use calculation functions
            $currency    = $configModel->get_unit_value($centerId,$isIntl);
            $intervals   = [];
            //get center configs and intervals
            if ( $data['Race']['national'] == 1 ) {
                //intervals por default
                $intervals = $intervModel->getByHtrack(
                    $centerId, 
                    $data['Race']['hipodrome'], 
                    0);
                //intervals by horses
                if ( in_array( $lastRiders, [4, 5, 6] ) ) {
                    $intervals = $intervModel->getByHtrack(
                        $centerId, 
                        $data['Race']['hipodrome'], 
                        $lastRiders
                    );
                }
            }

            pr($intervals);

            //envio intervalos
            $horseModel->setAllPrizes($data['Result'], $currency, $isIntl, $intervals);
            
            //BUSCAR PREMIOS EN TICKETS
            $prizesByTickets = $horseModel->getPrizeByTickets($horses);
            //SETEAR LOS PREMIOS EN TICKETS
            $ticketModel->setPrizes($prizesByTickets);
            //BUSCAR PREMIOS ESPECIALES EN TICKETS
            // => IF INTL!!
            $topPrizes = array();
            /*
            if ( $isIntl ) {
                $topPrizes = $topPrzIns->getByHcls($data['Center'],$hipodClass);
            }
            */
            $specialsByTickets = $horseModel->getPrizeSpecialTickets($horses,
                                                $data['Special'],$topPrizes);
            
            //SETEAR LOS PREMIOS ESPECIALES EN TICKETS
            $ticketModel->setPrizes($specialsByTickets,$topPrizes);
            
            //AQUI PREMIO LOS ONLINE
            $ticketModel->setOnlinePrizes($data['Race']['id'], $centerId);
            
            //die();
            
        }
        pr($centers);
        pr($data);
        die();
    }

    //nueva salvada desde root
    function setRootResults($data)
    {
        //echo ":: DATA ROOT::"; pr($data); 
        $horseModel   = ClassRegistry::init('Horse');
        $familyModel  = ClassRegistry::init('Family');
		
        //guardo solo datos de los resultados root
        $this->setAll($data['Result']);
        
        //resetear todos los caballos a enable = 1
        $horses = $horseModel->getRiders($data['Race']['id']);
        
        $horseModel->updateAll(
            array('enable'   => 1),
            array('Horse.id' => $horses)
        );
        
        //retirados
        if (isset($data['Retired'])) {
            $horseModel->setRetired(array_keys($data['Retired']));
        }
        
        //carrera ended y premios superf
        $this->Race->setRaceEnded($data['Race']['id'],$data['Special']);
        
        //carreras hijas
        $raceSons = $familyModel->find('list',array(
                        'conditions' => "race_id = " . $data['Race']['id'],
                        'fields'     => array('center_id','race_son')
                    ));
                
        //por cada carrera hija, usar el modelo anterior de calculo
        foreach ($raceSons as $cid => $rid) {
            
            $centerData = $data;
            
            $centerData['Race']['id'] = $rid;
            $centerData['Center']     = $cid;
            
            //cambio caballos, carrera y lo pongo como nuevo siempre
            foreach ($centerData['Result'] as $rk => $res) {
                
                $centerData['Result'][$rk]['horse_id'] = $horseModel->getHorseSon(
                                                        $res['horse_id'],
                                                        $rid);
                
                $centerData['Result'][$rk]['race_id']  = $rid;
                
                unset($centerData['Result'][$rk]['id']);
               
            }
            
            //modificar los retirados por sus hijos
            if (isset($centerData['Retired'])) {
                foreach ($centerData['Retired'] as $hid => $valOne) {
                    $hson = $horseModel->getHorseSon($hid,$rid);
                    $centerData['Retired'][$hson] = 1;
                    unset($centerData['Retired'][$hid]);
                }
            }
            
            $this->assignResultsCenter($centerData);
        }
        
        //die('root fnc');
    }
    
    function assignResultsCenter($data)
    {
        $horseModel  = ClassRegistry::init('Horse');
        $ticketModel = ClassRegistry::init('Ticket');
        $configModel = ClassRegistry::init('Config'); 
        $intervIns   = ClassRegistry::init('Interval');
        $topPrzIns   = ClassRegistry::init('TopPrize');
        $hipodIns    = ClassRegistry::init('Hipodrome');
        $raceId      = $data['Race']['id'];
        $horses      = $horseModel->getRiders($data['Race']['id']);
        $isIntl      = $this->Race->isIntl($raceId);
        $currency    = $configModel->get_unit_value($data['Center'],$isIntl);
        $htrackId    = $this->Race->getHtrackId($raceId);
        $hipodClass  = $hipodIns->getHclass($htrackId);
        $retired     = array();
        $intervals   = array();
        
        if (isset($data['Retired'])) {
            $retired = array_keys($data['Retired']);
        }
        
        //carrera finalizada
        $this->Race->setRaceEnded($raceId,$data['Special']);

        //Borrar results anteriores todos 
        $this->deleteAll(array('race_id = ' . $raceId));
        
        //Guarda los resultados
        $this->setAll($data['Result']);

        //COLOCA RETIRADOS
        if ( ! empty ( $retired ) ) {
            $horseModel->setRetired($retired);
        }
        
        // ==> SETEAR LOS STATUS EN HORSES-TICKETS
        $horseModel->setStatuses($horses,$retired,$data['Result']);		
        //setear los status retirados 
        $horseModel->setRetiredPrizes($retired);
        
        // ==> SETEAR LOS PREMIOS [NAC-INTL] EN HORSES-TICKETS
        $lastRiders = count($horses) - count($retired);
        
        if ( ! $isIntl ) {
            //intervals por default
            $intervals = $intervIns->getByHtrack($data['Center'],$htrackId,0);
            //intervals by horses
            if ( in_array($lastRiders, array( 4,5,6 ) ) ) {
                $intvByHorse = $intervIns->getByHtrack($data['Center'], $htrackId, $lastRiders);
                if ( ! empty ( $intvByHorse ) ) {
                    $intervals = $intvByHorse;
                }
            }
            /*
            if ( $data['Center'] == 16 ) { // 16 = nacional
                echo "<br> DATA Nac-16 ::"; 
                pr($data);
                //echo "ALL:" . count($horses) . "<br>";
                //echo "RET:" . count($retired) . "<br>";
                echo "LST:" . $lastRiders . "<br>";
                pr($intervals); 
                //die();
                //echo "---DATA 16";
            }
            */
        }
        
        //envio intervalos
        $horseModel->setAllPrizes($data['Result'],$currency,$isIntl,$intervals);
        //BUSCAR PREMIOS EN TICKETS
        $prizesByTickets = $horseModel->getPrizeByTickets($horses);
        //SETEAR LOS PREMIOS EN TICKETS
        $ticketModel->setPrizes($prizesByTickets);
        //BUSCAR PREMIOS ESPECIALES EN TICKETS
        // => IF INTL!!
        $topPrizes = array();
        if ( $isIntl ) {
            $topPrizes = $topPrzIns->getByHcls($data['Center'],$hipodClass);
        }
        $specialsByTickets = $horseModel->getPrizeSpecialTickets($horses,
                                            $data['Special'],$topPrizes);
        
        //SETEAR LOS PREMIOS ESPECIALES EN TICKETS
        $ticketModel->setPrizes($specialsByTickets,$topPrizes);
        
        //AQUI PREMIO LOS ONLINE
        $ticketModel->setOnlinePrizes($data['Race']['id'],$data['Center']);
        
        //die();
        
    }
    
    //guardar resultados
	function setAll($results)
	{
		$this->create();
		foreach($results as $result){
			
			if(!empty($result['id']) && $result['id'] == 0)
				unset($result['id']);
				
			$this->save($result);		
			unset($this->id);
		}
		
	}
	
	//		<<<<<----     S  E  T  T  E  R  S  
	
	
	/**
     *  GET RESULTS REMOTE
     * 
     */
    function getResultsService($brisCode,$race,$fullJson)
    {
        $dataRes  = $this->_getResultServiceNick($brisCode);

        $results  = array(
                        'Headers' => array(),
                        'Horses'  => array(),
                        'Exotics' => array(),
                        'Retired' => array(),
                        'Status'  => 'NO',
                        'URL'     => $dataRes['URL']
                    );

        if ($fullJson == 1) {
            //echo $urlCheck;
            //pr($jsondata); die();  
        }
        
        //pr($dataRes); die();
        foreach ($dataRes['Races'] as $dk => $data) {
            $raceNum = $data['Number'];
            if ($raceNum == $race) {
                
                // I want to know all the first level Headers
                foreach ($data as $headKey => $levelTwo) {
                    $results['Headers'][] = $headKey; 
                }
                // I want to know all the first level Headers

                //Late changes
                //$results['LateChanges'] = $data['LateChanges'];
                //betting interests you can count the runners in each horse
                $results['RunnerByNumber'] = [];

                foreach ($data['BettingInterests'] as $betInt) {
                    $results['RunnerByNumber'][$betInt['Number']]['count'] = count($betInt['Runners']);
                    if (count($betInt['Runners']) > 1) {
                        $results['RunnerByNumber'][$betInt['Number']]['Runners'] = $betInt['Runners'];
                    }
                }

                $results['Status']  = $data['Status'];
                $results['Runners'] = $data['Results']['Runners'];
                
                if ($data['Status'] == 'RO') {
                    $results['Horses']  = $this->_getFromRunners($data['Results']['Runners']);
                    $results['Exotics'] = $this->_getPayOffs($data['Results']['Payoffs']);
                }
                $results['Retired'] = $this->_getLastChanges($data['LateChanges']['HorseChanges']);
            }
            
            
        }
        
        return $results;
    }
    
    //function to change race local time
    function getRaceTimeSrv($brisCode,$races)
    {
        $raceNums = array_keys($races);
        $dataRes = $this->_getResultServiceNick($brisCode);
        //echo "<h2>$brisCode</h2><br> -- <br>";
        //echo " - dataRes ::<br>" . json_encode($dataRes) . "<br>";
        $toChange = array();
        foreach ( $dataRes['Races'] as $race ) {
            //if ($race['Status'] == 'IC' || $race['Status'] == 'O') {
            if ( in_array( $race['Number'],$raceNums ) ) {
                $srvPtime = $this->_getTimeSrvFormat($race['PostTime']);
                echo "<br>" . $race['Number'] . ": $srvPtime <br>";
                if ( $races[$race['Number']]['ltime'] != $srvPtime ) {
                    $toChange[$race['Number']] = array(
                                                    'id'    => $races[$race['Number']]['id'],
                                                    'ltime' => $srvPtime
                                                    );
                }
                /* echo $race['Number'] . ' is ' . $race['Status'] . '<br />';
                echo "POSTIME :: " . $race['PostTime'] . '<br />';
                echo "REAL-PTIME :: $timeSrv <br />";
                echo 'END-RACE--<br />-<br />'; */
            }
        }
        return $toChange;
    }
    
    //retired returned on object grouped by number race
    function getRetiresService($brisCode)
    {
        $dataRes  = $this->_getResultServiceNick($brisCode);
        $retires  = array();
        //echo $brisCode . " - dataRes ::"; pr($dataRes['BettingInterests']);
        
        foreach ($dataRes['Races'] as $data) {
            $retired = $this->_getLastChanges($data['LateChanges']['HorseChanges']);
            $raceNum = $data['Number'];
            if ( !empty( $retired ) ) {
                $retires[$raceNum] = $retired;
            }     
        }
        
        return $retires;
    }
    
    
    //function fixes the format done to save :D 
    function getFixedToSave($resultObj,$raceId,$horses)
    {
        $superf = 0;
        if (isset($resultObj['Exotics']['SUP'])) {
            $superf = $resultObj['Exotics']['SUP']['one'];
        }
        $results = array(
                        'Race'    => array('id' => $raceId),
                        'Result'  => array(),
                        'Retired' => array(),
                        'Center'  => 1,
                        'Special' => array(
                                    'exacta'     => $resultObj['Exotics']['EXA']['one'],
                                    'trifecta'   => $resultObj['Exotics']['TRI']['one'],
                                    'superfecta' => $superf
                                )
                    );
               
        foreach ($resultObj['Horses'] as $rk => $resob) {
            
            $horseId = array_search($resob['number'],$horses);
            $results['Result'][$rk] = array(
                                            'id'       => null,
                                            'race_id'  => $raceId,
                                            'horse_id' => $horseId,
                                            'position' => $resob['position'],
                                            'win'      => $resob['win'],
                                            'place'    => $resob['place'],
                                            'show'     => $resob['show']
                                        );
        }
        
        foreach ($resultObj['Retired'] as $numRet) {
            $retk = array_search($numRet,$horses);
            $results['Retired'][$retk] = 1;
        }
        
        $this->setRootResults($results);
        
        return 'ok';
        //pr($resultObj); 
        //echo "-- RID:: $raceId --";
        //pr($horses);
        //die('SAVED! from Result');
       
    }
    
    //		              U  T  I  L  S    ------------->>>>>
	
    /*
    examples:: 

    https://www.tvg.com/ajax/races/track/PHI/performance/Day/get/collection

    *** Pay attention to lateChanges Index, it contains the coupleType Horse info
    
    */
    function _getResultServiceNick($brisCode)
    {
        $urlOne   = "https://www.tvg.com/ajax/races/track/";
        $urlDay   = "/performance/Day/get/collection";
        $urlNit   = "/performance/Ngt/get/collection";
        $urlTwi   = "/performance/Twi/get/collection";
        
        $urlCheck = $urlOne . $brisCode . $urlDay;
        $jsondata = file_get_contents($urlCheck); 
        $dataRes  = json_decode($jsondata, true);
       
        //set night
        if (empty($dataRes['Races'])) {
            $urlCheck = $urlOne . $brisCode . $urlNit;
            $jsondata = file_get_contents($urlCheck);
            $dataRes  = json_decode($jsondata, true);
        }
        
        //set Twi
        if (empty($dataRes['Races'])) {
            $urlCheck = $urlOne . $brisCode . $urlTwi;
            $jsondata = file_get_contents($urlCheck);
            $dataRes  = json_decode($jsondata, true);
        }
        
        $dataRes['URL'] = $urlCheck;
        //echo $urlCheck;
        //pr($dataRes);
        
        return $dataRes;
    }
    
	//filters array of horse and returns the one you want
	function _getHorsesByStatus($horses,$stat)
	{
		foreach ($horses as $key => $value) {
			if($value == $stat)
				unset($horses[$key]);
		}
		return array_keys($horses);
	}
    
    function _getPayOffs($payoffs)
    {
        
        $exotics = array();
        foreach ($payoffs as $po) {
            
            $thisSelection = $po['Selections'][0]['Selection'];
            
            if ( strpos($thisSelection,'-') !== FALSE) {
                
                //$parts = explode('-',$thisSelection);
                
                switch ($po['WagerTypeID']) {
                    case 110:
                        $exotic = 'EXA';
                        break;
                    
                    case 160:
                        $exotic = 'TRI';
                        break;
                    
                    case 210:
                        $exotic = 'SUP';
                        break;
                    
                    default:
                        $exotic = $po['WagerTypeID'];
                        break;
                    
                }
                
                $newWager = $po['Selections'][0]['PayoutAmount'];
                
                if ($po['WagerAmount'] != 1) {
                    $newWager = $po['Selections'][0]['PayoutAmount'] / $po['WagerAmount'];
                } 
                
                $exotics[$exotic] = array(
                                    'sel' => $thisSelection,
                                    'amo' => $po['Selections'][0]['PayoutAmount'],
                                    'wag' => $po['WagerAmount'],
                                    'one' => $newWager
                    );
            }
        }
        
        //pr($exotics); pr($payoffs);
        return $exotics;
    }
    
    function _getFromRunners($dataRunners)
    {
        $positions = array();
        
        foreach ($dataRunners as $run) {
            array_push($positions,array(
                                    'position'  => $run['FinishPosition'],
                                    'number'    => $run['BettingInterestNumber'],
                                    'name'      => $run['RunnerName'],
                                    'win'       => $run['WinPayoff'],
                                    'place'     => $run['PlacePayoff'],
                                    'show'      => $run['ShowPayoff'],
                                ));
        }
        
        return $positions;
    }
    
    function _getLastChanges($horseChanges)
    {
        //pr($horseChanges);
        
        $retires = array();
        
        //aqui verifico si la razon es Scratched, sino, no!
        foreach ($horseChanges as $ch) {
            $ret = false;
            foreach ($ch['Changes'] as $chan) {
                if ($chan['Description'] == 'Scratched') {
                    $ret = true;
                }
            }
            
            if ($ret) {
                array_push($retires,$ch['ProgramNumber']);
            }
            
        }
        
        return $retires;
        
    }
	 
    function _getTimeSrvFormat($postTime)
    {
        $ptime     = str_replace('Date', '',$postTime);
        $ptime     = str_replace('/', '', $ptime);
        $ptime     = str_replace('(', '', $ptime);
        $ptime     = str_replace(')', '', $ptime);
        $ptime     = $ptime / 1000; 
        $formatted = date("Y-m-d H:i:s", $ptime );
        $parts     = explode(' ', $formatted);
        return $parts[1];
    }
}
?>