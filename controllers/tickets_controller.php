<?php
App::Import('Model','HorsesTicket');

class TicketsController extends AppController 
{

	var $name    = 'Tickets';
	var $helpers = array('Html', 'Form');

	function beforeFilter()
    {
		parent::beforeFilter();	
        
        $this->Authed->allow(array('betsms'));    
	}
	
	function isAuthorized()
    {
		
		$ret = true;
		
		$actions_adm = array(
			"admin_index","admin_sales","admin_salesnew",
            "admin_follow","admin_fwraceprof",
            "admin_newfollow",'admin_followraces','admin_followhorses',
            "admin_horses_details","admin_lines",
            "admin_anull","admin_create_proof","admin_onlinew","admin_payonline"
		);
		
		$actions_taq = array(
            "admin_prntkt","admin_addnew",
			"admin_add","admin_bet",
            "admin_add_pick","admin_print","admin_pay",
            "admin_paybarc",'admin_newpaybarc',
            "admin_pay_ticket","admin_horses_details","admin_taquilla",
            "admin_salestaq",
            "admin_sales_taquilla","admin_anull_last","admin_reprint_last"
            ,"admin_anulltaq"
            //,"admin_pay_nc"
		);
		
		$actions_onl = array(
			"admin_add","admin_bet",
            "admin_addnew","admin_print",
            "admin_horses_details","admin_taquilla",
            "admin_salestaq","admin_anull_last"
            ,"admin_anulltaq"
		);
        
        $actions_aut = array('admin_add','admin_autotaq',"admin_horses_details");
		
		if($this->isAdmin() && in_array($this->action, $actions_adm)){
			$ret = true;
		}elseif($this->isTaquilla() && in_array($this->action, $actions_taq)){
			$ret = true;	
		}elseif($this->isOnline() && in_array($this->action, $actions_onl)){
			$ret = true;	
		}elseif($this->isAuto() && in_array($this->action, $actions_aut)){
			$ret = true;	
		}else{
			$ret = false;
		}
				
		if($ret == false)
			$this->Session->setFlash("Direccion NO permitida");
		
		return $ret;
	}
    
    /**
     * ==  C E N T R O    ==> 
     */
    function admin_index($since = null, $until = null, $profileId = 0, $winner = 0, $payed = 0) 
    {
		if($since == null){
			$since = date('Y-m-d');
			$until = date('Y-m-d');
		}
		
		$profs = $this->Ticket->Profile->find('all',array(
                        'conditions' => array(
                            'center_id'    => $this->authUser['center_id'],
                            'User.role_id' => array(3,4)),
                        'fields' => array('Profile.name','Profile.id')
                    ));
		
		foreach($profs as $pro){
 			$profiles[$pro['Profile']['id']] = $pro['Profile']['name'];
 		}
 		
 		$conds = array(
			'date(created) BETWEEN ? AND ?' => array($since,$until),
			'Ticket.center_id'              => $this->authUser['center_id']
		);
		
		if($profileId != 0){
			$conds['profile_id'] = $profileId;
		}
		
		if($winner != 0){
			if($winner == 1)
				$conds['prize >'] = 0;
			else
				$conds['prize'] = 0;	
		}
		
		switch ($payed) {
			case 1:
				$conds['payed_status_id'] = 2;
				break;
			case 2:
				$conds['payed_status_id'] = 1;
				break;
			case 3:
				$conds['Ticket.enable'] = 0;
				break;
		}
		
		$config  = ClassRegistry::init('Config');
        $hipod   = ClassRegistry::init('Hipodrome');
        $intls   = $hipod->getIntlIds();
		$unitNac = $config->get_unit_value($this->authUser['center_id']);
		$unitInt = $config->get_unit_value($this->authUser['center_id'],true);
		

        $hipods = $this->Ticket->Race->getHorsetracksByDay(
                        array($since,$until), 
                        1,0,true);

        // PATCH-admonline
        /*
        if ( $this->authUser['profile_id'] == 10 ) {
            $hipods = $this->Ticket->Race->getHorsetracksByDay(
                        array($since,$until), 
                        1,0,true);

        }
        */
        // PATCH-admonline

        //$this->Ticket->unbindModel(array('belongsTo'=>array('PlayType')),false);
        
		$this->paginate['conditions'] = $conds;
		$this->paginate['order']      = array('created'=>'DESC');
		$this->paginate['recursive']  = 0;
		$this->paginate['limit']      = 30;
        
		$this->set('tickets', $this->paginate());
		
		$this->set(compact('unitNac','unitInt','since','until','profiles',
                            'winner','payed','profileId','intls','hipods'));
	}
    
    function admin_sales ($since = null, $until = null, $htrackid = null, $raceid = null)
    {
        
        $cid = $this->authUser['center_id'];

        // PATCH-admonline

        //if ( $this->authUser['profile_id'] == 10 ) {
        //$cid = 1;
        //}
        // PATCH-admonline
        
        if ( $since == null ) {
            $since = date('Y-m-d');
            $until = date('Y-m-d');
        }
        
        $totals    = $this->Ticket->getTotalSales($since,$until,$cid,null,$raceid);
        $profSales = $this->Ticket->getSalesProfiles($since,$until,$cid,$raceid);
        $profiles  = $this->Ticket->Profile->getPlayers($cid);
 		$htracks   = $this->Ticket->Race->getHtracksByRange(1, $since, $until);

        $races = array();
        
        if ( $since == $until && $htrackid != null) {
            $races = $this->Ticket->Race->find('list',array(
                        'conditions' => array(
                            'race_date'    => $since,
                            'center_id'    => 1,
                            'hipodrome_id' => $htrackid),
                        'fields' => 'number'));
        }
        
        $this->set(compact('since','until','totals','profSales','profiles',
            'htracks','htrackid','races','raceid'));
    }

    function admin_follow ( $date = null , $htrackid = 0, $raceid = 0 ) 
    {
        if ( $date == null ) $date = date('Y-m-d');

        //echo $date;
        //getHorsetracksByDay($date, $centerId, $nationals = 0, 
        //$allRaces = false, $counter = false)
        //Only nationals
        $htracks = $this->Ticket->Race->getHorsetracksByDay(
                                            $date , 
                                            $this->authUser['center_id'] ,
                                            1 , //nationals
                                            true //included ended
                                            ) ;

        $races = [] ;

        //pr($htracks);

        if ( $htrackid != 0 ) {

            $races  = $this->Ticket->Race->find('list', [
                            'conditions' => [
                                            'center_id'    => $this->authUser['center_id'] , 
                                            'hipodrome_id' => $htrackid,
                                            'race_date'    => $date , 

                                        ], 'fields'=>'number' ] ) ; 
        }

        $profs = $this->Ticket->Profile->find('all',array(
                        'conditions' => array(
                            'center_id'    => $this->authUser['center_id'],
                            'User.role_id' => array(3,4)),
                        'fields' => array('Profile.name','Profile.id')
                    ));
        
        foreach($profs as $pro){
            $profiles[$pro['Profile']['id']] = $pro['Profile']['name'];
        }

        //pr($profiles);
        //die();
        $this->set(compact('date','htrackid','raceid','htracks','races','profiles'));
    }

    function admin_fwraceprof ( $raceid , $profileid = 0 )
    {       
        $config  = ClassRegistry::init('Config');

        $unitNac = $config->get_unit_value($this->authUser['center_id']);
        //echo  'race id: ' . $raceid ;
        //echo  'profile id: ' . $profileid ;
        
        $horseModel = ClassRegistry::init('Horse');

        $horsesRace = $horseModel->find('list',[
                            'conditions' => ['race_id'=>$raceid],
                            'recursive'  => 1
                        ]);
        
        $this->Ticket->HorsesTicket->bindModel([
                        'belongsTo'=>[
                            'Ticket'
                    ]]);
        
        $byTicket = $this->Ticket->HorsesTicket->find('all', [
                            'conditions' => [
                                    'horse_id'=> array_keys($horsesRace),
                            ],
                            'order'      => ['ticket_id','horse_id']
                        ] ) ;

        //pr($sales);
        $horseSales = [];

        foreach ( $byTicket as $horseBet ) {

            //if ( $profileid != 0 ) {


                if ( 
                    ($profileid != 0  && $horseBet['Ticket']['profile_id'] == $profileid )
                    ||
                    $profileid == 0
                ) {

                    if ( isset ($horseSales[$horseBet['HorsesTicket']['horse_id']]) ) {
                    
                        $horseSales[$horseBet['HorsesTicket']['horse_id']]['tickets'] ++;
                        
                        $horseSales[$horseBet['HorsesTicket']['horse_id']]['units'] = 
                            $horseSales[$horseBet['HorsesTicket']['horse_id']]['units'] + 
                            $horseBet['HorsesTicket']['units'];

                        $horseSales[$horseBet['HorsesTicket']['horse_id']]['prize'] = 
                            $horseSales[$horseBet['HorsesTicket']['horse_id']]['prize'] + 
                            $horseBet['HorsesTicket']['prize'];

                        $horseSales[$horseBet['HorsesTicket']['horse_id']]['unibs'] = 
                            $horseSales[$horseBet['HorsesTicket']['horse_id']]['unibs'] + 
                            ($horseBet['HorsesTicket']['units'] * $unitNac );

                        $horseSales[$horseBet['HorsesTicket']['horse_id']]['pribs'] = 
                            $horseSales[$horseBet['HorsesTicket']['horse_id']]['pribs'] + 
                            ($horseBet['HorsesTicket']['prize'] * $unitNac );

                    } else {

                        $horseSales[$horseBet['HorsesTicket']['horse_id']] = [
                            'tickets' => 1, 
                            'horse'   => $horsesRace[$horseBet['HorsesTicket']['horse_id']],
                            'units'   => $horseBet['HorsesTicket']['units'],
                            'prize'   => $horseBet['HorsesTicket']['prize'],
                            'unibs'   => ($horseBet['HorsesTicket']['units'] * $unitNac ) , 
                            'pribs'   => ($horseBet['HorsesTicket']['prize'] * $unitNac ) 
                        ];
                        
                    }

            //    }

            }





            
            
        }

        //pr($horseSales);
        //pr($horsesRace);
        //die();
        
        $this->set(compact('horseSales'));

        $this->layout = null;
    }
    
