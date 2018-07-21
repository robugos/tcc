<?php

$var = '20/04/2012 10:00';
$date = str_replace('/', '-', $var);
$event['START_TIME'] = date('Y-m-d h:i:s', strtotime($date));
echo $event['START_TIME'];

?>