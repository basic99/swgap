<?php
require("sw_config.php");
$swdbcon = pg_connect($pg_connect);

//////////////////////////////////////////////////////////////////////////////////
// this class has a constructor that takes as a parameter an AOI name
// the constructor calculates the bounding box and imports a mask into GRASS
// various functions that depend on the AOI can then be called
///////////////////////////////////////////////////////////////////////////////

require("sw_config.php");
putenv("GISBASE={$GISBASE}");
putenv("GISRC={$GISRC}");
putenv("PATH={$PATH}");

class sw_aoi_class{

	private $aoi_name;
	private $mask_name;
	private $min_x;
	private $min_y;
	private $max_x;
	private $max_y;
	private $area;
	//public  $max_area_exception;

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $a
	 */
	public function __construct($a) {
		//import max aoi area as global from sw_config.php
		global $max_aoi_area;
		global $swdbcon;

		//assign parameter class variable
		$this->aoi_name = $a;

		//get max extents of aoi
		$query_fid = "select ogc_fid from aoi where name='{$this->aoi_name}'";
		$result_i = pg_query($swdbcon, $query_fid);
		$min_x = $min_y = 9999999;
		$max_x = $max_y =  -9999999;
		while ($row_i = pg_fetch_array($result_i)){

			$query_minx = "select x(pointn(exteriorring(envelope(wkb_geometry)), 1)) from aoi where ogc_fid={$row_i[0]}";
			$query_miny = "select y(pointn(exteriorring(envelope(wkb_geometry)), 1)) from aoi where ogc_fid={$row_i[0]}";
			$query_maxx = "select x(pointn(exteriorring(envelope(wkb_geometry)), 3)) from aoi where ogc_fid={$row_i[0]}";
			$query_maxy = "select y(pointn(exteriorring(envelope(wkb_geometry)), 3)) from aoi where ogc_fid={$row_i[0]}";

			$result = pg_query($swdbcon, $query_minx);
			$row = pg_fetch_array($result);
			$this->min_x = min($row[0], $min_x);
			// $min_x = $row[0]-10000;
			$min_x = $this->min_x;

			$result = pg_query($swdbcon, $query_miny);
			$row = pg_fetch_array($result);
			$this->min_y = min($row[0], $min_y);
			// $min_y = $row[0] - 10000;
			$min_y = $this->min_y;

			$result = pg_query($swdbcon, $query_maxx);
			$row = pg_fetch_array($result);
			$this->max_x = max($row[0], $max_x);
			//$max_x = $row[0] + 10000;
			$max_x = $this->max_x;

			$result = pg_query($swdbcon, $query_maxy);
			$row = pg_fetch_array($result);
			$this->max_y = max($row[0], $max_y);
			// $max_y = $row[0] + 10000;
			$max_y =  $this->max_y;
		}
		$this->area = ($max_x - $min_x) * ($max_y - $min_y);
		if ($this->area > $max_aoi_area) {
			//throw new large_aoi_exception($this->area);
		}

		//check if can use mask already in GRASS
		$query = "select ogc_fid, aoi_data from aoi where name='{$this->aoi_name}'";
		$result = pg_query($swdbcon, $query);
		$row = pg_fetch_array($result);
		if (!empty($row['aoi_data'])) {
			$aoi_data = unserialize($row['aoi_data']);
			//var_dump($aoi_data);

			if ($aoi_data['ecosys_aoi'] == 1) {
				$this->mask_name = 'ecosys';
				return;
			}
			switch ($aoi_data['state_aoi']){
				case "1":
					$this->mask_name = 'Utah';
					return;
				case "2":
					$this->mask_name = 'Nevada';
					return;
				case "3":
					$this->mask_name = 'Colorado';
					return;
				case "4":
					$this->mask_name = 'Arizona';
					return;
				case "5":
					$this->mask_name = 'New_Mexico';
					return;

			}
			switch ($aoi_data['bcr_aoi']) {
				case "1":
					$this->mask_name = 'GREAT_BASIN';
					return;
				case "2":
					$this->mask_name = 'NORTHERN_ROCKIES';
					return;
				case "3":
					$this->mask_name = 'SIERRA_NEVADA';
					return;
				case "4":
					$this->mask_name = 'SOUTHERN_ROCKIES_COLORADO_PLATEAU';
					return;
				case "5":
					$this->mask_name = 'SHORTGRASS_PRAIRIE';
					return;
				case "6":
					$this->mask_name = 'SONORAN_AND_MOJAVE_DESERTS';
					return;
				case "7":
					$this->mask_name = 'SIERRA_MADRE_OCCIDENTAL';
					return;
				case "8":
					$this->mask_name = 'CHIHUAHUAN_DESERT';
					return;

			}
		}


		//create name for mask
		$blank_file = aoi.rand(0,9999999);
		$blank = "/pub/server_temp/".$blank_file;
		$this->mask_name = $blank_file;

		//copy blank file to rectangle of AOI
		$gdal_cmd1 = "/usr/local/bin/gdal_translate -of GTiff -projwin {$min_x} {$max_y} {$max_x} {$min_y} /var/www/html/data/swgap/sw_blank.gtiff {$blank} &>/dev/null";
		//echo $gdal_cmd1;ob_flush();flush();
		system($gdal_cmd1);

		//burn aoi into blank file
		$gdal_cmd = "/usr/local/bin/gdal_rasterize -burn 1 -sql \"SELECT AsText(wkb_geometry) FROM  aoi  where aoi.name='{$this->aoi_name}' \"   PG:\"host=localhost port=5432 dbname=swgap user=postgres\"  {$blank} &>/dev/null";
		//echo $gdal_cmd; ob_flush();flush();
		system($gdal_cmd);

		//import mask into GRASS
		$grass_cmd=<<<GRASS_SCRIPT
g.region -d &>/dev/null
r.in.gdal input={$blank} output={$blank_file}a &>/dev/null
cat /var/www/html/swgap/grass/mask_recl | r.reclass input={$blank_file}a output={$blank_file} &>/dev/null
GRASS_SCRIPT;
		//echo $grass_cmd."<br>";ob_flush();flush();
		system($grass_cmd);
		//system('whoami');

	}
	public function get_area(){
		return $this->area;
	}
	// function for testing only
	public function show_vars(){
		echo $this->aoi_name."<br>";
		echo $this->mask_name."<br>";
		echo $this->min_x."<br>";
		echo $this->min_y."<br>";
		echo $this->max_y."<br>";
		echo $this->max_x."<br>";

	}