    function admin_newfollow($date = null, $htkid = null, $race = null)
    {
        if ( $date == null ) $date = date('Y-m-d');
        
        $byHipo = array(); $byRace = array();
        //BY HORSETRACK
        if ( $htkid != null ) {
			$cond['race_id'] = $this->Ticket->Horse->Race->find('list',array(
                                    'conditions' => array(
                                        'race_date' => $date, 
                                        'center_id' => $this->authUser['center_id'],
                                        'hipodrome_id' => $htkid ), 
                                    'fields' => 'Race.id','group'=>'Race.id'));
            
            //$obyHipo = $this->Ticket->races_by_hipo($this->authUser['center_id'],$date,$htkid);
            //echo "THISHIPO"; pr($byHipo);
            
            $byHipo = $this->Ticket->racesByHipo($this->authUser['center_id'],$date,$htkid);
            //echo "NEW THISHIPO"; pr($byHipo);
		}
		
        //BY HORSETRACK
        if ( $race != null ) {
            //echo "BY RACE...";
            $byRace = $this->Ticket->horsesByRace($race);
        }
        
		$cond['center_id'] = $this->authUser['center_id'];
		$cond['date(created)'] = $date;
		$cond['enable'] = 1;
		
        //pr($cond);
        $tickets = $this->Ticket->find('first',array(
                        'conditions' => $cond,
                        'fields' => array('count(*) AS co','sum(units) AS un','sum(prize) AS pr'),
                        'recursive' => -1
                    ));
 		
        //pr($tickets);
        $profiles = $this->Ticket->Profile->find('list',array(
                        'conditions' => array('center_id' => $this->authUser['center_id'],
                                            'User.role_id' => array(3,4)),
                        'recursive'=> 2 ) );
 		//echo "USERS";
        //pr($profiles);
        
        $htracks = $this->Ticket->followByHipos($this->authUser['center_id'],$date);
        //echo "NEW HTRACKS"; pr($htracks);
        
        $this->set(compact('date','htkid','race','htracks','profiles','tickets',
                    'byHipo','byRace','byHorse'));
        //die('here');
    }
    
    function admin_followraces($date,$htkid)
    {
        $profiles = $this->Ticket->Profile->find('list',array(
                        'conditions' => array('center_id' => $this->authUser['center_id'],
                                            'User.role_id' => array(3,4)),
                        'recursive'=> 2 ) );
        $races    = $this->Ticket->racesByHipo($this->authUser['center_id'],$date,$htkid);
        
        $this->set(compact('date','htkid','races','profiles'));
    }
    
    function admin_followhorses($race)
    {
        $byRace = $this->Ticket->horsesByRace($race);
        $profiles = $this->Ticket->Profile->find('list',array(
                        'conditions' => array('center_id' => $this->authUser['center_id'],
                                            'User.role_id' => array(3,4)),
                        'recursive'=> 2 ) );
        
        $this->set(compact('byRace','profiles'));
    }	
    
    function admin_anull($found = null)
    {
		if(!empty($this->data)){
			//pr($this->data); die();
			
			if(!empty($this->data['Ticket']['number'])){ //BUSQUEDA				
				$tid = $this->Ticket->find('first',array(
					'conditions' => array(
						'number'=>$this->data['Ticket']['number'],
						'center_id'=>$this->authUser['center_id']
				),
					'fields' => array('id','prize','units','enable'), 
					'recursive' => -1
				));
				
				if ( !empty ( $tid ) ) {
					$this->redirect(array('action'=>'anull',$tid['Ticket']['id']));
				}else{
					$this->Session->setFlash("Ticket NO encontrado");
					$this->redirect($this->referer());
				}
			}else{ //ANULACION
				$this->Ticket->updateAll(
					array('Ticket.enable' => 0),
					array('Ticket.id' => $this->data['Ticket']['id'])
				);
				
                $num = $this->Ticket->find('first',array(
					'conditions' => array('Ticket.id'=>$this->data['Ticket']['id']),
					'fields'     => array('number','units','race_id','profile_id')
				));
                
                if ( $this->Ticket->Profile->isOnline($num['Ticket']['profile_id']) ) {
                    $configModel = ClassRegistry::init('Config');
                    $isIntl      = $this->Ticket->Race->isIntl($num['Ticket']['race_id']);
                    $currency    = $configModel->get_unit_value($this->authUser['center_id'],$isIntl);
                    $ticketValue = $currency * $num['Ticket']['units'];
                    $account     = ClassRegistry::init('Account');
                    
                    $account->addMovem( array (
                            'profile_id' => $num['Ticket']['profile_id'],
                            'title'      => 'ANULACION',
							'amount'     => $ticketValue,
                            'metainf'    => 'TID: '. $this->data['Ticket']['id'] ) );
                }
                
				$operInst = ClassRegistry::init('Operation');
				$operInst->ins_op(6,$this->authUser['profile_id'],"Tickets",
                            $this->data['Ticket']['id'],"Ticket Nro ".$num['Ticket']['number']." Anulado");			
			
				$this->Session->setFlash("Ticket ANULADO");
				$this->redirect($this->referer());
			}
		}
		
		$ticket = null;
		$pct = null;
		$money = null;
		if($found != null){
			$this->Ticket->unbindModel(array('belongsTo'=>array('PlayType','Race','Center')),false);
			$ticket = $this->Ticket->find('first',array(
				'conditions' => array('Ticket.id'=>$found),
				'recursive' => 0
			));
			
			$config = ClassRegistry::init('Config');
			$pct = $config->get_pct_ticket($this->authUser['center_id']);
			$money = $config->get_unit_value($this->authUser['center_id']);
		}
		
		$this->set('ticket',$ticket);
		$this->set('pct',$pct);
		$this->set('money',$money);
	}
    
    /**
     * <==  C E N T R O  ==
     * 
     *  == T A Q U I L L A  ==> 
     */
    
    function betsms ()
    {
        /**
         * http://smsenabler.com/receive-sms-to-web.html
         *
         * 
            $senderPhone = $_POST['sender'];//sender's phone number
            $messageText = $_POST['text'];  //text of the message

            //TODO: IMPLEMENT ANY PROCESSING HERE THAT YOU NEED TO 
            //PERFORM UPON RECEIPT OF A MESSAGE 


            // ---- Sending a reply SMS ---- 

            // Setting the recipients of the reply. Otherwise the reply is sent back to the sender of the original SMS message.

            // header('X-SMS-To: +97771234567 +15550987654');

            // Setting the content type and character encoding
            header('Content-Type: text/plain; charset=utf-8');
            // Comment the next line out if you do not want to send a reply
            echo 'Reply message text'; 
         * 
         * 
         */
        //die('hello');
        
        if ( !empty($this->data) ) {
            
            /**
             * ESTRUCTURA DE LA APUESTA ::
            * 
            * numHtrack  Horse   Tipo  Unds  
            * 3BEL       5       W     10       
            * 
            * SMS: 3bel 5 W 10 
            * 
             * * VALIDATIONS :: 
             *  
             *  - OK :: NUMBER IS ONLINE AND EXISTS
             *  - BALANCE IS ENOUGH
             *  - OK :: HIPODROME IS REAL
             *  - OK :: RACE EXISTS
             *  - OK :: HORSE EXISTS
             *  - OK :: TYPE IS W-P-S
             * ==
             *  => RETURNS : the tkt number and balance
             * 
             * debo unir el proceso de ticket entre add y betsms! 
             * se me deberia ocurrir algo asi como un inner usable en ambos 
             * metodos add y betsms
             * 
             */
            
            $date = date('Y-m-d');

            $results = array('Date'    => $date, 
                             'Number'  => $this->data['Ticket']['number'],
                             'Message' => $this->data['Ticket']['message']);
            
            $results['Profile'] = $this->Ticket->Profile->getByMobile($this->data['Ticket']['number']);
            
            $ticketData  = $this->Ticket->getSmsInfo($results['Message']);
            $ticketData['Ticket']['profile_id'] = $results['Profile']['id'];
            $ticketData['Ticket']['center_id']  = $results['Profile']['center_id'];
            
            //pr($this->data); die();
            pr($ticketData);
            pr($results);
            
            die();
            
            
        }
        //pr($results);
        //die('hello');
        $this->set(compact('results'));
    }
    

