<?php
class RacesController extends AppController {

	var $name = 'Races';
	
    var $uses = array('Race','Horse');

	function beforeFilter(){
        
		parent::beforeFilter();
        
        $this->Authed->allow(array("nextones",'autoptime'));
        
	}
	
	function isAuthorized(){
		
		$ret = true;
		
		$actions_root = array(
            "admin_index_root", "admin_assign", "admin_add", "admin_edit",
            "admin_set_enable", "admin_delete", "admin_loadfile","admin_view",
            "admin_pogetmygmt","admin_reprog","admin_set_post_time",
            "admin_goodques","admin_checksrv",
            "admin_nextones",
            "admin_verifysrvc",'admin_resultsrv','admin_getsrvres',
            'admin_verify_ours', 'admin_ptimeserv'
        );

		$actions_adm = array(
            "admin_view","admin_viewdep",
            "admin_list_ajax","admin_restrict",
            "admin_data_pick", "admin_getmygmt"
        );
		
        $actions_taq = array(
            "admin_view","admin_viewdep",
            "admin_list_ajax","admin_data_pick"
        );

        $actions_onl = array(
            "admin_view","admin_viewdep",
            "admin_list_ajax","admin_data_pick"
        );
		
		if($this->isRoot() && in_array($this->action, $actions_root)){
			$ret = true;
		}elseif($this->isAdmin() && in_array($this->action, $actions_adm)){
			$ret = true;
		}elseif($this->isTaquilla() && in_array($this->action, $actions_taq)){
			$ret = true;	
		}elseif($this->isOnline() && in_array($this->action, $actions_onl)){
			$ret = true;	
		}elseif($this->isAuto() && in_array($this->action, $actions_onl)){
			$ret = true;	
		}else{
			$ret = false;
		}
				
		return $ret;
	}

	function admin_index_root($date = null, $htrackid = 0, $ended = 0) {
		
		if($date == null) {
			$date = date('Y-m-d');
        }
        
		$cond['race_date'] = $date;
		$cond['center_id'] = $this->authUser['center_id'];
		
		if ($ended != 2) {
			$cond['ended'] = $ended;
        }
        
		if ($htrackid != 0){
			$cond['hipodrome_id'] = $htrackid;
		}
		
        $hipodromes = $this->Race->getHorsetracksByDay(
							$date, 
							$this->authUser['center_id'],
                            0, //nationals?
                            true,
                            true
                            );
        
        $this->paginate['recursive']  = 0;
		$this->paginate['conditions'] = $cond;
		
        $races    = $this->paginate();
        
        //pr($races);
        
        $races_id = $this->Race->find('list',array('fields'=>'id','conditions'=>$cond));
		 
		$horses   = $this->Horse->find('list',array(
                        'fields'     => 'race_id', 
                        'conditions' => array('race_id' => $races_id),
                        'group'      => 'race_id'
                    ));
        
        
        $this->set(compact('races','hipodromes','date','ended','htrackid','horses'));
	}
    
    function admin_set_post_time($raceId,$mins)
    {
        //families
        $famInst  = ClassRegistry::init('Family');
        $raceSons = $famInst->find('list',array(
                        'conditions' => "race_id = $raceId",
                        'fields'     => 'race_son'
                    )); 
        
        //die('set post!');   
        
        $this->Race->updateAll(
            array('post_time' => $mins),
            array('Race.id'   => $raceId)
        );
        
        //put to the sons
        foreach ($raceSons as $rs) {
            $this->Race->updateAll(
                array('post_time' => $mins),
                array('Race.id'   => $rs)
            );
        }
        
        $this->redirect($this->referer());
    }
     
    function admin_reprog ($date = null, $htrackid = 0, $minsRep = 0)
    {
        if ( !empty($this->data) ) {
            
            $races    = $this->Race->find('all',array(
                            'conditions' => array(
                                'race_date'    => $this->data['Race']['date'],
                                'center_id'    => $this->authUser['center_id'],
                                'hipodrome_id' => $this->data['Race']['hipodrome_id']
                                
                            ),
                            'fields'     => array('id','number','race_time','local_time'),
                            'recursive'  => -1
                        ));
            
            $famInst  = ClassRegistry::init('Family');
        
            foreach ($races as $rk => $racev) {
                
                $nrtime = $this->_addMins($racev['Race']['race_time'],
                                        $this->data['Race']['minsrep']);
                
                $nltime = $this->_addMins($racev['Race']['local_time'],
                                        $this->data['Race']['minsrep']);
                
                $this->Race->updateAll(
                    array(  'race_time'  => "'".$nrtime."'", 
                            'local_time' => "'".$nltime."'"),
                    array('Race.id' => $racev['Race']['id'])
                );
                
                $raceSons = $famInst->find('list',array(
                                'conditions' => "race_id = " . $racev['Race']['id'],
                                'fields'     => 'race_son'
                            ));
                
                //put to the sons
                foreach ($raceSons as $rs) {
                    
                    $this->Race->updateAll(
                        array(  'race_time'  => "'".$nrtime."'", 
                                'local_time' => "'".$nltime."'"),
                        array('Race.id' => $rs)
                    );
                    
                }
                
            }
            
            
            $this->Session->setFlash('Carreras con horas reprogramadas.');
            $this->redirect(array('action' => 'reprog',$this->data['Race']['date'],
                            $this->data['Race']['hipodrome_id']));
            
            //pr($this->data); 
            //pr($races);
            //die();
        }        
        
        if($date == null) {
			$date = date('Y-m-d');
        }
        
        $races = array();
        
		if ($htrackid != 0){
            $cond['race_date']   = $date;
            $cond['center_id']   = $this->authUser['center_id'];
		
            $cond['hipodrome_id'] = $htrackid;
            
            $races    = $this->Race->find('all',array(
                            'conditions' => $cond,
                            'fields'     => array('id','number','race_time','local_time'),
                            'recursive'  => -1
                        ));
            
        }
        
        if ($minsRep != 0) {
            foreach ($races as $rk => $racev) {
                
                $races[$rk]['New']['rtime'] = $this->_addMins(
                                                $racev['Race']['race_time'],$minsRep);
                
                $races[$rk]['New']['local'] = $this->_addMins(
                                                $racev['Race']['local_time'],$minsRep);
                
            }
        }
		
        $hipodromes = $this->Race->getHorsetracksByDay(
							$date, 
							$this->authUser['center_id'],
                            0, //nationals?
                            true,
                            true
                            );
        
        //$minutes = array(1,2,3,4,5,6,7,8,9,10);
        //pr($races);
        $this->set(compact('races','hipodromes','date','htrackid','minsRep'));
    }
    
