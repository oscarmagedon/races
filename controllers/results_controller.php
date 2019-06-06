<?php
class ResultsController extends AppController {

	var $name = 'Results';
	var $helpers = array('Html', 'Form');

	function beforeFilter(){
		parent::beforeFilter();	
        
        $this->Authed->allow(array(
            "checkfromservice","checkretires","checksrvtime",
            'readservice',
            'closebovada','closebovadanew',
            'proservice','proservrace','proservbytrack'
        ));
	}
	
	function isAuthorized(){
		
		$ret = true;
		
		$actions_root = array(
			"admin_set_root","admin_assign","admin_getthem","admin_centers",
            "admin_rootset",'admin_fromsrv','admin_getsrv',
            'admin_chkclosebov','admin_services'
		);
		$actions_adm = array(
			"admin_set", 
            //"admin_setnew",
			"admin_view","admin_race_prices","admin_ticket_prices","admin_pick_prices"
		);
		
		$actions_taq = array(
			"admin_view"
		);
        
        $actions_onl = array(
			"admin_view"
		);
		
		if($this->isRoot() && in_array($this->action, $actions_root)){
			$ret = true;
		}elseif($this->isAdmin() && in_array($this->action, $actions_adm)){
			$ret = true;
		}elseif($this->isTaquilla() && in_array($this->action, $actions_taq)){
			$ret = true;	
		}elseif($this->isOnline() && in_array($this->action, $actions_onl)){
			$ret = true;	
		}else{
			$ret = false;
		}
				
		if($ret == false)
			$this->Session->setFlash("Direccion NO permitida");
		
		return $ret;
	}
	
	//		         N  E  W    F  N  C  T  S    ------------->>>>>
	
	function admin_set($race_id = null) {
		
		if (!empty($this->data)) {
			
			$this->data['Center']   = $this->authUser['center_id'];
				
			$operationModel         = ClassRegistry::init('Operation');
			
			//seteo basico de estatus y guardado de data
			$this->Result->setResults($this->data);
			
			//guardar log operaciones
			$operationModel->ins_op(3,$this->authUser['profile_id'],"Resultados",$this->data['Result'][0]['race_id'],"Resultados en carrera");
			
			$this->redirect($this->referer());
		}
		
		$horseIns = ClassRegistry::init('Horse');
		
		// FIND RESULTS 
		$results = $this->Result->get_results($race_id);
		
        if (!empty($results)) {
            $results = $results[$race_id];
        }
         
		//race details and specials
		$race = $this->Result->Race->getRaceResults($race_id);
		
		// NEW HORSES METHOD
		$allHorses = $horseIns->getAllHorses($race_id);
		
		$this->set(compact('race','results','allHorses'));
		
	}
    
