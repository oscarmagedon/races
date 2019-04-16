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

    /**
    $this->proservBase = https://proservice-bets.drf.com/proservice/superBets/
    results/track/TP/
    country/USA/
    date/03-16-2019/
    race/3
    */
    public function createProserviceResultsUrl($date, $raceNum, $trackId, $country)
    {
        $byRaceUrl  = $this->proservBase;
        $byRaceUrl .= 'results/track/' . $trackId;
        $byRaceUrl .= '/country/' . $country;
        $byRaceUrl .= '/date/' . date('m-d-Y',strtotime($date));
        $byRaceUrl .= '/race/' . $raceNum;

        return $byRaceUrl; 
    
    }

    /*

    */
    public function createBovadaUrl($nickBovada)
    {
        return "https://horses.bovada.lv/services/sports/" .
                    "event/v2/events/B/description/horse-racing/".
                    $nickBovada;
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