<?php
//date_default_timezone_set("America/New_York");
require('sw_config.php');
pg_connect($pg_connect);
$mapfile = "../swgap.map";
require('sw_aoi_class.php');
require('sw_define_aoi.php');

session_start();

//click points for navigation
$click_x = $_POST['clickx'];
$click_y = $_POST['clicky'];
//click points  for custom aoi
$posix = $_POST['posi_x'];
$posiy = $_POST['posi_y'];

$extent = $_POST['extent'];
$win_w = $_POST['winw'];
$win_h = $_POST['winh'];
$layer = $_POST['layers'];

//flag that causes extent to be calculated from AOI class instead of previous extent
$zoom_aoi = $_POST['zoomaoi'];
//zoom
$zoom = $_POST['zoom'];
//name of current AOI
$aoi_name = $_POST['aoi_name'];
//name of AOI to be created from previous saved AOI
$aoi_name_saved = $_POST['aoi_name_saved'];
//for new AOI tells how to create
$type = $_POST['type'];

//ogc_fid for predefined aoi
$owner_aoi = $_POST['owner_aoi'];
$manage_aoi = $_POST['manage_aoi'];
$status_aoi = $_POST['status_aoi'];
$county_aoi = $_POST['county_aoi'];
$state_aoi = $_POST['state_aoi'];
$topo_aoi = $_POST['topo_aoi'];
$basin_aoi = $_POST['basin_aoi'];
$bcr_aoi = $_POST['bird_consv_aoi'];
$ecosys_aoi = $_POST['ecosys_aoi'];
$file_shp = $_POST['shapefile'];
$pred_transp = $_POST['pred_transp'];
$range_transp = $_POST['range_transp'];
$range_transp_prev = $_POST['range_transp_prev'];

$itiscode = $_POST['itiscode'];
$species_layer = $_POST['species_layer'];
$species_layer_prev = $_POST['species_layer_prev'];
$map_species = $_POST['map_species'];
$richness_species = stripslashes($_POST['richness_species']);
$job_id = $_POST['job_id'];

//print logs fot t/s
$post = print_r($_POST, true);
$logfileptr = fopen("/var/log/weblog/swgap", "a");
fprintf($logfileptr, "\n\n\nInput  %s  %s\n%s ", date('G:i:s'), __FILE__, $post);
fclose($logfileptr);

$click_point = ms_newPointObj();
$click_point->setXY($click_x, $click_y);

//check that script is still running after mapobj creation
$query = "insert into check_mapobj(job_id ) values ( $job_id )";
pg_query($query);

//create map object
$map = ms_newMapObj($mapfile);

//check that script is still running after mapobj creation
$query = "delete from check_mapobj where  job_id = $job_id ";
pg_query($query);

//if AOI is undefined then create it in postgis and create new AOI object else get aoi from form variable
if (strlen($aoi_name) ==0){
	//create aoi name
	$now = localtime(time(),1);
	$aoi_name = "aoi".$now['tm_yday'].rand(0,9999999);
	if ($type == 'custom'){
		get_custom_aoi($aoi_name, $posix, $posiy, $extent, $win_w, $win_h );
	}elseif($type == 'predefined'){
		$aoi_predefined['owner_aoi'] = $owner_aoi;
		$aoi_predefined['manage_aoi'] = $manage_aoi;
		$aoi_predefined['county_aoi'] = $county_aoi;
		$aoi_predefined['basin_aoi'] = $basin_aoi;
		$aoi_predefined['state_aoi'] = $state_aoi;
		$aoi_predefined['bcr_aoi'] = $bcr_aoi;
		$aoi_predefined['ecosys_aoi'] = $ecosys_aoi;
		$aoi_predef_save = pg_escape_string(serialize($aoi_predefined));
		$query = "update aoi set aoi_data = '{$aoi_predef_save}' where name = '{$aoi_name}'";

		get_predefined_aoi($aoi_name, $owner_aoi, $manage_aoi, $status_aoi, $county_aoi, $state_aoi, $basin_aoi, $bcr_aoi, $ecosys_aoi);
		pg_query($query);
	}elseif($type == 'uploaded') {
		//echo $aoi_name."  ".$file_shp; die();
		get_uploaded_aoi($aoi_name, $file_shp);
	}elseif ($type == 'saved_aoi'){
		$aoi_name = $aoi_name_saved;
		$query = "select description from aoi where name = '{$aoi_name}'";
		$result = pg_query($query);
		$row = pg_fetch_array($result);
		$aoi_desc = $row['description'];
	}
	$new_page = true;
	$_SESSION[$aoi_name] = new sw_aoi_class($aoi_name);
}else{
	$new_page = false;
}

$sw_aoi_class = $_SESSION[$aoi_name];
$aoi_area = $sw_aoi_class->get_area();

$mapname = "map".rand(0,9999999).".png";
//$mspath = "/data/server_temp/";
$maploc = "{$mspath}{$mapname}";

//get calculated maps for single species or richness from aoi_class, but first test to see if we can use previous map
		
if (preg_match("/habitat/", $species_layer) && !preg_match("/habitat/", $species_layer_prev)) {    
    $map_species = $sw_aoi_class->landcover_map($itiscode);
}
if (preg_match("/ownership/", $species_layer) && !preg_match("/ownership/", $species_layer_prev)) {
		$map_species = $sw_aoi_class->ownership_map($itiscode);
}
if (preg_match("/status/", $species_layer) && !preg_match("/status/", $species_layer_prev)) {
		$map_species = $sw_aoi_class->protection_map($itiscode);
}
if (preg_match("/status/", $species_layer) && !preg_match("/status/", $species_layer_prev)) {
		$map_species = $sw_aoi_class->management_map($itiscode);
}
if (preg_match("/richness/", $species_layer) && !preg_match("/richness/", $species_layer_prev)) {
		$map_species = $sw_aoi_class->richness($richness_species);
}