    function admin_rootset($raceId = null) {

        $horseIns    = ClassRegistry::init('Horse');
        $horseTksMod = ClassRegistry::init('HorsesTicket');
        $raceMod     = ClassRegistry::init('Race');
        $centerMod   = ClassRegistry::init('Center');
        $ticketMod   = ClassRegistry::init('Ticket');
        
		if (!empty($this->data)) {
			//setear valores del data
           
            $this->data['Result'][0]['position'] = 1;
            $this->data['Result'][1]['position'] = 2;
            $this->data['Result'][2]['position'] = 3;
            $this->data['Result'][3]['position'] = 4;
            
            $this->data['Result'][0]['race_id'] = $this->data['Race']['id'];
            $this->data['Result'][1]['race_id'] = $this->data['Race']['id'];
            $this->data['Result'][2]['race_id'] = $this->data['Race']['id'];
            $this->data['Result'][3]['race_id'] = $this->data['Race']['id'];
            
            if ($this->data['Result'][4]['horse_id'] != 0) {
                $this->data['Result'][4]['position'] = 1;
                $this->data['Result'][4]['race_id']  = $this->data['Race']['id'];
            } else {
                unset($this->data['Result'][4]);
            }
        
            
            //NEW MODEL FUNCTION works with services
            $horseTksMod->saveWinnersPrizes(
                $this->data['Race']['id'], 
                $this->data['Result'],
                $this->data['Race']['national'],
                array_keys($this->data['Retired'])
            );
            
            if ($this->data['Race']['national'] == 1) {

                $horses = $horseIns->find('count',[
                    'conditions' => ['race_id' => $this->data['Race']['id']]
                ]);

                //ANOTHER FUNCTION that recalculates if NATIONAL
                    
                $lastRetires = (isset($this->data['Retired'])) ? count($this->data['Retired']) : 0;
                $lastRiders  = $horses - $lastRetires;
                // ##
                $lastRiders = ($lastRiders >= 4 && $lastRiders <= 6) ? $lastRiders : 0;
                $horseTksMod->recalculateIntervals(
                    $this->data['Race']['id'],
                    $this->data['Result'], 
                    $this->data['Race']['hipodrome'], 
                    $lastRiders
                );              
                    
            }
            
            //save results
            $this->Result->saveAll($this->data['Result']);
            //ended and specials    
            $raceMod->setRaceEnded($this->data['Race']['id'],$this->data['Special']);
            //pr($this->data);
            //die();
            //$this->data['Center'] = $this->authUser['center_id'];
            //OLD:: seteo de estatus y guardado de data ROOT
            //$this->Result->setRootResults($this->data);
			
            //set OnlinePrizes
            $centers = $centerMod->find('list');
            //pr($centers);
            foreach ($centers as $ckey => $cname) {
                $ticketMod->setOnlinePrizes($this->data['Race']['id'], $ckey);
            }
            // ... set OnlinePrizes

            //$this->Result->setResults($this->data);
            $operationModel = ClassRegistry::init('Operation');
			//guardar log operaciones
			$operationModel->ins_op(3,$this->authUser['profile_id'],"Resultados",
                    $this->data['Race']['id'],"ROOT: Resultados en carrera");
			//die('OK');
			$this->redirect($this->referer());
		}
		
		
		
        //race details and specials
		$race = $this->Result->Race->getRaceResults($raceId);
		
        
		// FIND RESULTS 
		//$results = $this->Result->get_results($raceId);
        $this->data = $this->Result->getDataResults($raceId);
        
		$this->data['Special'] = array(
                                'exacta'     => $race['Race']['exacta'],
                                'trifecta'   => $race['Race']['trifecta'],
                                'superfecta' => $race['Race']['superfecta']
                                );
        //pr($race);
		// NEW HORSES METHOD
		$allHorses = $horseIns->getAllHorses($raceId);
		
		$this->set(compact('race','allHorses'));
		
	}

	function admin_getthem($hid){
		$races = $this->Result->Race->find('list',array(
			'conditions' => array('race_date' => date('Y-m-d'), 'center_id' => 1, 'hipodrome_id'=>$hid,'ended'=>1),
			'fields' => 'number'
		));
		
		echo json_encode($races);die();
	}
	
	function admin_centers($rid){
		$cent = ClassRegistry::init('Center');
		$familyInst = ClassRegistry::init('Family');
		
		$centers_got = $familyInst->find('all',array(
			'conditions'=>array('race_id'=>$rid),'fields'=>array('center_id','race_son','results')
		));
		
		$cgot = array(); $rgot = array();
		foreach ($centers_got as $c) {
			$cgot[$c['Family']['center_id']] = $c['Family']['race_son'];
			$rgot[$c['Family']['center_id']] = $c['Family']['results'];
		}
		
		$centers = $cent->find('list',array(
			'conditions'=>array('Center.id'=>array_keys($cgot))
		));
		
		foreach ($centers as $key => $value) {
			if($rgot[$key] == 1)
				$centers[$key] = $centers[$key]." (R)";
		}
		
		$all = array('races' => $cgot, 'centers' => $centers);
		
		echo json_encode($all); die();
		
	}

