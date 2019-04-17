<?php

App::Import('Model','Apidata');
App::Import('Model','Race');
App::Import('Model','Horse');
//App::Import('Model','Result');
App::Import('Model','HorsesTicket');
//App::Import('Model','Ticket');
App::Import('Model','Operation');
//App::Import('Helper','Time');

class Bovada extends Apidata {
	
	var $name = 'Bovada';


	public function getAllInfo($bovadaNick)
	{
		$urlCheck   = $this->createBovadaUrl($bovadaNick);        
        $dataString = file_get_contents($urlCheck);
        $dataBovada = json_decode($dataString);
        $bovadaLog  = [];

        foreach ($dataBovada[0]->events as $race) {
            
            $raceLog = [
                'number' => $race->details->raceNumber,
                'status' => $race->status,
                'Horses' => []
            ];
           
            foreach ($race->displayGroups[0]->markets[0]->outcomes as $horse) {
                $horseLog = [
                    'number' => $horse->details->saddleNumber,
                    'ccode'  => $horse->details->coupledCode,
                    'name'   => $horse->description,
                    'status' => $horse->status
                ];

                if ($horse->details->scratched) {
                    //SCRATCH!! ';
                    $horseLog['scratched'] = 'true';
                }

                $raceLog['Horses'][] = $horseLog;
            }
            
            $bovadaLog[] = $raceLog;
        }

        return $bovadaLog;
	}

	public function getByRace($bovadaNick, $raceNumber, $raceId)
	{
		$bovadaInfo = $this->getAllInfo($bovadaNick);
		$raceFound  = false;
		$retiresBov = [];

		foreach ($bovadaInfo as $raceBovada) {
			if ( $raceBovada['number'] == $raceNumber) {
				$raceFound = true;
				
				foreach ($raceBovada['Horses'] as $horseb) {
					if (isset($horseb['scratched'])) {
						array_push($retiresBov, $horseb['number']); //ccode		
					}
				}
			}
		}

		if ( !$raceFound ) {
			//close by raceId
		} else {
			//get my retires and compare
			$horseMod = new Horse();
			$retires  = $horseMod->find('all',[
				'conditions' => ['race_id' => $raceId],
				'fields'     => ['number','Horse.id'] //ccode
			]);

			pr($retires);
			die();
		}

		return ['retires' => $retiresBov];

	}
}