	// getter functions for max extent of AOI
	public function get_minx(){
		return $this->min_x;
	}

	public function get_maxx(){
		return $this->max_x;
	}

	public function get_miny(){
		return $this->min_y;
	}

	public function get_maxy(){
		return $this->max_y;
	}

	/////////////////////////////////////////////////////////////////////////
	//functions that print reports for all AOI, not dependant on species
	//////////////////////////////////////////////////////////////////////////


	/**
	 * Enter description here...
	 *
	 * @return unknown
	 */
	public function aoi_landcover(){
		//echo "<pre>";
		$str=<<<GRASS_SCRIPT
g.region n={$this->max_y} s={$this->min_y} w={$this->min_x} e={$this->max_x} &>/dev/null
r.mapcalc {$this->mask_name}calc_lc = '{$this->mask_name}  * sw_lcover' &>/dev/null
cat /var/www/html/swgap/grass/sw_lc_recl | r.reclass input={$this->mask_name}calc_lc output={$this->mask_name}recl_lc &>/dev/null
r.report -n map={$this->mask_name}recl_lc units=a,h,p 2>/dev/null
GRASS_SCRIPT;
		//echo ($str);
		//system($str);
		//echo "</pre>";
		$rep = `$str`;
		return $rep;
	}

	public function aoi_management(){
		//echo "<pre>";
		$str=<<<GRASS_SCRIPT
g.region n={$this->max_y} s={$this->min_y} w={$this->min_x} e={$this->max_x} &>/dev/null
r.mapcalc {$this->mask_name}calc_man = '{$this->mask_name}  * sw_manage' &>/dev/null
cat /var/www/html/swgap/grass/sw_manage_recl | r.reclass input={$this->mask_name}calc_man output={$this->mask_name}recl_man &>/dev/null
r.report -n map={$this->mask_name}recl_man units=a,h,p 2>/dev/null
GRASS_SCRIPT;
		//system($str);
		//echo "</pre>";
		$rep = `$str`;
		return $rep;
	}