	function admin_restrict($race_id = null) {
		$rest_ins = ClassRegistry::init('Restriction');
		if(!empty($this->data)){
			//pr($this->data); die();
			$rid = $this->data['Race']['id'];
			$rest_ins->create();			
			foreach($this->data['Conf'] as $pid => $conf){
				foreach($conf as $cid => $val){
					if($val == 1){
						$co = $rest_ins->find('count',array('conditions'=>array(
							'race_id'=>$rid,'profile_id'=>$pid,'play_type_id'=>$cid
						)));
						if($co == 0){
							$rest_ins->save(array('race_id'=>$rid,'profile_id'=>$pid,'play_type_id'=>$cid));	
						}
					}else{
						$rest_ins->deleteAll(array('race_id'=>$rid,'profile_id'=>$pid,'play_type_id'=>$cid));	
					}
					unset($rest_ins->id);					
				}
			}
			$this->Session->setFlash("Restricciones guardados");
			$this->redirect($this->referer());
		}
		
		$profs_ins = ClassRegistry::init('Profile');
		$profs = $profs_ins->find('all',array(
			'conditions' => array('center_id' => $this->authUser['center_id'],'User.role_id'=>3),
 			'fields' => array('Profile.name','Profile.id')
		));
		
		foreach($profs as $pro){
 			$profiles[$pro['Profile']['id']] = $pro['Profile']['name'];
 		}
		
		$race = $this->Race->find('first',array(
			'conditions'=>array('Race.id' => $race_id),
			'fields'=> array('number','race_date','Hipodrome.name')
		));
		
		$restrict = $rest_ins->get_by_race($race_id);
		
		$this->set(compact('profiles','race','restrict'));
		$this->set('race_id',$race_id);
	}

	function admin_add() 
    {
		if ( !empty( $this->data ) ) {
			$centerModel = ClassRegistry::init('Center');
            $familyModel = ClassRegistry::init('Family');
            $operModel   = ClassRegistry::init('Operation');
			
            $centerIds   = $centerModel->find('list',array(
                            'conditions' => array('id >' => 1),
                            'recursive'  => -1,
                            'fields'     => 'id'
                            ));
            
            $hipodInfo   = $this->Race->Hipodrome->findById($this->data['General']['hipodrome_id']);
            //pr($centerIds); pr($this->data); pr($hipodInfo); die();
            $cars = "";
			$this->Race->create();
			foreach ( $this->data['Race'] as $race ) {
			    $race['center_id']    = $this->authUser['center_id'];
				$race['hipodrome_id'] = $this->data['General']['hipodrome_id'];
				$race['race_date']    = $this->data['General']['race_date'];
				
                //if national
                if ($hipodInfo['Hipodrome']['national'] == 1) {
                    $race['local_time'] = $race['race_time'];
                }
                    
                $this->Race->save($race);
				$this->Horse->saveHorses($this->Race->id,$race['horses_num']);
                
                $cars   .= $race['number'].", ";			
                $raceMom = $this->Race->id;
                
                unset($this->Race->id);
                
                foreach ($centerIds as $cid) {
                    //por cada center creo un objeto con center_id nuevo
                    $race['center_id'] = $cid;
                    //lo guardo con la misma data
                    $this->Race->save($race);
                    $this->Horse->saveHorses($this->Race->id,$race['horses_num']);
                    
                    //guardo en family ese son
                    $familyModel->save(array(
                        'race_id'   => $raceMom, 'race_son' => $this->Race->id,
                        'center_id' => $cid
                    ));
                    unset($this->Race->id);
                    unset($familyModel->id);
                }				
            }
            
			$operModel->ins_op(3,$this->authUser['profile_id'],"Carreras","",
                    "Carreras [ $cars ] de " . $hipodInfo['Hipodrome']['name']);			
			
			$this->Session->setFlash(count($this->data['Race']) . ' Carreras Guardadas, ' . 
                                        count($centerIds). ' centros copiados.');
			$this->redirect(array('action' => 'index_root'));
		}
		$hipodromes = $this->Race->Hipodrome->find('list',array('order'=>array('name' => 'ASC')));
		$this->set(compact('hipodromes'));
	}