    function admin_fromsrv()
    {
        $horseInst  = ClassRegistry::init('Horse');
        $operationModel = ClassRegistry::init('Operation');
                
        $nextNicks = $this->Result->getNextNicks(3);
        $saveRaces = array('Count' => 0,'All'  => array());
        //pr($nextNicks);
        //echo "-->";
        
        foreach ($nextNicks as $nick) {
    
            $saveThis = array(
                        'Race' => array(
                                    'id'     => $nick['Race']['id'],
                                    'number' => $nick['Race']['number'],
                                    'htrk'   => $nick['Hipodrome']['name'],
                                    'ltime'  => $nick['Race']['local_time']
                                )
                        );  
            
            $lastNick = $nick['Hipodrome']['nick'];
            if ($nick['Hipodrome']['tvgnick'] != '') {
                $lastNick = $nick['Hipodrome']['tvgnick'];
            }
            
            $resultObj  = $this->Result->getResultsService($lastNick, $nick['Race']['number'],0);
        
            //pr($resultObj);
            
            if (!empty($resultObj['Horses'])) {

                $horses     = $horseInst->find('list',array(
                                'conditions' => array('race_id' => $nick['Race']['id']),
                                'fields'     => 'number'
                              ));


                $saveThis['Saved'] = $this->Result->getFixedToSave($resultObj,$nick['Race']['id'],$horses);
                $saveRaces['Count'] ++;
                //guardar log operaciones
                $raceDets = ", " . $nick['Race']['number'] . "a " . $nick['Hipodrome']['name'];
                
                $operationModel->ins_op(3,$this->authUser['profile_id'],"Resultados",
                    $nick['Race']['id'],"SERV-TVG::Result. Autom." . $raceDets);
                    
            } else {
                $saveThis['Error'] = 'No Results.';
            }
            
            array_push($saveRaces['All'],$saveThis);
            
        }
        
        die(json_encode($saveRaces));
        //pr($saveRaces);die();
        $this->set(compact('nextNicks'));
    }
    
    function admin_getsrv($raceId, $tvgBris, $numRace = 0, $fullJson = 0)
    {
        
        $resultObj  = $this->Result->getResultsService($tvgBris, $numRace, $fullJson);
        
        //pr($resultObj);
            
        $resultMesg = array();
        
        if (!empty($resultObj['Horses'])) {
            $horseInst  = ClassRegistry::init('Horse');
        
            $horses     = $horseInst->find('list',array(
                            'conditions' => array('race_id' => $raceId),
                            'fields'     => 'number'
                          ));
            
            
            $resultMesg['Saved'] = $this->Result->getFixedToSave($resultObj,$raceId,$horses);
            
            $operationModel = ClassRegistry::init('Operation');
            //guardar log operaciones
            
            $operationModel->ins_op(3,$this->authUser['profile_id'],"Resultados",
                $raceId,"SERV-TVG::Result. Autom.");
            
        } else {
            $resultMesg['Error'] = 'No hay Data';
        }
        
        echo json_encode($resultMesg);
        
        //pr($resultMesg);
        die();
    }
    
    /**
     *  ==> S E R V I C I O S ==>
     */
    
    function admin_services ()
    {
        
    }

    /**
        ! ! !    N E W    P R O S E R V I C E  ! ! !

    **/

    /**
     *  Tracks URL
    */
    public function proservice()
    {
        $protracks  = ClassRegistry::init('Protracks');
        $proFields  = $protracks->tracksFields;
       
        $proserviceTracks = $protracks->getInfoTracks();

        $trackIds = $protracks->getTracksIds();
       
        //pr($trackIds);

        $this->set(compact('proserviceTracks','proFields','trackIds'));
    }

    /**
     *  
    */
    public function proservrace($raceNum, $trackId, $country, $dayEve) 
    {
        $raceApi  = ClassRegistry::init('Prorace');

        $raceInfo = $raceApi->getByRace($raceNum, $trackId, $country, $dayEve);
        
        //pr($raceInfo);
        die(json_encode($raceInfo));
        
    }

    public function proservbytrack($trackId, $country, $dayEve) {
        
        $raceApi  = ClassRegistry::init('Prorace');

        $infoTrack = $raceApi->exploreTrack($trackId, $country, $dayEve, 20);
        
        //pr($infoTrack);
        //die('link to purge and save again');
        echo json_encode($infoTrack);
        die();


        //When testing, delete
        //race delete
        //If saved
        //check on races
        //

        //If saved
        $infoTrack[$key]['Saved']     = 'Count by horsetrack and center';
        //
        $infoTrack[$key]['Operation'] = 'PROSERV::Track-'.$trackId;

        
    }