	public function aoi_ownership(){
		//echo "<pre>";
		$str=<<<GRASS_SCRIPT
g.region n={$this->max_y} s={$this->min_y} w={$this->min_x} e={$this->max_x} &>/dev/null
r.mapcalc {$this->mask_name}calc_own = '{$this->mask_name}  * sw_owner' &>/dev/null
cat /var/www/html/swgap/grass/sw_owner_recl | r.reclass input={$this->mask_name}calc_own output={$this->mask_name}recl_own &>/dev/null
r.report -n map={$this->mask_name}recl_own units=a,h,p 2>/dev/null
GRASS_SCRIPT;
		//system($str);
		//echo "</pre>";
		$rep = `$str`;
		return $rep;
	}

	public function aoi_status(){
		//echo "<pre>";
		//return "hello world";
		$str=<<<GRASS_SCRIPT
g.region n={$this->max_y} s={$this->min_y} w={$this->min_x} e={$this->max_x} &>/dev/null
r.mapcalc {$this->mask_name}calc_stat = '{$this->mask_name}  * sw_status' &>/dev/null
cat /var/www/html/swgap/grass/sw_status_recl | r.reclass input={$this->mask_name}calc_stat output={$this->mask_name}recl_stat &>/dev/null
r.report -n map={$this->mask_name}recl_stat units=a,h,p 2>/dev/null
GRASS_SCRIPT;
		//system($str);
		$rep = `$str`;
		return $rep;
		//echo "</pre>";
	}

	/////////////////////////////////////////////////////////////////////////////
	//functions that print reports for  AOI, are dependant on species
	////////////////////////////////////////////////////////////////////////////

	public function predicted($a){
		//convert strelcode to raster name
		//$raster = "pd_".strtolower($a);
		global $swdbcon;
		$query = "select raster from itis_raster where itiscode = {$a}";
		$result = pg_query($swdbcon, $query);
		if($row = pg_fetch_array($result)){
			$raster = $row['raster'];
		} else{
			$raster = "pd_".strtolower($a);
		}
		//echo "<pre>";
		$str=<<<GRASS_SCRIPT
g.region n={$this->max_y} s={$this->min_y} w={$this->min_x} e={$this->max_x} &>/dev/null
r.mapcalc {$this->mask_name}calc_pred = '{$this->mask_name}  *{$raster}' &>/dev/null
cat /var/www/html/swgap/grass/sw_pred_recl | r.reclass input={$this->mask_name}calc_pred output={$this->mask_name}recl_pred &>/dev/null
r.report -n map={$this->mask_name}recl_pred units=a,h,p 2>/dev/null
GRASS_SCRIPT;
		//system($str);
		//echo "</pre>";
		$rep = `$str`;
		return $rep;
	}

	public function species_status($a){
		//convert strelcode to raster name
		//$raster = "pd_".strtolower($a);
		global $swdbcon;
		$query = "select raster from itis_raster where itiscode = {$a}";
		$result = pg_query($swdbcon, $query);
		if($row = pg_fetch_array($result)){
			$raster = $row['raster'];
		} else{
			$raster = "pd_".strtolower($a);
		}
		//echo "<pre>";
		$str=<<<GRASS_SCRIPT
g.region n={$this->max_y} s={$this->min_y} w={$this->min_x} e={$this->max_x} &>/dev/null
r.mapcalc {$this->mask_name}calc_stat_sp = '{$this->mask_name}  *{$raster}* sw_status' &>/dev/null
cat /var/www/html/swgap/grass/sw_status_recl | r.reclass input={$this->mask_name}calc_stat_sp output={$this->mask_name}recl_stat_sp &>/dev/null
r.report -n map={$this->mask_name}recl_stat_sp units=a,h,p 2>/dev/null
GRASS_SCRIPT;
		//echo $str;
		//system($str);
		//echo "</pre>";
		$rep = `$str`;
		return $rep;

	}

	public function species_ownership($a){
		//convert strelcode to raster name
		//$raster = "pd_".strtolower($a);
		global $swdbcon;
		$query = "select raster from itis_raster where itiscode = {$a}";
		$result = pg_query($swdbcon, $query);
		if($row = pg_fetch_array($result)){
			$raster = $row['raster'];
		} else{
			$raster = "pd_".strtolower($a);
		}
		//echo "<pre>";
		$str=<<<GRASS_SCRIPT
g.region n={$this->max_y} s={$this->min_y} w={$this->min_x} e={$this->max_x} &>/dev/null
r.mapcalc {$this->mask_name}calc_own_sp = '{$this->mask_name}  *{$raster}* sw_owner' &>/dev/null
cat /var/www/html/swgap/grass/sw_owner_recl | r.reclass input={$this->mask_name}calc_own_sp output={$this->mask_name}recl_own_sp &>/dev/null
r.report -n map={$this->mask_name}recl_own_sp units=a,h,p 2>/dev/null
GRASS_SCRIPT;
		//system($str);
		//echo "</pre>";
		$rep = `$str`;
		return $rep;
	}

