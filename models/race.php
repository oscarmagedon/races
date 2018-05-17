<?php
class Race extends AppModel {

	var $name = 'Race';
	
	var $belongsTo = array('Hipodrome');

	/**
	 * Verifies race started
	 * 
	 * return @bool
	 */
	function verify_started($id){
		
		$hour = $this->find('first',array(
                    'conditions' => array('id' => $id),
                    'fields'     => array('local_time','post_time','enable'), 
                    'recursive'  => -1
                ));
		
        $started   = false;
        
        if ($hour['Race']['enable'] == 0) {
            $started = true;
        }
        /*
        $repTime   = $hour['Race']['post_time'] * 60;
        
        $realtmStr = strtotime($hour['Race']['local_time']) + $repTime;

        $realTime  = date('H:i:s', $realtmStr);
        
        if ( strtotime(date("H:i:s")) >= strtotime($realTime) ) {
            $started = true;
        }
        */
		return $started;
	}
    
    // GMT FUNCTION
    function getLocalTime($time,$htGmt,$winter = 0)
    {
        //GMT fijo -4.5 Vzla
        $myGmt     = -4;//-4.5;
        
        if ($winter == 1) {
            $myGmt += 1;
        }
        
        $diffGmt   = $htGmt - $myGmt;
        
        $plusHours = $diffGmt * 3600 * -1;
        
        $hoursStr  = strtotime($time) + $plusHours;
        
        return date('H:i:s', $hoursStr);
    }
    
    function getHtrackId($id)
    {
        $htr = $this->find('first',array(
                    'conditions' => array('id' => $id),
                    'fields'     => array('hipodrome_id'),
                    'recursive'  => -1 ));
        
        return  $htr['Race']['hipodrome_id'];
    }
	
	/**
	 * Returns the hipodromes by center and date
	 * 
	 * return list
	 */
	function getHorsetracksByDay($date, $centerId, $nationals = 0, 
        $allRaces = false, $counter = false)
	{
        
        $conds = array(
                        //'Race.enable' => 1, 
                        'center_id'   => $centerId,
                 );
        
        if ($allRaces !== true ) {
            $conds['ended']      = 0;
            //$conds['local_time >'] = date('H:i:s');
        } 
         
        if ( is_array($date) ) {
            $conds['race_date BETWEEN ? AND ?'] = array($date[0],$date[1]);        
        } else {
            $conds['race_date'] = $date;
        }
        
		$todayHips = $this->find('all',
                            array(
                                'conditions' => $conds,
                                'fields'     => array('count(*) as co', 'hipodrome_id'),
                                'group'      => 'hipodrome_id',
                                'recursive'  => -1
                            )
                        );
        
        $counts = array();
        
        foreach ($todayHips as $today) {
            $counts[$today['Race']['hipodrome_id']] = $today[0]['co'];
        }
		
		$conds = array('Hipodrome.id' => array_keys($counts));
		
		if($nationals == 1) 
			$conds['national'] = 1;
		
		if($nationals == 2) 
			$conds['national'] = 0;
		
		
        $finalHtracks = $this->Hipodrome->find('list', array(
                            'conditions' => $conds,
                            'order'      => array('Hipodrome.name' => 'ASC'),
                        ));
        
        if ($counter == true) {
            foreach ($finalHtracks as $fk => $ft) {
                $finalHtracks[$fk] .= " (" . $counts[$fk]. ")";
            }
        }
        
		return $finalHtracks;	
		
	}
    
    /**
	 * Returns the hipodromes by center and dates since and from
	 * 
	 * return list
	 */
	function getHtracksByRange( $centerId, $dfrom, $duntil, $nationals = 0 )
	{
        $conds   = array(
                        'center_id'                 => $centerId,
                        'race_date BETWEEN ? AND ?' => array($dfrom,$duntil));
        
		$htracks = $this->find('list', array(
                                    'conditions' => $conds,
                                    'fields'     => 'hipodrome_id',
                                    'group'      => 'hipodrome_id',
                                    'recursive'  => -1 ));
        //pr($htracks);
        
		if($nationals == 1) 
			$conds['national'] = 1;
		
		if($nationals == 2) 
			$conds['national'] = 0;
		
		
        $finalHtracks = $this->Hipodrome->find('list', array(
                            'conditions' => array('id'   => $htracks),
                            'order'      => array('name' => 'ASC') ) );
        
        
		return $finalHtracks;
	}
    
