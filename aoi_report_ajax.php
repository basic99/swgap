<?php

require('sw_aoi_class.php');
require("sw_config.php");
session_start();

$aoi_name = $_POST['aoiname'];
$a = $_SESSION[$aoi_name];

$report = $_POST['report'];
$itiscode = $_POST['itiscode'];
$species = stripslashes($_POST['species']);
$reportid = $_POST['reportid'];

//echo json_encode(array("header"=>"","rep"=>$aoi_name));die();

pg_connect($pg_connect);
$query2 = "select aoi_data from aoi where name = '$aoi_name'";
$result = pg_query($query2);
$row = pg_fetch_array($result);
$aoi_data = unserialize($row['aoi_data']);
$states = explode(":", $aoi_data['state_aoi']);
$bcr =  explode(":", $aoi_data['bcr_aoi']);
if($states[0] == ""){
	unset($states);
}
if($bcr[0] == ""){
	unset($bcr);
}

if ($aoi_data['ecosys_aoi'] == 1) {
	if ($report == 'landcover'){
		$query = "select report, description from sw_reports_pre where ecosys_ogc_fid = 1 and type = 'landcover'";
		$result = pg_query($query);
		$row = pg_fetch_array($result);
		$grass = $row['report'];
		$response .=  "<h1>AOI Land Cover Report for Full Extent</h1>";
		$response .= "<pre>{$grass}</pre>";
	}
		if ($report == 'management') {
		$query = "select report, description from sw_reports_pre where ecosys_ogc_fid = 1 and type = 'management'";
		$result = pg_query($query);
		$row = pg_fetch_array($result);
		$grass = $row['report'];
		$response .=  "<h1>AOI Management Report for Full Extent</h1>";
		$response .= "<pre>{$grass}</pre>";
	}

	if ($report == 'owner') {
		$query = "select report, description from sw_reports_pre where ecosys_ogc_fid = 1 and type = 'ownership'";
		$result = pg_query($query);
		$row = pg_fetch_array($result);
		$grass = $row['report'];
		$response .=  "<h1>AOI Ownership Report for Full Extent</h1>";
		$response .= "<pre>{$grass}</pre>";
	}

	if ($report == 'status') {
		$query = "select report, description from sw_reports_pre where ecosys_ogc_fid = 1 and type = 'status'";
		$result = pg_query($query);
		$row = pg_fetch_array($result);
		$grass = $row['report'];
		$response .=  "<h1>AOI GAP Status Report for Full Extent</h1>";
		$response .= "<pre>{$grass}</pre>";
	}
}

//echo json_encode(array("rep"=>$bcr[0]));die();
for ($i = 0; $i < sizeof($states); $i++){
	if ($report == 'landcover'){
		$query = "select report, description from sw_reports_pre where states_ogc_fid = '{$states[$i]}' and type = 'landcover'";
		$result = pg_query($query);
		$row = pg_fetch_array($result);
		$grass = $row['report'];
		$response .=  "<h1>AOI Land Cover Report for {$row['description']}</h1>";
		$response .= "<pre>{$grass}</pre>";
	}
	if ($report == 'management') {
		$query = "select report, description from sw_reports_pre where states_ogc_fid = '{$states[$i]}' and type = 'management'";
		$result = pg_query($query);
		$row = pg_fetch_array($result);
		$grass = $row['report'];
		$response .=  "<h1>AOI Management Report for {$row['description']}</h1>";
		$response .= "<pre>{$grass}</pre>";
	}

	if ($report == 'owner') {
		$query = "select report, description from sw_reports_pre where states_ogc_fid = '{$states[$i]}' and type = 'ownership'";
		$result = pg_query($query);
		$row = pg_fetch_array($result);
		$grass = $row['report'];
		$response .=  "<h1>AOI Ownership Report for {$row['description']}</h1>";
		$response .= "<pre>{$grass}</pre>";
	}

	if ($report == 'status') {
		$query = "select report, description from sw_reports_pre where states_ogc_fid = '{$states[$i]}' and type = 'status'";
		$result = pg_query($query);
		$row = pg_fetch_array($result);
		$grass = $row['report'];
		$response .=  "<h1>AOI GAP Status Report for {$row['description']}</h1>";
		$response .= "<pre>{$grass}</pre>";
	}
}
//die();

