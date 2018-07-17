<?php
class Ticket extends AppModel {

	var $name = 'Ticket';
	
	var $belongsTo = array(
                    'PayedStatus',
                    'PlayType',
                    'Race'    => array('fields'=>array('number','hipodrome_id')),
                    'Profile' => array('fields'=>'name'),
                    'Center'  => array('fields'=>array('name','commercial_name'))
                    );
	
	var $hasAndBelongsToMany = array('Horse');

	function horse_setter($horses_bet,$ptid)
    {
		
		if($ptid == 1 || $ptid == 2 || $ptid == 3){
			foreach($horses_bet as $hk => $hv){
				$horses_bet[$hk]['play_type_id'] = $ptid;
			}
		}
		
		if($ptid == 4){ //WP
			foreach($horses_bet as $hk => $hv){
				$horses_bet[$hk]['play_type_id'] = 1;
				array_push(
					$horses_bet,
					array('horse_id' => $hv['horse_id'],'play_type_id' => 2)
				);
			}
		}
		
		if($ptid == 5){ //WS
			foreach($horses_bet as $hk => $hv){
				$horses_bet[$hk]['play_type_id'] = 1;
				array_push(
					$horses_bet,
					array('horse_id' => $hv['horse_id'],'play_type_id' => 3)
				);
			}
		}
		
		if($ptid == 6){ // WPS
			foreach($horses_bet as $hk => $hv){
				$horses_bet[$hk]['play_type_id'] = 1;
				array_push(
					$horses_bet,
					array('horse_id' => $hv['horse_id'],'play_type_id' => 2)
				);
				array_push(
					$horses_bet,
					array('horse_id' => $hv['horse_id'],'play_type_id' => 3)
				);
			}
		}
		
		
		return $horses_bet;
	}
	
	function horse_specials($horses_bet,$ptid)
    {
		switch ($ptid) {
			case 7:
				$horses_by_play = 2;
				break;
			case 8:
				$horses_by_play = 3;
				break;
			case 9:
				$horses_by_play = 4;
				break;
		}
				
		for($i = 1; $i <= $horses_by_play; $i ++){
			$positions[$i] = array();	
		}
		
		foreach($horses_bet as $horse){
			array_push($positions[$horse['position']],$horse['horse_id']);				
		}
		
		switch($horses_by_play){
			case 2:
				$boxes = $this->exacta($positions);
				break;
			case 3:
				$boxes = $this->trifecta($positions);
				break;
			case 4:
				$boxes = $this->superfecta($positions);
				break;
		}
		
		return $boxes;
	}
	
	function exacta($positions){
		
		$boxes = array();
		$i = 0;
		
		foreach($positions[1] as $first){
			foreach($positions[2] as $second){
				$boxes[$i][1] = $first;
				
				if(!array_search($second,$boxes[$i]))
					$boxes[$i][2] = $second; 
				
				if(count($boxes[$i]) != 2)
					unset($boxes[$i]);
				else	
					$i ++;
			}
		}
		
		$horses['Quantity'] = count($boxes);
		$box_num = 1;
		foreach ($boxes as $box){
			foreach ($box as $pos => $hor){
				if($pos == 1){
					$ptid = 10;
				}else{
					$ptid = 11;
				}
				array_push($horses,array('horse_id' => $hor,'play_type_id'=>$ptid,'box_number'=>$box_num));
			}
			$box_num ++;
		}
				
		return $horses;
	}
	
	function trifecta($positions){
		
		$boxes = array();
		$i = 0;
		foreach($positions[1] as $first){
			 
			foreach($positions[2] as $second){
				
				foreach($positions[3] as $third){
					$boxes[$i][1] = $first;
					
					if(!array_search($second,$boxes[$i]))
						$boxes[$i][2] = $second; 
						
					if(!array_search($third,$boxes[$i]))
						$boxes[$i][3] = $third; 
					
					if(count($boxes[$i]) != 3)
						unset($boxes[$i]);
					else	
						$i ++;
				}
			}
		}
		
		$horses['Quantity'] = count($boxes);
		$box_num = 1;
		foreach ($boxes as $box){
			foreach ($box as $pos => $hor){
				if($pos == 1){
					$ptid = 12;
				}elseif($pos == 2){
					$ptid = 13;
				}else{
					$ptid = 14;
				}
				array_push($horses,array('horse_id' => $hor,'play_type_id'=>$ptid,'box_number'=>$box_num));
			}
			$box_num ++;
		}
		
		return $horses;
	}
	