    /**
	 * Returns the hipodromes by center and date
	 * 
	 * return list
	 */
	function getHtracksNicksDay($date)
    {
        /*
         * ALTER TABLE `hipodromes` ADD `last_ret_check` DATETIME NULL DEFAULT NULL ;
         */
        $conds = array(
                        'center_id' => 1,
                        'race_date' => $date,
                        'ended'     => 0
                 );
        
		$todayHips = $this->find('list', array(
                                'conditions' => $conds,
                                'fields'     => 'hipodrome_id',
                                'group'      => 'hipodrome_id',
                                'recursive'  => -1
                            ));
		
        $finalHtracks = $this->Hipodrome->find('all', array(
                            'conditions' => array(
                                                    'Hipodrome.id' => $todayHips,
                                                    'national'     => 0
                                        ),
                            'order'      => array('last_ret_check' => 'ASC'),
                            'limit'      => 3,
                            'fields'     => array('id','nick','tvgnick','last_ret_check')
                        ));
        
        $htracks = array();
        
        foreach ($finalHtracks as $fh) {
            $lastNick = $fh['Hipodrome']['nick'];
            if ($fh['Hipodrome']['tvgnick'] != '') {
                $lastNick = $fh['Hipodrome']['tvgnick'];
            }
            
            $htracks[$fh['Hipodrome']['id']] = array(
                                                    'nick'  => $lastNick,
                                                    'check' => $fh['Hipodrome']['last_ret_check']
                                                );
        }
        
		return $htracks;	
		
	}
    /**
     * NExt three without results INTL'S
     */
    function getNextNicks($lim)
    {
        $date     = date('Y-m-d'); 
        $centerId = 1;
        $races    = $this->find('all',array(
                    'conditions' => array(
                        'race_date'          => $date,
                        'center_id'          => $centerId,
                        'Race.ended'         => 0,
                        'Hipodrome.national' => 0
                     ),
                    'fields' => array('Race.id', 'number','local_time',
                                        'Hipodrome.nick','Hipodrome.tvgnick',
                                        'Hipodrome.name'),
                    'order'  => array('local_time' => 'ASC'),
                    'limit'  => $lim
                )); 
        
        return $races;
    }
    
    /**
     * 
     * @param type $date
     * @param type $centerId
     * @param type $national
     * @return type
     */
    function getByNation($date, $centerId, $national)
	{
        $conds = array(
                        'enable'    => 1, 
                        'center_id' => $centerId
                 );
         
        if ( is_array($date) ) {
            $conds['race_date BETWEEN ? AND ?'] = array(
                        $date[0],$date[1]);        
        } else {
            $conds['race_date'] = $date;
        }
        
        if($national == 0) 
			$hipods = $this->Hipodrome->getIntlIds();
		else
            $hipods = $this->Hipodrome->getNatIds();
		
        $conds['hipodrome_id'] = $hipods;
        
		return $this->find('list',
                    array(
                        'conditions' => $conds,
                        'fields'     => 'Race.id'
                    )
                );
	}
    
	/**
	 * Gets the nearest race to the add tickets method
	 */
	function getNearest($centerId, $date, $hipodromes)
	{
		
		//$theTime = date("H:i:s");
				
		return $this->find('first',array(
						'conditions' => array(
											'hipodrome_id' => $hipodromes,
											//'local_time >' => $theTime,
											'race_date'    => $date,
											'center_id'    => $centerId,
                                            'Race.enable'  => 1,
                                            'ended'        => 0
									    ),
						'fields'     => array('Race.id','hipodrome_id'),
						'order'      => 'local_time'
				));
	}
	
	//Get Races and special results
	function getRaceResults($id)
	{
		return $this->find('first', array(
					'conditions' => array('Race.id' => $id),
					'fields'     => 
							array(
								'Race.id','Hipodrome.name','number','race_date',
								'exacta','trifecta','superfecta'
							)));
	}
    
