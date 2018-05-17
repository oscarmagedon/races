<?php
class HorsesTicket extends AppModel {

	var $name = 'HorsesTicket';

	//determina cantidad necesaria segun tipo para ser ganador		 
	var $quants = array(
						10 => 2, 11 => 2,                   //exactas
						12 => 3, 13 => 3, 14 => 3,			//trifectas
						15 => 4, 16 => 4, 17 => 4, 18 => 4  //superfcts
					);
					
					
					
					
	//		              G  E  T  T  E  R  S    ------------>>>>>
	
	//get listed prizes by BASIC
	function getPrizeByTickets($horses)
	{
		$prizes = $this->find('all',array(
								'conditions' => array('prize >' => 0, 'horse_id' => $horses),
								'fields'     => array('ticket_id','prize'), 
								'recursive'  => -1
					));
			
		$byTicket = array();	
		foreach($prizes as $pr){
			
			if(!empty($byTicket[$pr['HorsesTicket']['ticket_id']]))
				$byTicket[$pr['HorsesTicket']['ticket_id']] += $pr['HorsesTicket']['prize'];
			else
				$byTicket[$pr['HorsesTicket']['ticket_id']] = $pr['HorsesTicket']['prize'];
		}
		
		return $byTicket;
	}
	
	//Get listed prizes SPECIALS by ticket 
	function getPrizeSpecialTickets($horses,$prizes)
	{
		$specials = $this->find('all',array(
						'conditions' => array(
											'horse_id'                     => $horses,
											'horses_tickets_status_id'     => 2,
											'play_type_id BETWEEN ? AND ?' => array(10,18),
										),
						'fields'     => array(  'ticket_id','horses_tickets_status_id',
												'units','play_type_id','box_number','horse_id'
											  ), 
						'recursive'  => -1
					 ));
		
		//determina el betamax del premio
		$priceSet = array(
						2 => $prices['exacta'],
						3 => $prices['trifecta'],
						4 => $prices['superfecta']
					);
					
		$detsFinal = array();
		foreach($specials as $d){
			$tkid = $d['HorsesTicket']['ticket_id'];
			$boxn = $d['HorsesTicket']['box_number'];
			$hsid = $d['HorsesTicket']['horse_id'];
			
			$detsFinal[$tkid]['units'] = $d['HorsesTicket']['units'];
			$detsFinal[$tkid]['towin'] = $this->quants[$d['HorsesTicket']['play_type_id']];
			
			if (!empty($detsFinal[$tkid]['boxes'][$boxn]))
				array_push($detsFinal[$tkid]['boxes'][$boxn],$hsid);
			else 
				$detsFinal[$tkid]['boxes'][$boxn][0] = $hsid;
		}

		$byTicket = array();
		foreach($detsFinal as $tkid => $det){
			
			foreach($det['boxes'] as $box){
				if(count($box) == $det['towin']){
					$byTicket[$tkid] = $det['units'] * $priceSet[$det['towin']];
				}
			}
			
		}
					
		return $byTicket;		
	}
	
	//		<<<<<----     G  E  T  T  E  R  S   
	
	
	//		              S  E  T  T  E  R  S    ------------->>>>>
	
	
	//main method to set status
	function setStatus($raceId,$riders,$retired,$results)
	{
		$winners = $this->_utilWinners($results);
		
		//$prizes  = $this->_utilPrizes($results);
		
		//RESETEO DE RESULTADOS
		$this->_executeStatus(1,array('horse_id' => array_merge($riders,$retired)));
		
		//Retirados
		$this->_executeStatus(4,array('horse_id' => $retired));
		
		//seteo BASICO de premios
		$this->setBasicStatuses($retired,$winners);
		
		//seteo de premios ESPECIAL
		$this->setSpecialStatuses($winners);
		 
		//Perdedores todos los corredores q sobraron
		$this->_executeStatus(3,array('horse_id' => $riders, 'horses_tickets_status_id' => 1));
		
	}
	
	//seteo de statuses basicos
	function setBasicStatuses($retired,$winners)
	{
		//Ganadores al primero (TODAS LAS APUESTAS)
		$this->_executeStatuses(2, array('horse_id' => $winners[1]));
		//Ganadores al segundo (DE SEGUNDO O DE PRIMERO)
		$this->_executeStatuses(2, array('horse_id' => $winners[2], 'play_type_id' => array(2,3)));
		//Ganadores al tercero ( SOLO DE TERCERO PAGA)
		$this->_executeStatuses(2, array('horse_id' => $winners[3], 'play_type_id' => 3));
	}
	