    //NUEVO ADD
    function admin_addnew()
    {
        $theDate   = date("Y-m-d");
        $myNatCnf  = $this->Ticket->Profile->getNationalConf($this->authUser['profile_id']);
        $balance   = $this->Ticket->Profile->getBalance($this->authUser['profile_id']);
        $eachUnits = array(1,2,3,4,5,10,20,25,50,100,200,500,1000);
        $nexts     = $this->Ticket->Race->getNextOnes($theDate,$this->authUser['center_id'],15,$myNatCnf);
        pr($nexts);
        die();
        //$this->set(compact('raceId','theDate','balance','eachUnits','nextones','myNatCnf'));
    }



    function admin_add($raceId = 0) 
    { 
        $confIns  = ClassRegistry::init('Config');
        $limitIns = ClassRegistry::init('Limit');
		//   BEGIN POST PROCESS
		if ( ! empty ( $this->data ) ) {
            
            //AUTOPIN ONLINE
            $this->data['Online'] = $this->Ticket->Profile->find('first',array(
                            'conditions' => array('autopin' => $this->data['Ticket']['autopin']),
                            'fields'     => 'Profile.id' ) );
            
            $validData = $this->_validTicketData($this->data);
            
            if ( $validData  != '' ) {
                $this->Session->setFlash($validData);
				$this->redirect($this->referer());
            }
            
            //Fullfill data
            $this->_completeData();
            //pr($this->authUser); 
            //pr($this->data); die();
            
            $isIntl      = $this->Ticket->Race->isIntl($this->data['Ticket']['race_id']);
            $currency    = $confIns->get_unit_value($this->authUser['center_id'], $isIntl);
            $ticketValue = $currency * $this->data['Ticket']['units'];
            $byHorse     = $this->_byHorse($this->data['Horse'],$currency);
            $limitsNow   = $limitIns->getActual($this->data['Ticket']['profile_id'],
                                $this->data['Ticket']['race_id']);
            
            //validate LIMITS new
            $validLimit = $this->_validLimits($ticketValue, $this->data['Ticket']['profile_id'],
                            $this->data['Ticket']['race_id'], $byHorse, $limitsNow);
            
            if ($validLimit  != '' ) {
                $this->Session->setFlash($validLimit);
				$this->redirect($this->referer());
            }
            
            //revisar el balance del user en linea
            if ( $this->authUser['role_id'] >= 4 ) { 
			    $balance = $this->Ticket->Profile->getBalance($this->data['Ticket']['profile_id']);
			    if ( $balance < $ticketValue ) {
					$this->Session->setFlash("ERROR: Su balance (Bs. $balance) "
                        . "es menor a Bs. $ticketValue");
					$this->redirect($this->referer());
				}
			}
            
            //CREATE TICKET MODEL
			$this->Ticket->create();
            
			if ($this->Ticket->saveAll($this->data)) {
				//add to the limits the acum
                //$limitIns->saveByHorse($this->authUser['profile_id'],$this->data['Ticket']['race_id'], $byHorse);
                $limitIns->add($this->authUser['profile_id'],
                                $this->data['Ticket']['race_id'], $byHorse, $limitsNow);
                
                //acciones usuario en linea
                if ( $this->authUser['role_id'] >= 4 ) { 
						//crear asiento
						$account = ClassRegistry::init('Account');
						$account->addMovem(array(
                            'profile_id' => $this->data['Ticket']['profile_id'],
                            'title'      => 'APUESTA',
							'amount'     => $ticketValue,
                            'metainf'    => 'TID: '.$this->Ticket->id
                        ));
                        $balfin = $balance-$ticketValue; 
                        $this->Session->setFlash(" Ticket ".$this->Ticket->id." creado por Bs. ".number_format($ticketValue,0,',','.')."<br>" . 
                                                 " Su saldo restante es de Bs. ".number_format($balfin,0,',','.')."");                        
                  $this->redirect($this->referer());
                }else {
                    //$this->redirect(array('action' => 'print',$this->Ticket->id));
                    $this->redirect(array('action' => 'prntkt',$this->Ticket->id));
                }		
			}else{
				$this->Session->setFlash("ERROR: Ticket NO Creado");
				$this->redirect($this->referer());		
			}
		}
        
        $theDate      = date("Y-m-d");
		$myNatCnf  = $this->Ticket->Profile->getNationalConf($this->authUser['profile_id']);
		$balance   = $this->Ticket->Profile->getBalance($this->authUser['profile_id']);
		$eachUnits = array(1,2,3,4,5,10,20,25,50,100,200,500,1000);
        $nexts     = $this->Ticket->Race->getNextOnes($theDate,$this->authUser['center_id'],15,$myNatCnf);
        $nextones  = array();
        //pr($nexts['races']);
        foreach ($nexts['races'] as $race) {
            $nextones[$race['id']] = $race['race'].'a '.$race['htrack']. ': '. $race['diff'];
         
        }
        //pr($nextones);
		$this->set(compact('raceId','theDate','balance','eachUnits','nextones','myNatCnf'));
    }

    /**
     *
     *  ***  NEW CENTRAL BET!!!
     *
    */

    public function admin_bet()
    {
        $theDate   = date("Y-m-d");
        $myNatCnf  = $this->Ticket->Profile->getNationalConf($this->authUser['profile_id']);
        $balance   = $this->Ticket->Profile->getBalance($this->authUser['profile_id']);
        $eachUnits = array(1,2,3,4,5,10,20,25,50,100,200,500,1000);
        $nexts     = $this->Ticket->Race->getNextOnes(
                        $theDate,
                        1, //$this->authUser['center_id'],
                        15,
                        $myNatCnf);
        $nextones  = array();
        //pr($nexts['races']);
        foreach ($nexts['races'] as $race) {
            $nextones[$race['id']] = $race['race'].'a '.$race['htrack']. ': '. $race['diff'];
         
        }
        //pr($nextones);
        
        $raceId = 0;
        $title_for_layout = 'FOO!!';

        $this->set(compact('theDate','raceId','title_for_layout','balance',
                            'eachUnits','nextones','myNatCnf'));

        $this->render('admin_add');
    }


            
    function admin_taquilla($date = null) 
    {
		if ( $date == null ) {
			$date = date('Y-m-d');
		}
		
		$config   = ClassRegistry::init('Config');
		//$currency = $config->get_unit_value($this->authUser['center_id']);
		$hipod   = ClassRegistry::init('Hipodrome');
        $intls   = $hipod->getIntlIds();
		$unitNac = $config->get_unit_value($this->authUser['center_id']);
		$unitInt = $config->get_unit_value($this->authUser['center_id'],true);
		$hipods  = $this->Ticket->Race->getHorsetracksByDay($date, 
                        1,0,true);

        //new patch online only
        /*
        if ($this->authUser['profile_id'] == 11) {
            $unitNac = $config->get_unit_value($this->authUser['center_id']);
            $unitInt = $config->get_unit_value($this->authUser['center_id'],true);
            $hipods  = $this->Ticket->Race->getHorsetracksByDay($date,1,0,true);
        }
        */

        //new patch
         
		$this->Ticket->recursive = 0;
		$this->Ticket->unbindModel(array('belongsTo'=>array('PlayType')),false);
		
		$this->paginate['conditions'] = array(
			'DATE(created)' => $date,
			'profile_id'    => $this->authUser['profile_id']
		);
		
		$this->paginate['order'] = array('created'=>'DESC');		
		$tickets = $this->paginate();
		$this->set(compact('tickets','date','unitNac','unitInt','intls','hipods'));
	}
    
    function admin_autotaq ( ) 
    {
        $tickets = array();
        $date = date('Y-m-d');
        if ( !empty($this->data) ) {
            //pr($this->data); //die();
            $date = $this->data['Ticket']['date'];
            //AUTOPIN ONLINE
            $online = $this->Ticket->Profile->find('first',array(
                            'conditions' => array('autopin' => $this->data['Ticket']['pin']),
                            'fields'     => array('Profile.id','name') ) );
            
            //pr($online);
            $this->Ticket->unbindModel(array('belongsTo'=>array('PlayType')),false);
		
            $this->paginate['conditions'] = array(
                                            'DATE(created)' => $date,
                                            'profile_id'    => $online['Profile']['id'] ) ;

            $this->paginate['order'] = array('created'=>'DESC');
            $tickets = $this->paginate();
        }
		
		$config  = ClassRegistry::init('Config');
		$hipod   = ClassRegistry::init('Hipodrome');
    $intls   = $hipod->getIntlIds();
		$unitNac = $config->get_unit_value($this->authUser['center_id']);
		$unitInt = $config->get_unit_value($this->authUser['center_id'],true);
		$hipods  = $this->Ticket->Race->getHorsetracksByDay($date,
                        $this->authUser['center_id'],0,true);
         
		
		$this->set(compact('tickets','date','unitNac','unitInt','intls','hipods','online'));
	}
    

