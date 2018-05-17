<?php
class Hipodrome extends AppModel {

	var $name = 'Hipodrome';

    
    function getIntlIds()
    {
        return $this->find('list',array(
                'conditions' => 'national = 0',
                'fields'     => 'id'
            ));
    }
    
    function getNatIds()
    {
        return $this->find('list',array(
                'conditions' => 'national = 1',
                'fields'     => 'id'
            ));
    }
    
    function getByNick($nick)
    {
        $hipod = $this->find('first',array(
                        'conditions' => array('nick' => $nick),
                        'fields'     => array('id','name','htgmt')
                ));
        
        return $hipod;
    }
    
    function getHclass($id)
    {
        $hipod = $this->find('first',array('conditions' => "id = $id",
                                        'fields' => 'class'));
        
        return $hipod['Hipodrome']['class'];
    }
}