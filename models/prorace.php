<?php

App::Import('Model','Apidata');
App::Import('Model','Race');
App::Import('Model','Horse');
App::Import('Model','Operation');
App::Import('Helper','Time');

class Prorace extends Apidata {

	var $name = 'Prorace';

	private function _getWinterCnf()
	{
		$configInst = ClassRegistry::init('Config');
        $cnfsRoot   = $configInst->find('first',array(
                        'conditions' => array('config_type_id' => 6)
                        ));

        return $cnfsRoot['Config']['actual'];
	}

	private function _getLocalTimeSrv($time, $gmtSrv, $winter)
    {
    	$thisGmt = ($winter == 1) ? 3 : 4;

        $gmtRest = $gmtSrv + $thisGmt;
        
        $minutes = $gmtRest * 60 * -1;
            
        $rTime   = new DateTime($time);
        
        $newTime = $rTime->modify("$minutes minutes");
        
        return date_format($newTime,'H:i:s');
    }

	public function saveRaceSys($raceInfo, $htId, $htGmt, $isWinter)
	{
		/*
		SELECT * from races WHERE center_id = 5 AND race_date = '2019-02-24'
		AND hipodrome_id IN(SELECT id FROM `hipodromes` WHERE nick = 'GP')
		*/
		$timeClass  = ClassRegistry::init('TimeHelper');
		$raceClass  = new Race();
		$horseClass = new Horse();
		//$opertClass = new Operation();

		$infoLog = ['Data'=>$raceInfo];
		// testing
		$centerId = 1;
		
		$infoLog['RaceSys']['Race'] = $raceInfo['Race'];
		//time convert
		$raceTime  = $timeClass->format('H:i:s', $raceInfo['Race']['race_time']);
		//local convert
		//$localTime = $this->_getLocalTimeSrv($raceTime, $htGmt, $isWinter);
		//complete data
		$infoLog['RaceSys']['Race']['center_id']    = $centerId;
		$infoLog['RaceSys']['Race']['hipodrome_id'] = $htId;
		$infoLog['RaceSys']['Race']['race_time']    = $raceTime; 
		$infoLog['RaceSys']['Race']['race_time']    = $raceTime; 
		$infoLog['RaceSys']['Race']['local_time']   = $raceTime; 
		//$infoLog['RaceSys']['Race']['local_time']   = $localTime; 

		//horses to save
		$infoLog['RaceSys']['Horse'] = $raceInfo['Horse'];

		// PATCH ===>>
		//$infoLog['RaceSys']['Race']['enable'] = 0;
		//$infoLog['RaceSys']['Race']['ended']  = 1;
		//$infoLog['RaceSys']['Race']['number'] = (20 + $infoLog['RaceSys']['Race']['number']); 
		//$infoLog['RaceSys']['Race']['race_date'] = date('Y-m-d', strtotime("+1 months", strtotime($infoLog['RaceSys']['Race']['race_date'])));
		//  ===>> PATCH


		// SAVE RACE
		$raceClass->save($infoLog['RaceSys']['Race']);
		$infoLog['RaceSys']['Race']['id'] = $raceClass->id;
		//complete horses
		foreach ($infoLog['RaceSys']['Horse'] as $hk => $horse) {	
			$infoLog['RaceSys']['Horse'][$hk]['race_id'] = $raceClass->id;
		}
		//SAVE HORSES
		$horseClass->saveAll($infoLog['RaceSys']['Horse']);

		//ADD THe operation detail 
		//	Saying how many horses and how many centers were copied
		//add it to the infolog['Operation']
		//check operations
		/*
		$opertClass->ins_op(3,1,'Race',$raceClass->id,
			"Proserv: nro " . $infoLog['RaceSys']['Race']['number'].
			' htid: ' . $htId .
			' horses: ' . count($infoLog['RaceSys']['Horse']) );
		*/
		return $infoLog;
	}