    function admin_salestaq ( $since = null, $until = null, 
                                $htrackid = null, $raceid = null)
    {
        $cid     = $this->authUser['center_id'];
        $pid     = $this->authUser['profile_id'];
        $config  = ClassRegistry::init('Config');
        $perct   = $config->getPct($this->authUser['profile_id']);
        $htracks = $this->Ticket->Race->getHtracksByRange($cid, $since, $until);
        
        if ( $since == null ) {
            $since = date('Y-m-d');
            $until = date('Y-m-d');
        }
        
        $races = array();
        
        if ( $since == $until && $htrackid != null) {
            $races = $this->Ticket->Race->find('list',array(
                        'conditions' => array(
                            'race_date'    => $since,
                            'center_id'    => $cid,
                            'hipodrome_id' => $htrackid),
                        'fields' => 'number'));
        }
        
        $totals = $this->Ticket->getTotalSales($since,$until,$cid,$pid,$raceid);
        
        if ( $perct > 0 ) {
            $pctAmo               = $totals['tot']['pay'] * ($perct / 100);
            $totals['tot']['fin'] = $totals['tot']['fin'] + $pctAmo;
        } 
        
        $this->set(compact('totals','since','until','perct','htrackid','raceid',
            'htracks','races'));
    }

    function admin_anulltaq($found = null)
    {
        
        $configInst = ClassRegistry::init('Config');
        $operInst   = ClassRegistry::init('Operation');
        $isOnline   = ($this->authUser['role_id'] == 4);
        if ( !empty($this->data ) ) {
			//pr($this->data); die();
			//BUSQUEDA
			if ( isset ( $this->data['Ticket']['number'] ) ) { 				
				$tid = $this->Ticket->find('first',array(
                            'conditions' => array(
                                'number'     => $this->data['Ticket']['number'],
                                'profile_id' => $this->authUser['profile_id']
                        ),
                            'fields'     => 'id', 
                            'recursive'  => -1
                        ));
				
				if ( !empty ( $tid ) ) {
					$this->redirect(array('action'=>'anulltaq',$tid['Ticket']['id']));
				}else{
					$this->Session->setFlash("Ticket NO encontrado");
					$this->redirect($this->referer());
				}
                
            //ANULACION    
			} else { 
                
                $condTks = array('Ticket.id' => $this->data['Ticket']['id']);
            
                if ( $isOnline == false) {
                    $condTks['confirm'] = $this->data['Ticket']['confirm'];
                }

                $conf = $this->Ticket->find('count',array('conditions' => $condTks));

                if ( $conf != 0 ) {
                    $this->Ticket->updateAll(
                        array('Ticket.enable' => 0),
                        array('Ticket.id' => $this->data['Ticket']['id'])
                    );

                    $num = $this->Ticket->find('first',array(
                                'conditions' => array('Ticket.id' => $this->data['Ticket']['id']),
                                'fields'     => array('number','units','race_id','profile_id')
                            ));
                } else {
                    $this->Session->setFlash("Error en numero de confirmacion.");
                    $this->redirect($this->referer());
                }
                
                
                if ( $this->Ticket->Profile->isOnline($this->authUser['profile_id']) ) {
                    $isIntl      = $this->Ticket->Race->isIntl($num['Ticket']['race_id']);
                    $currency    = $configInst->get_unit_value($this->authUser['center_id'],$isIntl);
                    $ticketValue = $currency * $num['Ticket']['units'];

                    //sumar al balance
                    $this->Ticket->Profile->updateAll(
                        array('balance'    => "balance + $ticketValue"),
                        array('Profile.id' => $num['Ticket']['profile_id'])
                    );

                    //crear asiento
                    $account = ClassRegistry::init('Account');
                    $account->save(array(
                        'profile_id' => $this->authUser['profile_id'],
                        'add'        => 1,
                        'title'      => 'ANULACION',
                        'amount'     => $ticketValue,
                        'metainf'    => 'Anulacion Ticket ID '.$this->data['Ticket']['id']
                    ));
                }
                
				$operInst->ins_op(6,$this->authUser['profile_id'],"Tickets",
                            $this->data['Ticket']['id'],"Ticket Nro ".$num['Ticket']['number']." Anulado por Taquilla");			
			
				$this->Session->setFlash("Ticket ANULADO");
				$this->redirect($this->referer());
			}
			
		}
		
		$ticket = null;
		$pct    = null;
		$money  = null;
		$errLine = "";
        if ( $found != null ) {
            
            //verify if is my ticket
            $tid = $this->Ticket->find('first',array(
                            'conditions' => array(
                                'id'         => $found,
                                'profile_id' => $this->authUser['profile_id']
                        ),
                            'fields'     => 'id', 
                            'recursive'  => -1
                        ));
				
            if ( empty ( $tid ) ) {
                $this->Session->setFlash("Ticket NO es de su taquilla.");
                $this->redirect(array('action' => 'anulltaq'));
            }
            
            //get data of ticket
			$this->Ticket->unbindModel(array('belongsTo'=>array('PlayType','Race','Center')),false);
			$ticket = $this->Ticket->find('first',array(
                        'conditions' => array('Ticket.id' => $found),
                        'recursive'  => 0
                      ));
			
			$pct     = $configInst->get_pct_ticket($this->authUser['center_id']);
			$money   = $configInst->get_unit_value($this->authUser['center_id']);
            $myconfs = $this->Ticket->Profile->find('first',array(
                            'conditions' => array('Profile.id' => $this->authUser['profile_id']),
                            'fields' => array('anull_last')
                        ));
            
            if ( $myconfs['Profile']['anull_last'] == 0 ) {
                $errLine = "Ud. no tiene permitido anular el ultimo ticket.";
            } else {
                $pct    = $configInst->get_pct_ticket($this->authUser['center_id']);
                $money  = $configInst->get_unit_value($this->authUser['center_id']);

                $this->Ticket->unbindModel(array('belongsTo'=>array(
                        'PlayType','Race','Center')),false);

                $ticket = $this->Ticket->find('first',array(
                                    'conditions' => array('Ticket.id' => $found),
                                    'order' => array('Ticket.id'=>'DESC')
                                ));

                $race = $this->Ticket->Race->find('first',array(
                            'conditions' => array('Race.id' => $ticket['Ticket']['race_id']),
                            'fields'     => 'enable'
                        ));

                if ($race['Race']['enable'] == 0) {
                    $errLine = "La carrera ha comenzado, NO se puede anular.";
                }elseif($ticket['Ticket']['enable'] == 0){
                    $errLine = "El ticket ya esta ANULADO, NO se puede anular.";
                }elseif($ticket['Ticket']['payed_status_id'] != 1){
                    $errLine = "El ticket esta PAGADO, NO se puede anular.";
                }
            }            
		}
		
		$this->set(compact('ticket','pct','money','errLine','isOnline'));
    }
    
	function admin_anull_last(){
		
        $isOnline = ($this->authUser['role_id'] == 4);
        
        if(!empty($this->data)){
            
            $condTks = array('Ticket.id' => $this->data['Ticket']['id']);
            
            if ( $isOnline == false) {
                $condTks['confirm'] = $this->data['Ticket']['confirm'];
            }
            
			$conf = $this->Ticket->find('count',array('conditions' => $condTks));
            
			if($conf != 0){
				$this->Ticket->updateAll(
					array('Ticket.enable' => 0),
					array('Ticket.id'=>$this->data['Ticket']['id'])
				);
				
				
				$num = $this->Ticket->find('first',array(
                            'conditions' => array('Ticket.id'=>$this->data['Ticket']['id']),
                            'fields'     => array('number','units','race_id')
                        ));
                
                //monto real del ticket
                $configModel = ClassRegistry::init('Config');
        
                $isIntl      = $this->Ticket->Race->isIntl($num['Ticket']['race_id']);
                
                $currency    = $configModel->get_unit_value(
                                    $this->authUser['center_id'],$isIntl);
        
                $ticketValue = $currency * $num['Ticket']['units'];
                
                //sumar al balance
                $this->Ticket->Profile->updateAll(
                    array('balance'    => "balance + $ticketValue"),
                    array('Profile.id' => $this->authUser['profile_id'])
                );
                
                //crear asiento
                $account = ClassRegistry::init('Account');
                $account->save(array(
                    'profile_id' => $this->authUser['profile_id'],
                    'title'      => 'ANULACION',
                    'amount'     => $ticketValue,
                    'metainf'    => 'Anulacion Ticket ID '.$this->data['Ticket']['id']
                ));
				
				$operInst = ClassRegistry::init('Operation');
				$operInst->ins_op(6,$this->authUser['profile_id'],"Tickets",
                                $this->data['Ticket']['id'],"Ticket Nro ".
                                $num['Ticket']['number']." Anulado");			
			
				$this->Session->setFlash("Ultimo Ticket Anulado");	
			}else{
				$this->Session->setFlash("Error en numero de confirmacion");
			}
			
			$this->redirect($this->referer());
		}
		
		$err_line = "";
		$last_ticket = null;
		$pct = null;
		$money = null;
		
		$myconfs = $this->Ticket->Profile->find('first',array(
			'conditions' => array('Profile.id' => $this->authUser['profile_id']),
			'fields' => array('anull_last')
		));
		
		if($myconfs['Profile']['anull_last'] == 0){
			$err_line = "Ud. no tiene permitido anular el ultimo ticket.";
		}else{
			$config = ClassRegistry::init('Config');
			$pct    = $config->get_pct_ticket($this->authUser['center_id']);
			$money  = $config->get_unit_value($this->authUser['center_id']);
			
			$this->Ticket->unbindModel(array('belongsTo'=>array(
                    'PlayType','Race','Center')),false);
			
            $last_ticket = $this->Ticket->find('first',array(
				'conditions' => array('profile_id' => $this->authUser['profile_id']),
				'order' => array('Ticket.id'=>'DESC')
			));
			
			$race = $this->Ticket->Race->find('first',array(
                        'conditions' => array('Race.id' => $last_ticket['Ticket']['race_id']),
                        'fields'     => 'enable'
                    ));
			
			if ($race['Race']['enable'] == 0) {
				$err_line = "La carrera ha comenzado, NO se puede anular.";
			}elseif($last_ticket['Ticket']['enable'] == 0){
				$err_line = "El ticket ya esta ANULADO, NO se puede anular.";
			}elseif($last_ticket['Ticket']['payed_status_id'] != 1){
				$err_line = "El ticket esta PAGADO, NO se puede anular.";
			}
		}
		
        $this->set(compact('isOnline','pct','money'));
		$this->set('ticket',$last_ticket);
		$this->set('err_line',$err_line);
	}	
	