    function getDayRaces($date,$centerId,$lim = 10)
    {
        $races = $this->find('all',array(
                    'conditions' => array(
                        'race_date'    => $date,
                        'center_id'    => $centerId,
                        'Race.enable'  => 1,
                        'Race.ended'   => 0
                    ),
                    'fields' => array(
                        'Race.id','number','Hipodrome.name','Hipodrome.national',
                        'local_time','post_time'
                    ),
                    'order' => array(
                        'local_time' => 'ASC'
                    ),
                    'limit' => $lim
                ));
        
        return $races;
    }
	
	//set race ended results 
	function setRaceEnded($raceId,$specials)
	{
		
		$this->updateAll(
					array(
						'ended'      => 1,
						'exacta'     => $specials['exacta'],
						'trifecta'   => $specials['trifecta'],
						'superfecta' => $specials['superfecta']
					),
					array('Race.id'  => $raceId)
				);
	}
	
	//if race is InterNational 
	function isIntl($id)
	{
		$htrack = $this->find('first',array(
					'conditions' => array('Race.id' => $id),
					'fields'     => 'Hipodrome.national'
				  ));
				  
		return ($htrack['Hipodrome']['national'] == 0);
	}
    
    //if races list are InterNational 
	function intlRaceList( $races )
	{
		$htracks = $this->find('list',array(
					'conditions' => array( 'Race.id' => $races ),
					'fields'     => 'Hipodrome.national',
                    'group'      => 'Race.id',
                    'recursive'  => 1
            ));
				  
		return $htracks;
	}
    
    function getByNickNumber($nick,$number)
    {
        $race = $this->find('first',array(
                    'conditions' => array(
                                    'center_id'      => 1,
                                    'Hipodrome.nick' => $nick,
                                    'number'         => $number,
                                    'race_date'      => date('Y-m-d')
                    ),
                    'fields'     => array(
                                    'Race.id','Hipodrome.name','Race.enable',
                                    'number', 'race_time','local_time','post_time'
                    )
                ));
        return $race;
    }
    
    //get next ones
    function getNextOnes($date, $centerId, $lim = 10, $restr = 0)
    {
        //$date = date('Y-m-d'); $centerId = 1;
        $races    = $this->getDayRaces($date, $centerId, $lim);                        
        $racesObj = array( 'name' => 'response', 'races' => array());
        //$racesObj['name'] = "response";
        //pr($races); echo $restr;
        foreach ($races as $race) {
            
            $realtime  = $this->_addMins($race['Race']['local_time'],
                                        $race['Race']['post_time']);
            $minutes   = $this->_minsToStart($realtime);
            $rtime     = new DateTime($realtime);
            $timeStart = $this->_toStartFormat($minutes);
            
            if ( $restr == 0 || ($restr == 1 && $race['Hipodrome']['national'] == 1 ) || 
                ($restr == 2 && $race['Hipodrome']['national'] == 0 )) {
                $raceob = array(
                            'id'     => $race['Race']['id'],
                            'race'   => $race['Race']['number'],
                            'time'   => date_format($rtime, 'g:i A'),
                            'diff'   => $timeStart,
                            'htrack' => $race['Hipodrome']['name'],
                            'ptime'  => $race['Race']['post_time']
                        );
                array_push($racesObj['races'],$raceob);
            }
        }
        return $racesObj;
    }
    
    //get races by hipod and number
    function getRacesOnByNum($date,$hid,$raceNums)
    {
        $races = $this->find('list',array(
                    'conditions' => array(
                                        'hipodrome_id' => $hid,
                                        'race_date'    => $date,
                                        'number'       => $raceNums,
                                        'center_id'    => 1,
                                        'enable'       => 1
                                    ),
                    'fields'     => 'number',
                    'recursive' => -1
                 ));
        
        return $races;
    }
    
