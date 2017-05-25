<?php
$reportid = $_POST['reportid'];
$now = time();
require("sw_config.php");
$swdbcon = pg_connect($pg_connect);
$query = "select report from sw_reports where reportid = {$reportid}";
$result = pg_query($swdbcon, $query);
if($row = pg_fetch_array($result))	{
	$report = $row['report'];
	$status = true;
} else {
	$status = false;
}

echo json_encode(array("time"=>$now, "status"=>$status, "rep"=>$report));die();

?>