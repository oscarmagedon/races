<?php

App::Import('Model','Apidata');
App::Import('Helper','Time');

class Protracks extends Apidata {

	var $name         = 'Protracks',
	
		$tracksFields = [
    						'dayEvening',
    						'country',
    						'postTime',
    						'trackId',
    						'trackName',
    						'currentRace'
    					];

	public function getInfoTracks($usaDate) //$dbDate old
	{
		/*
		$timeClass = ClassRegistry::init('TimeHelper');
		//Y-m-d
		$dbDate  = ($dbDate!=null)?$dbDate:date('Y-m-d');
		//
		$usaDate = $timeClass->format('m-d-Y', $dbDate);
		*/
		$fullUrl = $this->proservBase . $this->paramTracks;

		$jsonResponse = file_get_contents($fullUrl);

		return $this->normalTracks($jsonResponse);
	}

	public function getTracksIds($country = "USA")
	{
		$fullUrl    = $this->proservBase . $this->paramTracks;
		$jsonTracks = file_get_contents($fullUrl);
		$fullInfo   = json_decode($jsonTracks);
		$trackIds   = [];

        foreach ($fullInfo->data->trackList as $track) {
            if ($country == $track->country) {
	            if(!isset($trackIds[$track->trackId])) {
	                //array_push($trackIds, $track->trackId);
	            	$trackIds[$track->trackId] = [
	            			'country' => $track->country,
	            			'dayEve'  => $track->dayEvening
	            	];
	            }	
            }
        }

        return $trackIds;
	}
	public function normalTracks($jsonTracks)
	{
		$fullInfo = json_decode($jsonTracks);

		$filteredTracks = [];

		foreach ($fullInfo->data->trackList as $track) {
			
			$trkFields = array();
			
			foreach ($this->tracksFields as $field) {
				$trkFields[$field] = $track->$field;	
			}

			$trkFields['RaceLink'] = $this->createProserviceRaceUrl(
											$track->currentRace,
											$track->trackId,
											$track->country,
											$track->dayEvening
										);

			$filteredTracks[] = $trkFields;
		}

		$fullServiceInfo = [
								'Tracks'  => $filteredTracks,
								'DateNow' => $fullInfo->data->todaysDate,
								'TimeNow' => $fullInfo->data->currentTime,
								'NextDs'  => $fullInfo->data->dates
							];

		return $fullServiceInfo;
	}

	/**
        ** PROSERVICE Usefull indexes ::

        postTime
        trackId
        trackName
        country
        currentRace
		dayEvening

        ...
        
        :: Full response trackslist ::
		
		- trackList => tracks!
		- dates
		- currentTime
		- todaysDate


		:: Response order to race ::
		
		- currentRace
		- trackId
		- country
		- dayEvening

        "data": {
            "trackList": [
              {
                "postTime": "12:35 PM",
                "postTimeDisplay": "12:35 PM",
                "trackId": "GP",
                "trackName": "Gulfstream Park",
                "country": "USA",
                "currentRace": 2,
                "dayEvening": "D",
                "mtp": 0,
                "mtpDisplay": "0",
                "distanceDescription": "6 Furlongs",
                "raceTypeDescription": "ALLOWANCE OPTIONAL CLAIMING",
                "minClaimPrice": 12500,
                "minClaimPriceDisplay": "12500",
                "ageRestrictionDescription": "4 Year Olds And Up",
                "sexRestrictionDescription": "Open",
                "surface": "Dirt",
                "purse": 44000,
                "purseDisplay": "44000",
                "isLive": true,
                "raceOffer": null,
                "isFeatured": true,
                "isHarness": false,
                "breed": "Thoroughbred",
                "cardOver": false,
                "currentRaceCancelled": false
              },
        }
    */
}