	function admin_reprint_last(){		
        $errLine = ""; $ticket  = null;
		
        if (! $this->Ticket->Profile->canReprint($this->authUser['profile_id'])) {
            $errLine = "Ud. no tiene permitido reimprimir el ultimo ticket.";
        } else {
			$this->Ticket->unbindModel(array('belongsTo' => array( 
                'PlayType','Race','Center')),false);
            
			$ticket = $this->Ticket->find('first',array(
                            'conditions' => array('profile_id' => $this->authUser['profile_id']),
                            'order'      => array('Ticket.id'=>'DESC') ) );	
		}
        $this->set(compact('ticket','errLine'));
	}
    
	function admin_prntkt($id, $reprint = false, $test = null)
    {
        
        if ( $reprint ) {
            $this->Ticket->updateAll(
                array(
                    'confirm' => "'" . $this->_getConfirm($this->authUser['center_id']) . "'",
                    'copies'  => "(copies + 1)"
                ),
                array('Ticket.id' => $id)
            );
        }
        
		$ticket = $this->Ticket->find('first',array(
                        'conditions' => "Ticket.id = $id", 'recursive' => 0,
                        'fields'     => array(
                            'Ticket.id','number','confirm','created','units','race_id',
                            'play_type_id','PlayType.name','Profile.name','Profile.barcode',
                            'Center.commercial_name','Center.rif','Center.nro_lic','copies'
                        )
                    ));
		
        //pr($ticket);
        //die();
        
		$race = $this->Ticket->Race->find('first',array(
                    'conditions' => array('Race.id' => $ticket['Ticket']['race_id']),
                    'fields'     => array('number','race_date','race_time','Hipodrome.name')
                ));
		
		$ticketHorse = ClassRegistry::init('TicketHorse');
		$picks       = 0;
		
        //PICKS!!
        if ($ticket['Ticket']['play_type_id'] == 19) {
            $pick_details  = $this->Ticket->show_picks($id);	
            $details       = $pick_details['details'];
            $picks         = $pick_details['picks'];

            $race['Hipodrome']['name'] = $pick_details['hipodrome'];
            $race['Race']['number']    = "";
            $race['Race']['race_date'] = "";	
            $race['Race']['race_time'] = "";
            $ticket['Ticket']['type']  = 'PICK';
        }
        
        //SPECIALS EXA TRIF SUP
		if ($ticket['Ticket']['play_type_id'] >= 7) {
			//$details = $ticketHorse->find_specials($id);
            $details = $ticketHorse->findExoticDetails($id);
            $ticket['Ticket']['type']  = 'EXOTIC';	
		}
        
        //BASICS
        if($ticket['Ticket']['play_type_id'] < 7){
		    $details = $ticketHorse->find_basics($id);
            $ticket['Ticket']['type']  = 'BASIC';
		}
        				
		$config = ClassRegistry::init('Config');
		$valid  = $config->get_valid_ticket($this->authUser['center_id']);
		
		$ticketFinal = array(
                    'id'           => $ticket['Ticket']['id'],
                    'number'       => $ticket['Ticket']['number'],
                    'confirm'      => $ticket['Ticket']['confirm'],
                    'created'      => $ticket['Ticket']['created'],
                    'units'        => $ticket['Ticket']['units'],
                    'play_type'    => $ticket['PlayType']['name'],
                    'play_type_id' => $ticket['Ticket']['play_type_id'],
                    'center'       => $ticket['Center']['commercial_name'],
                    'rif'          => $ticket['Center']['rif'],
                    'lic'          => $ticket['Center']['nro_lic'],
                    'profile'      => $ticket['Profile']['name'],
                    'race_number'  => $race['Race']['number'],
                    'race_id'      => $ticket['Ticket']['race_id'],
                    'race_date'    => $race['Race']['race_date'],	
                    'race_time'    => $race['Race']['race_time'],
                    'hipodrome'    => $race['Hipodrome']['name'],
                    'valid'        => $valid,
                    'pick'         => $picks,
                    'barcode'      => $ticket['Profile']['barcode'],
                    'Details'      => $details,
                    'copies'       => $ticket['Ticket']['copies']
                );
		
		//pr($ticket);pr($race);pr($details);pr($ticketFinal);
        //die();
        
		$this->set('ticket',$ticketFinal);
		$this->set('ticketnw',$ticket);
		$this->set(compact('race','details','test'));
		$this->layout = "print_ticket";	
	}
    
	function admin_pay($found = null)
    {
		if(!empty($this->data)){
			//pr($this->data); die();
			$tid = $this->Ticket->find('first',array(
                        'conditions' => array(
                                'number'     => $this->data['Ticket']['number'], 
                                'profile_id' => $this->authUser['profile_id']),
                        'fields'     => array('id','prize','units'), 
                        'recursive'  => -1
                    ));
			
			if(!empty($tid)){
				$this->redirect(array('action'=>'pay',$tid['Ticket']['id']));
			}else{
				$this->Session->setFlash("Ticket NO encontrado");
				$this->redirect($this->referer());
			}
		}
		
		$ticket = null;
		$pct    = null;
		$unit   = null;
		
        if($found != null){
			
            $config  = ClassRegistry::init('Config');
            $hipod   = ClassRegistry::init('Hipodrome');
            
            $this->Ticket->unbindModel(array( 'belongsTo' => 
                    array('PlayType','Center')),false);
			
            $intls  = $hipod->getIntlIds();
            $ticket = $this->Ticket->find('first',array(
                        'conditions' => array('Ticket.id'=>$found),
                        'recursive' => 0
                    ));
			
            if ( in_array($ticket['Race']['hipodrome_id'], $intls) ) {
               $unit = $config->get_unit_value($this->authUser['center_id'],true);                
            } else {
               $unit = $config->get_unit_value($this->authUser['center_id']); 
            }
            
            $pcts  = $config->get_pct_profile($this->authUser['center_id']);	
			
            //pr($pcts);
            
            $pct   = 0;
            
            if(!empty($pcts[$this->authUser['profile_id']])) {
                $pct = $pcts[$this->authUser['profile_id']];
            }
			
		}
        
		$this->set('ticket',$ticket);
		$this->set('pct',$pct);
		$this->set('unit',$unit);
	}
    
    function admin_paybarc()
    {
        
        $configInst = ClassRegistry::init('Config');
        $hipod      = ClassRegistry::init('Hipodrome');
        
		if ( !empty ( $this->data ) ) {
			//pr($this->data);			
			$this->Ticket->unbindModel(array('hasAndBelongsToMany'=>array('Horse')),false);
			$ticket = $this->Ticket->find('first',array(
                        'conditions' => array(
                                'Ticket.id'  => $this->data['Ticket']['barcode'],
                                'profile_id' => $this->authUser['profile_id']),
                        'fields' => array('Ticket.id','prize','units','number',
                                    'confirm','profile_id','created','payed_status_id',
                                        'Race.hipodrome_id'), 
                        'recursive' => 2
                    ));
			
            if ( empty ( $ticket ) ) {
                $this->Session->setFlash("Ticket NO encontrado");
                $this->redirect($this->referer());
            }
            
            //$money = $configInst->get_unit_value($this->authUser['center_id']);
			$intls  = $hipod->getIntlIds();
            
            if ( in_array($ticket['Race']['hipodrome_id'], $intls) ) {
               $money = $configInst->get_unit_value($this->authUser['center_id'],true);                
            } else {
               $money = $configInst->get_unit_value($this->authUser['center_id']); 
            }
            

            //arreglar!!
            $pct  = 0;
			$pcts = $configInst->get_pct_profile($this->authUser['center_id']);	
			//pr($pcts);
            if ( ! empty ( $pcts[$this->authUser['profile_id']] ) ) {
                $pct = $pcts[$this->authUser['profile_id']];
            }
            //pr($ticket);die();
			$this->set(compact('ticket','pct','money'));
		}else{
			$ticket = array();
		}
		
		$this->set('ticket',$ticket);
	}
    
