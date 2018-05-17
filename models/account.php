<?php

class Account extends AppModel {

	var $name      = 'Account',
        $belongsTo = array('Profile'),
        $titles    = array('ANULACION','APUESTA','PREMIO','RECARGA','REVERSO','RETIRO');
    
    public function getTitles( $single = 0 )
    {
        $all = array();
        foreach ( $this->titles as $tl ) {
            $all[$tl] = $tl;
        }
        if ( $single == 1 ) {
            unset($all['ANULACION']);unset($all['APUESTA']);unset($all['PREMIO']);
        }
        return $all;
    }
    
    /* SETTERS */
    
    public function addMovem($data)
    {
        //consultar el balance before con la funcion
        $balance = $this->Profile->getBalance($data['profile_id']);
        $data['balance'] = $balance;
        //agregar el movimiento
        $data['add'] = 0;
        if ( $data['title'] == 'PREMIO' || $data['title'] == 'RECARGA' 
             || $data['title'] == 'ANULACION' ) {
            $data['add'] = 1;
        }
        /*
        if ( $data['add'] == 1 && strpos($data['metainf'],'Anulacion') === FALSE ) {
            $data['metainf'] = "Recarga. ". $data['metainf']; 
        }*/
        $this->create();
        $saverr = "Movimiento NO Guardado. Intente de nuevo.";
        if ( $this->save( $data ) ) {
            $saverr = "Movimiento Guardado";
            //mover balance
            $this->Profile->moveBalance($data);
        }
        return $saverr;
    }

    /*
     * $account->save(array(
            'profile_id' => $this->authUser['profile_id'],
            'add'        => 0,
            'amount'     => $ticketValue,
            'metainf'    => 'Ticket ID '.$this->Ticket->id
        ));

        //restar del balance
        $this->Ticket->Profile->updateAll(
            array('balance' => "balance - ".$ticketValue),
            array('Profile.id'=>$this->authUser['profile_id'])
        );
     */
    
    public function getTotals($pid,$since,$until,$type = 0)
    {
        $conds = array('date(created) BETWEEN ? AND ?'=>array($since,$until),'profile_id' => $pid);
        
        if ( $type == 1) {
            $conds['add'] = 0;
        }
        
        if ( $type == 2) {
            $conds['add'] = 1;
            $conds['metainf LIKE'] = "%Ganador%";
        }
        
        if ( $type == 3) {
            $conds['add'] = 1;
            $conds['metainf NOT LIKE'] = "%Ganador%";
            //$conds['metainf'] = array("%Recarga%","%Anulacion%" );
        }
        
        $dt = $this->find('first',array('conditions'=>$conds,'fields'=>'sum(amount)'));
        
        return $dt[0]['sum(amount)'];
    }
    
    public function getTots($pid,$since,$until,$title = '')
    {
        $conds = array('date(created) BETWEEN ? AND ?'=>array($since,$until),
                    'profile_id' => $pid);
        
        if ( $title != '' ) {
            $conds['title'] = $title;
        }
        
        $dt = $this->find('first',array('conditions'=>$conds,'fields'=>'sum(amount)'));
        
        return $dt[0]['sum(amount)'];
    }
}
?>