	public function species_management($a){
		//convert strelcode to raster name
		//$raster = "pd_".strtolower($a);
		global $swdbcon;
		$query = "select raster from itis_raster where itiscode = {$a}";
		$result = pg_query($swdbcon, $query);
		if($row = pg_fetch_array($result)){
			$raster = $row['raster'];
		} else{
			$raster = "pd_".strtolower($a);
		}
		//echo "<pre>";
		$str=<<<GRASS_SCRIPT
g.region n={$this->max_y} s={$this->min_y} w={$this->min_x} e={$this->max_x} &>/dev/null
r.mapcalc {$this->mask_name}calc_man_sp = '{$this->mask_name}  *{$raster}*  sw_manage' &>/dev/null
cat /var/www/html/swgap/grass/sw_manage_recl | r.reclass input={$this->mask_name}calc_man_sp output={$this->mask_name}recl_man_sp &>/dev/null
r.report -n map={$this->mask_name}recl_man_sp units=a,h,p 2>/dev/null
GRASS_SCRIPT;
		//system($str);
		//echo "</pre>";
		$rep = `$str`;
		return $rep;

	}

	public function species_landcover($a){
		//convert strelcode to raster name
		//$raster = "pd_".strtolower($a);
		global $swdbcon;
		$query = "select raster from itis_raster where itiscode = {$a}";
		$result = pg_query($swdbcon, $query);
		if($row = pg_fetch_array($result)){
			$raster = $row['raster'];
		} else{
			$raster = "pd_".strtolower($a);
		}
		//echo "<pre>";
		$str=<<<GRASS_SCRIPT
g.region n={$this->max_y} s={$this->min_y} w={$this->min_x} e={$this->max_x} &>/dev/null
r.mapcalc {$this->mask_name}calc_lc_sp = '{$this->mask_name}  *{$raster}*  sw_lcover' &>/dev/null
cat /var/www/html/swgap/grass/sw_lc_recl | r.reclass input={$this->mask_name}calc_lc_sp output={$this->mask_name}recl_lc_sp &>/dev/null
r.report -n map={$this->mask_name}recl_lc_sp units=a,h,p 2>/dev/null
GRASS_SCRIPT;
		//system($str);
		//echo "</pre>";
		$rep = `$str`;
		return $rep;
	}

	//////////////////////////////////////////////////////////////////////////////////////
	//functions that return handle to map created for single species
	//////////////////////////////////////////////////////////////////////////////////////

	public function landcover_map($a){

		//convert strelcode to raster name
		//$raster = "pd_".strtolower($a);
		global $swdbcon;
		$query = "select raster from itis_raster where itiscode = {$a}";
		$result = pg_query($swdbcon, $query);
		if($row = pg_fetch_array($result)){
			$raster = $row['raster'];
		} else{
			$raster = "pd_".strtolower($a);
		}

		//calculate 10% padding
		$x_pad = ($this->max_x - $this->min_x) * 0.1;
		$y_pad = ($this->max_y - $this->min_y) * 0.1;
		$max_x = $this->max_x + $x_pad;
		$min_x = $this->min_x - $x_pad;
		$max_y = $this->max_y + $y_pad;
		$min_y = $this->min_y - $y_pad;

		//create map name
		$map = "map".rand(0,999999);

		$str=<<<GRASS_SCRIPT
g.region n={$max_y} s={$min_y} w={$min_x} e={$max_x} &>/dev/null
r.mapcalc {$map} = '{$raster} *  sw_lcover' &>/dev/null
cat  /var/www/html/swgap/grass/sw_lc_color | r.colors map={$map} color=rules &>/dev/null
GRASS_SCRIPT;
		system($str);
		return $map;
	}

