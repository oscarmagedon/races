<?php

App::Import('Helper','Time');

class ApidatasController extends AppController {

	public $name = 'Apidatas';
	
	public function beforeFilter(){
		parent::beforeFilter();

		$this->Authed->allow([
			'proservtracks',
			'proservbytrack',
			'deletebytrack'
		]);
	}
	
	/**
	 *
	 */	
	public function proservtracks($dbDate = null, $fullMode = 0)
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
        //pr($racesNick);
       	//$this->set('title_for_layout','Proservice Tracks');

       	$title_for_layout = 'Proservice Tracks';

        $this->set(compact('dbDate','usaDate','fullMode','proserviceTracks',
        	'proFields','trackIds','racesNick','title_for_layout'));
	}

	public function proservbytrack($trackId, $country, $dayEve) {
        
        $raceApi  = ClassRegistry::init('Prorace');

        $infoTrack = $raceApi->exploreTrack($trackId, $country, $dayEve, 20);
        //echo json_encode($infoTrack);die();

        $message = (count($infoTrack) - 1) . ' races saved.';

        $this->Session->setFlash($message);
        $this->redirect($this->referer());        
    }

    public function deletebytrack($trackId)
    {
    	$prorace = ClassRegistry::init('Prorace');

    	$message = $prorace->deleteByNick($trackId);

        $this->Session->setFlash($message);

        $this->redirect($this->referer());   
    }
}