    /**
        ! ! !    N E W   P R O S E R V I C E   ! ! !

    **/
    
    
    /** B O V A D A  SERV.
     * 
     */
    function admin_chkclosebov()
    {
        $operInst  = ClassRegistry::init('Operation');
        $closed    = 0;
        $nextRaces = $this->Result->Race->find('all',array(
                        'conditions' => array(
                                            'center_id'          => 1,
                                            'race_date'          => date('Y-m-d'),
                                            'Race.enable'        => 1,
                                            'ended'              => 0,
                                            'Hipodrome.national' => 0
                                        ),
                        'fields'     => array(
                                            'Race.id', 'number', 'local_time',
                                            'Hipodrome.id','Hipodrome.bovada',
                                            'Hipodrome.name'
                                        ),
                        'order'      => array('local_time' => 'ASC'),
                        'limit'      => 5
                    ));
        
        //pr($nextRaces);
        
        foreach ($nextRaces as $next) {
            
            $htrack   = $next['Hipodrome']['bovada'];
            $numRace  = $next['Race']['number'];            
            $urlCheck = "https://horses.bovada.lv/services/sports/" .
                        "event/lookup/B/$htrack/race$numRace";
            //echo $urlCheck;
            //echo "<br> -- <br>";
            $xmlStr   = file_get_contents($urlCheck);
            if (!$xmlStr) {
                die('Error reading ' . $next['Hipodrome']['name']);
            }
            
            $xml      = new SimpleXMLElement($xmlStr);
            $statSrv  = $xml->markets->status;
            //echo "STATUS:: " . $xml->markets->status . "<br>";
            //echo "<br>OBJ<br>";
            //pr(json_encode($xml));
            if ($statSrv == 'SUSPENDED' || $statSrv == 'SETTLED') {
                //closeSrv
                $this->_closeRaceSrv($next['Race']['id']);
                //operation line 
                $operInst->ins_op(4,$this->authUser['profile_id'],'SRV-BOVADA',
                    'Cierre', $next['Race']['number']. '-' . $htrack . ': ' . $statSrv);
                unset($operInst->id);
                $closed ++;
            }
            //SAVE OPERATION AND STATUS
        }
        //pr($raceCloseIds);
        //die("From Nexts!");
        die(json_encode(array('closed' => $closed)));
    }
    
    
    /** B O V A D A    P U B L I C    S E R V.
     */
    function closebovada()
    {
        $operInst  = ClassRegistry::init('Operation');
        $closed    = 0;
        $nextRaces = $this->Result->Race->find('all',array(
                        'conditions' => array(
                                            'center_id'          => 1,
                                            'race_date'          => date('Y-m-d'),
                                            'Race.enable'        => 1,
                                            'ended'              => 0,
                                            'Hipodrome.national' => 0
                                        ),
                        'fields'     => array(
                                            'Race.id', 'number', 'local_time',
                                            'Hipodrome.id','Hipodrome.bovada',
                                            'Hipodrome.bovalt','Hipodrome.name'
                                        ),
                        'order'      => array('local_time' => 'ASC'),
                        'limit'      => 5
                    ));
        
        //pr($nextRaces);
        
        foreach ($nextRaces as $next) {
            
            $htrack   = $next['Hipodrome']['bovada'];
            $htracalt = $next['Hipodrome']['bovalt'];
            $numRace  = $next['Race']['number'];            
            
            $urlCheck = "https://horses.bovada.lv/services/sports/" .
                        "event/lookup/B/$htrack/race$numRace";
        
            $urlChkAlt = "https://horses.bovada.lv/services/sports/" .
                        "event/lookup/B/$htracalt/race$numRace";

            echo $urlCheck;
            echo "<br> -- <br>";
            $dataStr  = file_get_contents($urlCheck);
            if ( empty ( $dataStr ) ) {
                echo "<br> -ALT- <br>";
                echo $urlCheck;
                $dataStr  = file_get_contents($urlChkAlt);
            }
            
            $data     = json_decode($dataStr, true);
            
            pr($data);

            echo "STATUS:: " . $data['status'] . "<br>";
            //pr($data); die();
            $statSrv  = $data['status'];
            //SAVE OPERATION AND STATUS
            if ($statSrv == 'S' || $statSrv == 'P') {
                //closeSrv
                $this->_closeRaceSrv($next['Race']['id']);
                //operation line 
                $operInst->ins_op(4,1,'SRV-BOVADA-AUT',
                    '', $next['Race']['number']. '-' . $htrack . ': ' . $statSrv);
                unset($operInst->id);
                $closed ++;
            }
        }
        //pr($raceCloseIds);
        //die("From Nexts!");
        die(json_encode(array('closed' => $closed)));
    }



