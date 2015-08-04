<?php

include('api_key.php.inc');

$base_url = 'http://api.routeshout.com/v1/rs.stops.getTimes?key=';
$agency = 'eldorado'
$stop_code = $_GET['stop_code'];


// borrowed from http://phillippuleo.com/articles/lightweight-caching-proxy-php/


function fetchURL() {

	global $base_url;
	global $stop_code;
	
    $url = $base_url.$api_key.'&agency='.$agency.'&stop='.$stop_code; // Be careful with posting variables.

	$file = file_get_contents($url); // Fetch from the API.

	return $file;
}

$output = json_decode(fetchURL());

$example_stuff = '{
    "status": "ok",
    "meta": {
        "horizon": 60,
        "source": "realtime",
        "displayMode": 0,
        "timezone": "America/Los_Angeles"
    },
    "response": [
        {
            "route_short_name": "",
            "type": "predicted",
            "stop_point": false,
            "direction": "Outbound",
            "trip_id": "#0_2015-06-26T11:48:21",
            "route_long_name": "30 Diamond Springs",
            "vehicle": "0906",
            "arrival_time": "2015-08-04T18:48:40-0400",
            "lastRunStop": false,
            "departure_time": "2015-08-04T18:48:40-0400",
            "depart_complete": false,
            "firstRunStop": false,
            "arrive_complete": false,
            "cancelled": false,
            "scheduled_time": "2015-08-04T18:46:00-0400"
        }
    ]
}';



// $ontvia_output = preg_replace ( '<bold>Anaheim: (.*?)<\/bold><br>', '', $ontvia_output);
$ontvia_output = preg_replace ( '/<bold>Anaheim: (.*?)<\/bold><br>\n/', '', $ontvia_output);
$search = array('<br>','ROUTE','<!-- EOR -->','<bold>','</bold>','n/a');
$ontvia_output = str_replace ( $search , '' , $ontvia_output );


$response = $output->response;

$arrival_object = array();

foreach ($response as &$value) {
	$arrival_estimate = strtotime(str_replace('-0400','-04:00',$value->arrival_time));
	$departure_estimate = strtotime(str_replace('-0400','-04:00',$value->departure_time));
	$scheduled_time = strtotime(str_replace('-0400','-04:00',$value->scheduled_time))
	$push_object = array('route_short_name' => $value->route_short_name, 'route_short_name' => $value->route_long_name, 'type' => $value->type, 'arrival_estimate' => $arrival_estimate, 'departure_estimate' => $departure_estimate, 'scheduled_time' => $scheduled_time);
	array_push($arrival_object, $push_object);
}

echo json_encode($arrival_object);

?>