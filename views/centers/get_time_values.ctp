<?php
//pr($values);

$values['now']  = $dtime->time_to_human($values['now']);

$values['diff'] = $dtime->time_to_human($values['diff']);


die(json_encode($values));
