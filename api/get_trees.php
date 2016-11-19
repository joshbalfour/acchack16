<?php
require("../sitetools.php");

$sitetools = new sitetools();

$db = $sitetools->connect();

$get = $db->prepare("SELECT * FROM `trees-sanitised`");
$get->execute();
$results = $get->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($results);

?>