	//seteo de statuses especiales exa-tri-sup
	function setSpecialStatuses($winners)
	{
		
		//Ganadores 1er lugar EXACTA,TRIFECTA y SUPERFECTA
		$this->_executeStatuses(2, array('horse_id' => $winners[1], 'play_type_id' => array(10,12,15)));
		
		//Ganadores 2do lugar EXACTA,TRIFECTA y SUPERFECTA 
		//   11: EXA2, 13: TRI2, 16: SUP2        
		$this->_executeStatuses(2, array('horse_id' => $winners[2], 'play_type_id' => array(11,13,16)));
		
		//Ganadores 3er lugar TRIFECTA y SUPERFECTA 
		//   14: TRI3, 17: SUP3
		$this->_executeStatuses(2, array('horse_id' => $winners[3], 'play_type_id' => array(14,17)));
		
		//Ganadores 4to lugar SUPERFECTA
		//   18: SUP4 
		$this->_executeStatuses(2, array('horse_id' => $winners[4], 'play_type_id' => 18));
		
	}
	
	//seteo de premios = monto en retirados
	function setRetiredPrizes($retires)
	{
		$this->_executePrize("units",array('horse_id' => $retires));
	}
	
	//seteo de PREMIOS NACIONAL basicos
	function setAllPrizes($result,$currency,$isIntl)
	{
		//DEPENDIENDO EN NAC-INTL RETORNA EL ARRAY DE CALCULO
		$winners = $this->_utilPrizes($result,$isIntl);
		
		//  ---- P  R  I  M  E  R    C  A  B  A  L  L  O   -------
		
		//apuestas WIN AL GANADOR
		$this->_executePrize(
							"(units * ". ($winners[1]['win'] / $currency) .")", 
							array('horse_id' => $winners[1]['horse_id'], 'play_type_id' => 1));
							
		//APUESTAS PLACE AL GANADOR
		$this->_executePrize(
							"(units * ". ($winners[1]['place'] / $currency) .")", 
							array('horse_id' => $winners[1]['horse_id'], 'play_type_id' => 2));
		
		//APUESTAS SHOW AL GANADOR
		$this->_executePrize(
							"(units * ". ($winners[1]['show'] / $currency) .")",
							array('horse_id' => $winners[1]['horse_id'], 'play_type_id' => 3));
		
		
		//  ---- S  E  G  U  N  D  O    C  A  B  A  L  L  O   -------
		
		//APUESTAS PLACE AL SEGUNDO
		$this->_executePrize(
							"(units * ". ($winners[2]['place'] / $currency) .")",
							array('horse_id' => $winners[2]['horse_id'], 'play_type_id' => 2));
		
		//APUESTAS SHOW AL SEGUNDO
		$this->_executePrize(
							"(units * ". ($winners[2]['show'] / $currency) .")",
							array('horse_id' => $winners[2]['horse_id'], 'play_type_id' => 3));
		
		
		//  ---- T  E  R  C  E  R    C  A  B  A  L  L  O   -------
		
		//APUESTAS SHOW AL TERCERO
		$this->_executePrize(
							"(units * ". ($winners[3]['show'] / $currency) .")",
							array('horse_id' => $winners[3]['horse_id'], 'play_type_id' => 3));
		
		
	}	
	
	//		<<<<<----     S  E  T  T  E  R  S   
	
	//utilidad que setea el status
	function _executeStatus($stat, $conditions)
	{
		$this->updateAll(array('horse_tickets_status_id' => $stat), $conditions);	
	}
	
	//utilidad que setea el premio
	function _executePrize($prize, $conditions)
	{
		$this->updateAll(array('prize' => $prize), $conditions);	
	}
	
	//utilidad que devuelve array util
	function _utilWinners($result)
	{
		$winners = array();
		foreach ($result as $tw){
			for ($i = 1; $i <= 4; $i ++) {	
				if($tw['position'] == $i)
					$winners[$i] = $tw['horse_id'];
			}
		}
		
		return $winners;
	}
	
	//utilidad que devuelve array util
	function _utilPrizes($result, $intl)
	{
		$winners = array();
		
		foreach ($result as $tw){
			for ($i = 1; $i <= 3; $i ++) {
				if($tw['position'] == $i) {
					
					$pWin = $tw['win'];	
					$pPla = $tw['place'];
					$pSho = $tw['show'];
					
					if ($intl) {
						$pWin = $tw['win'] / 2;	
						$pPla = $tw['place'] / 2;
						$pSho = $tw['show'] / 2;	
					}
					
					$winners[$i] = array(
										'horse_id' => $tw['horse_id'],
										'show'     => $pSho
									);
					
					if (isset($tw['win']))
						$winners[$i]['win'] = $pWin;
					
					if (isset($tw['place']))
						$winners[$i]['place'] = $pPla;									
				}
			}
		}
		
		return $winners;
	}
	
}
?>