    function admin_newpaybarc()
    {
        
        $configInst = ClassRegistry::init('Config');
        $hipod      = ClassRegistry::init('Hipodrome');
        
		if ( !empty ( $this->data ) ) {
			//pr($this->data);	
            
            if ( strpos ( $this->data['Ticket']['allbarcode'] , '-') ) {
                $parts = explode('-',$this->data['Ticket']['allbarcode']);
            }
            
            if ( strpos ( $this->data['Ticket']['allbarcode'] , "'") ) {
                $parts = explode("'",$this->data['Ticket']['allbarcode']);
            }
                
            //pr($parts);
            //die();
            
            $this->Ticket->unbindModel(array('hasAndBelongsToMany'=>array('Horse')),false);
            
			$ticket = $this->Ticket->find('first',array(
                        'conditions' => array(
                                'Ticket.id'  => $parts[0],
                                'confirm'    => $parts[1],
                                'profile_id' => $this->authUser['profile_id']),
                        'fields' => array('Ticket.id','prize','units','number',
                                    'confirm','profile_id','created','payed_status_id',
                                        'Race.hipodrome_id'), 
                        'recursive' => 2
                    ));
			
            if ( empty ( $ticket ) ) {
                $this->Session->setFlash("Ticket NO encontrado");
                $this->redirect($this->referer());
            }
            
            //$money = $configInst->get_unit_value($this->authUser['center_id']);
			$intls  = $hipod->getIntlIds();
            
            if ( in_array($ticket['Race']['hipodrome_id'], $intls) ) {
               $money = $configInst->get_unit_value($this->authUser['center_id'],true);                
            } else {
               $money = $configInst->get_unit_value($this->authUser['center_id']); 
            }
            

            //arreglar!!
            $pct  = 0;
			$pcts = $configInst->get_pct_profile($this->authUser['center_id']);	
			//pr($pcts);
            if ( ! empty ( $pcts[$this->authUser['profile_id']] ) ) {
                $pct = $pcts[$this->authUser['profile_id']];
            }
            //pr($ticket);die();
			$this->set(compact('ticket','pct','money'));
		} else {
			$ticket = array();
		}
		
		$this->set('ticket',$ticket);
	}
    
    function admin_pay_ticket()
    {
        $operInst = ClassRegistry::init('Operation');
				
		if ( empty ( $this->data ) ) {
			$this->redirect(array('action'=>'admin_pay'));
		} else {
			$ticket = $this->Ticket->find('first',array(
                            'conditions' => array(
                                        'confirm'   => $this->data['Ticket']['confirm'],
                                        'Ticket.id' => $this->data['Ticket']['id']),
                            'fields'     => array('id','number'),
                            'recursive'  => -1
                        ));
			
			if ( ! empty ( $ticket ) ) {
				$this->Ticket->updateAll(
					array(
                            'payed_status_id' => 2 ,
                            'payed_at'        => "'". 
                                                 date('Y-m-d H:i:s') .
                                                 "'"
                    ),
                    array('Ticket.id' => $ticket['Ticket']['id'])
				);
				
				$operInst->ins_op(7,$this->authUser['profile_id'],"Tickets",
                    $ticket['Ticket']['id'],"Ticket Nro ".$ticket['Ticket']['number']." Pagado");			
				
				$this->Session->setFlash("Ticket pagado.");
			}else{
				$this->Session->setFlash("Error en numero de confirmacion.");
			}
			$this->redirect(array('action'=>'admin_add'));
		}			
	}
    
	function admin_sales_taquilla($since = null, $until = null){
		if($since == null){
			$since = date('Y-m-d');
			$until = date('Y-m-d');
		}
		
		$tickets = $this->Ticket->find('first',array(
			'conditions' => array(
				'profile_id' => $this->authUser['profile_id'],'enable' => 1,
				'date(created) BETWEEN ? AND ?' => array($since,$until)
			),
			'fields' => array('count(*) AS co','sum(units) AS un'), 'recursive' => -1
 		));
 		
		$opers = ClassRegistry::init('Operation');
		$mypayed = $opers->find('list',array(
			'conditions' => array(
				'profile_id' => $this->authUser['profile_id'],
				'date(created) BETWEEN ? AND ?' => array($since,$until),
				'operation_type_id'=>7
			), 'fields' => 'model_id'
		));
		
		$payed_mines = $this->Ticket->find('all',array(
			'conditions' => array('Ticket.id' => $mypayed),
			'fields' => array('count(*) AS co','sum(prize) AS pr'),
 			'recursive' => -1
 		));
 		 		
 		$config = ClassRegistry::init('Config');
		$money = $config->get_unit_value($this->authUser['center_id']);
		$pcts = $config->get_pct_profile($this->authUser['center_id']);		
		$pct = 0;
		
		if(!empty($pcts[$this->authUser['profile_id']]))
			$pct = $pcts[$this->authUser['profile_id']];
 		
 		$units_bs = $tickets[0]['un'] * $money;
		$prize_bs = $payed_mines[0][0]['pr'] * $money;
		$premios_pct = $payed_mines[0][0]['pr'] * $pct/100;
		$premios_pct_bs = $premios_pct * $money;
		$utilidad = $tickets[0]['un'] - $payed_mines[0][0]['pr'] + $premios_pct;
		$utilidad_bs = $utilidad * $money;
			
 		$values = array(
 			'tickets' => $tickets[0]['co'],
 			'unidades' => $tickets[0]['un'],
 			'premios' => $payed_mines[0][0]['pr'],
 			'pct' => $pct,
 			'premios_pct' => $premios_pct,
 			'premios_pct_bs' => $premios_pct_bs,
 			'unidades_bs' => $units_bs,
 			'premios_bs' => $prize_bs,
 			'utilidad' => $utilidad,
	 		'utilidad_bs' => $utilidad_bs
 		);
 		
 		$this->set(compact('since','until','values'));
	}
    
    function admin_add_pick(){
		if(!empty($this->data)){
			$this->data['Picks'] = $this->Ticket->make_picks($this->data['Ticket']['picks'],$this->data['Horses']);
			//pr($this->data); die();
			
			$this->data['Ticket']['units'] = count($this->data['Picks']) * $this->data['Ticket']['units'];
			$this->data['Ticket']['prize'] = 0;
			$this->data['Ticket']['play_type_id'] = 19;
			$this->data['Ticket']['confirm']= up(substr(md5(date('h:i:s'.$this->authUser['center_id'])),0,7));
			$this->data['Ticket']['center_id'] = $this->authUser['center_id'];
			$this->data['Ticket']['profile_id'] = $this->authUser['profile_id'];
			$this->data['Ticket']['race_id'] = $this->data['Ticket']['hipodrome_id']; 
			
			$i = 0;
			foreach($this->data['Picks'] as $box => $horses){
				foreach ($horses as $h) {
					$this->data['Horse'][$i]['units'] = 0;
					$this->data['Horse'][$i]['prize'] = 0;
					$this->data['Horse'][$i]['play_type_id'] = 19;
					$this->data['Horse'][$i]['horse_id'] = $h;
					$this->data['Horse'][$i]['box_number'] = $box;	
					$i ++;	
				}
			}
			
			unset($this->data['Horses']);
			unset($this->data['Picks']);
			//pr($this->data);  die();
			
			$this->Ticket->create();
			if ($this->Ticket->saveAll($this->data)) {
				//die("OK");
				$this->Session->setFlash("Ticket Creado");
				$this->redirect(array('action'=>'print',$this->Ticket->id));
			} else {
				$this->Session->setFlash("ERROR: Ticket NO Creado");
			}
			
		}
		
		$today_hips = $this->Ticket->Horse->Race->find('list',array(
			'conditions' => array('race_date' => date("Y-m-d"), 'enable' =>1, 'center_id' => $this->authUser['center_id']),
			'fields' => 'hipodrome_id','group'=>'hipodrome_id'
		));
		
		$cond_hips['id'] = $today_hips;
		
		$myconfs = $this->Ticket->Profile->find('first',array(
			'conditions' => array('Profile.id' => $this->authUser['profile_id']),
			'fields' => array('bet_tracks')
		));
		
		if($myconfs['Profile']['bet_tracks'] == 1) 
			$cond_hips['national'] = 1;
		
		if($myconfs['Profile']['bet_tracks'] == 2) 
			$cond_hips['national'] = 0;
			
		$hipodromes = $this->Ticket->Horse->Race->Hipodrome->find('list',array('conditions'=>$cond_hips));
		$this->set(compact('hipodromes'));
	}
    
      
    /**
     * ==  M U L T I  U S E R    ==> 
     */
    
