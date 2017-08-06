<?php
//date_default_timezone_set("America/New_York");
session_start();
require('sw_config.php');
pg_connect($pg_connect);

$mapfile = "/var/www/html/swgap/swgap.map";
ini_set("display_errors", 0);
ini_set("error_log", "/var/www/html/swgap/logs/php-error.log");

//process ajax input data
$extent_raw = $_POST['extent'];
$zoom = $_POST['zoom'];
$layer = $_POST['layers'];
$win_w = $_POST['winw'];
$win_h = $_POST['winh'];
$click_x = $_POST['clickx'];
$click_y = $_POST['clicky'];
$county_aoi = $_POST['county_aoi'];
$owner_aoi = $_POST['owner_aoi'];
$manage_aoi = $_POST['manage_aoi'];
$basin_aoi = $_POST['basin_aoi'];
$bcr_aoi = $_POST['bird_consv_aoi'];
$state_aoi = $_POST['state_aoi'];
$ecosys_aoi = $_POST['ecosys_aoi'];
$job_id = $_POST['job_id'];


$post = print_r($_POST, true);
$logfileptr = fopen("/var/log/weblog/swgap", "a");
fprintf($logfileptr, "\n\n\nInput  %s  %s\n%s ", date('G:i:s'), __FILE__, $post);
fclose($logfileptr);


$click_point = ms_newPointObj();
$click_point->setXY($click_x, $click_y);

//save extent to rect
$old_extent =  ms_newRectObj();
$extent = explode(" ", $extent_raw);
$old_extent->setextent($extent[0], $extent[1], $extent[2], $extent[3]);

//check that script is still running after mapobj creation
$query = "insert into check_mapobj(job_id ) values ( $job_id )";
pg_query($query);

//create map object
$map = ms_newMapObj($mapfile);

//check that script is still running after mapobj creation
$query = "delete from check_mapobj where  job_id = $job_id ";
pg_query($query);