	public function ownership_map($a){

		//convert strelcode to raster name
		//$raster = "pd_".strtolower($a);
		global $swdbcon;
		$query = "select raster from itis_raster where itiscode = {$a}";
		$result = pg_query($swdbcon, $query);
		if($row = pg_fetch_array($result)){
			$raster = $row['raster'];
		} else{
			$raster = "pd_".strtolower($a);
		}

		//calculate 10% padding
		$x_pad = ($this->max_x - $this->min_x) * 0.1;
		$y_pad = ($this->max_y - $this->min_y) * 0.1;
		$max_x = $this->max_x + $x_pad;
		$min_x = $this->min_x - $x_pad;
		$max_y = $this->max_y + $y_pad;
		$min_y = $this->min_y - $y_pad;

		//create map name
		$map = "map".rand(0,999999);

		$str=<<<GRASS_SCRIPT
g.region n={$max_y} s={$min_y} w={$min_x} e={$max_x} &>/dev/null
r.mapcalc {$map} = '{$raster} *  sw_owner' &>/dev/null
cat  /var/www/html/swgap/grass/sw_owner_color | r.colors map={$map} color=rules &>/dev/null
GRASS_SCRIPT;
		system($str);
		return $map;
	}

	public function protection_map($a){

		//convert strelcode to raster name
		//$raster = "pd_".strtolower($a);
		global $swdbcon;
		$query = "select raster from itis_raster where itiscode = {$a}";
		$result = pg_query($swdbcon, $query);
		if($row = pg_fetch_array($result)){
			$raster = $row['raster'];
		} else{
			$raster = "pd_".strtolower($a);
		}

		//calculate 10% padding
		$x_pad = ($this->max_x - $this->min_x) * 0.1;
		$y_pad = ($this->max_y - $this->min_y) * 0.1;
		$max_x = $this->max_x + $x_pad;
		$min_x = $this->min_x - $x_pad;
		$max_y = $this->max_y + $y_pad;
		$min_y = $this->min_y - $y_pad;

		//create map name
		$map = "map".rand(0,999999);

		$str=<<<GRASS_SCRIPT
g.region n={$max_y} s={$min_y} w={$min_x} e={$max_x} &>/dev/null
r.mapcalc {$map} = '{$raster} *  sw_status' &>/dev/null
cat  /var/www/html/swgap/grass/sw_sta_color | r.colors map={$map} color=rules &>/dev/null
GRASS_SCRIPT;
		system($str);
		return $map;
	}

	public function management_map($a){

		//convert strelcode to raster name
		//$raster = "pd_".strtolower($a);
		global $swdbcon;
		$query = "select raster from itis_raster where itiscode = {$a}";
		$result = pg_query($swdbcon, $query);
		if($row = pg_fetch_array($result)){
			$raster = $row['raster'];
		} else{
			$raster = "pd_".strtolower($a);
		}

		//calculate 10% padding
		$x_pad = ($this->max_x - $this->min_x) * 0.1;
		$y_pad = ($this->max_y - $this->min_y) * 0.1;
		$max_x = $this->max_x + $x_pad;
		$min_x = $this->min_x - $x_pad;
		$max_y = $this->max_y + $y_pad;
		$min_y = $this->min_y - $y_pad;

		//create map name
		$map = "map".rand(0,999999);

		$str=<<<GRASS_SCRIPT
g.region n={$max_y} s={$min_y} w={$min_x} e={$max_x} &>/dev/null
r.mapcalc {$map} = '{$raster} *  sw_manage' &>/dev/null
cat  /var/www/html/swgap/grass/sw_manage_color  | r.colors map={$map} color=rules &>/dev/null
GRASS_SCRIPT;
		system($str);
		return $map;
	}

	//function that returns handle to map created for richness
	//accepts as parameter colon delimted species list

	public function richness($a){
		global $swdbcon;

		$species = explode(":", $a);
		for ($i=0; $i<sizeof($species); $i++){
			$species_esc = addslashes($species[$i]);
			$query1 = "select stritiscode from info_spp where strscomnam = '{$species_esc}'";
			$result1 = pg_query($swdbcon, $query1);
			$row1 = pg_fetch_array($result1);
			//$layers[$i] = "pd_".strtolower($row1[0]);
			$query2 = "select raster from itis_raster where itiscode = {$row1[0]}";
			//echo $query2; /*
			$result2 = pg_query($swdbcon, $query2);
			if($row2 = pg_fetch_array($result2)){
				$layers[$i] = $row2['raster'];
			} else{
				$layers[$i] = "pd_".strtolower($row1['stritiscode']);
			}
		}
		$layer_str = implode(" + ", $layers);
		$rules_file = "/var/www/html/swgap/grass/richness_rule";

		//calculate 10% padding
		$x_pad = ($this->max_x - $this->min_x) * 0.1;
		$y_pad = ($this->max_y - $this->min_y) * 0.1;
		$max_x = $this->max_x + $x_pad;
		$min_x = $this->min_x - $x_pad;
		$max_y = $this->max_y + $y_pad;
		$min_y = $this->min_y - $y_pad;

		//create map name
		$map = "map".rand(0,999999);
		$str=<<<GRASS_SCRIPT
g.region n={$max_y} s={$min_y} w={$min_x} e={$max_x} &>/dev/null
r.mapcalc  {$map} = '{$layer_str}' &>/dev/null
cat {$rules_file} | r.colors map={$map} color=rules &>/dev/null
GRASS_SCRIPT;
		system($str);
		return $map;
	}

