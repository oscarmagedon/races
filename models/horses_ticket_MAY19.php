<?php

App::Import('Model','Ticket');
App::Import('Model','Horse');
App::Import('Model','Interval');
App::Import('Model','Center');

class HorsesTicket extends AppModel {

	var $name = 'HorsesTicket';


	/**
			NEW BETTER FUNCTIONS = = >>
	*/

	//main calculator of w-P-S
	//will receive intervals
	public function saveWinnersPrizes($raceId, $winners, $national = 0)
	{
		//SETS PRIZES
		$this->_setPrizesWinners($winners, $national);
		//SET LOSERS
		$this->_setLosersWinners($winners);

		// GET TOTALS BY TICKET
		$horsesIds = [];
		foreach ($winners as $winner) {
			array_push($horsesIds, $winner['horse_id']);
		}

		//
		$prizesByTicket = $this->_getPrizeByTickets($horsesIds);
		//pr($prizesByTicket);
		
		// prize setter method
		//here i work with the intervals if it's national
		$this->_setTicketsPrizes($prizesByTicket);

		// Tickets affected
		return count($prizesByTicket);

	}

	//main calculator function for specials
	public function saveSpecialPrizes($winners, $prizes)
	{
		//$ticketMod  = new Ticket();
		// $winners -- $data['Results'][0]
		//$exaPrizes = 
		$this->getExactaPrizes(
			$winners[0]['horse_id'], //$win 
			$winners[1]['horse_id'], //$place 
			$prizes['exacta']//$prize
		);

		//die();
		/*
		$triPrizes = $this->getTrifectaPrizes(
			$winners[0]['horse_id'], //$win 
			$winners[1]['horse_id'], //$place 
			$winners[2]['horse_id'], //$show 
			$prizes['trifecta']//$prize
		);
		
		$specialPrizes = $exaPrizes + $triPrizes;
		
		foreach ($specialPrizes as $ticketId => $prize) {
        	$ticketMod->updateAll(
        		['prize'     => $prize],//"($prize * `units`)"
        		['Ticket.id' => $ticketId]
        	);
        }
		*/        
	}

	// special details on tickets
	public function getSpecialDetails($ticketId,$typeBox = 2)
	{
		$lines = $this->find('all',[
			'conditions' => [
				'ticket_id' => $ticketId
			],
			'order' => [
				'box_number','play_type_id'
			]
		]);

		//echo count($lines);

		$byBox  = [];
		
		foreach ($lines as $box) {
			$boxn = $box['HorsesTicket']['box_number'];
            
            if (!empty($byBox[$boxn])) {	
	            array_push($byBox[$boxn],$box['HorsesTicket']);
	        } else {
            	$byBox[$boxn] = [$box['HorsesTicket']];
            }
		}

		$winner = [];
		$losers = [];

		foreach ($byBox as $boxNum => $htick) {
            if ( $htick[0]['horses_tickets_status_id'] == 2 ) {
	        	$winner['Box']   = $boxNum;
	        	$winner['Prize'] = $htick[0]['prize'];
	        	$winner['Bets']  = $htick;
       			//unset winner from the rest
       			// ...
        	} else {
	        	array_push($losers, $htick);
	        }
		}
		return [
				'Losers' => $losers, 
				'Winner' => $winner
			];	
	}

	//exacta ticket prizes
	public function getExactaPrizes($win, $place, $prize)
	{
		$exactas = $this->find('all',[
            'conditions' => [
                'OR' => [
                	[
                		'horse_id'     => $win,
                		'play_type_id' => 10
                	],
                	[
                		'horse_id'     => $place,
                		'play_type_id' => 11
                	]
                ]
            ]
        ]);
		//pr($exactas);
		//die();
		$boxedHorses    = $this->_getBoxesByTicket($exactas);

		$newBoxed       = $this->_setBoxesPrizes($exactas);
		
		$newBoxedExas = $this->_getSpecialPrizes($newBoxed, 2);

		//pr($newBoxedExas);
		//only SET process and will replace next one
		$this->_setSpecialPrizes($newBoxedExas, $prize);

		// 2 => EXACTA
		//return $this->_specialPrizeTickets($boxedHorses, 2, $prize);
	}