	function superfecta($positions){
		
		$boxes = array();
		$i = 0;
		foreach($positions[1] as $first){
			 
			foreach($positions[2] as $second){
				
				foreach($positions[3] as $third){
					
					foreach($positions[4] as $fourth){
						$boxes[$i][1] = $first;
						
						if(!array_search($second,$boxes[$i]))
							$boxes[$i][2] = $second; 
							
						if(!array_search($third,$boxes[$i]))
							$boxes[$i][3] = $third; 
							
						if(!array_search($fourth,$boxes[$i]))
							$boxes[$i][4] = $fourth; 
						
						if(count($boxes[$i]) != 4)
							unset($boxes[$i]);
						else	
							$i ++;
					}
				}	
			}
		}
		
		$horses['Quantity'] = count($boxes);
		$box_num = 1;
		foreach ($boxes as $box){
			foreach ($box as $pos => $hor){
				if($pos == 1){
					$ptid = 15;
				}elseif($pos == 2){
					$ptid = 16;
				}elseif($pos == 3){
					$ptid = 17;
				}else{
					$ptid = 18;
				}
				array_push($horses,array('horse_id' => $hor,'play_type_id'=>$ptid,'box_number'=>$box_num));
			}
			$box_num ++;
		}
		
		return $horses;
	}

	function make_picks($type,$horses){
		$real_picks = array();
		$i = 1;
		foreach($horses[1] as $h1){
			foreach($horses[2] as $h2){
				
				if($type == 2){
					$real_picks[$i][1] = $h1;
					$real_picks[$i][2] = $h2;
					$i ++;
				}
					
				
				if(!empty($horses[3])){
					foreach ($horses[3] as $h3) {
						$real_picks[$i][1] = $h1;
						$real_picks[$i][2] = $h2;
						$real_picks[$i][3] = $h3;
						if($type == 3)
							$i ++;
						
						
					}
				}
				
				if(!empty($horses[4])){
					foreach ($horses[4] as $h4) {
						$real_picks[$i][1] = $h1;
						$real_picks[$i][2] = $h2;
						$real_picks[$i][3] = $h3;
						$real_picks[$i][4] = $h4;
						if($type == 4)
							$i ++;
					}
				}
				
				if(!empty($horses[6])){
					foreach ($horses[5] as $h5) {
						foreach ($horses[6] as $h6) {
							$real_picks[$i][1] = $h1;
							$real_picks[$i][2] = $h2;
							$real_picks[$i][3] = $h3;
							$real_picks[$i][4] = $h4;
							$real_picks[$i][5] = $h5;						
							$real_picks[$i][6] = $h6;
							if($type == 6)
								$i ++;
						}
					}
				}
				
				if(!empty($horses[9])){
					foreach ($horses[7] as $h7) {
						foreach ($horses[8] as $h8) {
							foreach ($horses[9] as $h9) {
								$real_picks[$i][1] = $h1;
								$real_picks[$i][2] = $h2;
								$real_picks[$i][3] = $h3;
								$real_picks[$i][4] = $h4;
								$real_picks[$i][5] = $h5;						
								$real_picks[$i][6] = $h6;
								$real_picks[$i][7] = $h7;
								$real_picks[$i][8] = $h8;
								$real_picks[$i][9] = $h9;
								if($type == 9)
									$i ++;
							}
						}
					}
				}
			}
		}
		return $real_picks;
	}