	function admin_edit($id = null) {
		if (!empty($this->data)) {	
			//pr($this->data); die();
            
            if ($this->Race->save($this->data)) {
				$mess = "Carrera Editada.";
                
				
                $familyInst = ClassRegistry::init('Family');
                $races_sons = $familyInst->find('list',array(
                    'conditions' => array('race_id' => $this->data['Race']['id']),
                    'fields' => 'race_son'
                ));

                $my_race = array();
                foreach($races_sons as $rs){
                    $my_race = $this->data;
                    $my_race['Race']['id'] = $rs;
                    $this->Race->save($my_race);
                }
                $mess .= " Y sus respectivas creaciones.";
				
                
				$operInst = ClassRegistry::init('Operation');
				$operInst->ins_op(4,$this->authUser['profile_id'],"Carreras",$this->data['Race']['id'],"Carrera Editada");			
				
			} else {
				$mess = "Error: Carrera NO Editada.";
			}
			
			$this->Session->setFlash($mess);
			$this->redirect($this->referer());
		}
		
		$this->data = $this->Race->read(null, $id);
		$hipodromes = $this->Race->Hipodrome->find('list');
		$this->set(compact('hipodromes'));
	}
	
	function admin_viewdep($date = null, $htrackid = 0){
		
        if($date == null)
			$date = date('Y-m-d');
		
		$cond['race_date'] = $date;
		$cond['center_id'] = $this->authUser['center_id'];
		if($htrackid != 0){
			$cond['hipodrome_id'] = $htrackid;
		}
		
		$racesId  = $this->Race->find('list',array('fields'=>'id','conditions'=>$cond));
		
		$resultIns = ClassRegistry::init('Result');
		
        $results   = $resultIns->get_results($racesId);
       
        $hipodromes = $this->Race->getHorsetracksByDay(
							$date, 
							$this->authUser['center_id'],
                            0, //nationals?
                            true,
                            true
                            );
		
		$this->Race->recursive = 1;
		$this->Race->bindModel(array('hasMany'=>array('Horse')),false);
		$this->paginate['conditions'] = array('Race.id' => $racesId);
		$races = $this->paginate();
        
        foreach ($races as $rk => $race) {
            $races[$rk]['Horse'] = $this->Race->Horse->orderThem($race['Horse'],true);
        }
        
        $this->set(compact('races','hipodromes','results','date','htrackid'));
        
	}
	
    function admin_view($date = null, $htrackid = 0){
        
        if($date == null)
            $date = date('Y-m-d');
        
        $cond['race_date'] = $date;
        $cond['center_id'] = 1; //$this->authUser['center_id'];
        if($htrackid != 0){
            $cond['hipodrome_id'] = $htrackid;
        }
        
        $racesId  = $this->Race->find('list',array('fields'=>'id','conditions'=>$cond));
        
        $resultIns = ClassRegistry::init('Result');
        
        $results   = $resultIns->get_results($racesId);
       
        $hipodromes = $this->Race->getHorsetracksByDay(
                            $date, 
                            1,
                            0, //nationals?
                            true,
                            true
                            );
        
        $this->Race->recursive = 1;
        $this->Race->bindModel(array('hasMany'=>array('Horse')),false);
        $this->paginate['conditions'] = array('Race.id' => $racesId);
        $races = $this->paginate();
        
        foreach ($races as $rk => $race) {
            $races[$rk]['Horse'] = $this->Race->Horse->orderThem($race['Horse'],true);
        }
        
        $this->set(compact('races','hipodromes','results','date','htrackid'));
        
        //$this->render('admin_view');
    }


	function admin_set_enable($id,$status) {
		$this->Race->updateAll(array('enable'=>$status),array('Race.id'=>$id));
		
        $familyInst = ClassRegistry::init('Family');
        $races_sons = $familyInst->find('list',array(
            'conditions' => array('race_id' => $id),
            'fields' => 'race_son'
        ));

        foreach($races_sons as $rs){
            $this->Race->updateAll(array('enable'=>$status),array('Race.id'=>$rs));
        }

		$stat = "Habilitada";
		if($status == 0)
			$stat = "Deshabilitada";
		
		$operInst = ClassRegistry::init('Operation');
		$operInst->ins_op(4,$this->authUser['profile_id'],"Carreras",$id,"Carrera $stat");			
				
		$this->redirect($this->referer());
	}
	