	public function exploreTrack($trackId, $country, $dayEve, $topRace = 20)
	{
		$hipodrome  = ClassRegistry::init('Hipodrome');
		$opertClass = new Operation();
		$htrack     = $hipodrome->find('first',[
						'conditions' => [
							'nick' => $trackId
						],
						'fields' => ['id','htgmt']
					 ]);
		$hipodId    = $htrack['Hipodrome']['id'];
		$hipodGmt   = $htrack['Hipodrome']['htgmt'];
		$isWinter   = $this->_getWinterCnf();

		$infoArray  = [];

		//operation concat
		$operationMeta = '';

		for ( $raceNum = 1; $raceNum <= $topRace; $raceNum ++ ) { 
            //info race
            $raceInfo = $this->getByRace($raceNum, $trackId, $country, $dayEve);
            //pr($raceInfo);
            $raceStat =  ['Race' => $raceNum];

            if(!isset($raceInfo['Race'])) {
                $raceStat['error'] = 'Error: '. $raceInfo['error'];
            } else {

                //$raceStat = $raceInfo;
                if(!empty ($htrack)) {
                	$raceStat = $this->saveRaceSys($raceInfo, $hipodId, $hipodGmt, $isWinter);
                	
                	//Oeration Info
                	$operationMeta .= $raceStat['RaceSys']['Race']['id']. ": ";
                	$operationMeta .= $raceStat['RaceSys']['Race']['number']. " (";
                	$operationMeta .= count($raceStat['RaceSys']['Horse']). ")<br>";
                } else {
                	$raceStat = ['error'=>'No Hipodrome'];
                	$raceInfo['error'] = 'No htrack';
                }
                //
            }

            array_push($infoArray,$raceStat);            

            if(isset($raceInfo['error'])) {
                break;    
            }
        }

        //Operation by Horsetrack
		$opertClass->ins_op(3, 1, 'Race', $hipodId, "Proserv -".$trackId."::<br>".$operationMeta);

        return $infoArray;
	}

	public function getByRace($raceNum, $trackId, $country, $dayEve)
	{	
		$byRaceUrl = $this->createProserviceRaceUrl($raceNum, $trackId, $country, $dayEve);

		$jsonResp  = file_get_contents($byRaceUrl);
		
		return $this->normalRace($jsonResp);
	}

	public function normalRace($jsonRace)
	{
		//echo $jsonRace;
		//die();
		$fullInfo = json_decode($jsonRace);
		//return $fullInfo->horseDTOs;
		//
		if ($fullInfo->success == false) {
			$fullInfo = ['error'=>'No Race!'];
		} else {
			$fullInfo = [
				'Race'  => $this->getRaceDetails($fullInfo),
				'Horse' => $this->getHorsesDetails($fullInfo->horseDTOs)
			];
				
		}

		return $fullInfo;
	}

	public function getRaceDetails($raceObj)
	{	
		$monthKey   = $raceObj->raceKey->raceDate->month;
		$monthDb    = $monthKey + 1;
		$raceNumber = $raceObj->raceKey->raceNumber; 
				
		$raceDate   = $raceObj->raceKey->raceDate->year .'-'. 
					  $monthDb .'-'. 
					  $raceObj->raceKey->raceDate->day;
		
		$raceDate   = $this::getDbDate($raceDate);

		$raceSys = [
			'number'       => $raceNumber,
			'race_date'    => $raceDate,
			'race_time'    => $raceObj->postTimeDisplay
		];

		return $raceSys;
	}

	/**
		After enlisting the real fields
		
		morningLineOdds
		programNumber
		programNumberStripped
		horseName
		jockey => alias


		We can test inserting them in the center=5
	*/
	public function getHorsesDetails($horseObj)
	{
		$horsesInfo = [];
		
		foreach ($horseObj as $horse) {
			
			$horsesInfo[] = [
								'number'  => $horse->programNumber,
								//'numbstp' => $horse->programNumberStripped,
								'name'    => $horse->horseName,
								'jockey'  => $horse->jockey->alias,
								'mlodds'  => $horse->morningLineOdds
							];
		}

		return $horsesInfo;
	}

	public function deleteByNick($nick, $raceDate = null)
	{
		$raceDate   = ($raceDate==null)?date('Y-m-d'):$raceDate;
		$raceClass  = new Race();
		$horseClass = new Horse();

		$hipodrome = ClassRegistry::init('Hipodrome');
		$htrack    = $hipodrome->find('first',[
						'conditions' => [
							'nick' => $nick
						],'fields' => ['id','name','htgmt']
					 ]);

		$hipodId   = $htrack['Hipodrome']['id'];
		$hipName   = $htrack['Hipodrome']['name'];

		//
		$racesIds = $raceClass->find('list',[
			'fields'     => 'id',
			'conditions' => [
				'hipodrome_id' => $hipodId,
				'center_id'    => 1,
				'race_date'    => $raceDate
			]
		]);

		//pr($racesIds);
		
		$raceClass->delete($racesIds);

		$horseClass->deleteAll(['race_id'=>$racesIds]);
		

		//echo 'htrack:' .$hipodId;

		//echo '<br />'. count($racesIds) .' races '. $hipName .' deleted!';

		//pr($delTrack);

		//die(':::');

		return count($racesIds) .' races '. $hipName .' deleted!';
	}




}