	public function richnessreport($a){
		global $swdbcon;
		$species = explode(":", $a);
		for ($i=0; $i<sizeof($species); $i++){
			$species_esc = addslashes($species[$i]);
			$query = "select stritiscode from info_spp where strscomnam  = '$species_esc'";
			$result = pg_query($swdbcon, $query);
			$row = pg_fetch_array($result);
			//$layers[$i] = "pd_".strtolower($row[0]);
			$query2 = "select raster from itis_raster where itiscode = {$row[0]}";
			//echo $query2;
			$result2 = pg_query($swdbcon, $query2);
			if($row2 = pg_fetch_array($result2)){
				$layers[$i] = $row2['raster'];
			} else{
				$layers[$i] = "pd_".strtolower($row['stritiscode']);
			}
		}
		$layer_str = implode(" + ", $layers);
		//var_dump($layers);
		//echo "<pre>";
		$str=<<<GRASS_SCRIPT
g.region n={$this->max_y} s={$this->min_y} w={$this->min_x} e={$this->max_x} &>/dev/null
r.mapcalc {$this->mask_name}richness_report = '{$this->mask_name}  *({$layer_str})' &>/dev/null
r.report -n map={$this->mask_name}richness_report units=a,h,p 2>/dev/null
GRASS_SCRIPT;

		//system($str);
		//echo "</pre>";
		$rep = `$str`;
		return $rep;
	}


	public function richnessexport($a){
		$map = "richness".rand(0,9999999).".tif";
		$str=<<<GRASS_SCRIPT
r.out.gdal input={$a} format=GTiff type=Byte output=/pub/richness_export/{$map}  &>/dev/null
GRASS_SCRIPT;
		system($str);
		return $map;

	}

	/////////////////////////////////////////////////////////////
	///spreadsheet reports for AOI not dependant on species
	//////////////////////////////////////////////////////////

	public function aoi_landcover_ss(){
		//echo "test";
		$report_name = "report".rand(0,999999);
		$str=<<<GRASS_SCRIPT
g.region n={$this->max_y} s={$this->min_y} w={$this->min_x} e={$this->max_x} &>/dev/null
r.stats -a -p -l -n input={$this->mask_name}recl_lc fs=tab output=/data/server_temp/$report_name &>/dev/null
GRASS_SCRIPT;
		system($str);

		//add column headers
		$handle1 = @fopen("/data/server_temp/{$report_name}", "r");
		$handle2 = @fopen("/data/server_temp/{$report_name}.xls", "w");
		$column_headers = "# \tdescription \tsq meters \t% cover \n";
		fwrite($handle2, $column_headers);
		while (!feof($handle1)) {
			$buffer = fgets($handle1, 4096);
			fwrite($handle2, $buffer);
		}
		fclose($handle1);
		fclose($handle2);
		return $report_name.".xls";
	}

	public function aoi_management_ss(){
		$report_name = "report".rand(0,999999);
		$str=<<<GRASS_SCRIPT
g.region n={$this->max_y} s={$this->min_y} w={$this->min_x} e={$this->max_x} &>/dev/null
r.stats -a -p -l -n input={$this->mask_name}recl_man fs=tab output=/data/server_temp/$report_name &>/dev/null
GRASS_SCRIPT;
		system($str);

		//add column headers
		$handle1 = @fopen("/data/server_temp/{$report_name}", "r");
		$handle2 = @fopen("/data/server_temp/{$report_name}.xls", "w");
		$column_headers = "# \tdescription \tsq meters \t% cover \n";
		fwrite($handle2, $column_headers);
		while (!feof($handle1)) {
			$buffer = fgets($handle1, 4096);
			fwrite($handle2, $buffer);
		}
		fclose($handle1);
		fclose($handle2);
		return $report_name.".xls";
	}

