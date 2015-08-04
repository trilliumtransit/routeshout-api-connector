<?php

$base_url = 'http://207.8.40.69';
$predictions_path = '/art/predictions/stop_prediction?stop=';
$stop_code = $_GET['stop_code'];

$quiet = false;
if (isset($_GET['quiet']) ) {
	$quiet = true;
}

// borrowed from http://phillippuleo.com/articles/lightweight-caching-proxy-php/


function fetchURL() {

	global $base_url;
	global $predictions_path;
	global $stop_code;
	
    $url = $base_url.$predictions_path.$stop_code; // Be careful with posting variables.

	$file = file_get_contents($url); // Fetch the file.

	return $file;
}

$ontvia_output = fetchURL();

$example_stuff = "<bold>Anaheim: GRAND PLAZA</bold><br>
<!-- EOR --><!-- rt=4 --><bold>ROUTE 4</bold> ~ n/a<br>
<!-- EOR --><!-- rt=5 --><bold>ROUTE 5</bold> ~   4:43 PM<br>
<!-- EOR --><!-- rt=18 --><bold>ROUTE 18</bold> ~ n/a<br>
<!-- EOR --><!-- rt=19 --><bold>ROUTE 19</bold> ~ n/a<br>
<!-- EOR --><!-- rt=30 --><bold>ANGELS EXPRESS</bold> ~ n/a<br>
<!-- EOR --><!-- rt=31 --><bold>DUCKS EXPRESS</bold> ~ n/a<br>";


// $ontvia_output = preg_replace ( '<bold>Anaheim: (.*?)<\/bold><br>', '', $ontvia_output);
$ontvia_output = preg_replace ( '/<bold>Anaheim: (.*?)<\/bold><br>\n/', '', $ontvia_output);
$search = array('<br>','ROUTE','<!-- EOR -->','<bold>','</bold>','n/a');
$ontvia_output = str_replace ( $search , '' , $ontvia_output );


$arrival_array = explode('
',$ontvia_output);

foreach ($arrival_array as &$value) {
	$value = str_replace('<!-- rt=' , '', $value);
	$value = str_replace(' -->' , ' ~ ', $value);
    $value = explode(' ~ ', $value);
    
    foreach ($value as &$sub_value) {
    $sub_value = trim($sub_value);
    }
    
}

$arrival_object = array();

foreach ($arrival_array as &$value) {
	if (count($value) == 3) {
		$push_object = array('short_name' => $value[0], 'long_name' => $value[1], 'estimate' => $value[2]);
		array_push($arrival_object, $push_object);
	}
}

echo json_encode($arrival_object);

?>