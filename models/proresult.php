<?php

App::Import('Model','Apidata');
App::Import('Model','Race');
App::Import('Model','Horse');
App::Import('Model','Result');
App::Import('Model','HorsesTicket');
App::Import('Model','Ticket');
App::Import('Model','Operation');
App::Import('Helper','Time');

class Proresult extends Apidata {

	var $name = 'Proresult';

	public function getNextRaces($date, $lim)
	{
		//$date = date('Y-m-d');
        $race = new Race();
        //$lim  = 5;
         
        // conditions to nexOnes
        $cndNxt = [
                    'race_date'    => $date,
                    'center_id'    => 1,
                    //'Race.enable'  => 1,
                    //'Race.ended'   => 0
                ];

        //patch to FROM-NOW-ON -1 hour
        //$timeNow = date('H:i:s');
        $timeNow = date('H:i:s', strtotime('-1 hours'));
        //$timeNow = '20:16:00';
        $cndNxt['local_time >='] = $timeNow;
        //patch to FROM-NOW-ON 

        return $race->find('all',array(
                    'conditions' => $cndNxt,
                    /*
                    'fields' => array(
                        'Race.id','number','Hipodrome.name','Hipodrome.national',
                        'local_time','post_time'
                    ),
                    */
                    'order' => array(
                        'local_time' => 'ASC'
                    ),
                    'limit' => $lim
                ));

	}

	public function getHtracks($date,$centerId)
	{
		$race = new Race();

		return $race->getHorsetracksByDay(
                            $date, 
                            $centerId,
                            0, //nationals?
                            true,
                            true
                            );
	}


	public function saveResults($raceId, $date, $nick, $number)
	{
		$raceMod    = new Race();

		$horseMod   = new Horse();

		$resultMod  = new Result();

		$operMod    = new Operation();


		// operation START MONITORING insert
		$operationMeta = "Proservice Start.". $nick .' '. $number;
		$operMod->ins_op(3, 1, 'Resultados', $raceId, $operationMeta);

		
		$proservurl = $this->createProserviceResultsUrl($date, $number, $nick, 'USA');

		$jsonResp   = file_get_contents($proservurl);

		$resultLog  = ['Data' => $this->normalResults($jsonResp)];
		
		if (isset($resultLog['Data']['Error'])){
			die($resultLog['Data']['Error']);
		}

		$resultLog['HorseIds'] = $horseMod->find('list',[
									'conditions' => ['race_id' => $raceId],
									'fields'     => ['id','number'],
									'recursive'  => -1 
								]);

		$resultLog['Results']  = [];
		
		foreach ($resultLog['Data']['Results'] as $result) {
			$result['horse_id'] = $this->_findHorseId($resultLog['HorseIds'], $result['number']);
			$result['race_id']  = $raceId;

			unset($result['number']);
			
			//result to save
			$resultLog['Results'][]   = $result;
		}


		//save results
		$resultMod->saveAll($resultLog['Results']);

		//after saving results update RACE and special-prizes:
		$raceMod->setRaceEnded($raceId,$resultLog['Data']['Specials']);

		//SETS PRIZES
		$this->_setPrizesWinners($resultLog['Results']);

		//SETS RETIRED AND RETURNS UNITS
		//...

		//PUT THE REST OF THE HORSES AS LOSERS
		//...


		$horseIds = [];
		foreach ($resultLog['Results'] as $winner) {
			array_push($horseIds, $winner['horse_id']);
		}

		//GET TOTAL BY TICKETS
		$prizesByTicket = $this->_getPrizeByTickets($horseIds);

		$tksCount = count($prizesByTicket);

		//reset prizes method 
		//...

		// prize setter method
		$this->_setTicketsPrizes($prizesByTicket);

		//pr($prizesByTicket);
		//pr($resultLog);
		//die();		

		// operation insert!!
		$operationMeta = "Proservice End." . $tksCount . ' tks. '. $nick .' '. $number;
		$operMod->ins_op(3, 1, 'Resultados', $raceId, $operationMeta);

		//return log
		return $resultLog;
	}

