<?php
require("sw_config.php");
$swdbcon = pg_connect($pg_connect);

function get_custom_aoi($aoi_name, $result_x, $result_y, $result_ext, $size_w, $size_h ){
	global $swdbcon;

	//put results into arrays
	$click_x_vals = explode(",", $result_x);
	$click_y_vals = explode(",", $result_y);
	$mapext = explode(" ", $result_ext);

	//convert extent arrays to variables
	$minx = $mapext[0];
	$miny = $mapext[1];
	$maxx = $mapext[2];
	$maxy = $mapext[3];
	$extx = $maxx - $minx;
	$exty = $maxy - $miny;

	//calculate x values of map co-ords
	$i=0;
	foreach($click_x_vals as $click_x_val){
		$x[$i++] = (($click_x_val/$size_w)*$extx+$minx);
	}

	//calculate y values of map co-ords
	$i=0;
	foreach($click_y_vals as $click_y_val){
		$y[$i++] = ((($size_h - $click_y_val)/$size_h)*$exty+$miny);
	}

	//create query to make aoi
	$query_values = "";
	for($i=0; $i<count($x); $i++){
		$query_values = $query_values."$x[$i] $y[$i], ";
	}

	$query_values = $query_values."$x[0] $y[0]";
	$query = "insert into aoi(wkb_geometry, name) values
     ((select multi(intersection(GeometryFromText('MULTIPOLYGON((($query_values)))', 32769),wkb_geometry)) from sw_bnd where ogc_fid = 2), '{$aoi_name}')";
	pg_query($swdbcon, $query);

	/*
	GeometryFromText('MULTIPOLYGON((($query_values)))', 32119)


	*/
}