//convert itiscode to raster name 
if(isset($itiscode) && strlen($itiscode) != 0){
	$query = "select raster from itis_raster where itiscode = {$itiscode}";
	//echo $query;
	@$result = pg_query($query);
	if($row = pg_fetch_array($result)){
		$raster = $row['raster'];
	} else{
		$raster = "pd_".strtolower($itiscode);
	}
}
//set layers from controls

if(preg_match("/landcover/", $layer)){
	$this_layer = $map->getLayerByName('landcover');
	$this_layer->set('status', MS_ON);
}else{
	$this_layer = $map->getLayerByName('landcover');
	$this_layer->set('status', MS_OFF);
}
if(preg_match("/elevation/", $layer)){
	$this_layer = $map->getLayerByName('elevation');
	$this_layer->set('status', MS_ON);
}else{
	$this_layer = $map->getLayerByName('elevation');
	$this_layer->set('status', MS_OFF);
}

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
if(preg_match("/states/", $layer)){
	$this_layer = $map->getLayerByName('states');
	$this_layer->set('status', MS_ON);
}else{
	$this_layer = $map->getLayerByName('states');
	$this_layer->set('status', MS_OFF);
}
if(preg_match("/hydro/", $layer)){
	$this_layer = $map->getLayerByName('rivers');
	$this_layer->set('status', MS_ON);
}else{
	$this_layer = $map->getLayerByName('rivers');
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
//set raster to display species maps
if(preg_match("/range/", $species_layer)){
	$this_layer = $map->getLayerByName('wtshd_range');
	$this_layer->set('classitem', $itiscode);
	$this_layer->set('status', MS_ON);
	$this_layer->set('opacity', $range_transp);
   //set layers from controls
   if(preg_match("/landcover/", $layer)){
      $this_layer = $map->getLayerByName('landcover');
      $this_layer->set('status', MS_ON);
   }else{
      $this_layer = $map->getLayerByName('landcover');
      $this_layer->set('status', MS_OFF);
   }
   if(preg_match("/elevation/", $layer)){
      $this_layer = $map->getLayerByName('elevation');
      $this_layer->set('status', MS_ON);
   }else{
      $this_layer = $map->getLayerByName('elevation');
      $this_layer->set('status', MS_OFF);
   }
}


if(preg_match("/habitat|ownership|status|manage|richness/", $species_layer)){
	$this_layer = $map->getLayerByName('mapcalc');
	//echo ($grass_raster.$map_species);
	$this_layer->set('data', $grass_raster.$map_species);
	$this_layer->set('status', MS_ON);
	//turn off other rasters
	$this_layer = $map->getLayerByName('elevation');
	$this_layer->set('status', MS_OFF);
	$this_layer = $map->getLayerByName('landcover');
	$this_layer->set('status', MS_OFF);
}


if(preg_match("/predicted/", $species_layer)){
	$this_layer = $map->getLayerByName('mapcalc');
	$this_layer->set('data', $grass_raster_perm.$raster);
	//echo $grass_raster_perm.$raster;
	$this_layer->set('status', MS_ON);
	
	$this_layer->set('opacity', $pred_transp);
   //set layers from controls
   if(preg_match("/landcover/", $layer)){
      $this_layer = $map->getLayerByName('landcover');
      $this_layer->set('status', MS_ON);
   }else{
      $this_layer = $map->getLayerByName('landcover');
      $this_layer->set('status', MS_OFF);
   }
   if(preg_match("/elevation/", $layer)){
      $this_layer = $map->getLayerByName('elevation');
      $this_layer->set('status', MS_ON);
   }else{
      $this_layer = $map->getLayerByName('elevation');
      $this_layer->set('status', MS_OFF);
   }


}

$filter = "(name = '{$aoi_name}')";
$this_layer = $map->getLayerByName('aoi');
$this_layer->setFilter($filter);
$this_layer->set('status', MS_ON);

//calculate extent from class variables the first time or zoom to aoi, else use previous extent
$extent_obj =  ms_newRectObj();

if ($new_page  || $zoom_aoi) {
	$min_x = $sw_aoi_class->get_minx();
	$min_y = $sw_aoi_class->get_miny();
	$max_x = $sw_aoi_class->get_maxx();
	$max_y = $sw_aoi_class->get_maxy();
	$x_adj = ($max_x - $min_x)*0.1;
	$y_adj = ($max_y - $min_y)*0.1;
	$extent_obj->setExtent($min_x-$x_adj, $min_y-$y_adj, $max_x+$x_adj, $max_y+$y_adj);
}else {
	$mapext = explode(" ", $extent);
	$minx = $mapext[0];
	$miny = $mapext[1];
	$maxx = $mapext[2];
	$maxy = $mapext[3];
	$extent_obj->setExtent($minx, $miny, $maxx, $maxy);
}
$map->setSize($win_w, $win_h);
$map->zoompoint($zoom, $click_point, $win_w, $win_h, $extent_obj);
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

$ret = json_encode(array("mapname"=>$mapname,"extent"=>$new_extent, "refname"=>$refurl, "aoiname"=>$aoi_name, "mapspecies"=>$map_species, "aoiarea"=>$aoi_area));

$logfileptr = fopen("/var/log/weblog/swgap", "a");
fprintf($logfileptr, "\nOutput %s  %s\n%s", date('G:i:s'), __FILE__,  $ret);
fclose($logfileptr);

echo $ret;

?>