	function admin_list_ajax($hipodrome_id,$date,$json = 0){
		
		$races = $this->Race->find('all',array(
				'conditions'=>array(
					'hipodrome_id' => $hipodrome_id,
					'center_id'    => $this->authUser['center_id'],
					'race_date'    => $date,
                    //'local_time >' => date('H:i:s'),
					'Race.enable'  => 1,
                    'ended'        => 0
				),
				'fields' => array('Race.id','number','race_time','local_time',
                                'post_time','Hipodrome.name','Hipodrome.htgmt'),
                'order'  => array('race_time' => 'ASC')
		));
        
        $theTime  = date("H:i:s");
        
        $theRaces = array();
        
        foreach ($races as $race) {
        
            $ptime = "";
            if ($race['Race']['post_time'] > 0) {
                $ptime = " (pt:" .$race['Race']['post_time'] . ")";
            }
            
            //aqui debo sumarle el post-time a el local time y a ese
            //es que le hago el calculo de iniciado.
            $realtime  = $this->_addMins($race['Race']['local_time'],
                                        $race['Race']['post_time']);
            
            
            $strval    = strtotime($realtime) - strtotime($theTime);
            
            $minutes   = round($strval / 60 );
            
            $timeStart = $this->_toStartFormat($minutes);
            $theRaces[$race['Race']['id']] = $race['Race']['number'] . "a: "  
                                            . $timeStart . $ptime;
            
        }
        
        //pr($theRaces);
        //pr($races);
        
        
		if($json == 1){
			echo json_encode($theRaces);
			die();		
		}else{
			$this->set('races',$theRaces);
			$this->layout = 'ajax';	
		}
	}
	
	function admin_data_pick($hipodrome_id,$date){
		$races = $this->Race->find('list',array('conditions'=>array(
				'hipodrome_id'=>$hipodrome_id,'center_id'=>$this->authUser['center_id'],
				'race_date'=>$date,'race_time >'=>date('H:i:s'),'enable'=>1),
				'fields' => 'number'
		));
		
		//buscar todos  los caballos y arreglarlos pal JSON bellezo
		$horsesFind = $this->Horse->find('all',array(
			'conditions'=>array('race_id'=>array_keys($races),'enable'=>1),
			'fields'=>array('race_id','number','id'),'recursive'=>-1
		));
		
		$horses = array();
		foreach ($horsesFind as $h) {
			$horses[$h['Horse']['race_id']][$h['Horse']['id']] = $h['Horse']['number'];
		}
		
		echo json_encode(array("Races"=>$races,"Horses"=>$horses));
		die();		
		
	}
	
	function admin_delete($race_id){
		$ticket = ClassRegistry::init('Ticket');
		
		$familyInst = ClassRegistry::init('Family');
        $races_sons = $familyInst->find('list',array(
                        'conditions' => array('race_id' => $race_id),
                        'fields' => 'race_son'
                    ));

        $by_race = $ticket->find('count',
                        array('conditions' => array('race_id' => $races_sons)));
		
		if($by_race > 0){
			$this->Session->setFlash("Carrera NO PUEDE SER BORRADA, ya tiene apuestas en ella.");
		}else{
            if($this->Race->del($race_id)) {
                foreach($races_sons as $rs){
                    $this->Race->del($rs);
                }
                $this->Session->setFlash("Carrera Borrada con exito.");
            }
		}
        
		$operInst = ClassRegistry::init('Operation');
        
		$operInst->ins_op(5,$this->authUser['profile_id'],"Carreras",$race_id,"Carrera Borrada");
		
		$this->redirect($this->referer());	
	}

    function admin_getmygmt($time,$htgmt)
    {
        $configInst = ClassRegistry::init('Config');
        $cnfsRoot   = $configInst->find('first',array(
                        'conditions' => array('config_type_id' => 6)
                        ));
        $winter     = $cnfsRoot['Config']['actual'];
        
        $localtime  = $this->Race->getLocalTime(str_replace('-',':',$time),$htgmt,$winter);
        
        $ltime      = new DateTime($localtime);
        
        echo json_encode(array('valraw' => $localtime, 'valform' => date_format($ltime,"h:i A")));
        
        die();
    }
    /**
     * LOAD FROM CSV :: 
     */
    function admin_loadfile ()
    {
        if (!empty($this->data)) {
            
            //pr($this->data); die();
            
            $this->data['Race']['csvfile'];
            
            $saved = $this->Race->loadCsv($this->data['Race']['csvfile']['tmp_name']);
            
            if (isset($saved['Error'])) {
                $flMess = $saved['Error'];
            } else {
                $mparts = explode('.',$saved['saved']);
                
                $flMess = $mparts[0]. " carreras del " . $mparts[1] . " de " .
                            $mparts[2] . " guardadas con exito, para " .
                            $mparts[3] ." centros.";
            }
            $this->Session->setFlash($flMess);
            $this->redirect($this->referer());
        }
        
    }
    
    
    function admin_verifysrvc ($minutes = 10)
    {
        $urlCheck   = "http://www.twinspires.com/php/fw/php_BRIS_BatchAPI/2.3/Tote/CurrentRace?affid=2800&cDate=20150922&username=my_sports&output=json&password=Gltbatm&username=my_sports";
        
        $jsondata   = file_get_contents($urlCheck);    
        
        $data       = json_decode($jsondata, true);
        
        $raceStats  = $this->_arrangeByStatus($data['CurrentRace'],'',$minutes); //array();
        
        unset($jsondata);
        
        unset($data);
        
        //pr($raceStats); die();
        //debo hacer otra que reciba el status, o modificar esta
        
        $this->set(compact('raceStats','minutes'));
        
    }
    
