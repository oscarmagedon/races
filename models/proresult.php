<?php

App::Import('Model','Apidata');
App::Import('Model','Race');
App::Import('Model','Horse');
App::Import('Model','Result');
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
		/*
		SELECT * FROM `races` WHERE hipodrome_id = 39 and race_date = '2019-03-16' ;
		SELECT * FROM `results` WHERE race_id = 16582;
		*/
		
		$raceMod    = new Race();

		$horseMod   = new Horse();

		$resultMod  = new Result();

		$operMod    = new Operation();

		$proservurl = $this->createProserviceResultsUrl($date, $number, $nick, 'USA');

		$jsonResp   = file_get_contents($proservurl);

		$resultLog  = ['Data' => $this->normalResults($jsonResp)];

		$resultLog['HorseIds'] = $horseMod->find('list',[
									'conditions' => ['race_id' => $raceId],
									'fields'     => ['id','number'],
									'recursive'  => -1 
								]);

		$resultLog['Results'] = [];
		
		foreach ($resultLog['Data'] as $result) {
			$result['horse_id'] = $this->_findHorseId($resultLog['HorseIds'], $result['number']);
			$result['race_id']  = $raceId;

			unset($result['number']);
			
			//result to save
			$resultLog['Results'][]   = $result;
		}

		//save results
		$resultMod->saveAll($resultLog['Results']);

		// Update race
		$raceMod->updateAll(['ended'=>1,'enable'=>0],['Race.id'=>$raceId]);


		//retiures!!!



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


        
		//retiures!!!






		// operation insert!!
		$operationMeta = "Proservice ". $nick .' '. $number;
		$operMod->ins_op(3, 1, 'Resultados', $raceId, $operationMeta);

		//return log
		return $resultLog;
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

		$resultsInfo = [];

		foreach ($resultServ[0]['runners'] as $key => $result) {
			$resultsInfo[] = [
				'position' => ($key + 1),
				//'race_id'  => 12345,
				//'horse_id' => 78999,
				'number'   => $result['programNumber'],
				'win'      => $result['winPayoff'],
				'place'    => $result['placePayoff'],
				'show'     => $result['showPayoff']
			];
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
