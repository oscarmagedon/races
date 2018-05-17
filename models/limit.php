<?php
class Limit extends AppModel {

    var $name      = 'Limit';

    //var $belongsTo = array('Race');

    function getActual($profileId, $raceId)
    {
        $allLimits = array('Horses' => array(),'Total' => 0);
        
        $myLimit = $this->find('all',array(
                        'conditions' => array(
                                'profile_id' => $profileId,
                                'race_id'    => $raceId
                        )
                   ));
        
        foreach ($myLimit as $ml) {
            $allLimits['Horses'][$ml['Limit']['horse_id']] = $ml['Limit']['amount'];
            $allLimits['Total'] += $ml['Limit']['amount'];
        }
        
        return $allLimits;
    }
    
    function getActualByHorses ($profileId, $raceId, $horses)
    {
        $date = date('Y-m-d');
        
        return $this->find('first',array(
                        'conditions' => array(
                                'date'       => $date,
                                'profile_id' => $profileId,
                                'race_id'    => $raceId,
                                'horse_id'   => $horses
                        )
                   ));
    }
    
    function saveByHorse($profileId, $raceId, $horses)
    {
        $myLimits = $this->getActualByHorses($profileId, $raceId,array_keys($horses));
        
        if (empty ($myLimits) ) {
            
            foreach ($horses as $horseId => $horseAmo) {
                
                $this->save(array('date' => date('Y-m-d'), 'profile_id' => $profileId, 
                                'race_id' => $raceId , 'horse_id' => $horseId, 
                                'amount' => $horseAmo));
            
                unset($this->id);
            }
        } else {
            $this->updateAll(
                array('amount'   => "(amount + $amount)"),
                array('Limit.id' => $myLimit['Limit']['id'])
            );
        }
        
    }
    
    //Check and add limit
    function add($profileId, $raceId, $byHorse, $limitsNow)
    {
        $limitHorses = array_keys($limitsNow['Horses']);
        
        foreach ( $byHorse as $horseId => $amount ) {
            if ( in_array($horseId,$limitHorses) ) {
                //if ticket horse has limit
                $this->updateAll(
                    array('amount'   => "(amount + $amount)"),
                    array('horse_id' => $horseId, 'profile_id' => $profileId)
                );
            } else {
                //a new record
                $this->save(array(
                    'profile_id' => $profileId, 'race_id' => $raceId, 
                    'horse_id'   => $horseId,   'amount'  => $amount
                ));
            }
            unset($this->id);
        }
        
        /*echo "PROF: $profileId";
        echo "-- RACE: $raceId";
        echo "<br>--THIS HORSES-";
        pr($byHorse);
        echo "-LIMITS NOW-";
        pr($limitsNow);
        echo "-LIMITS NOW-";
        $limNow = $this->getActual($profileId, $raceId);
        pr($limNow);
        die('on limit model');*/
    }

    function saveByRace($profileId, $raceId, $amount)
    {
        $myLimit = $this->getActual($profileId, $raceId);
        
        if (empty ($myLimit) ) {
            $this->save(array('date' => date('Y-m-d'), 'profile_id' => $profileId, 
                'race_id' => $raceId ,'amount' => $amount));
        } else {
            $this->updateAll(
                array('amount'   => "(amount + $amount)"),
                array('Limit.id' => $myLimit['Limit']['id'])
            );
        }
        
    }
    
}
?>