    function admin_verify_ours($nick,$number,$srvTime,$srvGmt)
    {
        $srvGmt = $srvGmt * -1;
            
        $race = $this->Race->getByNickNumber($nick,$number);
        
        if ( empty($race) ) {
            echo "NO Race found!";
        } else {
            $srvLocalTime = $this->Race->getLocalTime($srvTime,$srvGmt,0);
            
            $realtime  = $this->_addMins($race['Race']['local_time'],
                                        $race['Race']['post_time']);
            
            $minutes   = $this->_minsToStart($realtime);
            
            $timeStart = $this->_toStartFormat($minutes);
            
            echo $race['Race']['number'] . " - ". $race['Hipodrome']['name'];
            echo "<br />". $race['Race']['race_time']. " :: " . $race['Race']['local_time'];
            echo "<br /> $timeStart - $srvLocalTime";
        }
        //pr($race);
        
        die();
    }
    
    function admin_checksrv($noajax = '')
    {
        $operInst   = ClassRegistry::init('Operation');
        
        $familyInst = ClassRegistry::init('Family');
                
        $urlCheck   = "http://www.twinspires.com/php/fw/php_BRIS_BatchAPI/2.3/Tote/CurrentRace?affid=2800&cDate=20150922&username=my_sports&output=json&password=Gltbatm&username=my_sports";
        
        $jsondata   = file_get_contents($urlCheck);    
        
        $data       = json_decode($jsondata, true);
        
        $raceStats  = $this->_arrangeByStatus($data['CurrentRace'],'toclose'); //array();
        
        $changeTxt  = array('Count' => 0,'Races' => array());
        //pr($data);
               
        unset($jsondata);
        
        unset($data);
        
        
        foreach ($raceStats as $stat => $races ) {
                
            foreach ($races as $race ) {
            
                //$raceStats[$stat][$rk]['MyRace'] 
                    
                $myRace = $this->Race->getByNickNumber($race['BrisCode'],$race['RaceNum']);
                
                // I have a race
                if ( !empty( $myRace ) ) {
                    
                    //needs to be disabled 
                    if ($myRace['Race']['enable'] == 1) {
                       
                        
                        //change message
                        $changeTxt['Count'] ++;
                        array_push($changeTxt['Races'],array(
                                'nro'  => $race['RaceNum'], 
                                'htrk' => $race['DisplayName'],
                                'type' => $stat));
                        
                        $this->Race->updateAll(
                            array('enable'  => 0),
                            array('Race.id' => $myRace['Race']['id']));
                        
                        //operation root line
                        $operInst->ins_op(4,$this->authUser['profile_id'],
                            "Carreras",$myRace['Race']['id'],"SERV:$stat Carrera suspendida.(".
                                    $race['RaceNum'] . "-" . $race['DisplayName'] . ")"
                            );
                        
                        //and its sons
                        $racesSons = $familyInst->find('list',array(
                                            'conditions' => array('race_id' => $myRace['Race']['id']),
                                            'fields' => 'race_son'
                                        ));

                        foreach($racesSons as $rs){
                            $this->Race->updateAll(
                                array('enable'  => 0),
                                array('Race.id' => $rs));
                            
                            $operInst->ins_op(4,$this->authUser['profile_id'],
                            "Carreras",$rs,"SERV:$stat Carrera suspendida.(".
                                    $race['RaceNum'] . "-" . $race['DisplayName'] . ")");
                            
                        }
                    }
                }
            }
        }
        
        if ($noajax == 'foo') {
            pr($changeTxt);
            pr($raceStats);
            die('--');
        }else {
            die(json_encode($changeTxt));
        }
       
    }
    
    function admin_goodques()
    {
        $races = $this->Race->find('all',array(
                    //'conditions' => array(),
                    'group'  => array('race_date'),
                    'fields' => array('race_date','COUNT(*)'),
                    'order'  => array('race_date' => 'DESC'),
                    'limit'  => 10
                ));
        
        echo "<h2>Last races on system:</h2>";
        pr($races);
        die("---");
    }
    
    function admin_nextones()
    {
        $date     = date('Y-m-d'); 
        
        $centerId = 1;
        
        $races    = $this->Race->getDayRaces($date, $centerId);                        
        
        $racesObj = array();
        
        foreach ($races as $race) {
            
            $realtime  = $this->_addMins($race['Race']['local_time'],
                                        $race['Race']['post_time']);
            
            $minutes   = $this->_minsToStart($realtime);
            
            $rtime     = new DateTime($realtime);
            $timeStart = $this->_toStartFormat($minutes);
            $raceob    = array(
                            'id'     => $race['Race']['id'],
                            'race'   => $race['Race']['number'],
                            'time'   => date_format($rtime, 'g:i A'),
                            'diff'   => $timeStart,
                            'htrack' => $race['Hipodrome']['name'],
                            'ptime'  => $race['Race']['post_time']
                        );

            array_push($racesObj,$raceob);

        }
        
        $this->set(compact('racesObj'));
    }
    