	function show_picks($id,$detail = false){
		$this->HorsesTicket->bindModel(array(
			'belongsTo' => array('Horse'=>array('fields'=>array('race_id','number')))
		),false);
		$this->HorsesTicket->bindModel(array(
			'belongsTo' => array('HorsesTicketsStatus')
		),false);
		
		$htiks = $this->HorsesTicket->find('all',array(
			'conditions' => array('ticket_id'=>$id),
			'recursive' => 2
		));
		
		$horses = array();
		$allhorses = array();
		
		if($detail){
			foreach ($htiks as $h) {
				$horses[$h['HorsesTicket']['box_number']][$h['Horse']['Race']['number']]['horse'] = $h['Horse']['number'];
				$horses[$h['HorsesTicket']['box_number']][$h['Horse']['Race']['number']]['stat'] = $h['HorsesTicketsStatus']['name'];
			}	
		}else{
			foreach ($htiks as $h) {
				if($h['HorsesTicket']['box_number'] == 1)
					$horses[$h['HorsesTicket']['box_number']][$h['Horse']['Race']['number']] = $h['Horse']['number'];
				
				if(!empty($allhorses[$h['Horse']['Race']['number']])){
					if(!in_array($h['Horse']['number'],$allhorses[$h['Horse']['Race']['number']])){
						array_push($allhorses[$h['Horse']['Race']['number']],$h['Horse']['number']);
					}
				}else{
					$allhorses[$h['Horse']['Race']['number']][0] = $h['Horse']['number'];
				}
			}
		}
		
		$picks = count($horses[1]);	
		$racer = $this->Race->Hipodrome->find('first',array(
			'conditions' => array('id' => $htiks[0]['Horse']['Race']['hipodrome_id']),
			'fields' => array('name'),'recursive'=>-1
		));
		
		return array(
			'details'=>$allhorses,'picks'=>$picks,'hipodrome'=>$racer['Hipodrome']['name']
		);
	}
    
    function getSalesByHtrack($cid, $date)
    {
        $fixed = array();
        $sales = $this->find('all',array(
                                'conditions' => array('DATE(created)'=>$date,
                                            'Ticket.center_id' => $cid),
                                'fields' => array('profile_id','count(*) AS co',
                                    'SUM(units) AS un','SUM(prize) as pr',
                                    'Race.hipodrome_id'),
                                'group' => array('profile_id','Race.hipodrome_id'),
                                'recursive' => 0 ));
        
        foreach ( $sales as $sale ) {
            $fixed[$sale['Race']['hipodrome_id']][$sale['Ticket']['profile_id']] = 
                $sale[0];
        }
        
        return $fixed;
    }
    
    function getSalesByRaces($cid, $date,$racesIds)
    {
        $races = $this->find('all',array(
                    'conditions' => array('DATE(created)'=>$date,
                                    'Ticket.center_id' => $cid,'race_id'=> $racesIds),
                    'fields'    => array('profile_id','race_id','count(*) AS co',
                                'SUM(units) AS un','SUM(prize) as pr'),
                    'group'     => array('race_id','profile_id'),
                    'recursive' => 0 ) );
        
        $fixed = array();
        foreach ( $races as $race ) {
            $fixed[$race['Ticket']['race_id']][$race['Ticket']['profile_id']] = $race[0];
        }
        
        return $fixed;
    }
    
    function followByHipos($cid, $date )
    {
        $sales  = $this->getSalesByHtrack($cid, $date);
        $totals = array();
        //pr($sales);
        
        foreach ( $sales as $hid => $sale ) {
            $totals[$hid] = array('co' => 0 ,'un' => 0 ,'pr' => 0);
            foreach ( $sale as $sl ) {
                //pr($sl);
                $totals[$hid]['co'] += $sl['co'];
                $totals[$hid]['un'] += $sl['un'];
                $totals[$hid]['pr'] += $sl['pr'];
            }
        }
        
        return array( 'Listed' => $this->Race->getHorsetracksByDay($date, $cid),
                      'Sales'  => $sales,
                      'Totals' => $totals
            );
    }