//set layers
if(preg_match("/cities/", $layer)){
	$this_layer = $map->getLayerByName('urban');
	$this_layer->set('status', MS_ON);
}else{
	$this_layer = $map->getLayerByName('urban');
	$this_layer->set('status', MS_OFF);
}
if(preg_match("/counties/", $layer)){
	$this_layer = $map->getLayerByName('counties');
	$this_layer->set('status', MS_ON);
}else{
	$this_layer = $map->getLayerByName('counties');
	$this_layer->set('status', MS_OFF);
}
if(preg_match("/roads/", $layer)){
	$this_layer = $map->getLayerByName('roads');
	$this_layer->set('status', MS_ON);
}else{
	$this_layer = $map->getLayerByName('roads');
	$this_layer->set('status', MS_OFF);
}
if(preg_match("/hydro/", $layer)){
	$this_layer = $map->getLayerByName('rivers');
	$this_layer->set('status', MS_ON);
}else{
	$this_layer = $map->getLayerByName('rivers');
	$this_layer->set('status', MS_OFF);
}
if(preg_match("/elevation/", $layer)){
	$this_layer = $map->getLayerByName('elevation');
	$this_layer->set('status', MS_ON);
}else{
	$this_layer = $map->getLayerByName('elevation');
	$this_layer->set('status', MS_OFF);
}
if(preg_match("/landcover/", $layer)){
	$this_layer = $map->getLayerByName('landcover');
	$this_layer->set('status', MS_ON);
}else{
	$this_layer = $map->getLayerByName('landcover');
	$this_layer->set('status', MS_OFF);
}
if(preg_match("/states/", $layer)){
	$this_layer = $map->getLayerByName('states');
	$this_layer->set('status', MS_ON);
}else{
	$this_layer = $map->getLayerByName('states');
	$this_layer->set('status', MS_OFF);
}
if(preg_match("/wtshds/", $layer)){
	$this_layer = $map->getLayerByName('watersheds');
	$this_layer->set('status', MS_ON);
}else{
	$this_layer = $map->getLayerByName('watersheds');
	$this_layer->set('status', MS_OFF);
}
if(preg_match("/bcr/", $layer)){
	$this_layer = $map->getLayerByName('bcr');
	$this_layer->set('status', MS_ON);
}else{
	$this_layer = $map->getLayerByName('bcr');
	$this_layer->set('status', MS_OFF);
}
if(preg_match("/ownership/", $layer)){
	$this_layer = $map->getLayerByName('gapown');
	$this_layer->set('status', MS_ON);
}else{
	$this_layer = $map->getLayerByName('gapown');
	$this_layer->set('status', MS_OFF);
}
if(preg_match("/management/", $layer)){
	$this_layer = $map->getLayerByName('gapman');
	$this_layer->set('status', MS_ON);
}else{
	$this_layer = $map->getLayerByName('gapman');
	$this_layer->set('status', MS_OFF);
}
if(preg_match("/status/", $layer)){
	$this_layer = $map->getLayerByName('gapsta');
	$this_layer->set('status', MS_ON);
}else{
	$this_layer = $map->getLayerByName('gapsta');
	$this_layer->set('status', MS_OFF);
}
if (isset($county_aoi) && !empty($county_aoi)){
	$key_gap = explode(":", $county_aoi);
	$filter = "(ogc_fid = {$key_gap[0]})";
	for($i=1; $i<count($key_gap); $i++){
		$filter .= " or (ogc_fid = {$key_gap[$i]})";
	}
	$this_layer = $map->getLayerByName('counties_select');
	$this_layer->setFilter($filter);
	$this_layer->set('status', MS_ON);
}
if (isset($basin_aoi) && !empty($basin_aoi)){
	$key_gap = explode(":", $basin_aoi);
	$filter = "(ogc_fid = {$key_gap[0]})";
	for($i=1; $i<count($key_gap); $i++){
		$filter .= " or (ogc_fid = {$key_gap[$i]})";
	}
	$this_layer = $map->getLayerByName('basin_select');
	$this_layer->setFilter($filter);
	$this_layer->set('status', MS_ON);
}
if (isset($bcr_aoi) && !empty($bcr_aoi)){
	$key_gap = explode(":", $bcr_aoi);
	$filter = "(ogc_fid = {$key_gap[0]})";
	for($i=1; $i<count($key_gap); $i++){
		$filter .= " or (ogc_fid = {$key_gap[$i]})";
	}
	//echo $filter;
	$this_layer = $map->getLayerByName('bcr_select');
	$this_layer->setFilter($filter);
	$this_layer->set('status', MS_ON);
}
if (isset($state_aoi) && !empty($state_aoi)){
	$key_gap = explode(":", $state_aoi);
	$filter = "(ogc_fid = {$key_gap[0]})";
	for($i=1; $i<count($key_gap); $i++){
		$filter .= " or (ogc_fid = {$key_gap[$i]})";
	}
	$this_layer = $map->getLayerByName('state_select');
	$this_layer->setFilter($filter);
	$this_layer->set('status', MS_ON);
}
if (isset($owner_aoi) && !empty($owner_aoi)){
	$key_gap = explode(":", $owner_aoi);
	$key_gap_explode = explode("|",$key_gap[0]);
	$filter = "(state_fips = {$key_gap_explode[0]} and own_c = {$key_gap_explode[1]})";
	for($i=1; $i<count($key_gap); $i++){
		$key_gap_explode = explode("|",$key_gap[$i]);
		$filter .= " or (state_fips = {$key_gap_explode[0]} and own_c = {$key_gap_explode[1]})";
	}
	//echo $filter;
	$this_layer = $map->getLayerByName('owner_select');
	$this_layer->setFilter($filter);
	$this_layer->set('status', MS_ON);
}
if (isset($manage_aoi) && !empty($manage_aoi)){
	$key_gap = explode(":", $manage_aoi);
	$key_gap_explode = explode("|",$key_gap[0]);
	$filter = "(state_fips = {$key_gap_explode[0]} and man_c_cond = {$key_gap_explode[1]})";
	for($i=1; $i<count($key_gap); $i++){
		$key_gap_explode = explode("|",$key_gap[$i]);
		$filter .= " or (state_fips = {$key_gap_explode[0]} and man_c_cond = {$key_gap_explode[1]})";
	}
	$this_layer = $map->getLayerByName('manage_select');
	$this_layer->setFilter($filter);
	$this_layer->set('status', MS_ON);
}
if (isset($ecosys_aoi) && !empty($ecosys_aoi)){
	$this_layer = $map->getLayerByName('ecosys_select');
	$this_layer->set('status', MS_ON);
}
//creating main map
$mapname = "map".rand(0,9999999).".png";
//$mspath = "/data/server_temp/";

$maploc = "{$mspath}{$mapname}";
$map->setSize($win_w, $win_h);
$map->zoompoint($zoom, $click_point, $win_w, $win_h, $old_extent);
//programmers note
//if map->draw fails without message check mapfile, esp fontset location
$mapimage = $map->draw();
$mapimage->saveImage($maploc);

//create ref map
$refname="refmap".rand(0,9999999).".png";
$refurl="/server_temp/".$refname;
$refname = $mspath.$refname;
$refimage = $map->drawReferenceMap();
$refimage->saveImage($refname);

//get new extent
$new_extent = 	sprintf("%3.6f",$map->extent->minx)." ".
sprintf("%3.6f",$map->extent->miny)." ".
sprintf("%3.6f",$map->extent->maxx)." ".
sprintf("%3.6f",$map->extent->maxy);

$ret =  json_encode(array("mapname"=>$mapname,"extent"=>$new_extent, "refname"=>$refurl));

$logfileptr = fopen("/var/log/weblog/swgap", "a");
fprintf($logfileptr, "\nOutput  %s  %s\n%s", date('G:i:s'), __FILE__,  $ret);
fclose($logfileptr);

echo $ret;
?>