    function admin_ptimeserv()
    {
        
        $operInst   = ClassRegistry::init('Operation');
        
        $familyInst = ClassRegistry::init('Family');
        
        if (!empty($this->data)) {
            //pr($this->data); die();
            
            foreach ($this->data['Race'] as $rid => $change) {
                if ($change['sel'] == 1 ) {
                    $raceChange = array('id' => $rid, 
                                    'local_time' => $change['newtime']);
                
                    $this->Race->save($raceChange);
                
                    $races_sons = $familyInst->find('list',array(
                        'conditions' => array('race_id' => $rid),
                        'fields'     => 'race_son'
                    ));
                    
                    foreach ($races_sons as $rs) {
                        $raceChange['id'] = $rs;
                        $this->Race->save($raceChange);
                    }
                    
                    $operInst->ins_op(4,$this->authUser['profile_id'],"Carreras",
                        $rid,"SERV::Car-Edit. Hora Diff.");
                
                    }
            }
            
            $this->Session->setFlash("Carrera Editada.");
            $this->redirect($this->referer());
            
        }
        
                
        $urlCheck   = "http://www.twinspires.com/php/fw/php_BRIS_BatchAPI/2.3/Tote/CurrentRace?affid=2800&cDate=20150922&username=my_sports&output=json&password=Gltbatm&username=my_sports";
        
        $jsondata   = file_get_contents($urlCheck);    
        
        $data       = json_decode($jsondata, true);
            
        $raceStats  = $this->_arrangeByStatus($data['CurrentRace'],'next',30);
        
        if (isset($raceStats['Open'])) {
            unset($raceStats['Open']);
        }
        
        foreach ($raceStats['Next30'] as $rsk => $next) {
            
            $ptimePts = explode('T', $next['PostTime']);
            $theTime  = explode('-', $ptimePts[1]);
            $theGmt   = explode(':', $theTime[1]);
            $lastVal  = $this->_getLocalTimeSrv($theTime[0],-5);
            
            $raceStats['Next30'][$rsk]['Local']  = $lastVal;
            
            $raceStats['Next30'][$rsk]['MyRace'] = $this->Race->getByNickNumber(
                                                        $next['BrisCode'],$next['RaceNum']);
        }
        
        //echo "timeNow:" . date('H:i:s');pr($raceStats); die();
        $this->set('raceStats',$raceStats['Next30']);
    }
    
    
    /**
     * JSON TO PAGE NEXT RACES
     */
    function nextones()
    {
        Configure::write('debug',0);
        $racesObj = $this->Race->getNextOnes(date('Y-m-d'),1);
        echo $_GET['callback']."(".json_encode($racesObj).");";
        die();
    }
    
    //auto service
    function autoptime()
    {
        $operInst   = ClassRegistry::init('Operation');
        $familyInst = ClassRegistry::init('Family');        
        $urlCheck   = "http://www.twinspires.com/php/fw/php_BRIS_BatchAPI/2.3/Tote/CurrentRace?affid=2800&cDate=20150922&username=my_sports&output=json&password=Gltbatm&username=my_sports";
        $jsondata   = file_get_contents($urlCheck);    
        $data       = json_decode($jsondata, true);
        $minsCheck  = 60;  
        $indexMins  = 'Next' . $minsCheck;
        $raceStats  = $this->_arrangeByStatus($data['CurrentRace'],'next',$minsCheck);
        
        if (isset($raceStats['Open'])) {
            unset($raceStats['Open']);
        }
        
        foreach ($raceStats[$indexMins] as $rsk => $next) {
            $ptimePts = explode('T', $next['PostTime']);
            $theTime  = explode('-', $ptimePts[1]);
            //$theGmt   = explode(':', $theTime[1]);
            $lastVal  = $this->_getLocalTimeSrv($theTime[0],-5);
            $raceStats[$indexMins][$rsk]['Local']  = $lastVal;
            $myRace = $this->Race->getByNickNumber($next['BrisCode'],$next['RaceNum']);
            //echo "my Race";
            //pr($myRace);
            if ( !empty ( $myRace ) ) {
                //$raceStats[$indexMins][$rsk]['MyRace'] = $myRace;
                if ($myRace['Race']['local_time'] != $lastVal ) {
                    //$raceStats[$indexMins][$rsk]['DiffTime'] = 'YES : ' . $lastVal;
                    $raceChange = array('id' => $myRace['Race']['id'], 'local_time' => $lastVal);
                    $this->Race->save($raceChange);
                    $races_sons = $familyInst->find('list',array(
                                    'conditions' => array('race_id' => $myRace['Race']['id']),
                                    'fields'     => 'race_son'
                                  ));
                    
                    foreach ($races_sons as $rs) {
                        $raceChange['id'] = $rs;
                        $this->Race->save($raceChange);
                    }
                    
                    $operInst->ins_op(4,1,"Carreras", $myRace['Race']['id'],
                        "AUTOSERV::Edicion hora dif.: " . $myRace['Race']['number'] . 
                        'a ' . $myRace['Hipodrome']['name']);
                
                    echo "Cambiada la hora de " .$myRace['Race']['number'] . 
                        ' - ' . $myRace['Hipodrome']['name'];
                }                
            }            
        }
        
        echo "Time Now:" . date('H:i:s');
        //pr($raceStats); 
        die();
    }
    /*
    Internal functions
     */
    