	public  function aoi_ownership_ss(){
		$report_name = "report".rand(0,999999);
		$str=<<<GRASS_SCRIPT
g.region n={$this->max_y} s={$this->min_y} w={$this->min_x} e={$this->max_x} &>/dev/null
r.stats -a -p -l -n input={$this->mask_name}recl_own fs=tab output=/data/server_temp/$report_name &>/dev/null
GRASS_SCRIPT;
		system($str);

		//add column headers
		$handle1 = @fopen("/data/server_temp/{$report_name}", "r");
		$handle2 = @fopen("/data/server_temp/{$report_name}.xls", "w");
		$column_headers = "# \tdescription \tsq meters \t% cover \n";
		fwrite($handle2, $column_headers);
		while (!feof($handle1)) {
			$buffer = fgets($handle1, 4096);
			fwrite($handle2, $buffer);
		}
		fclose($handle1);
		fclose($handle2);
		return $report_name.".xls";
	}

	public function aoi_status_ss(){
		$report_name = "report".rand(0,999999);
		$str=<<<GRASS_SCRIPT
g.region n={$this->max_y} s={$this->min_y} w={$this->min_x} e={$this->max_x} &>/dev/null
r.stats -a -p -l -n input={$this->mask_name}recl_stat fs=tab output=/data/server_temp/$report_name &>/dev/null
GRASS_SCRIPT;
		system($str);

		//add column headers
		$handle1 = @fopen("/data/server_temp/{$report_name}", "r");
		$handle2 = @fopen("/data/server_temp/{$report_name}.xls", "w");
		$column_headers = "# \tdescription \tsq meters \t% cover \n";
		fwrite($handle2, $column_headers);
		while (!feof($handle1)) {
			$buffer = fgets($handle1, 4096);
			fwrite($handle2, $buffer);
		}
		fclose($handle1);
		fclose($handle2);
		return $report_name.".xls";
	}

	/////////////////////////////////////////////////////////////////////////////////
	////spreadsheet reports dependant on species
	///////////////////////////////////////////////////////////////////////////

	public function species_status_ss(){
		$report_name = "report".rand(0,999999);

		$str=<<<GRASS_SCRIPT
g.region n={$this->max_y} s={$this->min_y} w={$this->min_x} e={$this->max_x} &>/dev/null
r.stats -a -p -l -n input={$this->mask_name}recl_stat_sp fs=tab output=/data/server_temp/$report_name &>/dev/null
GRASS_SCRIPT;
		system($str);

		//add column headers
		$handle1 = @fopen("/data/server_temp/{$report_name}", "r");
		$handle2 = @fopen("/data/server_temp/{$report_name}.xls", "w");
		$column_headers = "# \tdescription \tsq meters \t% cover \n";
		fwrite($handle2, $column_headers);
		while (!feof($handle1)) {
			$buffer = fgets($handle1, 4096);
			fwrite($handle2, $buffer);
		}
		fclose($handle1);
		fclose($handle2);
		return $report_name.".xls";
	}

	public function species_landcover_ss(){
		$report_name = "report".rand(0,999999);

		$str=<<<GRASS_SCRIPT
g.region n={$this->max_y} s={$this->min_y} w={$this->min_x} e={$this->max_x} &>/dev/null
r.stats -a -p -l -n input={$this->mask_name}recl_lc_sp fs=tab output=/data/server_temp/$report_name &>/dev/null
GRASS_SCRIPT;
		system($str);

		//add column headers
		$handle1 = @fopen("/data/server_temp/{$report_name}", "r");
		$handle2 = @fopen("/data/server_temp/{$report_name}.xls", "w");
		$column_headers = "# \tdescription \tsq meters \t% cover \n";
		fwrite($handle2, $column_headers);
		while (!feof($handle1)) {
			$buffer = fgets($handle1, 4096);
			fwrite($handle2, $buffer);
		}
		fclose($handle1);
		fclose($handle2);
		return $report_name.".xls";
	}

