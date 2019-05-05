<?php

App::Import('Helper','Time');

class ApidatasController extends AppController {

    public $name = 'Apidatas';
    
    public function beforeFilter(){
        parent::beforeFilter();

        $this->Authed->allow([
            'checkraces',
            'checkresults',
            'checkbovada'
        ]);
    }

    public function isAuthorized()
    {
        $ret = false;

        $actions_root = [
            'admin_index',
            'admin_addbytrack',
            'admin_nextones',
            'admin_deletebytrack',
            'admin_proservbytrack',
            'admin_bovada',
            'admin_bovadarace',
            'admin_proresults',
            'admin_saveresults',
            'admin_resetrace'
        ];     

        if($this->isRoot() && in_array($this->action, $actions_root)){
            $ret = true;
        }

        return $ret;
    }


    public function checkraces()
    {
        $raceMod   = ClassRegistry::init('Race');
        $protracks = ClassRegistry::init('Protracks');
        $racePro   = ClassRegistry::init('Prorace');
        $dbDate    = date('Y-m-d');
        $trackPros = $protracks->getTracksIds();

        $masterRaces = $raceMod->find('all',
            [
                'conditions' => [
                    'race_date' => $dbDate,
                    'center_id' => 1
                ],
                'fields'     => [
                    'count(*) as co','Hipodrome.id',
                    'Hipodrome.nick'],
                'group'      => 'hipodrome_id',
                'recursive'  => 1
            ]
        );

        $nicksList = [];
        foreach ($masterRaces as $race) {
            array_push($nicksList, $race['Hipodrome']['nick']);
        }
        //nicks to save
        $nicksAva = [];
        foreach ($trackPros as $proNick => $vals) {
            if ( !in_array($proNick, $nicksList)) {
                $nicksAva [] = [
                    'nick'    => $proNick,
                    'dayEve'  => $vals['dayEve'],
                    'country' => $vals['country'],
                    'LOG'     => $racePro->exploreTrack(
                        $proNick, 
                        $vals['country'], 
                        $vals['dayEve']
                    )
                ];
            }
            
        }
        //pr($nicksAva);
        die();
    }

    public function checkresults()
    {
        $raceMod   = ClassRegistry::init('Race');
        $proresult = ClassRegistry::init('Proresult');
        $dbDate    = date('Y-m-d');

        $nextRaces = $raceMod->find('all',
            [
                'conditions' => [
                    'race_date'    => $dbDate,
                    'center_id'    => 1,
                    'ended'        => 0,
                    'local_time <' => date('H:i:s')
                ],
                'fields' => [
                    'Race.id','Race.number','Race.local_time',
                    'Hipodrome.id','Hipodrome.name','Hipodrome.nick'
                ],
                'order' => ['local_time' => 'ASC']
            ]
        );
        
        foreach ( $nextRaces as $nrk => $race ) {
            $nextRaces[$nrk]['Result'] = $proresult->saveResults(
                $race['Race']['id'], 
                $dbDate, 
                $race['Hipodrome']['nick'], 
                $race['Race']['number']
            );
        }
        
        //pr($nextRaces);
        die();
    }

    public function checkbovada ()
    {
        $raceMod   = ClassRegistry::init('Race');
        $bovadaMod = ClassRegistry::init('Bovada');
        $dbDate    = date('Y-m-d');

        $nextRaces = $raceMod->find('all',
            [
                'conditions' => [
                    'race_date'    => $dbDate,
                    'center_id'    => 1,
                    'Race.enable'  => 1
                ],
                'fields' => [
                    'Race.id','Race.number','Race.local_time',
                    'Hipodrome.id','Hipodrome.name',
                    'Hipodrome.nick','Hipodrome.bovada'
                ],
                'order' => ['local_time' => 'ASC'],
                'limit' => 30
            ]
        );

        foreach ( $nextRaces as $nrk => $race ) {
            
            if ($race['Hipodrome']['bovada'] != '') {
                
                $nextRaces[$nrk]['Retires'] = $bovadaMod->getByRace(
                    $race['Hipodrome']['bovada'], 
                    $race['Race']['number'], 
                    $race['Race']['id']
                );    
                
            } else {
                $nextRaces[$nrk]['Retires'] = 'No nick';
            }
            
        }

        pr($nextRaces);
        die();
    }

    /*
    
        ADMIN MODULES
    
    */

    public function admin_index()
    {

        $this->pageTitle = 'API Services';
    }

    //check only
    public function admin_nextones()
    {
        $protracks  = ClassRegistry::init('Protracks');
        $raceModel  = ClassRegistry::init('Race');
        $proFields  = $protracks->tracksFields;
        $dbDate     = date('Y-m-d');
        $usaDate    = $protracks::getUsaDate($dbDate);

        $proTracks  = $protracks->getInfoTracks($usaDate);
        $trackIds   = $protracks->getTracksIds();

        $this->pageTitle = 'All Proservice today';
        
        $this->set(compact('dbDate','usaDate','proTracks',
            'proFields','trackIds'));
    }