    function _getLocalTimeSrv($time,$gmtSrv)
    {
        $gmtRest = $gmtSrv + 4.5;
        
        $minutes = $gmtRest * 60 * -1;
            
        $rTime   = new DateTime($time);
        
        $newTime = $rTime->modify("$minutes minutes");
        
        return date_format($newTime,'H:i:s');
    }
    
    function _addMins($time,$mins)
    {
        $repTime  =  $mins * 60;
        
        $rtimeStr = strtotime($time) + $repTime;

        return date('H:i:s', $rtimeStr);
    }
    
    function _minsToStart($realTime)
    {
        $nowTime = date('H:i:s');
        
        $strval  = strtotime($realTime) - strtotime($nowTime);
            
        return round($strval / 60 );
    }
    
    function _toStartFormat($minutes)
    {
        if ($minutes <= 0 ) {
            $tmStart = "0m";
        }
        
        if ( $minutes >= 1 && $minutes <= 60 ) {
            $tmStart = $minutes . "m";
        }
        
        if ($minutes >= 60) {
            $hours   = round(($minutes / 60),2);
            $hrs     = explode('.',$hours);
            $mins    = 0;
            if (isset($hrs[1]))
                $mins = round($hrs[1] * 0.6);
            
            $tmStart = $hrs[0] ."h ". $mins . "m";
        }
        
        return $tmStart;
    }
    
    function _arrangeByStatus($dataRaces, $toReturn = '',$minutesNext = 0)
    {
        $fixedRaces = array();
        
        //'toclose'
        
        foreach ($dataRaces as $race ) {
            /*
             * [BrisCode] => med
                [TrackType] => Thoroughbred
                [RaceNum] => 1
                [Mtp] => 54
                [PostTime] => 2015-10-12T14:15:00-04:00
                [FirstPostTime] => 2015-10-12T14:15:00-04:00
                [RaceStatus] => Open
                [Status] => Open
                [DisplayName] => Monmouth at Meadowlands
                [TrackCanceled]
             */
            //solo las closed and off
            
            if ($toReturn == 'toclose') {
                if ($race['RaceStatus'] != 'Open' && $race['TrackType'] != "Harness") {
                
                    $stat = $race['RaceStatus'];
                    unset($race['RaceStatus']);
                    unset($race['Mtp']);
                    unset($race['FirstPostTime']);
                    unset($race['Status']);
                    unset($race['TrackCanceled']);

                    $rtime = explode('T',$race['PostTime']);
                    $race['RaceTime'] = $rtime[1];

                    if (!isset($fixedRaces[$stat])) 
                        $fixedRaces[$stat] = array();

                    array_push($fixedRaces[$stat],$race);
                }
            } elseif ($toReturn == 'next') {
                if ($race['RaceStatus'] != 'Off' &&
                  $race['RaceStatus'] != 'Closed' &&
                  $race['TrackType'] != "Harness") {
                
                    $stat  = $race['RaceStatus'];
                    $rtime = explode('T',$race['PostTime']);
                    $race['RaceTime'] = $rtime[1];

                    if ($race['Mtp'] <= $minutesNext && $race['RaceStatus'] == 'Open') {
                        $stat = "Next$minutesNext";
                    }
                    if (!isset($fixedRaces[$stat])) 
                        $fixedRaces[$stat] = array();

                    unset($race['RaceStatus']);
                    unset($race['Status']);
                    unset($race['FirstPostTime']);
                    unset($race['TrackType']);
                    unset($race['TrackCanceled']);
                    
                    array_push($fixedRaces[$stat],$race);
                }
            }else {
                
                if ($race['TrackType'] != "Harness") {
                    $stat  = $race['RaceStatus'];
                    $rtime = explode('T',$race['PostTime']);
                    $race['RaceTime'] = $rtime[1];

                    if ($race['Mtp'] <= $minutesNext && $race['RaceStatus'] == 'Open') {
                        $stat = "Next$minutesNext";
                    }
                    if (!isset($fixedRaces[$stat])) 
                        $fixedRaces[$stat] = array();

                    array_push($fixedRaces[$stat],$race);
                }
            
            }
            
            
        
        }
        
        return $fixedRaces;
    }
    
