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

        if (!isset($dataBovada[0])) {
        	return ['error'=>'Data not found'];
        }

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
			if ( isset($raceBovada['number']) && $raceBovada['number'] == $raceNumber) {
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
			$this->_closeRace($raceId, $raceNumber, $bovadaNick);

			return ['message'=>'Race ' .$raceNumber . ' retired.'];

		} else {
			//get my retires and compare
			$retires = $this->_checkRetires($bovadaNick, $raceId, $retiresBov, $raceNumber);
					
			return ['retires' => $retires ,'retiresBovada'=>$retiresBov];
		}
	}

	private function _closeRace($raceId, $raceNum, $nickb)
	{
		$raceMod = new Race();
		$operMod = new Operation(); 
		
		$raceMod->updateAll(
			['Race.enable' => 0],
			['Race.id'     => $raceId]
		);
		
		$operationMeta = "Carrera ".$raceNum . ' - ' .$nickb;
		
		$operMod->ins_op(4, 1, 'Bovada', $raceId, $operationMeta);
	}

	private function _checkRetires($bovadaNick, $raceId, $retiresBov, $raceNum)
	{
		$horseMod = new Horse();
		$newRets  = [];
		$retires  = $horseMod->find('list',[
			'conditions' => ['race_id' => $raceId,'Horse.enable'=>0],
			'fields'     => ['Horse.id', 'number'] //ccode
		]);
		
		//compare ARRAYS
		if ( count($retiresBov) != count($retires) ) {
			//to retire! 
			foreach ($retiresBov as $rbov) {
				if ( !in_array($rbov, $retires) ) {
					array_push($newRets, $rbov);
				}
			}
		}

		//RETIRE NEWS!!
		if ( count($newRets) > 0) {
			
			$operationMeta = $bovadaNick .', '. $raceNum. 'a: ';
			
			//update horses
			$horseM = ClassRegistry::init('Horse');
			$horseM->unbindModel(['belongsTo'=>['Race']]);
			
			foreach ($newRets as $newNumber) {
				$horseM->updateAll(
					['Horse.enable' => 0],
					[
						"Horse.number = $newNumber",
						'Horse.race_id' => $raceId
					]
				);
				unset($horseM->id);
				
				$operationMeta .= $newNumber .' ,';
			}
			/*
			unset($horseM->id);

			$horseM->updateAll(
				['Horse.enable' => false],
				[
					'Horse.number' => $newRets,
					'Horse.race_id'   => $raceId
				]
			);
			*/
			
			//Operation
			$operMod = new Operation(); 
			$operMod->ins_op(4, 1, 'Bovada', $raceId, $operationMeta);
		}

		return ['Saved'=>$retires,'New'=>$newRets];
	}
}