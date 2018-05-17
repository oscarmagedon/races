<?php
class TicketHorse extends AppModel {

	var $name = 'TicketHorse';

	
	function find_basics($ticket_id,$detail = false){
				
		if($detail){
			$fields = array('id','number','name','sum(units) AS und','sum(prize) AS pri','status_name');
		}else{
			$fields = array('number','name','sum(units) AS und','play_name');	
		}
		
		$details = $this->find('all',array(
			'conditions' => array('ticket_id'=>$ticket_id),
			'fields' => $fields,
			'order' => 'number', 
			'group' => 'number' 
		));
		
		//pr($details);die();
		$dets_final = array();
		
		foreach($details as $d){
			
			// PATCH SERVER
			if(!empty($d['TicketHorse'])){
				$ind = $d['TicketHorse'];
			}else{
				$ind = $d[0];
			}
			$toname = $ind['number']; 
			$name = $ind['name'];
			$und = $d[0]['und'];
			
			//if($name != "")
			//$toname .= " <span style='font-size:10pt'>($name)</span>";
			
			if($detail){
				$toshow = array(
					'id' => $ind['id'],
					'horse' => $toname,
					'und' => $und,
					'pri' => $d[0]['pri'],
					'stat' => $ind['status_name']
				);
			}else{
				$toshow = array(
					'horse' => $toname,
					'play_name' => $ind['play_name'],
					'und' => $und
				);
			}
			array_push($dets_final,$toshow);
			
		}
		
		return $dets_final;
		
	}
	
	function find_specials($ticket_id,$detail = false){
		
		$fields = array('number','name','units','play_name','box_num');
		if($detail){
			array_push($fields,'status_name');
		}
		
		$details = $this->find('all',array(
			'conditions' => array('ticket_id'=>$ticket_id),
			'fields' => $fields,
			'order'=>'play_name'
		));
		
		$dets_final = array();
		$each_box = 0;		
		foreach($details as $d){
			
			// PATCH SERVER
			if(!empty($d['TicketHorse'])){
				$ind = $d['TicketHorse'];
			}else{
				$ind = $d[0];
			}
			
			$toname = $ind['number']; 
			$name = $ind['name'];
	
			if($name != "")
				$toname = " ($name)";
				
			if($each_box == 0)
				$each_box = $ind['units'];
				
			//$dets_final[$ind['box_num']][$ind['play_name']] = $toname;
			
			if($detail){
				$dets_final[$ind['box_num']][$ind['play_name']]['horse'] = $toname;
				$dets_final[$ind['box_num']][$ind['play_name']]['stat'] = $ind['status_name'];
			}else{
				$dets_final[$ind['box_num']][$ind['play_name']] = $toname;
			}
		}
		
		ksort($dets_final);
		
		//pr($dets_final); die();
		if($detail){
			$new_dets = array();
			foreach($dets_final as $key => $box){
				$statbox = "PENDIENTE";
				$winner = 0;
				foreach($box as $type => $horse){
					if($horse['stat'] == "PERDEDOR")
						$statbox = "PERDEDOR";
					
					if($horse['stat'] == "GANADOR")
						$winner ++;
					
					$new_dets[$key]['Horses'][$type] = $horse['horse'];
				}
				
				if($winner == count($dets_final[$key]))
					$statbox = "GANADOR";
						
				$new_dets[$key]['Stat'] = $statbox;
			}
			
			$dets_final = $new_dets;
		}
		
		$all_dets['Boxes'] = $dets_final;
		$all_dets['Each'] = $each_box;
		
		return $all_dets;
	}
	
}
?>