    /*
	function admin_index($date = null, $hipodrome_id = 0, $ended = 0) {
		
		if($date == null)
			$date = date('Y-m-d');
		
		$cond['race_date'] = $date;
		$cond['center_id'] = $this->authUser['center_id'];
		
        
        if($hipodrome_id != 0){
			$cond['hipodrome_id'] = $hipodrome_id;
		}
		if($ended != 2)
			$cond['ended'] = $ended;
		
	
		$races_id = $this->Race->find('list',array('fields'=>'id','conditions'=>$cond));
		 
		$horses   = $this->Horse->find('list',array(
                        'fields' => 'race_id', 
                        'conditions' => array('race_id' => $races_id),
                        'group' => 'race_id'
                    ));
		
		$this->paginate['recursive']  = 0;
		
		$this->paginate['conditions'] = array('Race.id' => $races_id);
		
        $hipodromes = $this->Race->getHorsetracksByDay(
							$date, 
							$this->authUser['center_id'],
                            0, //nationals?
                            true,
                            true
                            );
        
		$this->set('races', $this->paginate());
		$this->set('hipodrome_id', $hipodrome_id);
		$this->set(compact('horses','ended','date','hipodromes'));
	}

	function admin_assign($date = null){
		
		$familyInst = ClassRegistry::init('Family');
		
		if(!empty($this->data)){
            
            pr($this->data); die();
            
			$races = $this->Race->find('all',array(
                            'conditions' => array(
                                'center_id'    => 1,
                                'hipodrome_id' => array_keys($this->data['Race']['races']),
                                'race_date'    => $this->data['Race']['date']),
                            'fields'     => array(
                                'Race.id',
                                'hipodrome_id',
                                'number',
                                'race_date',
                                'race_time',
                                'local_time') ,
                            'recursive' => -1
                        ));	
			
			$race_ids = array();
            
			foreach ($races as $race) {
				array_push($race_ids,$race['Race']['id']);
			}
			
			$horseInst = ClassRegistry::init('Horse');
			
			$horses = $horseInst->find('all',array(
				'conditions' => array('race_id'=>$race_ids),'recursive'=>-1
			));
			
			$realHorse = array();
			foreach ($horses as $hk => $horse) {
				
				if(!empty($realHorse[$horse['Horse']['race_id']]))
					array_push($realHorse[$horse['Horse']['race_id']],array('number'=>$horse['Horse']['number'],'name'=>$horse['Horse']['name'])); 
				else 
					$realHorse[$horse['Horse']['race_id']][0] = array('number'=>$horse['Horse']['number'],'name'=>$horse['Horse']['name']);
			}
			
			$realRace = array();
			foreach ($races as $race) {
				$realRace[$race['Race']['hipodrome_id']][$race['Race']['id']]['number'] = $race['Race']['number'];
				$realRace[$race['Race']['hipodrome_id']][$race['Race']['id']]['race_date'] = $race['Race']['race_date'];
				$realRace[$race['Race']['hipodrome_id']][$race['Race']['id']]['race_time'] = $race['Race']['race_time'];
                $realRace[$race['Race']['hipodrome_id']][$race['Race']['id']]['local_time'] = $race['Race']['local_time'];
                $realRace[$race['Race']['hipodrome_id']][$race['Race']['id']]['Horse'] = $realHorse[$race['Race']['id']];
			}
			
			$i = 0;
			$this->Race->create();
			foreach ($this->data['Race']['races'] as $hip_id => $race) {
				foreach ($race as $cent_id => $val) {
					//si fue seleccionado el checkbox
					if($val == 1){
						foreach($realRace[$hip_id] as $rk => $races){
							$this->Race->save(array(
								'number'       => $races['number'],
                                'race_date'    => $races['race_date'],
								'race_time'    => $races['race_time'],
                                'local_time'   => $races['local_time'],
								'center_id'    => $cent_id,
                                'hipodrome_id' => $hip_id
							));
							//guardar sus caballos
							foreach ($races['Horse'] as $hors) {
								$horseInst->save(array(
									'race_id' => $this->Race->id,
									'name'=>$hors['name'],'number'=>$hors['number']
								));
								unset($horseInst->id);
							}
							//dejar evidencia :P
							$familyInst->save(array(
								'race_id' => $rk, 'race_son' => $this->Race->id,
								'center_id' =>$cent_id
							));
							unset($familyInst->id);
							unset($this->Race->id);
						}
						$i ++;
					}
				}
			}
			
			//pr($this->data); pr($races); pr($horses); pr($realHorse); pr($realRace); die();
			$this->Session->setFlash('Carreras Asignadas');
			$this->redirect($this->referer());
		}
		
        //GET PART
        
        if ($date == null) {
            $date = date('Y-m-d');
        }
        
        
		$racesByHip = $this->Race->find('all',array(
                            'conditions' => array(
                                        'race_date' => $date, 
                                        'center_id' => 1
                            ),
                            'fields'     => array(
                                            'Hipodrome.id',
                                            'Hipodrome.name',
                                            'count(*) AS co'
                            ),
                            'group'     => 'hipodrome_id'
                        ));
		
		$centIns     = ClassRegistry::init('Center');
		
		$centers     = $centIns->find('list',array('conditions'=>'Center.id > 1'));
		
		$raceParents = $this->Race->find('list',array(
                            'conditions' => array(
                                        'race_date' => $date, 'center_id' => 1),
                            'fields'     => 'hipodrome_id'
                        ));
		
		$assigned = $familyInst->find('all',array(
                        'conditions' => array(
                                'race_id'   => array_keys($raceParents),
                                'center_id' => array_keys($centers)
                    )));
		
		$realAss = array();
		foreach ($assigned as $a) {
			$realAss[$raceParents[$a['Family']['race_id']]][$a['Family']['center_id']] = 1;
		}
		
		//pr($races_parents); pr($realAss); pr($assigned); die();
		$this->set(compact('centers','date'));
        $this->set('assigned',$realAss);
		$this->set('races',$racesByHip);
	}
	*/
}