    /** B O V A D A    P U B L I C    S E R V.
     */
    function closebovadanew()
    {
        $operInst  = ClassRegistry::init('Operation');
        $closed    = 0;
        $nextRaces = $this->Result->Race->find('all',array(
                        'conditions' => array(
                                            'center_id'          => 1,
                                            'race_date'          => date('Y-m-d'),
                                            'Race.enable'        => 1,
                                            'ended'              => 0,
                                            'Hipodrome.national' => 0
                                        ),
                        'fields'     => array(
                                            'Race.id', 'number', 'local_time',
                                            'Hipodrome.id','Hipodrome.bovada',
                                            'Hipodrome.bovalt','Hipodrome.name'
                                        ),
                        'order'      => array('local_time' => 'ASC'),
                        'limit'      => 5
                    ));
        
        //echo 'NEWBOV!!';
        //pr($nextRaces);
        
        foreach ($nextRaces as $next) {
            
            $htrack   = $next['Hipodrome']['bovada'];
            $htracalt = $next['Hipodrome']['bovalt'];
            $numRace  = $next['Race']['number'];            
            
            $urlCheck = "https://horses.bovada.lv/services/sports/" .
                        "event/v2/events/B/description/horse-racing/$htrack";
                
            //echo $urlCheck .' to '. $next['Hipodrome']['bovada'];
            echo "<br> -- <br>";
                        
            $dataStr  = file_get_contents($urlCheck);
            
            $data     = json_decode($dataStr, true);
            
            //echo count ($data[0]['events']);
            //pr($data[0]['events']);

            $notFound = true;


            foreach ( $data[0]['events'] as $raceEvent ) {
                
                if ( $raceEvent['details']['raceNumber'] == $numRace ) {
                    $notFound = false ;
                }                              

            }

            if ( $notFound == true ) {

                $this->_closeRaceSrv($next['Race']['id']);
                //operation line 
                $operInst->ins_op(4,1,'SRV-BOVADA-AUTNEW',
                    '', $next['Race']['number']. '-' . $htrack);
                unset($operInst->id);
                $closed ++;
            }


        }

        //die();
        
        //pr($raceCloseIds);
        //die("From Nexts!");
        die(json_encode(array('closed' => $closed)));
    }





    
    //autom Results from TVG (acomodar el get nicks)
    function checkfromservice()
    {
        $horseInst = ClassRegistry::init('Horse');
        $operInst  = ClassRegistry::init('Operation');
        $nextNicks = $this->Result->Race->getNextNicks(3);
        //$nextNicks = $this->Result->Race->getHtracksNicksDay(date('Y-m-d'));
        $saveRaces = array('Count' => 0,'Closed' => 0,'All'  => array());
        
        //pr($nextNicks);
        foreach ($nextNicks as $nick) {
            $saveThis = array(
                        'Race' => array( 'id'     => $nick['Race']['id'],
                                         'number' => $nick['Race']['number'],
                                         'htrk'   => $nick['Hipodrome']['name'],
                                         'ltime'  => $nick['Race']['local_time'] ) );  
            
            $lastNick = $nick['Hipodrome']['nick'];
            if ($nick['Hipodrome']['tvgnick'] != '') {
                $lastNick = $nick['Hipodrome']['tvgnick'];
            }
            
            $resultObj  = $this->Result->getResultsService($lastNick, $nick['Race']['number'],0);
            echo "<br>--<br>";
            pr($resultObj);
            
            // AQUI VERIFICO EL CIERRE Y LA CIERRO COMO BOVADA 
            //SAVE OPERATION AND STATUS
            if ( $resultObj['Status'] == 'RO' || $resultObj['Status'] == 'SK') {
                //closeSrv
                $this->_closeRaceSrv($nick['Race']['id']);
                //operation line 
                $raceDets = $nick['Race']['number'] . "a " . $nick['Hipodrome']['name'];
                //$operInst->ins_op(4,1,'SRV-TVG-CIERR-AUT', $raceDets': ' . $resultObj['Status']);
                $operInst->ins_op(3,1,"CierreTVG",$nick['Race']['id'],
                    "SERV-TVG::Cierre Autom." . $raceDets);
                unset($operInst->id);
                $saveRaces['Closed'] ++;
            }
            
            //AQUI ES SI TIENE CABALLOS QUE META RESULTADOS
            
            if (!empty($resultObj['Horses'])) {

                $horses     = $horseInst->find('list',array(
                                'conditions' => array('race_id' => $nick['Race']['id']),
                                'fields'     => 'number'
                              ));


                $saveThis['Saved'] = $this->Result->getFixedToSave($resultObj,$nick['Race']['id'],$horses);
                $saveRaces['Count'] ++;
                //guardar log operaciones
                $raceDets = ", " . $nick['Race']['number'] . "a " . $nick['Hipodrome']['name'];
                
                $operInst->ins_op(3,1,"Resultados",
                    $nick['Race']['id'],"OUTER-SERV-TVG::Result. Autom." . $raceDets);
                    
            } else {
                $saveThis['Error'] = 'No Results.';
            }
            array_push($saveRaces['All'],$saveThis);
        }
        
        //die(json_encode($saveRaces));
        pr($saveRaces);
        die();
        //$this->set(compact('nextNicks'));
    }

