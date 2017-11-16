<?php

//echo json_encode(array("result"=>"hello"));
//set mapfile and load mapscript if not already loaded
$mapfile = "/var/www/html/swgap/swgap.map";
require("sw_config.php");
pg_connect($pg_connect);

/**
 * function to convert clickpoint to map co-ords
 *
 * @param integer $width
 * @param integer $height
 * @param object $point
 * @param object $ext
 * @return array
 */

function img2map($width, $height, $point, $ext){
	if ($point->x && $point->y){
		$dpp_x = ($ext->maxx -$ext->minx)/$width;
		$dpp_y = ($ext->maxy -$ext->miny)/$height;
		$p[0] = $ext->minx + $dpp_x*$point->x;
		$p[1] = $ext->maxy - $dpp_y*$point->y;
	}
	return $p;
}

//get form variables
$win_w = $_POST['win_w'];
$win_h = $_POST['win_h'];

//img_x and img_y from click points on image name = "img" in map.php
$click_x =$_POST['img_x'];
$click_y = $_POST['img_y'] - 68;
$extent_raw = $_POST['extent'];
$query_layer = $_POST['query_layer'];


//create click obj
$click_point = ms_newPointObj();
$click_point->setXY($click_x, $click_y);
//echo "<h3>query results for layer {$query_layer} </h3>";

//save extent to object
$extent = explode(" ", $extent_raw);
$old_extent =  ms_newRectObj();
$old_extent->setextent($extent[0], $extent[1], $extent[2], $extent[3]);

//create map object
$map = ms_newMapObj($mapfile);
$map->setSize($win_w, $win_h);
list($qx, $qy) = img2map($map->width, $map->height, $click_point, $old_extent);
$qpoint = ms_newPointObj();
$qpoint->setXY($qx,$qy);


if(preg_match("/parcelname|manager_de|man_desc_condense|owner_desc|gap_status/", $query_layer)){
	@$qlayer = $map->getLayerByName('manage_q');
	@$qlayer->queryByPoint($qpoint, MS_SINGLE, 0);
	@$result = $qlayer->getResult(0);
	@$result = $result->shapeindex;
	$query = "select {$query_layer} from sw_manage_gap where ogc_fid = '{$result}'";
	if($result2 = pg_query($query)){
		$row = pg_fetch_array($result2);
		$msg = $row[0];
	} else {
		$msg = 'n/a';
	}
}
if(preg_match("/basin/", $query_layer)){
	@$qlayer = $map->getLayerByName('watersheds');
	@$qlayer->queryByPoint($qpoint, MS_SINGLE, 0);
	@$result = $qlayer->getResult(0);
	@$result = $result->shapeindex;
	$query = "select cat_name from sw_wtshds where ogc_fid = '{$result}'";
	if($result2 = pg_query($query)){
		$row = pg_fetch_array($result2);
		$msg = $row[0];
	} else {
		$msg = 'n/a';
	}
}
if(preg_match("/city/", $query_layer)){
	@$qlayer = $map->getLayerByName('urban');
	@$qlayer->queryByPoint($qpoint, MS_SINGLE, 0);
	@$result = $qlayer->getResult(0);
	@$result = $result->shapeindex;
	$query = "select name from sw_urban where ogc_fid = '{$result}'";
	if($result2 = pg_query($query)){
		$row = pg_fetch_array($result2);
		$msg = $row[0];
	} else {
		$msg = 'n/a';
	}
}
if(preg_match("/county/", $query_layer)){
	@$qlayer = $map->getLayerByName('counties');
	@$qlayer->queryByPoint($qpoint, MS_SINGLE, 0);
	@$result = $qlayer->getResult(0);
	@$result = $result->shapeindex;
	$query = "select county  from sw_counties where ogc_fid = '{$result}'";
	if($result2 = pg_query($query)){
		$row = pg_fetch_array($result2);
		$msg = $row[0];
	} else {
		$msg = 'n/a';
	}
}
if(preg_match("/bcr/", $query_layer)){
	@$qlayer = $map->getLayerByName('bcr');
	@$qlayer->queryByPoint($qpoint, MS_SINGLE, 0);
	@$result = $qlayer->getResult(0);
	@$result = $result->shapeindex;
	$query = "select bcr_name  from sw_bcr where ogc_fid = '{$result}'";
	//echo $query;
	if($result2 = pg_query($query)){
		$row = pg_fetch_array($result2);
		$msg = $row[0];
	} else {
		$msg = 'n/a';
	}
}
if(preg_match("/landcover/", $query_layer)){
	@$qlayer = $map->getLayerByName('landcover');
	@$qlayer->queryByPoint($qpoint, MS_SINGLE, 0);
	@$qlayer->open();
	@$items = $qlayer->getItems(); //not required, use with var_dump($items);
	@$shape = $qlayer->getShape(0, 0);
	@$x = $shape->values['value_0'];
	@$qlayer->close();
	$query = "select description from lcov_desc where cat_num = {$x}";
	if($result2 = pg_query($query)){
		$row = pg_fetch_array($result2);
		$msg = $row[0];
	} else {
		$msg = 'n/a';
	}
}


echo json_encode(array("result"=>$msg));

?>