	function admin_horses_details( $ticket_id )
    {
		$tik = $this->Ticket->find('first',array(
			'conditions' => array('Ticket.id' => $ticket_id),
			'fields' => array(
				'race_id','play_type_id','Race.number','Race.hipodrome_id','PlayType.name'
			), 'recursive' => 0,
		));
		
		$hipo = $this->Ticket->Race->Hipodrome->find('first',array(
			'conditions' => array('id'=>$tik['Race']['hipodrome_id']),
			'fields' => 'name','recursive' => -1
		));
			
		$ticketHorse = ClassRegistry::init('TicketHorse');
		$pick = "";
		if($tik['Ticket']['play_type_id'] >= 7){
			if($tik['Ticket']['play_type_id'] == 19){
				$pkdetails =  $this->Ticket->show_picks($ticket_id,true);	
				$details = $pkdetails['details'];	
				$hipo['Hipodrome']['name'] = $pkdetails['hipodrome'];
				$pick = $pkdetails['picks'];
			}else{
				$details = $ticketHorse->find_specials($ticket_id,true);
                //$details = $ticketHorse->findExoticDetails($ticket_id);
			}	
		}
		else
			$details = $ticketHorse->find_basics($ticket_id,true);
			
        //pr($details);

        $this->set('play_type',$tik['PlayType']['name']);
		$this->set('number',$tik['Race']['number']);
		$this->set('hipodrome',$hipo['Hipodrome']['name']);
		$this->set('details',$details);
		$this->set('pick',$pick);
	}

    public function admin_lines($ticketId, $raceId, $type)
    {
        //
        $hrsTksMod  = new HorsesTicket();
        //
        $lines    = $hrsTksMod->getSpecialDetails($ticketId, $type);
        
        //
        $horseMod = ClassRegistry::init('Horse');
        $horses   = $horseMod->find('list',[
            'conditions' => ['race_id' => $raceId],
            'fields'     => 'number'
        ]);

        //pr($lines);
        
        //die();
        $this->set(compact('lines','horses'));
    }
    
    /**
     * == I N N E R S  ==> 
     */
    
    function _byHorse($horses,$currency)
    {
        $byHorse = array();
        
        foreach ( $horses as $horse ) {
            $amount = $currency * $horse['units'];
            
            if (isset ($byHorse[$horse['horse_id']]) ) {
                $byHorse[$horse['horse_id']] += $amount;
            } else {
                $byHorse[$horse['horse_id']] = $amount;
            }
        }
        
        return $byHorse;
    }
    
    function _validTicketData($data)
    {
        if ( ! ( is_numeric( $data['Ticket']['each'] ) ) ) {
            return "Error en UNIDADES.";
        }
        
        if ( empty($data['Horse'] ) ) {
            return "Error: escoja al menos un Caballo.";
        }
            
        if ( $this->Ticket->Race->verify_started( $data['Ticket']['race_id'] ) ) {
            
            return "Error: Carrera Iniciada o Suspendida.";
        }
        
        if ( $this->authUser['role_id'] == ROLE_AUTO  && empty ( $this->data['Online'] ) ) {
            return "El PIN del Online NO EXISTE.";
        }
        
        /* if ($this->_verfyRestriction ) {
            "Error: Restriccion en carrera, verifique sus permisos hoy."
        }
        //para reemplazar =>
        $rest_ins = ClassRegistry::init('Restriction');
        if($rest_ins->verify_bet($this->data['Ticket']['race_id'],$this->authUser['profile_id'],$this->data['Ticket']['play_type_id'])){
            $this->Session->setFlash();
            $this->redirect(array('action'=>'add'));
        }*/
        return '';
    }
    
    //$this->data
    function _completeData()
    {
        unset($this->data['Seeker']);

        $this->data['Ticket']['center_id']  = $this->authUser['center_id'];
        $this->data['Ticket']['profile_id'] = $this->authUser['profile_id'];
        //AUTOTAQ
        if ( ! empty ( $this->data['Online'] ) && $this->authUser['role_id'] == ROLE_AUTO ) {
            $this->data['Ticket']['profile_id'] = $this->data['Online']['Profile']['id'];
            $this->data['Ticket']['via'] = 'AUTO'; 
        }
        
        // MULTIPLICADOR DE VECES
        $check_al = false;

        if($this->data['Ticket']['play_type_id'] < 7){
            $check_al = true;
            $this->data['Horse'] = $this->Ticket->horse_setter($this->data['Horse'],$this->data['Ticket']['play_type_id']);
        }else{
            $this->data['Horse'] = $this->Ticket->horse_specials($this->data['Horse'],$this->data['Ticket']['play_type_id']);
        }

        // SETEADOR GENERAL 
        if ( !empty ( $this->data['Horse']['Quantity'] ) ) {
            $ticket_units = $this->data['Horse']['Quantity'] * $this->data['Ticket']['each'];
            unset($this->data['Horse']['Quantity']);

            foreach ( $this->data['Horse'] as $hk => $hv ) {
                $this->data['Horse'][$hk]['units'] = $this->data['Ticket']['each'];
                $this->data['Horse'][$hk]['prize'] = 0;
            }
        }else{
            $ticket_units = 0;
            foreach ( $this->data['Horse'] as $hk => $hv ) {
                $this->data['Horse'][$hk]['units'] = $this->data['Ticket']['each'];
                $this->data['Horse'][$hk]['prize'] = 0;
                $ticket_units = $ticket_units + $this->data['Ticket']['each'];
            }
        }
        
        $this->data['Ticket']['units']   = $ticket_units;
        $this->data['Ticket']['confirm'] = $this->_getConfirm($this->authUser['center_id']);
    }
    
    function _getConfirm($cid)
    {
        return up(substr(md5(date('h:i:s' . $cid)),0,7));
    }
    
    
    function _validLimits($ticketValue,$profileId,$raceId,$byHorse,$limitsNow)
    {
        $confIns   = ClassRegistry::init('Config');
        $limitIns  = ClassRegistry::init('Limit');
        $limitsCnf = $confIns->getLimitsProfile($profileId);
        $nowByRace = $limitsNow['Total'] + $ticketValue;
        
        //MAXIMO POR TICKET
        if ( isset($limitsCnf[7]) && $ticketValue > $limitsCnf[7] ) {
            return 'MONTO PROHIBIDO: Maximo Ticket.';
        //MAXIMO POR CARRERAS
        } elseif ( isset( $limitsCnf[8]) && $nowByRace >= $limitsCnf[8] ) {
            return "MONTO PROHIBIDO: Maximo Por CARRERA";
        //MAXIMO POR CABALLOS
        } elseif ( isset( $limitsCnf[9]) && ! empty ($limitsNow['Horses']) ) {
            foreach ($byHorse  as $horseId => $amount ) {
                $nowAmount = $amount;
                
                if ( isset ( $limitsNow['Horses'][$horseId] ) ) {
                    $nowAmount = $amount + $limitsNow['Horses'][$horseId];
                }
                
                if ( $nowAmount >= $limitsCnf[9]) {
                    return "MONTO PROHIBIDO: Maximo Por CABALLO";
                }
            }
        } else {
            return '';
        }

    }
    
    
    /**
     * <==  I N N E R S  ==
     * 
     * 
     *  == T R A S H  ==> 
     */
    
    function admin_pay_nc()
    {
		if(empty($this->data)){
			$this->redirect(array('action'=>'admin_paybarc'));
		}else{
			//pr($this->data); die();
			
			$this->Ticket->updateAll(
				array('payed_status_id' => 2),
				array('Ticket.id' => $this->data['Ticket']['id'])
			);
			
			$num = $this->Ticket->find('first',array(
				'conditions' => array('Ticket.id'=>$this->data['Ticket']['id']),
				'fields' => 'number'
			));
			
			$operInst = ClassRegistry::init('Operation');
			$operInst->ins_op(7,$this->authUser['profile_id'],"Tickets",$this->data['Ticket']['id'],"Ticket Nro ".$num['Ticket']['number']." Pagado por Cod Barra");
			$this->Session->setFlash("Ticket pagado.");
			$this->redirect(array('action'=>'admin_paybarc'));
		}
	}
    