	//sets horses tickets statuses and prizes
	private function _setPrizesWinners($winners)
	{
		$hrsTicketsModel = new HorsesTicket();

		//  ---- P  R  I  M  E  R    C  A  B  A  L  L  O   -------
		$hrsTicketsModel->updateAll(
			[
				'prize' => '( `units` *' . $winners[0]['win'] . ")",
				'horses_tickets_status_id' => 2
			],
			[
				'horse_id'     => $winners[0]['horse_id'], 
				'play_type_id' => 1	
			]
		);	

		$hrsTicketsModel->updateAll(
			[
				'prize' => '( `units` *' . $winners[0]['place'] . ")",
				'horses_tickets_status_id' => 2
			],
			[
				'horse_id'     => $winners[0]['horse_id'], 
				'play_type_id' => 2	
			]
		);

		$hrsTicketsModel->updateAll(
			[
				'prize' => '( `units` *' . $winners[0]['show'] . ")",
				'horses_tickets_status_id' => 2
			],
			[
				'horse_id'     => $winners[0]['horse_id'], 
				'play_type_id' => 3	
			]
		);	

	    //  ---- S  E  G  U  N  D  O    C  A  B  A  L  L  O   -------

		$hrsTicketsModel->updateAll(
			[
				'prize' => '( `units` *' . $winners[1]['place'] . ")",
				'horses_tickets_status_id' => 2
			],
			[
				'horse_id'     => $winners[1]['horse_id'], 
				'play_type_id' => 2	
			]
		);

		$hrsTicketsModel->updateAll(
			[
				'prize' => '( `units` *' . $winners[1]['show'] . ")",
				'horses_tickets_status_id' => 2
			],
			[
				'horse_id'     => $winners[1]['horse_id'], 
				'play_type_id' => 3	
			]
		);

        //  ---- T  E  R  C  E  R    C  A  B  A  L  L  O   -------

		$hrsTicketsModel->updateAll(
			[
				'prize' => '( `units` *' . $winners[2]['show'] . ")",
				'horses_tickets_status_id' => 2
			],
			[
				'horse_id'     => $winners[2]['horse_id'], 
				'play_type_id' => 3	
			]
		);
	}

	//get listed prizes by 
    private function _getPrizeByTickets($horseIds)
    {
        $hrsTicketsModel = new HorsesTicket();

        $prizes = $hrsTicketsModel->find(
            'all',
            [
                'conditions' => [
                	'prize >'  => 0, 
                	'horse_id' => $horseIds
                ],
                'fields'     => ['ticket_id','prize'], 
                'recursive'  => -1
        	]
        );


        $byTicket = array();	
        foreach($prizes as $pr){
            if(!empty($byTicket[$pr['HorsesTicket']['ticket_id']]))
                $byTicket[$pr['HorsesTicket']['ticket_id']] += $pr['HorsesTicket']['prize'];
            else
                $byTicket[$pr['HorsesTicket']['ticket_id']] = $pr['HorsesTicket']['prize'];
        }

        return $byTicket;
    }

    // CALCULATE TICKETS
    private function _setTicketsPrizes($prizesWinners)
    {
    	$ticketMod = new Ticket();

    	foreach ($prizesWinners as $ticket => $prize ) {
            $ticketMod->updateAll(
                ['prize'     => $prize],
                ['Ticket.id' => $ticket]
			);
		}
    }
		    



	private function _findHorseId($horses,$number)
	{
		foreach ($horses as $key => $value) {
			
			//comparisson important!!
			if ( (int)$value == (int)$number ) {
				return $key;
			}
		}	
	}
	
	public function normalResults($jsonResp)
	{
		$resultServ = json_decode($jsonResp, TRUE);

		$resultsInfo = [
			'Results'  => [],
			'Specials' => []
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
				$resultsInfo['Specials']['exacta'] = $special['payoffAmount'];
			}

			if ($special['wagerName'] == 'TRIFECTA' || 
				$special['wagerType'] == 'T') {
				$resultsInfo['Specials']['trifecta'] = $special['payoffAmount'];
			}
			
			if ($special['wagerName'] == 'SUPERFECTA' ||
				$special['wagerType'] == 'S') {
				$resultsInfo['Specials']['superfecta'] = $special['payoffAmount'];
			}
		}


		return $resultsInfo;

		/**
			[programNumber] =>  2 
	        [programNumberStripped] => 2
	        [horseName] => Dance Doctor                       
	        [jockeyFirstName] => Ernesto        
	        [jockeyLastName] => Valdez-Jiminez                                                                  
	        [trainerFirstName] => Jody           
	        [trainerLastName] => Pruitt                                                                          
	        [ownerFirstName] => Jimmy          
	        [ownerLastName] => Sinclair                                                                        
	        [weightCarried] => 120
	        [winPayoff] => 10
	        [placePayoff] => 4.8
	        [showPayoff] => 3.8
	        [breederName] => Dream Walkin' Farms Inc
	        [sireName] => Doctor Chit
	        [beyerNumber] => 
	        [jockeyFirstNameInitial] => E

		*/
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
		//with a very nice superquery
		//maybe a inner normalizer in this model


		//Operation 
		$operationMeta = "RESET";
		$operMod->ins_op(4, 1, 'Resultados', $raceId, $operationMeta);
    }

}