    function readservice($nickTvg = '', $raceNum = 0)
    {
        if ($nickTvg == ''){
            echo 'Please write a nick';
        } else {

            if ($raceNum == 0 ){
                echo 'Please write a number';
            } else {
                $resultObj  = $this->Result->getResultsService($nickTvg, $raceNum, 0);
                //echo "<br>--<br>";
                //pr($resultObj);
                echo json_encode($resultObj);
            }

        }

        die();
        
    }
    
    //SERVICIO RETIRADOS DESDE TVG
    function checkretires ()
    {
        $operInst    = ClassRegistry::init('Operation');
        $horsetracks = $this->Result->Race->getHtracksNicksDay(date('Y-m-d'));
        pr($horsetracks);
        echo "-- --<br />";
        foreach ($horsetracks as $hid => $hdet) {
            $resultObj = $this->Result->getRetiresService($hdet['nick']);
            echo "<br>" . $hdet['nick'] . "<br>"; //echo " - retObj::"; pr($resultObj);
            if (!empty($resultObj)) {
                $raceNums = array_keys($resultObj);
                $myRaces  = $this->Result->Race->getRacesHorses(date('Y-m-d'),$hid,$raceNums);
                //echo "::my races!!"; pr($myRaces);
                $retireData = $this->_disableSrvHorses($resultObj,$myRaces);
                //pr($retireData);
                if ($retireData['ret'] == 1) {
                    $retireText = "Nick ". $hdet['nick'] . " : ";
                    $retireText .= $retireData['text'];
                    echo $retireText;
                    $operInst->ins_op(3,1,"Retirados TVG", 0 ,$retireText);
                }
            }
            //update htrack
            $this->Result->Race->Hipodrome->updateAll(
                array('last_ret_check' => 'current_timestamp'),
                array('Hipodrome.id' => $hid));
        }
        die();
    }
    
    function checksrvtime()
    {
        $nextRaces   = $this->Result->Race->getNextStartGrouped();
        $changes     = array();
        //pr($nextRaces);
        //echo "-- SERVICES :: --<br />";
        foreach ($nextRaces as $nick => $races) {
            $changes = $this->Result->getRaceTimeSrv($nick,$races);
            echo "<br>" . $nick . "<br>";
            //pr($races);
            echo " - races to change: "; 
            pr($changes);
            if  (!empty($changes)) {
                $this->_changePostTime($changes,$nick);
            }
        }
        
        die('END::');
    }
    
    /**
     * INNERS 
     */
    //close races from bovada
    function _closeRaceSrv($raceId)
    {
        $familyInst = ClassRegistry::init('Family');
        //todos los hijos de esa carrera
        $racesSons = $familyInst->find('list',array(
                        'conditions' => array('race_id' => $raceId),
                        'fields'     => 'race_son' ) );
        
        array_push($racesSons,$raceId);
        //disable ALL RACES
        $this->Result->Race->updateAll(
            array('enable'  => 0, 'close_time' => "'" . date('H:i:s') . "'"),
            array('Race.id' => $racesSons));
    }
    