function get_predefined_aoi($aoi_name, $owner_aoi, $manage_aoi, $status_aoi, $county_aoi, $state_aoi, $basin_aoi, $bcr_aoi, $ecosys_aoi){
	global $swdbcon;
	//pg_connect("host=localhost dbname=ncgap user=postgres");
	// $key_counties =  get_key_counties($counties);
	//$key_wtshds = get_key_wtshds($watersheds);
	//$key_subwtshds = get_key_subwtshds($swatersheds);
	$key_gapown = explode(":", $owner_aoi);
	$key_gapman = explode(":", $manage_aoi);
	$key_gapsta = explode(":", $status_aoi);
	$key_county = explode(":", $county_aoi);
	$key_state = explode(":", $state_aoi);
	$key_basin = explode(":", $basin_aoi);
	$key_bcr = explode(":", $bcr_aoi);

	if ($ecosys_aoi == 1) {
		$query = "insert into aoi(name, wkb_geometry) values ('{$aoi_name}',
		(select multi(wkb_geometry) from sw_bnd where ogc_fid = 2))";
		pg_query($swdbcon, $query);
		return "<p>created aoi named ".$aoi_name."</p>";
	}

	$feature_count = 0;
	$query = "insert into aoi(name) values ('{$aoi_name}')";
	pg_query($swdbcon, $query);
	if(strlen($key_county[0]) != 0){
		for ($i = 0; $i < count($key_county); $i++){
			if ($feature_count == 0) {
				$query2 = "update aoi set wkb_geometry =
            (select multi(wkb_geometry) from sw_counties where ogc_fid = '{$key_county[$i]}')
	         where name = '{$aoi_name}'";
				// echo $query2."\n";
				pg_query($swdbcon, $query2);
			}else {
				$query3 = "update aoi set wkb_geometry =
            (select multi(geomunion(aoi.wkb_geometry, sw_counties.wkb_geometry)) from aoi, sw_counties  
            where aoi.name = '{$aoi_name}' and sw_counties.ogc_fid = '{$key_county[$i]}')
	         where aoi.name = '{$aoi_name}'";
				//echo $query3."\n";
				pg_query($swdbcon, $query3);
			}
			$feature_count++;
		}
	}
	if(strlen($key_state[0]) != 0){
		for ($i = 0; $i < count($key_state); $i++){
			if ($feature_count == 0) {
				$query2 = "update aoi set wkb_geometry =
            (select multi(wkb_geometry) from sw_states where ogc_fid = '{$key_state[$i]}')
	         where name = '{$aoi_name}'";
				pg_query($swdbcon, $query2);
			}else {
				$query3 = "update aoi set wkb_geometry =
            (select multi(geomunion(aoi.wkb_geometry, sw_states.wkb_geometry)) from aoi, sw_states  
            where aoi.name = '{$aoi_name}' and sw_states.ogc_fid = '{$key_state[$i]}')
	         where aoi.name = '{$aoi_name}'";
				//echo $query3."\n";
				pg_query($swdbcon, $query3);
			}
			$feature_count++;
		}
	}
	if(strlen($key_basin[0]) != 0){
		for ($i = 0; $i < count($key_basin); $i++){
			if ($feature_count == 0) {
				$query2 = "update aoi set wkb_geometry =
            (select multi(wkb_geometry) from sw_wtshds_gap where ogc_fid = '{$key_basin[$i]}')
	         where name = '{$aoi_name}'";
				// echo $query2."\n";
				pg_query($swdbcon, $query2);
			}else {
				$query3 = "update aoi set wkb_geometry =
            (select multi(geomunion(aoi.wkb_geometry, sw_wtshds_gap.wkb_geometry)) from aoi, sw_wtshds_gap 
            where aoi.name = '{$aoi_name}' and sw_wtshds_gap.ogc_fid = '{$key_basin[$i]}')
	         where aoi.name = '{$aoi_name}'";
				//echo $query3."\n";
				pg_query($swdbcon, $query3);
			}
			$feature_count++;
		}
	}
	if(strlen($key_bcr[0]) != 0){
		for ($i = 0; $i < count($key_bcr); $i++){
			if ($feature_count == 0) {
				$query2 = "update aoi set wkb_geometry =
            (select multi(wkb_geometry) from sw_bcr where ogc_fid = '{$key_bcr[$i]}')
	         where name = '{$aoi_name}'";
				// echo $query2."\n";
				pg_query($swdbcon, $query2);
			}else {
				$query3 = "update aoi set wkb_geometry =
            (select multi(geomunion(aoi.wkb_geometry, sw_bcr.wkb_geometry)) from aoi, sw_bcr 
            where aoi.name = '{$aoi_name}' and sw_bcr.ogc_fid = '{$key_bcr[$i]}')
	         where aoi.name = '{$aoi_name}'";
				//echo $query3."\n";
				pg_query($swdbcon, $query3);
			}
			$feature_count++;
		}
	}
	if(strlen($key_gapsta[0]) != 0){
		for ($i = 0; $i < count($key_gapsta); $i++){
			if ($feature_count == 0) {
				$query2 = "update aoi set wkb_geometry =
            (select multi(wkb_geometry) from nc_status where ogc_fid = '{$key_gapsta[$i]}')
	         where name = '{$aoi_name}'";
				// echo $query2."\n";
				pg_query($swdbcon, $query2);
			}else {
				$query3 = "update aoi set wkb_geometry =
            (select multi(geomunion(aoi.wkb_geometry, nc_status.wkb_geometry)) from aoi, nc_status  
            where aoi.name = '{$aoi_name}' and nc_status.ogc_fid = '{$key_gapsta[$i]}')
	         where aoi.name = '{$aoi_name}'";
				//echo $query3."\n";
				pg_query($swdbcon, $query3);
			}
			$feature_count++;
		}
	}

	if(strlen($key_gapown[0]) != 0){
		for ($i = 0; $i < count($key_gapown); $i++){
			//echo $key_gapown[$i];
			$query = "delete from aoi where name = '{$aoi_name}' and wkb_geometry is null";
			pg_query($swdbcon, $query);
			$a = explode("|", $key_gapown[$i]);
			$query = "select ogc_fid from sw_owner where state_fips = {$a[0]} and own_c = {$a[1]}";
			//echo $query;
			$result = pg_query($swdbcon, $query);
			while ($row = pg_fetch_array($result)) {
				$query2 = "insert into aoi (name, wkb_geometry) values ('{$aoi_name}',
				(select multi(wkb_geometry) from sw_owner where ogc_fid = '{$row[0]}'))";
				pg_query($swdbcon, $query2);
			}
		}
	}

	if(strlen($key_gapman[0]) != 0){
		for ($i = 0; $i < count($key_gapman); $i++){
			//echo $key_gapown[$i];
			$query = "delete from aoi where name = '{$aoi_name}' and wkb_geometry is null";
			pg_query($swdbcon, $query);
			$a = explode("|", $key_gapman[$i]);
			$query = "select ogc_fid from sw_manage where state_fips = {$a[0]} and  man_c_cond = {$a[1]}";
			//echo $query;
			$result = pg_query($swdbcon, $query);
			while ($row = pg_fetch_array($result)) {
				$query2 = "insert into aoi (name, wkb_geometry) values ('{$aoi_name}',
				(select multi(wkb_geometry) from sw_manage where ogc_fid = '{$row[0]}'))";
				pg_query($swdbcon, $query2);
			}
		}
	}


	return "<p>created aoi named ".$aoi_name."</p>";
}

