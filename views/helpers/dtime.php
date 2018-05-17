<?php
class dtimeHelper extends AppHelper {
	
    //post time to views
    function addPostMins($time,$mins)
    {
        $repTime  =  $mins * 60;
        
        $rtimeStr = strtotime($time) + $repTime;

        return $this->time_to_human(date('H:i:s', $rtimeStr));
    }
    
	//coloca signo + a los logros hembra
	function signal_number($cypher){
		if($cypher > 0){
			$signed = "+".$cypher;
		}else{
			$signed = $cypher;
		}
		return $signed;
	}
	
	//Coloca la hora desde DB a humana
	function time_to_human($time){
		$divs = explode(':',$time);
		if($divs[0] > 12){
			$mdm = " PM";
			$hour = $divs[0] - 12;
		}elseif($divs[0] < 12){
			$mdm = " AM";
			$hour = $divs[0];
		}else{
			$mdm = " PM";
			$hour = $divs[0];
		}
		return $hour.":".$divs[1].$mdm;
	}
	//Coloca la hora desde DB a humana con segundos 
	function time_to_human_exact($time){
		$divs = explode(':',$time);
		if($divs[0] > 12){
			$mdm = " PM";
			$hour = $divs[0] - 12;
		}elseif($divs[0] < 12){
			$mdm = " AM";
			$hour = $divs[0];
		}else{
			$mdm = " PM";
			$hour = $divs[0];
		}
		return $hour.":".$divs[1].":".$divs[2].$mdm;
	}
	//Fecha a espaniol desde BD
	function date_spa_single($date){
		$divs = explode('-',$date);
		return $divs[2]."/".$divs[1]."/".$divs[0];
	}
	
	//Fecha a espaniol con mes abr desde BD
	function date_spa_mon_abr($date){
		$divs = explode('-',$date);
		return $divs[2]."-".$this->_meses_abr($divs[1])."-".$divs[0];
	}
	
	/**
	 * Recives the php date format date('d-D-m-Y')
	 * Only used for page and free functions date types
	 * (odds sheet)
	 * @param object $date
	 * @return 
	 */
	function date_spa_show($date){
		$xpl = explode('-',$date);
	  
	  	$dia = $this->_dias_letters($xpl[0]);
		$mes = $this->_meses_numbers($xpl[2]);
		
		return $dia.", ".$xpl[1]." de ".$mes." de ".$xpl[3];
	}
	
	function date_from_created($created){
		$date = explode(' ',$created);
		
		return $this->date_spa_mon_abr($date[0]);
	}
	
	function hour_from_created($created){
		$time = explode(' ',$created);
		
		return $this->time_to_human($time[1]);
	}
	
	function hour_exact_created($created){
		$time = explode(' ',$created);
		
		return $this->time_to_human_exact($time[1]);
	}
	
	function _dias_letters($index){
		$dia['Mon'] = "Lunes";
		$dia['Tue'] = "Martes";
		$dia['Wed'] = "Miercoles";
		$dia['Thu'] = "Jueves";
		$dia['Fri'] = "Viernes";
		$dia['Sat'] = "S&aacute;bado";
		$dia['Sun'] = "Domingo";
		
		return $dia[$index];
	}
	
	function _meses_letters($index){
		$mes['Jan'] = "Enero ";
		$mes['Feb'] = "Febrero ";
		$mes['Mar'] = "Marzo ";
		$mes['Apr'] = "Abril ";
		$mes['May'] = "Mayo ";
		$mes['Jun'] = "Junio ";
		$mes['Jul'] = "Julio ";
		$mes['Aug'] = "Agosto ";
		$mes['Sep'] = "Septiembre ";
		$mes['Oct'] = "Octubre ";
		$mes['Nov'] = "Noviembre ";
		$mes['Dec'] = "Diciembre ";

		return $mes[$index];
	}
	
	function _meses_abr($index){
		$mes = array(
			'01'=>"Ene", '02'=>"Feb", '03'=>"Mar", '04'=>"Abr",	'05'=>"May", '06'=>"Jun", 
			'07'=>"Jul", '08'=>"Ago", '09' => "Sep", 10=>"Oct", 11=>"Nov", 12=>"Dic"
		);

		return $mes[$index];
	}
	
	function _meses_numbers($index){
		$mes = array(
			'01' => "Enero", '02' => "Febrero", '03' => "Marzo", '04' => "Abril",
			'05' => "Mayo", '06' => "Junio", '07' => "Julio", '08' => "Agosto",
			'09' => "Septiembre", 10 => "Octubre",	11 => "Noviembre", 12 => "Diciembre"
		);

		return $mes[$index];
	}
	
	function color_stat($stat){
		switch($stat){
			case "RETIRADO":
				$col = "Blue";
				break;
			case "GANADOR":
				$col = "Green";
				break;
			case "PERDEDOR":
				$col = "Red";
				break;
			default:
				$col = "Black";
				break;
					
		}
		
		return "<span style='color:$col; font-weight:bold'>$stat</span>";
	}
	
	//Get good name and number
	function horseName($number,$name)
	{
		$fullName = $number;
		
		if ($name != "")
			$fullName .= ' -' . $name;
			
		return $fullName;
	}
	
	//hace una lista de solo los que estan habilitados
	function getListedEnabled($allHorses)
	{
		$horses = array();
		foreach ($allHorses as $hrs) {
			if($hrs['enable'] == 1)
				$horses[$hrs['id']] = $this->horseName($hrs['number'], $hrs['name']);
		}
		 
		 return $horses;
	}

	function getDetails($horses,$id)
	{
	    $horseDets = array();
	    foreach ($horses as $horse) {
	        if ($horse['id'] == $id) {
	            $horseDets = array(
	                            'number' => $horse['number'],
	                            'name'   => $horse['name']
	                         );
	        }
	    }
	    return $horseDets;
	}
	
}
?>