    //get races and horses by hipod
    function getRacesHorses($date,$hid,$raceNums)
    {
        $this->unbindModel(array('belongsTo' => array('Hipodrome')));
        
        $this->bindModel(array('hasMany' => array('Horses' => array(
            'fields' => array('id','number','name','enable')
        ))));
        
        $races = $this->find('all',array(
                    'conditions' => array(
                                        'hipodrome_id' => $hid,
                                        'race_date'    => $date,
                                        'number'       => $raceNums,
                                        'center_id'    => 1,
                                        'ended'        => 0
                                    ),
                    'fields'     => array('Race.id','number','local_time'),
                    'recursive' => 2
                 ));
        
        $finalRaces = array();
        
        //pr($races);
        
        foreach ($races as $race) {
            $horsesRace = array();
            foreach ($race['Horses'] as $horse ) {
                $enabGroup = 'enabOn';
                if ($horse['enable'] == 0)
                    $enabGroup = 'enabOff';
                
                $horsesRace[$enabGroup][$horse['id']] = $horse['number'];
            }
            
            $finalRaces[$race['Race']['number']] = array(
                                                        'id'     => $race['Race']['id'],
                                                        'Horses' => $horsesRace
                                                    );
        }
            
        return $finalRaces;
    }
    
    function getNextStartGrouped()
    {
        $seconds = 1200; //20 mins
        $endTime = date('H:i:s',(strtotime(date('H:i:s')) + $seconds)); 
        //echo $endTime;
        $nextRaces = $this->find('all',array(
                        'conditions' => array(
                            'race_date'          => date('Y-m-d'),
                            'center_id'          => 1,
                            'local_time <'       => $endTime,
                            'Race.enable'        => 1,
                            'ended'              => 0,
                            'Hipodrome.national' => 0
                        ),
                        'fields' => array(
                            'Race.id','number','race_time','local_time',
                            'Hipodrome.id', 'Hipodrome.nick', 'Hipodrome.tvgnick'
                        ),
                        'limit' => 5
                    ));
        
        $grouped = array();
        foreach ($nextRaces as $race) {
            if ($race['Hipodrome']['tvgnick'] != '') {
                $nick = $race['Hipodrome']['tvgnick'];
            } else {
                $nick = $race['Hipodrome']['nick'];
            } 
            
            $grouped[$nick][$race['Race']['number']] = array(
                                                        'id'    => $race['Race']['id'],
                                                        'ltime' => $race['Race']['local_time']
                                                        );
        }

        //pr($nextRaces);
        return $grouped;
    }
    
    /**
     * FUNCTION TO THE CSV LOADING
     * 
     */
    function loadCsv($tmpname)    
    {
        
        $data        = str_getcsv(file_get_contents($tmpname), "\n"); //parse the rows
        $i           = 0;
        //$headers     = array();
        //$headersReal = array();
        $myLastRows  = array();
        
        foreach($data as $row) {
            
            if ($i == 0) {
                $headers     = str_getcsv($row, ",");
                $headersReal = $this->_getRealHeaders($headers);
            } else {
                $allRow         = str_getcsv($row, ",");
                $myLastRows[$i] = $this->_setFinalRow($allRow,$headersReal);
            }
            
            $i ++;
        }
        
        $allRaces  = $this->_setFinalObjCsv($myLastRows);
        
        //pr($allRaces); die();
        
        $saveRaces = $this->_saveFromCsv($allRaces);
         
        return $saveRaces;

    }
    
    /**
     * INNER FUNCTIONS
     */
    
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
    
    function _getRealHeaders($headers)
    {
        //the real needed are 9
        $headFinals  = array(
                            'R_RCTrack',
                            'R_RCDate',
                            'R_RCRace',
                            'R_PostTime',
                            'B_ProgNum',
                            'B_PostPosition',
                            'B_Horse',
                            'B_Jockey',
                            'B_MLOdds'
                        );
        $headersReal = array();
        
        foreach ($headers as $hk => $headTitle) {
            if (in_array($headTitle,$headFinals)) {
                $headersReal[$hk] = $headTitle;
            }
        }
        
        return $headersReal;
    }
    
    function _setFinalRow($allRow,$realHeaders)
    {
        $realRow = array();
        
        foreach ($realHeaders as $hrk => $hrtitle) {
            if ($allRow[$hrk] != "") {
                $realRow[$hrtitle] = $allRow[$hrk];
            }
        }
        
        return $realRow;
    }
    