	function hipos_by_date($cid,$date){
		$result = $this->query("SELECT count(*) AS co,SUM(units) as un, SUM(prize) as pr, 
		race_id, t.profile_id,r.hipodrome_id,h.name FROM tickets AS t
		INNER JOIN races AS r ON t.race_id = r.id
		INNER JOIN hipodromes AS h ON r.hipodrome_id = h.id
		WHERE date(created) = '$date' 
		AND t.enable = 1 AND t.center_id = $cid
		GROUP BY h.name,profile_id");
		
		$final = array();
		
		foreach($result as $r){
			$final[$r['h']['name']]['hip_id'] = $r['r']['hipodrome_id'];
			$final[$r['h']['name']][$r['t']['profile_id']] = $r[0];
		}
		
		return $final;
	}
	
    function racesByHipo ($cid,$date,$hip) 
    {
        $racesIds = $this->Race->find('list',array(
                                'conditions' => array('race_date' => $date, 'center_id'=> $cid,
                                        'hipodrome_id' => $hip),'fields' =>'number'
                            ));
        $sales  = $this->getSalesByRaces($cid, $date,array_keys($racesIds));
        //pr($sales);
        foreach ( $sales as $rid => $sale ) {
            $totals[$rid] = array('co'=>0,'un'=>0,'pr'=>0);
            foreach ( $sale as $sl ) {
                $totals[$rid]['co'] += $sl['co'];
                $totals[$rid]['un'] += $sl['un'];
                $totals[$rid]['pr'] += $sl['pr'];
            }
        }
        
        
        return array(
                'Listed' => $racesIds,
                'Totals' => $totals,
                'Sales'  => $sales );
    }
    
	function races_by_hipo($cid,$date,$hip){
		
		$result = $this->query("SELECT COUNT(*) AS co,sum(units) AS un,sum(prize) AS pr,
		race_id,t.profile_id,r.number FROM tickets AS t
		INNER JOIN races AS r ON t.race_id = r.id
		INNER JOIN hipodromes AS h ON r.hipodrome_id = h.id
		WHERE DATE(created) = '$date' AND t.enable = 1 AND h.id = $hip AND t.center_id = $cid
		GROUP BY r.number, profile_id");
		
		$final = array();
		
		foreach($result as $r){
			$final[$r['r']['number']]['race_id'] = $r['t']['race_id'];
			$final[$r['r']['number']][$r['t']['profile_id']] = $r[0];
		}
		
		return $final;
	}
	
    function horsesByRace( $race )
    {
        /*
        //pr($totals);
        //2.- en horses tickets buscar por caballo
        
        //pr($riders);
        
        //3.- buscar en horses tickets y ordenar un array caballo=>tks
        $horseTksIns = ClassRegistry::init('HorsesTicket');
        $hrsTks      = $horseTksIns->find('all',array(
                            'conditions' => array('horse_id' => array_keys($riders)),
                            'fields' => array('horse_id','ticket_id')));     
        $byHorses = array();
        foreach ( $hrsTks as $hrs ) {
            if ( isset ( $byHorses[$hrs['HorsesTicket']['horse_id']] ) ) {
                array_push($byHorses[$hrs['HorsesTicket']['horse_id']],$hrs['HorsesTicket']['ticket_id']);
            } else {
                $byHorses[$hrs['HorsesTicket']['horse_id']] = array($hrs['HorsesTicket']['ticket_id']);
            }
        }
        */
        $horseIns = ClassRegistry::init('Horse');
        $riders   = $horseIns->find('list',array(
                        'conditions' => array('race_id' => $race),
                        'fields' => 'number' ));
        $test = $this->query('SELECT COUNT(*) AS co, SUM(t.units) as un , SUM(t.prize) as pr, ' .
                'profile_id, ht.horse_id FROM tickets AS t INNER JOIN ' .
                'horses_tickets AS ht ON ht.ticket_id = t.id ' .
                'WHERE race_id = ' . $race . ' GROUP BY ht.horse_id, profile_id');
        
        $byHorses = array();
        $totals   = array();
        foreach ( $test as $t) {
            $byHorses[$t['ht']['horse_id']][$t['t']['profile_id']] = $t[0];
            if ( isset ($totals[$t['ht']['horse_id']])) {
                $totals[$t['ht']['horse_id']]['co'] += $t[0]['co'];
                $totals[$t['ht']['horse_id']]['un'] += $t[0]['un'];
                $totals[$t['ht']['horse_id']]['pr'] += $t[0]['pr'];
            } else {
                $totals[$t['ht']['horse_id']] = array('co'=>$t[0]['co'],
                    'un'=>$t[0]['un'],'pr'=>$t[0]['pr']);
            }
        }
        return array(
                'Listed' => $riders, 'byHorses' => $byHorses, 'Totals' => $totals);
        //pr($hrsTks);
        //pr($byHorses);
        /*         
        $fixed = array();
        foreach ( $races as $race ) {
            $fixed[$race['Ticket']['race_id']][$race['Ticket']['profile_id']] = $race[0];
        }
        
        return $fixed;*/
    }
    
	function set_pick_price($dats,$cid){
		//pr($dats); 
		$hipopicks = array();
		foreach($dats['Pick'] as $hipo => $vals){
			array_push($hipopicks,$hipo);
		}
		//pr($hipopicks);
		//aqui me traigo todos los tickets de picks de hoy de mi centro y de los hipodromos que me llegaron, asi!
		$tickets_pick = $this->find('all',array(
			'conditions' => array(
				'play_type_id'=>19,'Ticket.center_id' =>$cid,'race_id' => $hipopicks,
				'date(created)'=>$dats['PickResult']['date']
			),
			'fields'=>array('id','race_id','units'),'recursive' => -1
		));
		//pr($tickets_pick);
		$tickets_pick_units = array();
		foreach ($tickets_pick as $vals) {
			$tk = $vals['Ticket'];
			$tickets_pick_units[$tk['id']] = array(
				'hip' => $tk['race_id'],
				'uni' => $tk['units']
			);
		}
		//pr($tickets_pick_units); die();
		//me traigo los caballos detallados
		$horsestks = $this->HorsesTicket->find('all',array(
			'conditions' => array('ticket_id' => array_keys($tickets_pick_units)),
			'fields' => array('id','ticket_id','horses_tickets_status_id','box_number')
		));
		//pr($horsestks);
		//los acomodo arreglandolo por ticket, box_number, y caballo diciendo el estado
		$real_horses = array();
		foreach ($horsestks as $hors) {
			$h = $hors['HorsesTicket'];
			$real_horses[$h['ticket_id']][$h['box_number']][$h['id']] = $h['horses_tickets_status_id'];
		}
		//pr($real_horses);
		$toset = array();
		foreach ($real_horses as $ticket => $boxes) {
			foreach ($boxes as $picks) {
				$thepick = count($picks);
				$allwin = true;
				foreach ($picks as $horse) {
					if($horse != 2)
						$allwin = false;
				}
				
				if($allwin)
					$toset[$ticket] = $thepick;
			}
		}
		
		//pr($toset);die();
		//aqui empieza
		if(!empty($toset)){
			foreach ($toset as $tikid => $pick) {
				$how_picks = count($real_horses[$tikid]);	
				$units = $tickets_pick_units[$tikid]['uni'];
				$each_pick = $units / $how_picks;
				if(!empty($dats['Pick'][$tickets_pick_units[$tikid]['hip']][$pick])){ // solo si en datos esta seteado
					$this->updateAll(
						array('prize' => $each_pick." * ".$dats['Pick'][$tickets_pick_units[$tikid]['hip']][$pick]['prize']),
						array('Ticket.id' => $tikid)
					);	
				}	
			}
		}
		//die();
	}

	//set prizes on tickets
	function setPrizes($tickets)
	{
        foreach ( $tickets as $tk => $pr ) {
            $this->updateAll(
                array('prize'     => $pr),
                array('Ticket.id' => $tk)
			);
		}
	}
    
    function getCurrencyTotals ($centerId, $since, $until, $intl, $profileId = null, $raceId = null)
    {
        $hipod   = ClassRegistry::init('Hipodrome');
        
        $intls   = $hipod->getIntlIds();
	    
        $this->unbindModel(array('hasAndBelongsToMany' => array('Horse')),false);
        
        $cond['Ticket.enable']    = 1;
		$cond['Ticket.center_id'] = $centerId;
        $cond['date(created) BETWEEN ? AND ?'] = array($since,$until);
	    
        if ($profileId != null) {
            $cond['profile_id'] = $profileId;
        }
        
        //toINt
        if ($intl == true) {
            $cond['Race.hipodrome_id'] = $intls;
        } else {
            $cond['NOT'] = array('Race.hipodrome_id' => $intls);
        }
        
        if ($raceId != null) {
            $cond['race_id'] = $raceId;
        }
        
        //SALES
		$tickets  = $this->find('first',array(
                            'conditions' => $cond,
                            'fields'     => array('count(*) AS co','sum(units) AS un')
                        ));
        
        //PRIZES 
        $cond['Ticket.prize >'] = 0;
		$prizes   = $this->find('first',array(
                            'conditions' => $cond,
                            'fields'     => array('count(*) AS co','sum(prize) AS pr')
                        ));
        
        //PAYED
        $cond['payed_status_id'] = 2;
        unset($cond['date(created) BETWEEN ? AND ?']);
        $cond['date(payed_at) BETWEEN ? AND ?'] = array($since,$until);
        $payed    = $this->find('first',array(
                            'conditions' => $cond,
                            'fields'     => array('count(*) AS co','sum(prize) AS pr')
                        ));
        
        $subtotals = array( 'sa' => $tickets[0],
                            'pr' => $prizes[0],
                            'py' => $payed[0]
                        );
        
        //pr($subtotals);
        
        return $subtotals;
    }
    
    function getTotalSales($since,$until,$centerId,$profileId = null, $raceId = null)
    {
        $config  = ClassRegistry::init('Config');
        $unitNac = $config->get_unit_value($centerId);
		$unitInt = $config->get_unit_value($centerId,true);
        
        $totals['nat'] = $this->getCurrencyTotals ($centerId, $since, $until,false,$profileId, $raceId);
        $totals['int'] = $this->getCurrencyTotals ($centerId, $since, $until, true,$profileId, $raceId);
        
        $totals['nat']['uv']       = $unitNac;
        
        $totals['nat']['sa']['to'] = $totals['nat']['sa']['un'] * $unitNac;
        $totals['nat']['pr']['to'] = $totals['nat']['pr']['pr'] * $unitNac;
        $totals['nat']['py']['to'] = $totals['nat']['py']['pr'] * $unitNac;
        
        $totals['int']['uv']       = $unitInt;
        
        $totals['int']['sa']['to'] = $totals['int']['sa']['un'] * $unitInt;
        $totals['int']['pr']['to'] = $totals['int']['pr']['pr'] * $unitInt;
        $totals['int']['py']['to'] = $totals['int']['py']['pr'] * $unitInt;
        
        /*
        if ($profileId != null) {
            $totals['int']['ut']   = $totals['int']['sa']['to'] - $totals['int']['pr']['to'];
            $totals['nat']['ut']   = $totals['nat']['sa']['to'] - $totals['nat']['pr']['to'];
        } else {
            $totals['int']['ut']   = $totals['int']['sa']['to'] - $totals['int']['pr']['to'];
            $totals['nat']['ut']   = $totals['nat']['sa']['to'] - $totals['nat']['pr']['to'];
        }
        */
        
        $totals['int']['ut']   = $totals['int']['sa']['to'] - $totals['int']['py']['to'];
        $totals['nat']['ut']   = $totals['nat']['sa']['to'] - $totals['nat']['py']['to'];
        
        //UTILIDAD
        $totals['tot'] = array(
                            'tks' => ($totals['nat']['sa']['co'] + $totals['int']['sa']['co']),
                            'amo' => ($totals['nat']['sa']['to'] + $totals['int']['sa']['to']),
                            'pri' => ($totals['nat']['pr']['to'] + $totals['int']['pr']['to']),
                            'pay' => ($totals['nat']['py']['to'] + $totals['int']['py']['to']),
                            'fin' => ($totals['nat']['ut'] + $totals['int']['ut'])
                        );
        
        
        return $totals;
    }
    
    function getSalesProfiles($since,$until,$centerId,$raceId = null)
    {
        $config       = ClassRegistry::init('Config');
        $unitNac      = $config->get_unit_value($centerId);
		$unitInt      = $config->get_unit_value($centerId,true);
        $percents     = $config->get_pct_profile($centerId);
        
        $byProfileInt = $this->getSaleProfileCurrency($centerId, $since, $until, true, $raceId);      
        $byProfileNat = $this->getSaleProfileCurrency($centerId, $since, $until, false, $raceId);      
        
        //pr($byProfileInt);
        //pr($byProfileNat);
        
        $profiles = $this->Profile->getPlayers($centerId);
 		
        $profs = array();
        
        foreach ($profiles as $pid => $name ) {
            $profs[$pid]['name'] = $name;
            $profs[$pid]['pct']  = 0;
            
            if ( isset ($percents[$pid] ) ) {
                $profs[$pid]['pct'] = $percents[$pid];
            }
        }
        
        $salesOne = $this->putUtilObject($profs, $byProfileNat, $unitNac, false);
        
        $sales    = $this->putUtilObject($salesOne, $byProfileInt, $unitInt, true);
        
        foreach ($sales as $pid => $sale) {
           
            if ($sale['int']['sa']['co'] == 0 && $sale['nat']['sa']['co'] == 0) {
                unset($sales[$pid]);
            } else {
                $subTotal = $sale['int']['st'] + $sale['nat']['st'];
                $subPayed = $sale['int']['py']['to'] + $sale['nat']['py']['to'];
                
                if ($sale['pct'] > 0) {
                    //echo $sale['pct'];
                    //echo $subPayed;
                    $payedPct = $subPayed * ($sale['pct'] / 100);
                    $subTotal = $subTotal + $payedPct;
                }
                
                $sales[$pid]['total'] = $subTotal;     
            }
            
                       
        }
        
        //pr($sales);
        
        //$profiles = array('int' => $byProfileInt, 'nat' => $byProfileNat);
        
        return $sales;
    }
    
    
    function getSaleProfileCurrency ($centerId, $since, $until, $intl = false, $raceId = null)
    {
        $hipod    = ClassRegistry::init('Hipodrome');
        $intls    = $hipod->getIntlIds();
		
        $this->unbindModel(array('hasAndBelongsToMany' => array('Horse')),false);
        
        $cond['Ticket.enable']    = 1;
		$cond['Ticket.center_id'] = $centerId;
		$cond['date(created) BETWEEN ? AND ?'] = array($since,$until);
		
        if ($intl == true) {
            $cond['Race.hipodrome_id'] = $intls;
        } else {
            $cond['NOT'] = array('Race.hipodrome_id' => $intls);
        }
        
        if ( $raceId != null ) {
            $cond['race_id'] = $raceId;
        } 
        
        $profiles = array();
        
        //SALES
        $byProfile = $this->find('all',array(
                            'conditions' => $cond,
                            'fields'     => array(
                                            'profile_id','count(*) AS co',
                                            'sum(units) AS un'),
                            'group'      => 'profile_id'
                        ));
        //PRIZES 
        $cond['Ticket.prize >']    = 0;
        
        $winners = $this->find('all',array(
                            'conditions' => $cond,
                            'fields'     => array(
                                            'profile_id','count(*) AS co',
                                            'sum(prize) AS pr'),
                            'group'      => 'profile_id'
                        ));
        
        //PAYED
        $cond['payed_status_id'] = 2;
        
        $payed = $this->find('all',array(
                            'conditions' => $cond,
                            'fields'     => array(
                                            'profile_id','count(*) AS co',
                                            'sum(prize) AS pr'),
                            'group'      => 'profile_id'
                        ));
        
        foreach ($winners as $win) {
            $profiles[$win['Ticket']['profile_id']]['pr'] = $win[0];
        }
        
        foreach ($payed as $pay) {
            $profiles[$pay['Ticket']['profile_id']]['py'] = $pay[0];
        }
        
        foreach ($byProfile as $pro) {
            $profiles[$pro['Ticket']['profile_id']]['sa'] = $pro[0];
        }
        
        return $profiles;
    }

    function putUtilObject ($main, $sales, $unitVal, $isIntl = false)
    {
        
        if ($isIntl == true) {
            $type = "int";
        } else {
            $type = "nat";
        }
        
        foreach ($main as $pid => $values) {
            
            if ( isset ($sales[$pid]['sa'])) {
                $main[$pid][$type]             = $sales[$pid];
                $main[$pid][$type]['sa']['to'] = $main[$pid][$type]['sa']['un'] * $unitVal;
                
            } else {
                $main[$pid][$type] = array(
                                        'sa' => array('co'=>0,'un'=>0,'to'=>0),
                                        'pr' => array('co'=>0,'pr'=>0,'to'=>0)
                                      );
            }
            
            if (isset($sales[$pid]['pr'])) {
                $main[$pid][$type]['pr']['to'] = $main[$pid][$type]['pr']['pr'] * $unitVal;
            } else {
                $main[$pid][$type]['pr'] = array('co'=>0,'pr'=>0,'to' => 0);
            }
            
            
            if (isset($sales[$pid]['py'])) {
                $main[$pid][$type]['py']['to'] = $main[$pid][$type]['py']['pr'] * $unitVal;
            } else {
                $main[$pid][$type]['py'] = array('co'=>0, 'pr'=>0,'to' => 0);
            }
            
            
            $main[$pid][$type]['st'] = ($main[$pid][$type]['sa']['to'] -
                                        $main[$pid][$type]['py']['to']);
            
        }
        
        return $main;
    }
    
    function setOnlinePrizes($raceId,$centerId)
    {
        $account    = ClassRegistry::init("Account");
        $cnfModel   = ClassRegistry::init("Config");
        $isIntl     = $this->Race->isIntl($raceId);
        $unitVal    = $cnfModel->get_unit_value($centerId,$isIntl);
        $pidsOnline = $this->Profile->getMyOnlines($centerId);
        
        $noPayed    = $this->find('all', array(
                        'conditions' => array(
                            'race_id'         => $raceId,
                            'profile_id'      => array_keys($pidsOnline),
                            'prize >'         => 0,
                            'payed_status_id' => 1,
                            'enable'          => 1
                        ),
                        'fields'     => array('id','number','prize','profile_id'),
                        'recursive'  => -1 ) );
        
        //payment process
        foreach ($noPayed as $tk) {
            $ticketAmount = $tk['Ticket']['prize'] * $unitVal;
            $account->addMovem( array (
                            'profile_id' => $tk['Ticket']['profile_id'],
                            'title'      => 'PREMIO',
							'amount'     => $ticketAmount,
                            'metainf'    => "Tkt. ".$tk['Ticket']['number']. 
                                            " Ganador. ID:".$tk['Ticket']['id']));
            unset($account->id);
            $this->updateAll(
                //array('payed_status_id' => 2),
                array(
                    'payed_status_id' => 2 ,
                    'payed_at'        => "'". 
                                         date('Y-m-d H:i:s') .
                                         "'"
                    ) 
               	,
                array('Ticket.id'       => $tk['Ticket']['id'] ) );
        }
    }
    
    function getSmsInfo($message)
    {
        $smsParts = explode(' ',$message);
        $numParts = count($smsParts);
        $tktTypes = array('W' => 1, 'P' => 2, 'S' => 3 );
        $smsInfo  = array('Ticket' => array(), 'Horse' => array());
        
        if ( $numParts != 4 ) {
            $smsInfo['Error'] = 'Error en formato SMS.';
        } else {
            $htrk  = substr($smsParts[0],-3);
            $rnum  = str_replace($htrk,'',$smsParts[0]);
            $race  = $this->Race->getByNickNumber($htrk,$rnum);
            $horse = 0;
            $ptype = 0;
            
            if ( isset ( $tktTypes[$smsParts[2]] ))
                $ptype = $tktTypes[$smsParts[2]];
            else 
                $smsInfo['Error'] = 'Tipo Apuesta NO existe.';
            //echo $htrk . '-' . $rnum; pr($race);
            
            if ( empty ( $race ) ) {
                $smsInfo['Error'] = 'Hip.-Carr. NO existe.';
            } else {
                //gethorse
                $horse = $this->Horse->find('first',array(
                                'conditions' => array(
                                    'race_id' => $race['Race']['id'],
                                    'Horse.number'  => $smsParts[1]
                                ),
                                'fields' => 'id', 'recursive' => -1 ));
                //pr($horse);
                if ( empty ( $horse ) ) {
                    $smsInfo['Error'] = 'Caballo NO existe.';
                }
            }
            
            $smsInfo['Ticket'] = array(
                                'race_id'      => $race['Race']['id'],
                                'units'        => $smsParts[3],
                                'play_type_id' => $ptype,
                                'via'          => 'SMS');
            
            $smsInfo['Horse'] = array(
                                0 => array(
                                        'horse_id'     => $horse['Horse']['id'],
                                        'play_type_id' => $ptype,
                                        'units'        => $smsParts[3]));
            
        }
        
        return $smsInfo;
    }
}
?>