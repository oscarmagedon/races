<?php

App::Import('Model','Apidata');
App::Import('Model','Race');
App::Import('Model','Horse');
App::Import('Model','Result');
App::Import('Model','HorsesTicket');
//App::Import('Model','Ticket');
App::Import('Model','Operation');
App::Import('Helper','Time');

class Proresult extends Apidata {

	var $name = 'Proresult';

	//determina cantidad necesaria segun tipo para ser ganador		 
    var $specialTypes = [
    	2 => [10, 11],
    	3 => [12, 13, 14],
    	4 => [15, 16, 17, 18]
	];

	public function getNextRaces($date, $lim)
	{
		$race = new Race();
        
        // conditions to nexOnes
        $cndNxt = ['race_date'    => $date,
                   'center_id'    => 1];

        $timeNow = date('H:i:s', strtotime('-1 hours'));
        $cndNxt['local_time >='] = $timeNow;
        
        return $race->find('all',array(
                    'conditions' => $cndNxt,
                    'order' => array('local_time' => 'ASC'),
                    'limit' => $lim
                ));

	}

	public function getHtracks($date,$centerId)
	{
		$race = new Race();

		return $race->getHorsetracksByDay($date, $centerId, 0, true, true);
	}

	public function resetRace($raceId)
    {
    	$raceMod   = new Race();
		$horseMod  = new Horse();
		$resultMod = new Result();
		$operMod   = new Operation();

		//delete results
		$resultMod->deleteAll(['race_id'=>$raceId]);
		
		//put race back no-ended
		$raceMod->updateAll(['ended'=>0],['Race.id'=>$raceId]);

		//recalculate horses_tickets

		//Operation 
		$operationMeta = "RESET";
		$operMod->ins_op(4, 1, 'Resultados', $raceId, $operationMeta);
    }

	public function saveResults($raceId, $date, $nick, $number)
	{
		$raceMod    = new Race();
		$horseMod   = new Horse();
		$resultMod  = new Result();
		$operMod    = new Operation();
		$hrsTksMod  = new HorsesTicket();
		$ticketMod  = new Ticket();
		$centerMod  = new Center();

		// operation START MONITORING insert
		//$operationMeta = "Proservice Check.". $nick .' '. $number;
		//$operMod->ins_op(3, 1, 'Result. Check', $raceId, $operationMeta);

		//GET results from URL and normalize		
		$proservurl = $this->createProserviceResultsUrl($date, $number, $nick, 'USA');
		$jsonResp   = file_get_contents($proservurl);
		$resultLog  = ['Data' => $this->_normalizeResults($jsonResp)];
		
		if (isset($resultLog['Data']['Error'])){
			return $resultLog['Data']['Error'];
		}

		$resultLog['Results'] = $this->_getHorsesInfo($raceId,$resultLog['Data']['Results']);

		//retired
		$retires = $horseMod->find('list', [
						'conditions' => [
							'race_id' => $raceId,
							'enable'  => 0
						]
					]);

		$resultLog['Saved']   = $hrsTksMod->saveWinnersPrizes(
									$raceId, 
									$resultLog['Results']['Results'],
									0,
									array_keys($retires));
		
		//pr($resultLog);
		//die();

		//save results
		$resultMod->saveAll($resultLog['Results']['Results']);

		//after saving results update RACE and special-prizes:
		$raceMod->setRaceEnded($raceId,$resultLog['Data']['Specials']);

		//SETS RETIRED AND RETURNS UNITS
		//...
		
		//PUT THE REST OF THE HORSES AS LOSERS
		$hrsTksMod->updateAll(
			['horses_tickets_status_id' => 3, 'prize' => 0],
			['horse_id' => array_keys($resultLog['Results']['Losers'])]
		);

		//set special prizes
        $hrsTksMod->saveSpecialPrizes(
        	$resultLog['Results']['Results'], 
        	$resultLog['Data']['Specials']);
        //die();

        //set OnlinePrizes
        $centers = $centerMod->find('list'
         /*,[
        	'conditions' => [
        		'Center.enable' => 1
        	]
        ]*/
    	);
        //pr($centers);
        // foreach center
        foreach ($centers as $ckey => $cname) {
        	$ticketMod->setOnlinePrizes($raceId, $ckey);
        	# code...
        }
        // ... set OnlinePrizes

		// operation insert!!
		$operationMeta = "Proservice End." . $resultLog['Saved'] . ' tks. '. $nick .' '. $number;
		$operMod->ins_op(3, 1, 'Resultados', $raceId, $operationMeta);

		//die();
		
		//return log
		return $resultLog;
	}

    /*
	Returns the HorseId from the list given the number
    */
	private function _findHorseId($horses,$number)
	{
		foreach ($horses as $key => $value) {
			
			//comparisson important!!
			if ( (int)$value == (int)$number ) {
				return $key;
			}
		}	
	}	

	/* Normalizes results from proservice */
	private function _getHorsesInfo($raceId,$resultsData)
	{
		$horseMod  = new Horse();
		$horsesIds = $horseMod->find('list',[
									'conditions' => ['race_id' => $raceId],
									'fields'     => ['id','number'],
									'recursive'  => -1 ]);
		$results   = [];
		
		foreach ($resultsData as $result) {
			$result['horse_id'] = $this->_findHorseId($horsesIds, $result['number']);
			$result['race_id']  = $raceId;

			unset($result['number']);
			//result to save
			$results[]   = $result;
			//unset to create losers
			unset($horsesIds[$result['horse_id']]);
		}	

		return ['Results' => $results , 'Losers' => $horsesIds];

	}

    //Normalizes 
	private function _normalizeResults($jsonResp)
	{
		$resultServ = json_decode($jsonResp, TRUE);

		$resultsInfo = [
			'Results'  => [],
			'Specials' => [
				'exacta'     => 0,
				'trifecta'   => 0,
				'superfecta' => 0
			]
		];

		if (!isset($resultServ[0])) {
			$resultsInfo['Error'] = 'No results yet';
			return $resultsInfo;
		}

		// W-P-S
		foreach ($resultServ[0]['runners'] as $key => $result) {
			$resultsInfo['Results'][] = [
				'position' => ($key + 1),
				//'race_id'  => 12345,
				//'horse_id' => 78999,
				'number'   => $result['programNumber'],
				'win'      => $result['winPayoff'],
				'place'    => $result['placePayoff'],
				'show'     => $result['showPayoff']
			];
		}

		//Specials
		foreach ($resultServ[0]['payoffs'] as $special) {
			
			if ($special['wagerName'] == 'EXACTA' ||
				$special['wagerType'] == 'E') {
				$resultsInfo['Specials']['exacta'] = (
					$special['payoffAmount'] / $special['baseAmount']
				);
			}

			if ($special['wagerName'] == 'TRIFECTA' || 
				$special['wagerType'] == 'T') {
				$resultsInfo['Specials']['trifecta'] = (
					$special['payoffAmount'] / $special['baseAmount']
				);
			}
			
			if ($special['wagerName'] == 'SUPERFECTA' ||
				$special['wagerType'] == 'S') {
				$resultsInfo['Specials']['superfecta'] = (
					$special['payoffAmount'] / $special['baseAmount']
				);
			}
		}

		return $resultsInfo;
	}
}