    function _setFinalObjCsv($allRows)
    {
        $races = array();
        $rind  = 0;
        foreach ($allRows as $row) {
            
            if (isset ($row['R_RCTrack']) ) {
                $hind  = 1;
                $rind ++;
                
                $races[$rind]['Race'] = array(
                                    'htrk' => $row['R_RCTrack'],
                                    'race' => $row['R_RCRace'],
                                    'date' => $row['R_RCDate'],
                                    'time' => $row['R_PostTime']
                                );
                
                $races[$rind]['Horses'][0] = array(
                                        'number' => $row['B_ProgNum'],
                                        'hsname' => $row['B_Horse'],
                                        'jockey' => $row['B_Jockey'],
                                        'mlodds' => $row['B_MLOdds']
                                    );
               
            } else {
                $races[$rind]['Horses'][$hind] = array(
                                        'number' => $row['B_ProgNum'],
                                        'hsname' => $row['B_Horse'],
                                        'jockey' => $row['B_Jockey'],
                                        'mlodds' => $row['B_MLOdds']
                                            );
            
                $hind ++;
            }
            
        }
        
        return $races;
    }
    
    function _saveFromCsv($races)
    {
        $centerId   = 1; 
        $results    = array();
        $horseIns   = ClassRegistry::init('Horse');
        $centerIns  = ClassRegistry::init('Center');
        $familyInst = ClassRegistry::init('Family');
        $configInst = ClassRegistry::init('Config');
        $centerIds  = $centerIns->find('list',array(
                        'conditions' => array('id >' => 1),
                        'recursive'  => -1,
                        'fields'     => 'id'));
        
        $cnfsRoot   = $configInst->find('first',array(
                        'conditions' => array('config_type_id' => 6)
                        ));
        $winter     = $cnfsRoot['Config']['actual'];
        
        //pr($centerIds); die();
        
        if ( isset($races[1]['Race']['htrk']) ) {
            $htrack = $this->Hipodrome->getByNick($races[1]['Race']['htrk']);
            
            if (empty($htrack)) {
                $results['Error'] = "Nick -" . $races[1]['Race']['htrk'] . " - NO reconocido";
            }
        }
        
        if ( !isset($results['Error']) ) {
            $this->create();
            
            foreach ($races as $race) {
                $toSave = array(
                            'hipodrome_id' => $htrack['Hipodrome']['id'],
                            'center_id'    => $centerId,
                            'number'       => $race['Race']['race']
                        );
                
                $timePs = explode(':',$race['Race']['time']);
                
                $hour   = $timePs[0];
                if ($hour < 12)
                    $hour = $timePs[0] + 12;
                
                $realTm = $hour . ":" . $timePs[1];
                $rdate  = new DateTime($race['Race']['date']);
                $rtime  = new DateTime($realTm);
                
                $toSave['race_date']  = date_format($rdate,'Y-m-d');
                $toSave['race_time']  = date_format($rtime,'H:i:s');
                //local time
                $localTimeNew         = $this->getLocalTime(
                                                $toSave['race_time'],
                                                $htrack['Hipodrome']['htgmt'],
                                                $winter);
                //echo $localTimeNew;
                $localTime            = new DateTime($localTimeNew);
                
                $toSave['local_time'] = date_format($localTime,'H:i:s');
                
                //if local time is overnight,and race time is PM,
                // then change the race date
                /*if ($localTime < $rtime ) {
                    $tomorrow = $rdate->modify('+1 day');
                    $toSave['race_date']  = date_format($tomorrow,'Y-m-d');
                }*/
                
                //pr($toSave);
                
                $this->save($toSave);
                
                $horseIns->horsesFromCsv($this->id,$race['Horses']);
                
                $raceMom = $this->id;
                
                unset($this->id);
                
                //aqui debo guardar las copias a los centros activos
                foreach ($centerIds as $cid) {
                    
                    //por cada center creo un objeto con center_id nuevo
                    $toSave['center_id'] = $cid;
                    
                    //lo guardo con la misma data
                    $this->save($toSave);
                
                    $horseIns->horsesFromCsv($this->id,$race['Horses']);

                    //guardo en family ese son
                    $familyInst->save(array(
                        'race_id'   => $raceMom, 'race_son' => $this->id,
                        'center_id' => $cid
                    ));
                    unset($this->id);
                    unset($familyInst->id);
                } 
                
                
            }
            
            $results['saved'] = count($races) . '.' . $toSave['race_date'] . 
                                '.' . $htrack['Hipodrome']['name'] . '.' . 
                                count($centerIds);
        }
        
        return $results;
    }
    
}
?>