function get_uploaded_aoi($aoi_name, $file_shp){
	global $swdbcon;

	//clean temp table
	$query = "delete from aoi_upload where name is null";
	//$query = "insert into aoi_upload (name) values ('test')";
	pg_query($swdbcon, $query);

	//upload file to temp table and give all rows aoi name
	$gdal_cmd = "/usr/local/bin/ogr2ogr -update -append  -f PostgreSQL  PG:'dbname=swgap user=postgres host=localhost'  {$file_shp} -t_srs /var/www/html/data/swgap/sw_proj_32769  -nln aoi_upload -nlt MULTIPOLYGON";
	//throw new Exception($gdal_cmd);
	exec($gdal_cmd);
	$query2 = "update aoi_upload set name = '{$aoi_name}' where name is null";
	pg_query($swdbcon, $query2);

	//create union of temp rows  into aoi table
	$feature_count = $row_count = 0;
	$query = "insert into aoi(name) values ('{$aoi_name}')";
	pg_query($swdbcon, $query);
	$query = "select ogc_fid from aoi_upload where name = '{$aoi_name}'";
	$result =  pg_query($swdbcon, $query);
	while($row = pg_fetch_array($result)){
		$key_upload[$row_count++] = $row[0];
	}

	for ($i = 0; $i < count($key_upload); $i++){
		//clip all polygons to full sw extent
		//this may be too slow for complicated shapefiles
		//$query4 = "update aoi_upload set wkb_geometry = (select multi(intersection(aoi_upload.wkb_geometry, sw_bnd.wkb_geometry))
	   // from sw_bnd, aoi_upload where sw_bnd.ogc_fid = 2 and aoi_upload.ogc_fid = '{$key_upload[$i]}') where aoi_upload.ogc_fid = '{$key_upload[$i]}'";
		//pg_query($swdbcon, $query4);

		if ($feature_count == 0) {
			$query2 = "update aoi set wkb_geometry =
            (select multi(wkb_geometry) from aoi_upload where ogc_fid = '{$key_upload[$i]}')
	         where name = '{$aoi_name}'";
			//echo $query2."\n";
			pg_query($swdbcon, $query2);
		}else {
			$query3 = "update aoi set wkb_geometry =
            (select multi(geomunion(aoi.wkb_geometry, aoi_upload.wkb_geometry)) from aoi,  aoi_upload 
            where aoi.name = '{$aoi_name}' and aoi_upload.ogc_fid = '{$key_upload[$i]}')
	         where aoi.name = '{$aoi_name}'";
			//echo $query3."\n";
			pg_query($swdbcon, $query3);
		}
		$feature_count++;
	}
	
	//old way of clipping to full extent, not perfect, but use it for now
	//upgrade geos postgis fixes some problems with this way!!! Yahoo!!!
	$query = "update aoi set wkb_geometry = (select multi(intersection(aoi.wkb_geometry, sw_bnd.wkb_geometry))
	    from sw_bnd, aoi where sw_bnd.ogc_fid = 2 and aoi.name = '{$aoi_name}') where aoi.name = '{$aoi_name}'";
	pg_query($swdbcon, $query);


	//cleanup temp table
	$query = "delete from aoi_upload where name = '{$aoi_name}'";
	//pg_query($swdbcon, $query);


}

?>