	//trifecta ticket prizes
	public function getTrifectaPrizes($win, $place, $show, $prize)
	{
		$trifectas = $this->find('all',[
            'conditions' => [
                'OR' => [
                	[
                		'horse_id'     => $win,
                		'play_type_id' => 12
                	],
                	[
                		'horse_id'     => $place,
                		'play_type_id' => 13
                	],
                	[
                		'horse_id'     => $show,
                		'play_type_id' => 14
                	]
                ]
            ]
        ]);

		$boxedHorses = $this->_getBoxesByTicket($trifectas);
		// 3 => EXACTA
		return $this->_specialPrizeTickets($boxedHorses, 3, $prize);
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

    // RE-CALCULATE TICKETS BASED ON INTERVALS
    public function recalculateIntervals($raceId, $winners, $horsetrack, $lastRiders)
    {	
    	//pr($winners);
    	$interval = new Interval();
		$center   = new Center();
		$ticket   = new Ticket();

    	$centers = $center->find('list');
    	unset($centers[1]);
        //patch
        //$centers  = [5=>'Horses Online'];
        //patch
        //$centerId = 5;

        $winSaved = $winners[0]['win'];
    	
    	foreach ($centers as $centerId => $centerName) {
    		
    		$intervals    = $interval->getCleanByHipo( $centerId, $horsetrack, $lastRiders);
    		
    		if (!empty($intervals)) {
    			$prizeFactor  = $this->_intervalOnWinner($intervals, $winSaved);	
	    		//echo '<br> Factor prizes : '. $prizeFactor. '<br>';
		    	//echo '<br> Example: '. (1200 * $prizeFactor) . '<br>';
		    	
		    	//UPDATING METHOD
		    	$ticket->updateAll(
		    		[
		    			'Ticket.prize' => "(`prize` * $prizeFactor)"
		    		],
		    		[
		    			'Ticket.center_id' => $centerId,
		    			'Ticket.race_id'   => $raceId
		    		]
		    	);	
    		}    		
    	}

    	//echo $raceId;
    	//pr($winners);
    	//pr($intervals);    	
    }

    private function _intervalOnWinner($intervals, $winSaved)
    {
    	//echo '<br>Tkt prize: ' . $winSaved . '<br>';
		$winnerPrize = 0;
        foreach ( $intervals as $intv ) {
        	
        	$changed  = false;

            if ( $winSaved >= $intv['Interval']['val_from'] && 
                 $winSaved <= $intv['Interval']['val_to'] ) {

                //echo "<br> Passed on intv: " . $intv['Interval']['val_from'];              
                if ( $intv['Interval']['div_add'] == 0 ) {
            		$prizesDiff  = $intv['Interval']['amount'] / $winSaved;
                    $winnerPrize = $prizesDiff;
                } else {
                	$prizesDiff  = $intv['Interval']['amount'] + $winSaved;
                    $winnerPrize = ($prizesDiff / $winSaved);
                	/*
                    if ( ! $changed ){
                        $winnerPrize = $winnerPrize + $intv['Interval']['amount'];
                        $changed  = true;
                    }
                    */
                }
            }
            
        }

        return $winnerPrize;
    }
	

	//get listed prizes by ds  sdsd fdf sdfs
    private function _getPrizeByTickets($horseIds)
    {
        //$hrsTicketsModel = new HorsesTicket();

        $prizes = $this->find(
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

	//sets horses tickets statuses and prizes
	private function _setPrizesWinners($winners, $isNational = 0)
	{
		$factor = ($isNational == 1) ? 200 : 2;
		//$hrsTicketsModel = new HorsesTicket();
		//RESULTS ARE DIVIDED BY TOTALRACES

		//  ---- P  R  I  M  E  R    C  A  B  A  L  L  O   -------
		$this->updateAll(
			[
				'prize' => '( `units` *' . ($winners[0]['win']/$factor) . ")",
				'horses_tickets_status_id' => 2
			],
			[
				'horse_id'     => $winners[0]['horse_id'], 
				'play_type_id' => 1	
			]
		);	

		$this->updateAll(
			[
				'prize' => '( `units` *' . ($winners[0]['place']/$factor) . ")",
				'horses_tickets_status_id' => 2
			],
			[
				'horse_id'     => $winners[0]['horse_id'], 
				'play_type_id' => 2	
			]
		);

		$this->updateAll(
			[
				'prize' => '( `units` *' . ($winners[0]['show']/$factor) . ")",
				'horses_tickets_status_id' => 2
			],
			[
				'horse_id'     => $winners[0]['horse_id'], 
				'play_type_id' => 3	
			]
		);	

	    //  ---- S  E  G  U  N  D  O    C  A  B  A  L  L  O   -------

		$this->updateAll(
			[
				'prize' => '( `units` *' . ($winners[1]['place']/$factor) . ")",
				'horses_tickets_status_id' => 2
			],
			[
				'horse_id'     => $winners[1]['horse_id'], 
				'play_type_id' => 2	
			]
		);

		$this->updateAll(
			[
				'prize' => '( `units` *' . ($winners[1]['show']/$factor) . ")",
				'horses_tickets_status_id' => 2
			],
			[
				'horse_id'     => $winners[1]['horse_id'], 
				'play_type_id' => 3	
			]
		);

        //  ---- T  E  R  C  E  R    C  A  B  A  L  L  O   -------

		$this->updateAll(
			[
				'prize' => '( `units` *' . ($winners[2]['show']/$factor) . ")",
				'horses_tickets_status_id' => 2
			],
			[
				'horse_id'     => $winners[2]['horse_id'], 
				'play_type_id' => 3	
			]
		);
	}

	//
	private function _setLosersWinners($winners)
	{
		//  ---- S  E  G  U  N  D  O    C  A  B  A  L  L  O   -------
		$this->updateAll(
			[ 'horses_tickets_status_id' => 3 ],
			[
				'horse_id'     => $winners[1]['horse_id'], 
				'play_type_id' => 1	
			]
		);

		//  ---- T  E  R  C  E  R    C  A  B  A  L  L  O   -------
		$this->updateAll(
			[ 'horses_tickets_status_id' => 3 ],
			[
				'horse_id'     => $winners[2]['horse_id'], 
				'play_type_id' => [1, 2]
			]
		);
	}

	//receives the arranged query 
	//type of bet (amount of horses temp.)
	private function _specialPrizeTickets($boxed, $type, $prize)
	{
		$prizes = [];
		foreach($boxed as $ticketId => $details){
        	$sumTicket = 0;

            foreach($details['boxes'] as $box ){
            	$prizeBox = 0;
            	//if 2 or 3 or 4
            	if (count($box) == $type ) {
            		$prizeBox = $box[0]['units'];//($box[0]['units'] * ($prize / count($box)));
            	}

            	$sumTicket += $prizeBox;
            }

            $prizes[$ticketId] = $sumTicket * $prize; //['pr'=>$prize,'sum'=>$sumTicket];
        }

        return $prizes;
	}

	private function _getSpecialPrizes($boxed, $type)
	{
		$ticketPrizes = [];

		foreach($boxed as $ticketId => $details){
        	
			$prizes = [
				'WinIds' => [],
				//'Losers' => [],
				'Units'  => 0,
			];

            foreach($details['boxes'] as $box ){
            	//$prizeBox = 0;
            	//if 2 or 3 or 4
            	if ( count($box) == $type ) {
            		//$prizeBox = $box[0]['units'];
            		//($box[0]['units'] * ($prize / count($box)));
            		
            		//these win-ids should be general 
            		//query based on this race
            		array_push($prizes['WinIds'], $box[0]['id']);
            		array_push($prizes['WinIds'], $box[1]['id']);
            		
            		$prizes['Units'] += $box[0]['units'];

            	} 
            	/*
            	else {
            		foreach ($box as $bx) {
            			array_push($prizes['Losers'], $bx['id']);
            		}
            	}
            	*/
            }
            /*
			this should not be done in this model
            */
            //$prizes['Units'] = $prizes['Units'] * $prize;

            $ticketPrizes[$ticketId] = $prizes;
        }
        //return full prize
        
        return $ticketPrizes;
	}

	private function _setSpecialPrizes($prizes, $payed)
	{
		$ticketMod  = new Ticket();

		foreach ($prizes as $ticketId => $horses) {
			
			//set winner and units
			$this->updateAll(
				[
					'prize' => $horses['Units'],
					'horses_tickets_status_id' => 2 
				],
				[
					'id'  => $horses['WinIds'],
				]
			);

			//set losers the rest of this type of bet
			//on status pending
			$this->updateAll(
				[
					'horses_tickets_status_id' => 3 
				],
				[
					'horses_tickets_status_id' => 1,
					'ticket_id' => $ticketId
				]
			);
			
			//set ticket prize
			$ticketMod->updateAll(
				[
					'Ticket.prize' => $horses['Units'] * $payed
				],
				[
					'Ticket.id' => $ticketId
				]
			);	
		}

		/*
		Array
			(
			    [3558] => Array
			        (
			            [WinIds] => Array
			                (
			                    [0] => 12127
			                    [1] => 12126
			                )

			            [Losers] => Array
			                (
			                    [0] => 12053
			                    [1] => 12065
			                    [2] => 12077
			                    [3] => 12103
			                    [4] => 12115
			                    [5] => 12120
			                    [6] => 12122
			                    [7] => 12124
			                    [8] => 12128
			                    [9] => 12130
			                )

			            [Units] => 3
			        )
		*/
	}

	//receives the query result 
	//returns the arranged data
	private function _getBoxesByTicket($queryRes)
	{
		$boxesByTicket = [];

		foreach ($queryRes as $box) {
			$tkid = $box['HorsesTicket']['ticket_id'];
            $boxn = $box['HorsesTicket']['box_number'];
            $hsid = $box['HorsesTicket']['horse_id'];
			$ptid = $box['HorsesTicket']['play_type_id'];
			$unts = $box['HorsesTicket']['units'];
			
            if (!empty($boxesByTicket[$tkid]['boxes'][$boxn])) {
            	array_push($boxesByTicket[$tkid]['boxes'][$boxn],
            		[
	                	'play_type_id' => $ptid,
	                	'horse_id'     => $hsid,
	                	'units'        => $unts 
	                ]
	            );
            } else {
            	$boxesByTicket[$tkid]['boxes'][$boxn][0] = [
                	'play_type_id' => $ptid,
                	'horse_id'     => $hsid,
                	'units'        => $unts
                ];
            }
		}

		return $boxesByTicket;
	}

	private function _setBoxesPrizes($queryRes)
	{
		$boxesByTicket = [];

		foreach ($queryRes as $box) {
			$htid = $box['HorsesTicket']['id'];
			$tkid = $box['HorsesTicket']['ticket_id'];
            $boxn = $box['HorsesTicket']['box_number'];
            $hsid = $box['HorsesTicket']['horse_id'];
			$ptid = $box['HorsesTicket']['play_type_id'];
			$unts = $box['HorsesTicket']['units'];
			
            if (!empty($boxesByTicket[$tkid]['boxes'][$boxn])) {
            	array_push($boxesByTicket[$tkid]['boxes'][$boxn],
            		[
	                	'id'           => $htid,
	                	'play_type_id' => $ptid,
	                	'horse_id'     => $hsid,
	                	'units'        => $unts 
	                ]
	            );
            } else {
            	$boxesByTicket[$tkid]['boxes'][$boxn][0] = [
	                'id'           => $htid,
                	'play_type_id' => $ptid,
                	'horse_id'     => $hsid,
                	'units'        => $unts
                ];
            }
		}

		return $boxesByTicket;	
	}

	/**
			<< = = NEW BETTER FUNCTIONS
	*/

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
	function setAllStatuses($raceId,$riders,$retired,$results)
	{
		die('here!');
		
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
							array(
								'horse_id'     => $winners[1]['horse_id'], 
								'play_type_id' => 1 )
							);
							
		//APUESTAS PLACE AL GANADOR
		$this->_executePrize(
							"(units * ". ($winners[1]['place'] / $currency) .")", 
							array(
								'horse_id'     => $winners[1]['horse_id'], 
								'play_type_id' => 2 )
							);
		
		//APUESTAS SHOW AL GANADOR
		$this->_executePrize(
							"(units * ". ($winners[1]['show'] / $currency) .")",
							array(
								'horse_id'     => $winners[1]['horse_id'], 
								'play_type_id' => 3 )
							);
		
		
		//  ---- S  E  G  U  N  D  O    C  A  B  A  L  L  O   -------
		
		//APUESTAS PLACE AL SEGUNDO
		$this->_executePrize(
							"(units * ". ($winners[2]['place'] / $currency) .")",
							array(
								'horse_id'     => $winners[2]['horse_id'], 
								'play_type_id' => 2 )
							);
		
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