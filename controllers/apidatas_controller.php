<?php

App::Import('Helper','Time');

class ApidatasController extends AppController {

    public $name = 'Apidatas';
    
    public function beforeFilter(){
        parent::beforeFilter();

        $this->Authed->allow([
            //'proservtracks',
            //'proservbytrack',
            //'deletebytrack','proresults', 'saveresults'
        ]);
    }


    public function isAuthorized()
    {

        $ret = true;
        
        $actions_root = [
            'admin_proservtracks',

            'admin_addbytrack',

            'admin_nextones',

            'admin_deletebytrack',
            'admin_proservbytrack',
            'admin_bovada',
            'admin_proresults','admin_saveresults','admin_resetrace'
        ];
        

        if($this->isRoot() && in_array($this->action, $actions_root)){
            $ret = true;
        }

        return $ret;
    }

    public function admin_index()
    {

        $this->pageTitle = 'API Services';
    }
    
    /**
     *
     */ 
    public function admin_proservtracks($dbDate = null, $fullMode = 0)
    {   
        //Y-m-d
        $dbDate     = ($dbDate!=null)?$dbDate:date('Y-m-d');
        $protracks  = ClassRegistry::init('Protracks');
        $raceModel  = ClassRegistry::init('Race');
        $proFields  = $protracks->tracksFields;
        
        $usaDate = $protracks::getUsaDate($dbDate);

        $proserviceTracks = $protracks->getInfoTracks($usaDate);

        $trackIds = $protracks->getTracksIds();
       
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
        $this->pageTitle = 'Tracks Proservice';
        
        $this->set(compact('dbDate','usaDate','fullMode','proserviceTracks',
            'proFields','trackIds','racesNick','title_for_layout'));
    }

    public function admin_addbytrack()
    {

    }

    public function admin_nextones()
    {

    }

    /**
        SAVE RACES 
    */
    public function admin_proservbytrack($trackId, $country, $dayEve) 
    {
        
        $raceApi  = ClassRegistry::init('Prorace');

        $infoTrack = $raceApi->exploreTrack($trackId, $country, $dayEve, 20);
        //echo json_encode($infoTrack);die();

        $message = (count($infoTrack) - 1) . ' races saved.';

        $this->Session->setFlash($message);
        $this->redirect($this->referer());        
    }

    public function admin_deletebytrack($trackId)
    {
        $prorace = ClassRegistry::init('Prorace');

        $message = $prorace->deleteByNick($trackId);

        $this->Session->setFlash($message);

        $this->redirect($this->referer());   
    }
    
    /**
        LOgged filter by htrack and date
        
        I have to split this in 2 different functions

    */
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


    //test by raceId function 
    public function admin_saveresults($raceId, $date, $nick,$number)
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

    public function admin_resetrace($raceId)
    {
        $proresult = ClassRegistry::init('Proresult');

        $results   = $proresult->resetRace($raceId);

        $this->Session->setFlash('Carrera ID ' .$raceId .' restaurada.');

        $this->redirect($this->referer());  
    }


    /*
        Become this a BovadaLog and send it to a small 
        view with the style created
    */

    public function admin_bovada($bovadaNick)
    {
        $bovadaMod = ClassRegistry::init('Bovada');  
        
        $bovadaLog = $bovadaMod->getAllInfo($bovadaNick);

        $urlCheck  = $bovadaMod->createBovadaUrl($bovadaNick);        

        $this->pageTitle = 'Bovada all races';        

        $this->set(compact('bovadaLog','urlCheck'));
    }

    public function admin_bovadarace($bovadaNick, $raceNumber, $raceId)
    {
        $bovadaMod = ClassRegistry::init('Bovada');  
        
        $raceLog   = $bovadaMod->getByRace($bovadaNick, $raceNumber, $raceId);

        pr($raceLog);
        die();

        //if racenotfound
            //set raceid suspended and log operation

        //else
        //check race retires
        //compare and add the new ones
        //if retires new were added add operations

        //$this->pageTitle = 'Bovada all races';

        //$this->set(compact('bovadaLog','urlCheck'));
    }
}