	public function species_management_ss(){
		$report_name = "report".rand(0,999999);

		$str=<<<GRASS_SCRIPT
g.region n={$this->max_y} s={$this->min_y} w={$this->min_x} e={$this->max_x} &>/dev/null
r.mapcalc {$this->mask_name}calc_man_sp = '{$this->mask_name}  *{$raster}*  sw_manage' &>/dev/null
cat /var/www/html/swgap/grass/sw_manage_recl | r.reclass input={$this->mask_name}calc_man_sp output={$this->mask_name}recl_man_sp &>/dev/null
r.stats -a -p -l -n input={$this->mask_name}recl_man_sp fs=tab output=/data/server_temp/$report_name &>/dev/null
GRASS_SCRIPT;
		system($str);

		//add column headers
		$handle1 = @fopen("/data/server_temp/{$report_name}", "r");
		$handle2 = @fopen("/data/server_temp/{$report_name}.xls", "w");
		$column_headers = "# \tdescription \tsq meters \t% cover \n";
		fwrite($handle2, $column_headers);
		while (!feof($handle1)) {
			$buffer = fgets($handle1, 4096);
			fwrite($handle2, $buffer);
		}
		fclose($handle1);
		fclose($handle2);
		return $report_name.".xls";
	}

	public function species_ownership_ss(){
		$report_name = "report".rand(0,999999);

		$str=<<<GRASS_SCRIPT
g.region n={$this->max_y} s={$this->min_y} w={$this->min_x} e={$this->max_x} &>/dev/null
r.mapcalc {$this->mask_name}calc_own_sp = '{$this->mask_name}  *{$raster}* sw_owner' &>/dev/null
cat /var/www/html/swgap/grass/sw_owner_recl | r.reclass input={$this->mask_name}calc_own_sp output={$this->mask_name}recl_own_sp &>/dev/null
r.stats -a -p -l -n input={$this->mask_name}recl_own_sp fs=tab output=/data/server_temp/$report_name &>/dev/null
GRASS_SCRIPT;
		system($str);

		//add column headers
		$handle1 = @fopen("/data/server_temp/{$report_name}", "r");
		$handle2 = @fopen("/data/server_temp/{$report_name}.xls", "w");
		$column_headers = "# \tdescription \tsq meters \t% cover \n";
		fwrite($handle2, $column_headers);
		while (!feof($handle1)) {
			$buffer = fgets($handle1, 4096);
			fwrite($handle2, $buffer);
		}
		fclose($handle1);
		fclose($handle2);
		return $report_name.".xls";
	}

	public function predicted_ss(){
		$report_name = "report".rand(0,999999);

		$str=<<<GRASS_SCRIPT
g.region n={$this->max_y} s={$this->min_y} w={$this->min_x} e={$this->max_x} &>/dev/null
r.mapcalc {$this->mask_name}calc_pred = '{$this->mask_name}  *{$raster}' &>/dev/null
cat /var/www/html/swgap/grass/sw_pred_recl | r.reclass input={$this->mask_name}calc_pred output={$this->mask_name}recl_pred &>/dev/null
r.stats -a -p -l -n input={$this->mask_name}recl_pred fs=tab output=/data/server_temp/$report_name &>/dev/null
GRASS_SCRIPT;
		system($str);

		//add column headers
		$handle1 = @fopen("/data/server_temp/{$report_name}", "r");
		$handle2 = @fopen("/data/server_temp/{$report_name}.xls", "w");
		$column_headers = "# \tdescription \tsq meters \t% cover \n";
		fwrite($handle2, $column_headers);
		while (!feof($handle1)) {
			$buffer = fgets($handle1, 4096);
			fwrite($handle2, $buffer);
		}
		fclose($handle1);
		fclose($handle2);
		return $report_name.".xls";
	}

	public function richnessreport_ss(){
		$report_name = "report".rand(0,999999);

		$str=<<<GRASS_SCRIPT
g.region n={$this->max_y} s={$this->min_y} w={$this->min_x} e={$this->max_x} &>/dev/null
r.stats -a -p -l -n input={$this->mask_name}richness_report  fs=tab output=/data/server_temp/$report_name
GRASS_SCRIPT;
		system($str);

		//add column headers
		$handle1 = @fopen("/data/server_temp/{$report_name}", "r");
		$handle2 = @fopen("/data/server_temp/{$report_name}.xls", "w");
		$column_headers = "# \tdescription \tsq meters \t% cover \n";
		fwrite($handle2, $column_headers);
		while (!feof($handle1)) {
			$buffer = fgets($handle1, 4096);
			fwrite($handle2, $buffer);
		}
		fclose($handle1);
		fclose($handle2);
		return $report_name.".xls";
}

}

?>