    //check and click-to-save
    public function admin_addbytrack()
    {
        $protracks  = ClassRegistry::init('Protracks');
        $raceModel  = ClassRegistry::init('Race');
        $proFields  = $protracks->tracksFields;
        $dbDate     = date('Y-m-d');
        $usaDate    = $protracks::getUsaDate($dbDate);

        //$proTracks  = $protracks->getInfoTracks($usaDate);
        $trackIds   = $protracks->getTracksIds();
        
        $masterRaces = $raceModel->find('all',
            array(
                'conditions' => [
                    'race_date' => $dbDate,
                    'center_id' => 1
                ],
                'fields'     => array(
                    'count(*) as co','Hipodrome.id',
                    'Hipodrome.nick'),
                'group'      => 'hipodrome_id',
                'recursive'  => 1
            )
        );

        $racesNick = [];
        foreach ($masterRaces as $master) {
            $racesNick[$master['Hipodrome']['nick']] = [
                'id'    => $master['Hipodrome']['id'],
                'races' => $master[0]['co']
            ];
        }
        
        $this->pageTitle = 'All Proservice today';
        
        $this->set(compact('dbDate','usaDate','proTracks',
            'proFields','trackIds','racesNick'));
    }

    /** RESULTS AND CLOSE*/
    public function admin_proresults($date = null, $htrack = null)
    {   
        $proresult = ClassRegistry::init('Proresult');
        $raceMod   = ClassRegistry::init('Race');

        if ($date == null) {
            $date = date('Y-m-d');
        }

        $htracks   = $proresult->getHtracks($date, $this->authUser['center_id']);

        //pr($htracks);

        $bovadaCheck = "";
        
        $racesLog    = [];

        if ( $htrack !== null ) {
            
            //$nextRaces = $proresult->getNextRaces($date, 15);
            
            $nextRaces = $raceMod->find('all',[
                'conditions' => [
                    'hipodrome_id' => $htrack,
                    'center_id'    => 1,
                    'race_date'    => $date

                ]
            ]);

            foreach ($nextRaces as $race) {
                $racesLog[] = [
                    'Info'   => $race,
                    'ProURL' => $proresult->createProserviceResultsUrl(
                                    $date, 
                                    $race['Race']['number'], 
                                    $race['Hipodrome']['nick'], 
                                    'USA'),
                    'ProRace' => $proresult->createProserviceRaceUrl(
                                $race['Race']['number'], 
                                $race['Hipodrome']['nick'], 
                                'USA', 
                                'D'),
                    'Bovada' => '#'
                ]; 
            } 

            if (!empty($nextRaces))
                $bovadaCheck = $race['Hipodrome']['bovada'];          
        }
        //pr($racesLog);
        //$protracks = ClassRegistry::init('Protrack');

        $usaDate = $proresult::getUsaDate($date);

        $this->pageTitle = 'Adm-Results Proservice';

        $this->set(compact('date','usaDate', 'htracks', 'htrack', 'racesLog','bovadaCheck'));
    }

    //SAVE RACES AND REDIRECT
    public function admin_proservbytrack($trackId, $country, $dayEve) 
    {
        
        $raceApi  = ClassRegistry::init('Prorace');

        $infoTrack = $raceApi->exploreTrack($trackId, $country, $dayEve, 20);
        //echo json_encode($infoTrack);die();

        $message = (count($infoTrack) - 1) . ' races saved.';

        $this->Session->setFlash($message);
        $this->redirect($this->referer());        
    }

    //DELETE RACES BY NICK AND REDIRECT
    public function admin_deletebytrack($trackId)
    {
        $prorace = ClassRegistry::init('Prorace');

        $message = $prorace->deleteByNick($trackId);

        $this->Session->setFlash($message);

        $this->redirect($this->referer());   
    }
    
    //TRY TO SAVES RESULTS AND REDIRECT 
    public function admin_saveresults($raceId, $date, $nick, $number)
    {   
        if ($date == null) {
            $date = date('Y-m-d');
        }

        $proresult = ClassRegistry::init('Proresult');

        $results   = $proresult->saveResults($raceId, $date, $nick, $number);
        //pr($results);
        //die();        
        $this->Session->setFlash('Resultados en Carrera ID ' .$raceId);

        $this->redirect($this->referer());  
    }

    // NOT YET WORKING bUT REDIREct
    public function admin_resetrace($raceId)
    {
        $proresult = ClassRegistry::init('Proresult');

        $results   = $proresult->resetRace($raceId);

        $this->Session->setFlash('Carrera ID ' .$raceId .' restaurada.');

        $this->redirect($this->referer());  
    }

    //bovada all check
    public function admin_bovada($bovadaNick)
    {
        if ( $bovadaNick != '' ) {
            $bovadaMod = ClassRegistry::init('Bovada');  
            $bovadaLog = $bovadaMod->getAllInfo($bovadaNick);
            $urlCheck  = $bovadaMod->createBovadaUrl($bovadaNick);     
            $this->pageTitle = 'Bovada all races';
        }               

        $this->set(compact('bovadaLog','urlCheck'));
    }

    //checks bovada race and returns!
    public function admin_bovadarace($bovadaNick, $raceNumber, $raceId)
    {
        $bovadaMod = ClassRegistry::init('Bovada');  
        $raceLog   = $bovadaMod->getByRace($bovadaNick, $raceNumber, $raceId);

        pr($raceLog);
        die();
        //$this->Session->setFlash('Bovada retiros carr. ID ' .$raceId);
        //$this->redirect($this->referer());  
    }
}