    function admin_print($id, $type = ''){
		$ticket = $this->Ticket->find('first',array(
			'conditions'=>"Ticket.id = $id", 'recursive' => 0,
			'fields' => array(
				'Ticket.id','number','confirm','created','units','race_id','play_type_id',
				'PlayType.name','Profile.name','Profile.barcode',
				'Center.commercial_name','Center.rif','Center.nro_lic'
			)
		));
		
		$race = $this->Ticket->Race->find('first',array(
                    'conditions' => array('Race.id' => $ticket['Ticket']['race_id']),
                    'fields'     => array('number','race_date','race_time','Hipodrome.name')
                ));
		
		$ticketHorse = ClassRegistry::init('TicketHorse');
		$picks       = 0;
		
		if($ticket['Ticket']['play_type_id'] >= 7){
			if($ticket['Ticket']['play_type_id'] == 19){
				$pick_details  = $this->Ticket->show_picks($id);	
				$details       = $pick_details['details'];
				$picks         = $pick_details['picks'];
				
                $race['Hipodrome']['name'] = $pick_details['hipodrome'];
				$race['Race']['number']    = "";
				$race['Race']['race_date'] = "";	
            	$race['Race']['race_time'] = "";
            }else {
                $details = $ticketHorse->find_specials($id);
            }	
		} else {
            $details = $ticketHorse->find_basics($id);
        }
						
		$config = ClassRegistry::init('Config');
		$valid  = $config->get_valid_ticket($this->authUser['center_id']);
		
		$ticketFinal = array(
                    'id'           => $ticket['Ticket']['id'],
                    'number'       => $ticket['Ticket']['number'],
                    'confirm'      => $ticket['Ticket']['confirm'],
                    'created'      => $ticket['Ticket']['created'],
                    'units'        => $ticket['Ticket']['units'],
                    'play_type'    => $ticket['PlayType']['name'],
                    'play_type_id' => $ticket['Ticket']['play_type_id'],
                    'center'       => $ticket['Center']['commercial_name'],
                    'rif'          => $ticket['Center']['rif'],
                    'lic'          => $ticket['Center']['nro_lic'],
                    'profile'      => $ticket['Profile']['name'],
                    'race_number'  => $race['Race']['number'],
                    'race_date'    => $race['Race']['race_date'],	
                    'race_time'    => $race['Race']['race_time'],
                    'hipodrome'    => $race['Hipodrome']['name'],
                    'valid'        => $valid,
                    'pick'         => $picks,
                    'barcode'      => $ticket['Profile']['barcode'],
                    'Details'     => $details
                );
		
		//pr($ticket);pr($race);pr($details);pr($ticketFinal);die();
		
		$this->set('ticket',$ticketFinal);
		$this->set('type',$type);
		$this->layout = "print";	
	}
    
    function admin_create_proof($race){
		$this->Ticket->create();
		$this->Ticket->HorsesTicket->create();
		$horses = $this->Ticket->Horse->find('list',array(
			'conditions'=>array('race_id'=>$race),
			'fields'=> 'Horse.id'
		));
		
		//pr($horses); die();
		
		for($i = 0; $i < 10; $i ++){
			$tosave = array(
				'race_id' => $race,
				'confirm' => 'AAA7777',
				'created' => '2011-10-17 20:55:32',
				'center_id' => 2,
				'profile_id' => 2,
				'units' => rand(10,20),
				'play_type_id' => rand(1,6)
			);
			
			if($this->Ticket->save($tosave)){
				foreach($horses as $h){
					$tos = array(
						'ticket_id' => $this->Ticket->id,
						'horse_id' => $h,
						'play_type_id' => rand(1,3),
						'box_number' => 1,
						'units' => rand(10,20)
					);	
					$this->Ticket->HorsesTicket->save($tos);	
					unset($this->Ticket->HorsesTicket->id);
				}
				
			}else{
				die("errorsirijillo");
			}
			unset($this->Ticket->id);
		}
		die("belleza");
	}
    
    //$date = null, , $racenum = null $htrackid = null
    function OLDadmin_add($race = null) 
    {  
        $confIns  = ClassRegistry::init('Config');
        $limitIns = ClassRegistry::init('Limit');
		//   BEGIN POST PROCESS
		if ( ! empty ( $this->data ) ) {
            
            //AUTOPIN ONLINE
            $this->data['Online'] = $this->Ticket->Profile->find('first',array(
                            'conditions' => array('autopin' => $this->data['Ticket']['autopin']),
                            'fields'     => 'Profile.id' ) );
            
            $validData = $this->_validTicketData($this->data);
            
            if ( $validData  != '' ) {
                $this->Session->setFlash($validData);
				$this->redirect($this->referer());
            }
            
            //Fullfill data
            $this->_completeData();
            //pr($this->authUser); pr($this->data); die();
            
            $isIntl      = $this->Ticket->Race->isIntl($this->data['Ticket']['race_id']);
            $currency    = $confIns->get_unit_value($this->authUser['center_id'], $isIntl);
            $ticketValue = $currency * $this->data['Ticket']['units'];
            $byHorse     = $this->_byHorse($this->data['Horse'],$currency);
            $limitsNow   = $limitIns->getActual($this->data['Ticket']['profile_id'],
                                $this->data['Ticket']['race_id']);
            
            //validate LIMITS new
            $validLimit = $this->_validLimits($ticketValue, $this->data['Ticket']['profile_id'],
                            $this->data['Ticket']['race_id'], $byHorse, $limitsNow);
            
            if ($validLimit  != '' ) {
                $this->Session->setFlash($validLimit);
				$this->redirect($this->referer());
            }
            
            //revisar el balance del user en linea
            if ( $this->authUser['role_id'] >= 4 ) { 
			    $balance = $this->Ticket->Profile->getBalance($this->data['Ticket']['profile_id']);
			    if ( $balance < $ticketValue ) {
					$this->Session->setFlash("ERROR: Su balance (Bs. $balance) "
                        . "es menor a Bs. $ticketValue");
					$this->redirect($this->referer());
				}
			}
            
            //CREATE TICKET MODEL
			$this->Ticket->create();
            
			if ($this->Ticket->saveAll($this->data)) {
				//add to the limits the acum
                //$limitIns->saveByHorse($this->authUser['profile_id'],$this->data['Ticket']['race_id'], $byHorse);
                $limitIns->add($this->authUser['profile_id'],
                                $this->data['Ticket']['race_id'], $byHorse, $limitsNow);
                
                //acciones usuario en linea
                if ( $this->authUser['role_id'] >= 4 ) { 
						//crear asiento
						$account = ClassRegistry::init('Account');
						$account->addMovem(array(
                            'profile_id' => $this->data['Ticket']['profile_id'],
                            'title'      => 'APUESTA',
							'amount'     => $ticketValue,
                            'metainf'    => 'TID: '.$this->Ticket->id
                        ));
                        $this->Session->setFlash("Ticket ".$this->Ticket->id." Creado");
						$this->redirect($this->referer());
                }else {
                    //$this->redirect(array('action' => 'print',$this->Ticket->id));
                    $this->redirect(array('action' => 'prntkt',$this->Ticket->id));
                }		
			}else{
				$this->Session->setFlash("ERROR: Ticket NO Creado");
				die();
				//$this->redirect($this->referer());		
			}
		
		}
		
		
		//---
		//    END POST PROCESS
		//-------------------
		
		//-------------------
		//   BEGIN GET PROCESS
		//---
		
		// ==> $htrackid = null, $racenum = null
		
		$theDate      = date("Y-m-d");
		$preSelHtrack = 0;
		$preSelRace   = 0;
		$reason       = '';
				
		//get national config profile
		$myNatCnf   = $this->Ticket->Profile->getNationalConf($this->authUser['profile_id']);
				
		//get available horsetracks by date, center and config.
		$hipodromes = $this->Ticket->Horse->Race->getHorsetracksByDay(
							$theDate, 
							$this->authUser['center_id'], 
							$myNatCnf,
                            false, // only available
                            true   //counter
						);
		
        
		if ($htrackid != null) { // && $racenum != null
			
			//GET RACE BY LINK PARAMS
			if (in_array($htrackid, array_keys($hipodromes))) {
				
                $preSelHtrack = $htrackid;
				$reason       = 'link';
                
                //find nearest race and its htrack
                $nearestRace = $this->Ticket->Race->getNearest(
                                                $this->authUser['center_id'], 
                                                $theDate,  
                                                $htrackid
                               );

                if (!empty($nearestRace)) {
                    $preSelRace   = $nearestRace['Race']['id'];
                }
                
                /*
				$raceByLink = $this->Ticket->Horse->Race->findByLink(
									$this->authUser['center_id'], 
									$theDate, 
									$this->hourInfo['alternate'], 
									$htrackid, 
									$racenum
							  );
                              
				if (!empty($raceByLink)) {
					$preSelHtrack = $htrackid;
					$preSelRace   = $raceByLink['Race']['id'];
					$reason       = 'link';
				}
                */
			}
			
		} else {
				
			//find nearest race and its htrack
			$nearestRace = $this->Ticket->Race->getNearest(
											$this->authUser['center_id'], 
											$theDate, 
											array_keys($hipodromes)
					       );
			
			if (!empty($nearestRace)) {
				$preSelHtrack = $nearestRace['Race']['hipodrome_id'];
				$preSelRace   = $nearestRace['Race']['id'];
				$reason       = 'near';
			}
		}
		
        //pr($hipodromes);
        
        $balance   = $this->Ticket->Profile->getBalance($this->authUser['profile_id']);
		    $eachUnits = array(1,2,3,4,5,10,20,25,50,100,200,500,1000);
        $nexts     = $this->Ticket->Race->getNextOnes($theDate,$this->authUser['center_id'],15);
        $nextones  = array();
        //pr($nexts['races']);
        foreach ($nexts['races'] as $race) {
            $nextones[$race['id']] = $race['race'].'a '.$race['htrack']. ': '. $race['diff'];
            /*
                [id] => 1317
                [race] => 1
                [time] => 12:00 AM
                [diff] => 0m
                [htrack] => Albuquerque
                [ptime] => 0
             */
        }
        //pr($nextones);
		$this->set(compact('hipodromes','theDate','preSelHtrack','preSelRace',
                           'balance' ,'reason','eachUnits','nextones'));
	}
    
}