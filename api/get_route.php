<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require("../sitetools.php");

$sitetools = new sitetools();

$db = $sitetools->connect();

array_map('htmlspecialchars', $_GET);

if(empty($_GET['start_lat']) || empty($_GET['end_lat']) || empty($_GET['start_lng']) || empty($_GET['end_lng'])){
	$sitetools->http_error(422, "Missing required parameter");
}
try {
	$start = ["lat" => round($_GET['start_lat'], 7), "lon" => round($_GET['start_lng'], 7)];
	$end = ["lat" => round($_GET['end_lat'], 7), "lon" => round($_GET['end_lng'], 7)];
} catch (Exception $e) {
	$sitetools->http_error(422, "Malformed JSON inputs");
}

try {
	$data = file_get_contents("http://router.project-osrm.org/route/v1/walking/" . $start['lon'] . "," . $start['lat'] . ";" . $end['lon'] . "," . $end['lat'] ."?alternatives=true&steps=true&geometries=geojson&overview=full&annotations=true");
	$data = json_decode($data, true);

	if($data['code'] != "Ok"){
		$sitetools->http_error(400, "Malformed JSON inputs");	
	}
} catch (Exception $e) {
	$sitetools->http_error(400, "Malformed JSON inputs");
}

$steps = [];
foreach ($data['routes'][0]['legs'][0]['steps'] as $key => $value) {
	array_push($steps, $value);
}

//prepare the query
$get = $db->prepare("SELECT (`id`) FROM `trees-sanitised` WHERE `lat` LIKE :lat AND `lng` LIKE :lon");
$all_the_trees = [];

foreach ($steps as $key => $value) {
	$lat = "%" . round($value['geometry']['coordinates'][0][0], 2) . "%";
	$lon = "%" . round($value['geometry']['coordinates'][0][1], 2) . "%";

	$get->bindParam(":lon", $lat);
	$get->bindParam(":lat", $lon);
	$get->execute();

	$these_trees = array_map(function($tree){return $tree['id'];}, $get->fetchAll(PDO::FETCH_ASSOC));

	array_push($all_the_trees, $these_trees);
}

$all_the_trees = call_user_func_array('array_merge', $all_the_trees);
$all_the_trees = array_unique($all_the_trees);
$no_trees = count($all_the_trees);

$return = 
['status' => [
	"code" => 200,
	"message" => "success"
],
'result' => [
	'trees' => $no_trees,
	'route' => $steps
]
];

echo json_encode($return);

?>