for ($i = 0; $i < sizeof($bcr); $i++){
	//echo json_encode(array("rep"=>$bcr[0]));die();
	if ($report == 'landcover'){
		$query = "select report, description from sw_reports_pre where bcr_ogc_fid = '{$bcr[$i]}' and type = 'landcover'";
		$result = pg_query($query);
		$row = pg_fetch_array($result);
		$grass = $row['report'];
		$response .=  "<h1>AOI Land Cover Report for {$row['description']}</h1>";
		$response .= "<pre>{$grass}</pre>";
	}
	if ($report == 'management') {
		$query = "select report, description from sw_reports_pre where bcr_ogc_fid = '{$bcr[$i]}' and type = 'management'";
		$result = pg_query($query);
		$row = pg_fetch_array($result);
		$grass = $row['report'];
		$response .=  "<h1>AOI Management Report for {$row['description']}</h1>";
		$response .= "<pre>{$grass}</pre>";
	}

	if ($report == 'owner') {
		$query = "select report, description from sw_reports_pre where bcr_ogc_fid = '{$bcr[$i]}' and type = 'ownership'";
		$result = pg_query($query);
		$row = pg_fetch_array($result);
		$grass = $row['report'];
		$response .=  "<h1>AOI Ownership Report for {$row['description']}</h1>";
		$response .= "<pre>{$grass}</pre>";
	}

	if ($report == 'status') {
		$query = "select report, description from sw_reports_pre where bcr_ogc_fid = '{$bcr[$i]}' and type = 'status'";
		$result = pg_query($query);
		$row = pg_fetch_array($result);
		$grass = $row['report'];
		$response .=  "<h1>AOI GAP Status Report for {$row['description']}</h1>";
		$response .= "<pre>{$grass}</pre>";
	}
}
if ((isset($states) || isset($bcr) || $aoi_data['ecosys_aoi'] == 1) && ($report == 'landcover' || $report == 'management' || $report == 'owner'|| $report == 'status')) {
	//die();
} else {


	if ($report == 'landcover'){
		$response =  "<h1>AOI Land Cover Report</h1>";
		$grass = $a->aoi_landcover();
		$response .= "<pre>{$grass}</pre>";
		
		
	}

	if ($report == 'management') {
		$response =  "<h1>AOI Management Report</h1>";
		$grass = $a->aoi_management();
		$response .= "<pre>{$grass}</pre>";
		
	}

	if ($report == 'owner') {
		$response =  "<h1>AOI Ownership Report</h1>";
		$grass = $a->aoi_ownership();
		$response .= "<pre>{$grass}</pre>";
		
	}

	if ($report == 'status') {
		$response =  "<h1>AOI GAP Status Report</h1>";
		$grass = $a->aoi_status();
		$response .= "<pre>{$grass}</pre>";
		
	}

	if ($report == 'status_sp') {
		$response =  "<h1>Species GAP Status Report</h1>";
		$response .= "<h3>{$species}</h3>";
		$grass = $a->species_status($itiscode);
		$response .= "<pre>{$grass}</pre>";
		
	}

	if ($report == 'landcover_sp') {
		$response =  "<h1>Species Land Cover Report</h1>";
		$response .= "<h3>{$species}</h3>";
		$grass = $a->species_landcover($itiscode);
		$response .= "<pre>{$grass}</pre>";
		
	}

	if ($report == 'management_sp') {
		$response =  "<h1>Species Management Report</h1>";
		$response .= "<h3>{$species}</h3>";
		$grass = $a->species_management($itiscode);
		$response .= "<pre>{$grass}</pre>";
		
	}

	if ($report == 'owner_sp') {
		$response =  "<h1>Species Ownership Report</h1>";
		$response .= "<h3>{$species}</h3>";
		$grass = $a->species_ownership($itiscode);
		$response .= "<pre>{$grass}</pre>";
		
	}

	if ($report == 'predicted') {
		$response =  "<h1>Predicted Distribution Report</h1>";
		$response .= "<h3>{$species}</h3>";
		$grass = $a->predicted($itiscode);
		$response .= "<pre>{$grass}</pre>";
		
	}
	if ($report == 'richness_report') {
		$response =  "<h1>Richness Report</h1>";
		$spec = str_replace(":", "<br />", $species);
		$response .= "<h3>{$spec}</h3>";
		$grass = $a->richnessreport($species);
		$response .= "<pre>{$grass}</pre>";
		
	}
}
$response = pg_escape_string($response);
$query = "insert into sw_reports(reportid, report) values ({$reportid}, '{$response}')";
pg_query($query);
//echo json_encode(array("header"=>"$query","rep"=>$response));die();
?>