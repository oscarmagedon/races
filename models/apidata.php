<?php
App::Import('Helper','Time');

class Apidata extends AppModel {

	var $name         = 'Apidata',

		$useTable     = false,

	    $proservBase  = "https://proservice-bets.drf.com/proservice/superBets/",

    	$paramTracks  = "entries?drfpro",

    	$paramByRace  = "liveOdds/trackPools/",

   		$racesParams =  [
    						'currentRace',
    						'trackId',
    						'country',
    						'dayEvening'
    					];

    public function createProserviceRaceUrl($raceNum, $trackId, $country, $dayEve)
	{
		$byRaceUrl  = $this->proservBase . $this->paramByRace;
		$byRaceUrl .= 'currentRace/' .$raceNum;
		$byRaceUrl .= '/trackId/' .$trackId;
		$byRaceUrl .= '/country/' .$country;
		$byRaceUrl .= '/dayEvening/' .$dayEve;

		return $byRaceUrl; 
	
	}

	public static function getUsaDate($dbDate)
    {
    	$timeClass = ClassRegistry::init('TimeHelper');

    	return $timeClass->format('m-d-Y', $dbDate);
    }

    public static function getDbDate($date)
    {
    	$timeClass = ClassRegistry::init('TimeHelper');

    	return $timeClass->format('Y-m-d', $date);
    }

}