    //interna de comparacion y suspension
    function _disableSrvHorses($resultObj,$myRaces)
    {
        //echo "RESULTOBJ::"; pr($resultObj);   echo "MYRACES::"; pr($myRaces);
        $familyModel = ClassRegistry::init('Family');
        $horseInst   = ClassRegistry::init('Horse');
        $retVal      = 0;
        $retires     = array();
        $retireText  = "";
        
        foreach ($myRaces as $numRace => $race) {
            $raceSons = $familyModel->find('list',array(
                            'conditions' => "race_id = " . $race['id'],
                            'fields'     => array('center_id','race_son')
                        ));
            
            $retireText .= "<br/>". $numRace . "a. HRS:[";
            
            foreach ($race['Horses']['enabOn'] as $horseId => $horseNum) {
                if ( in_array( $horseNum,$resultObj[$numRace] ) ) {
                    $retVal      = 1;
                    $retireText .= $horseNum . ", ";
                    //echo 'To Susp: R: ' .$numRace . '- H:' . $horseNum . '<br>'; echo "$horseId, ";
                    array_push($retires,$horseId);
                    foreach ($raceSons as $cid => $rid) {
                        $hson = $horseInst->getHorseSon($horseId, $rid);
                        array_push($retires,$hson);
                        //echo "$hson, ";
                    }
                }
            }
            $retireText .= "]";
        }
        //echo "TORETIRE::"; echo $retireText; pr($retires);
        $horseInst->setRetired($retires);
        return array('ret' => $retVal,'text' => $retireText);
    }
    
    function _changePostTime($changes,$nick)
    {
        $familyMod = ClassRegistry::init('Family');
        $operMod   = ClassRegistry::init('Operation');
        foreach ($changes as $rnum => $ch) {
            $raceSons  = $familyMod->find('list',array(
                        'conditions' => "race_id = " . $ch['id'],
                        'fields'     => array('center_id','race_son')
                    ));
            
            array_push($raceSons,$ch['id']);
            //echo "tochids::";
            //pr($raceSons);
            $this->Result->Race->updateAll(
                array('local_time' => "'" . $ch['ltime'] . "'"),
                array('Race.id'    => $raceSons)
            );
            
            //operation line 
            $operMod->ins_op(4, 1, 'SRV-TVG', 0, "PostTime $rnum - $nick");
            unset($operMod->id);
            
        }
        
    }
    
    /**
     *  ===> T  V  G  SERVICES EN CONSTRUCCION
     * Cambios Importantes
     * 
     * - Los htrack nick debe traerme segun las proximas abiertas a punto de
     *   empezar entre 3 y 0 mins.
     * 
     * - Tomar solo las abiertas segun mi servicio de proximas, hay que pasarle
     *   a la funcion de tomar cerradas de TVG el numero de carrera especifico
     *   y asi puedo hacerlo mas enano
     * 
     * - En las operaciones colocarlo como Tipo SERVICIO y en Tabla colocar
     *   CIERRE, luego colocar un buscador para la metadata y que le haga caso
     
     * 
     * 
     
    function admin_checkcloseOLD()
    {
        $operInst    = ClassRegistry::init('Operation');
        $horsetracks = $this->Result->Race->getHtracksNicksDay(date('Y-m-d'));
        $racesClosed = array('count' => 0);
        //pr($horsetracks); echo "-- --<br />";
        
        foreach ($horsetracks as $hid => $hdet) {
            $raceClose = array();
            if ($hdet['nick'] != '') {
                $raceClose = $this->Result->getRaceCloseSrv($hdet['nick']);           
                //echo "<br>" . $hdet['nick'] . "<br>"; 
                //echo " - closeObj::"; pr($raceClose);
            }
            
            if ( !empty($raceClose) ) {
                
                $raceNums = array_keys($raceClose);
                $myRaces  = $this->Result->Race->getRacesOnByNum(date('Y-m-d'),$hid,$raceNums);
                $numsTxt  = '';
                foreach ($raceClose as $nro => $stat) {
                    $numsTxt .= "$nro ($stat), ";
                }               
                //pr($raceNums); pr($myRaces);
                if (!empty($myRaces)) {
                    $racesClosed['count'] += count($myRaces);
                    //close races method
                    $this->_closeRaceSrv($myRaces);
                    //operation line (one by htrack)
                    $details = 'Cerradas, ' . $hdet['nick'] . ', races: [' . $numsTxt . ']';
                    $operInst->ins_op(4,$this->authUser['profile_id'],'','',$details);
                    //echo $details;
                }
            }
            
            //update htrack
            $this->Result->Race->Hipodrome->updateAll(
                array('last_ret_check' => 'current_timestamp'),
                array('Hipodrome.id' => $hid)
            );
        }
        
        die(json_encode($racesClosed));
        
    }
    */
}
?>