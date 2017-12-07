<?php

$swdbcon = pg_connect("host=localhost dbname=swgap user=postgres");

ini_set("display_errors", 0);
ini_set("error_log", "/var/www/html/swgap/logs/php-error.log");

//function to that does work of construction to assign class values
 function create($aoi_predefined, $aoi_name){

      global $swdbcon;

		$key_gapown = explode(":", $aoi_predefined['owner_aoi']);
		$key_gapman = explode(":", $aoi_predefined['manage_aoi']);
		$key_county = explode(":", $aoi_predefined['county_aoi']);
		$key_basin = explode(":", $aoi_predefined['basin_aoi']);
		$key_state = explode(":", $aoi_predefined['state_aoi']);
		$key_bcr = explode(":", $aoi_predefined['bcr_aoi']);

		if (strlen($key_gapown[0] == 0)) unset($key_gapown);
		if (strlen($key_gapman[0] == 0)) unset($key_gapman);
		if (strlen($key_county[0] == 0)) unset($key_county);
		if (strlen($key_basin[0] == 0)) unset($key_basin);
		if (strlen($key_state[0] == 0)) unset($key_state);
		if (strlen($key_bcr[0] == 0)) unset($key_bcr);

		//calcuate ranges from tables for predefined aoi
		if ($aoi_predefined['ecosys_aoi'] == 1) {
			//die();
			$query = "select ogc_fid from sw_wtshds_gap";
			$result=pg_query($swdbcon, $query) or die('failed spatial query to database');
			$i=0;
			while(($row = pg_fetch_array($result)) !== FALSE){
				$range[$i++] = $row[0];
			}
		}elseif (isset($key_gapown) || isset($key_gapman) || isset($key_county) || isset($key_basin) || isset($key_state) || isset($key_bcr)){
			//if (false){
			$j=0;

			for ($i=0; $i<sizeof($key_county); $i++){
				$query = "select sw_wtshds_gap_ogc_fid from range_from_aoi where sw_counties_ogc_fid  = {$key_county[$i]}";
				$results = pg_query($swdbcon, $query);
				while($row = pg_fetch_array($results)){
					$range[$j++] = $row['sw_wtshds_gap_ogc_fid'];
				}
			}

			for ($i=0; $i<sizeof($key_basin); $i++){
				$range[$j++] = $key_basin[$i];
			}

			for ($i=0; $i<sizeof($key_gapown); $i++){
				$a = explode("|", $key_gapown[$i]);
				$query = "select ogc_fid from sw_owner where state_fips = {$a[0]} and own_c = {$a[1]}";
				$result = pg_query($swdbcon, $query);
				while ($row = pg_fetch_array($result)) {
					$query = "select sw_wtshds_gap_ogc_fid from range_from_aoi where sw_owner_ogc_fid  = {$row['ogc_fid']}";
					$results = pg_query($swdbcon, $query);
					while($row = pg_fetch_array($results)){
						$range[$j++] = $row['sw_wtshds_gap_ogc_fid'];
					}
				}
			}
			for ($i=0; $i<sizeof($key_gapman); $i++){
				$a = explode("|", $key_gapman[$i]);
				$query = "select ogc_fid from sw_manage where state_fips = {$a[0]} and man_c_cond = {$a[1]}";
				$result = pg_query($swdbcon, $query);
				while ($row = pg_fetch_array($result)) {
					$query = "select sw_wtshds_gap_ogc_fid from range_from_aoi where sw_manage_ogc_fid  = {$row['ogc_fid']}";
					$results = pg_query($swdbcon, $query);
					while($row = pg_fetch_array($results)){
						$range[$j++] = $row['sw_wtshds_gap_ogc_fid'];
					}
				}
			}

			for ($i=0; $i<sizeof($key_state); $i++){
				$query = "select sw_wtshds_gap_ogc_fid from range_from_aoi where sw_states_ogc_fid  = {$key_state[$i]}";
				$results = pg_query($swdbcon, $query);
				while($row = pg_fetch_array($results)){
					$range[$j++] = $row['sw_wtshds_gap_ogc_fid'];
				}
			}

			for ($i=0; $i<sizeof($key_bcr); $i++){
				$query = "select sw_wtshds_gap_ogc_fid from range_from_aoi where sw_bcr_ogc_fid  = {$key_bcr[$i]}";
				$results = pg_query($swdbcon, $query);
				while($row = pg_fetch_array($results)){
					$range[$j++] = $row['sw_wtshds_gap_ogc_fid'];
				}
			}
		}
		//else calculate from geometry for custom aoi
		else{
			$query2 = "select ogc_fid from sw_wtshds_gap  where intersects(
       (select wkb_geometry from aoi where name = '{$aoi_name}'),
       sw_wtshds_gap.wkb_geometry)";
			$result=pg_query($swdbcon, $query2) or die('failed spatial query to database');
			$i=0;
			while(($row = pg_fetch_array($result)) !== FALSE){
				$range[$i++] = $row[0];
			}
		}
		$range = array_unique($range);

		//get strelcodes (swgap itiscodes) and store as key in associative array with key as strelcode (itis code) and value 0
		//loop through strelcodes (itiscodes) and ranges to find species in aoi and store in array
		$query = "select distinct itiscode from specieslist";
		$result = pg_query($swdbcon, $query);
		while(($row = pg_fetch_array($result)) !== FALSE){
			$itiscodes[$row[0]] = 0;
		}

		//join sw_wtshd_species, sw_wtshds_gap on huc and select habitat code
		//from list of watershed primary keys from above
		//$keys is array of all itiscodes
		$keys = array_keys($itiscodes);
		for($i=0; $i<count($keys); $i++){
			foreach ($range as $r){   //($j=0; $j<sizeof($range); $j++){
				$query = "select sw_wtshd_species.whrcatid from sw_wtshd_species, sw_wtshds_gap where sw_wtshds_gap.ogc_fid = {$r} and sw_wtshds_gap.huc = sw_wtshd_species.huc and  sw_wtshd_species.itiscode = {$keys[$i]}";
				$query2 = "select sw_wtshd_species2.whrcatid from sw_wtshd_species2, sw_wtshds_gap where sw_wtshds_gap.ogc_fid = {$r} and sw_wtshds_gap.huc = sw_wtshd_species2.huc and  sw_wtshd_species2.itiscode = {$keys[$i]}";
				//echo $query2; die();
				$result = pg_query($swdbcon, $query2);
				$row = pg_fetch_array($result);
				if(isset($row[0]) && ($row[0]!=61) && ($row[0]!=62) && ($row[0]!=69)){
					$itiscodes[$keys[$i]]=1;
					break;
				}

			}
		}

		//loop through strelcodes to calculate numbers of species for protection status in range
		$all_species =$fed_species =  $prot_az = $prot_co = $prot_nm = $prot_nv = $prot_ut =
		$ns_global = $ns_az = $ns_co =  $ns_nm = $ns_nv = $ns_ut = $sgcn_az = $sgcn_co = $sgcn_nv =
		$sgcn_nm = $sgcn_ut = $pif_g_basin = $pif_n_rockies = $pif_s_rockies = $pif_shortgrass =
		$pif_sonoran = $pif_s_madre = $pif_chihuahan = 0;
		$query = "select * from info_spp";
		//$query = "select distinct itiscode from specieslist";
		$result = pg_query($swdbcon, $query);
		while($row = pg_fetch_array($result)){
			if ($itiscodes[$row['stritiscode']] == 1){
				$all_species++;
				if ($row['strusesa'] !== NULL) $fed_species++;
				if ($row['strsprotaz'] !== NULL) $prot_az++;
				if ($row['strsprotco'] !== NULL) $prot_co++;
				if ($row['strsprotnm'] !== NULL) $prot_nm++;
				if ($row['strsprotnv'] !== NULL) $prot_nv++;
				if ($row['strsprotut'] !== NULL) $prot_ut++;
				if ($row['strgrank2'] !== NULL) $ns_global++;
				if ($row['strsrankaz2'] !== NULL) $ns_az++;
				if ($row['strsrankco2'] !== NULL) $ns_co++;
				if ($row['strsranknm2'] !== NULL) $ns_nm++;
				if ($row['strsranknv2'] !== NULL) $ns_nv++;
				if ($row['strsrankut2'] !== NULL) $ns_ut++;
				if ($row['strsgcnaz'] !== NULL) $sgcn_az++;
				if ($row['strsgcnco'] !== NULL) $sgcn_co++;
				if ($row['strsgcnnv'] !== NULL) $sgcn_nv++;
				if ($row['strsgcnnm'] !== NULL) $sgcn_nm++;
				if ($row['strsgcnut'] !== NULL) $sgcn_ut++;
				if ($row['strpif09'] !== NULL) $pif_g_basin++;
				if ($row['strpif10'] !== NULL) $pif_n_rockies++;
				if ($row['strpif16'] !== NULL) $pif_s_rockies++;
				if ($row['strpif18'] !== NULL) $pif_shortgrass++;
				if ($row['strpif33'] !== NULL) $pif_sonoran++;
				if ($row['strpif34'] !== NULL) $pif_s_madre++;
				if ($row['strpif35'] !== NULL) $pif_chihuahan++;

			}
		}

      $result = array();
      //assign class variable from preceeding calculations

		$result['range'] = $range;
		$result['itiscodes'] = $itiscodes;
		$result['fed_species'] = $fed_species;
		$result['all_species'] = $all_species;
		$result['prot_az'] = $prot_az;
		$result['prot_co'] = $prot_co;
		$result['prot_nm'] = $prot_nm;
		$result['prot_nv'] = $prot_nv;
		$result['prot_ut'] = $prot_ut;
		$result['ns_global'] = $ns_global;
		$result['ns_az'] = $ns_az;
		$result['ns_co'] = $ns_co;
		$result['ns_nm'] = $ns_nm;
		$result['ns_nv'] = $ns_nv;
		$result['ns_ut'] = $ns_ut;
		$result['sgcn_az'] = $sgcn_az;
		$result['sgcn_co'] = $sgcn_co;
		$result['sgcn_nv'] = $sgcn_nv;
		$result['sgcn_nm'] = $sgcn_nm;
		$result['sgcn_ut'] = $sgcn_ut;
		$result['pif_g_basin'] = $pif_g_basin;
		$result['pif_n_rockies'] = $pif_n_rockies;
		$result['pif_s_rockies'] = $pif_s_rockies;
		$result['pif_shortgrass'] = $pif_shortgrass;
		$result['pif_sonoran'] = $pif_sonoran;
		$result['pif_s_madre'] = $pif_s_madre;
		$result['pif_chihuahan'] = $pif_chihuahan;

      return $result;
 }

class sw_range_class
{
	private $range;
	private $itiscodes;
	public $num_species;
	private $tot_class;
	private $query;

	function __construct($aoi_name)	{
		error_log("sw_range_class");

		global $swdbcon;
		$query = "select aoi_data from aoi where name = '{$aoi_name}'";
		$result = pg_query($swdbcon, $query);
		$row = pg_fetch_array($result);
		$aoi_predefined = unserialize($row['aoi_data']);
		error_log($aoi_predefined);

      //check of AOI is predefined if so set is_predefined to true to submit function to zend cache
      $is_predefined = false;
      if($aoi_predefined){
         foreach($aoi_predefined as $v){
            if(strlen($v) != 0){$is_predefined = true; break;}
         }
      }

  //     //use zend cache to cache results for function create
		// require_once 'Zend/Loader.php';
  //     Zend_Loader::loadClass('Zend_Cache');
  //     try{
  //        $frontendOptions = array(
  //           'lifetime' => null, // cache lifetime no expiration
  //           'automatic_serialization' => true
  //        );
  //        $backendOptions = array(
  //            'cache_dir' => '../../temp/' // Directory where to put the cache files
  //        );
  //        // getting a Zend_Cache_Core object
  //        $cache = Zend_Cache::factory('Function',
  //                                     'File',
  //                                     $frontendOptions,
  //                                     $backendOptions);
  //     } catch(Exception $e) {
  //       echo $e->getMessage();
  //     }

  //     //call create function
  //     if($is_predefined){
  //        //submit to zend cache
  //        $result = $cache->call('create', array($aoi_predefined, "dummy"));
  //     } else {
  //        //submit as function not to zend cache for custon AOI
  //        $result = create($aoi_predefined, $aoi_name);
  //     }




		//assign class variable from preceeding calculations
		$this->range = $result['range'];
		$this->itiscodes = $result['itiscodes'];

		$this->num_species['fed_species'] = $result['fed_species'];
		$this->num_species['all_species'] = $result['all_species'];
		$this->num_species['prot_az'] = $result['prot_az'];
		$this->num_species['prot_co'] = $result['prot_co'];
		$this->num_species['prot_nm'] = $result['prot_nm'];
		$this->num_species['prot_nv'] = $result['prot_nv'];
		$this->num_species['prot_ut'] = $result['prot_ut'];
		$this->num_species['ns_global'] = $result['ns_global'];
		$this->num_species['ns_az'] = $result['ns_az'];
		$this->num_species['ns_co'] = $result['ns_co'];
		$this->num_species['ns_nm'] = $result['ns_nm'];
		$this->num_species['ns_nv'] = $result['ns_nv'];
		$this->num_species['ns_ut'] = $result['ns_ut'];
		$this->num_species['sgcn_az'] = $result['sgcn_az'];
		$this->num_species['sgcn_co'] = $result['sgcn_co'] ;
		$this->num_species['sgcn_nv'] = $result['sgcn_nv'] ;
		$this->num_species['sgcn_nm'] = $result['sgcn_nm'];
		$this->num_species['sgcn_ut'] = $result['sgcn_ut'];
		$this->num_species['pif_g_basin'] = $result['pif_g_basin'];
		$this->num_species['pif_n_rockies'] = $result['pif_n_rockies'];
		$this->num_species['pif_s_rockies'] = $result['pif_s_rockies'] ;
		$this->num_species['pif_shortgrass'] = $result['pif_shortgrass'];
		$this->num_species['pif_sonoran'] = $result['pif_sonoran'];
		$this->num_species['pif_s_madre'] = $result['pif_s_madre'];
		$this->num_species['pif_chihuahan'] = $result['pif_chihuahan'];


	}
	////////////////////////////////////////////////////////////////////////////////
	////////////end constructor
	//////////////////////////////////////////////////////////////////////////////////

	//given selections of controls3.php calculate numbers of each class for that selection
	//by constructing query, save query as class variable to get list of species
	//returns associative array
	function num_class($species, $sel, $fed, $state, $gap, $nsglobal, $nsstate, $pif, $sgcn){
		global $swdbcon;

		$query = "select strtaxclas, stritiscode, strscomnam, strelcode, strgname  from info_spp";
		$i=0;

		if ( $species ==='prot'){

			//case fed selected
			if($fed == 'on'){
				$query = $query." where (strusesa is not null";
				$i++;
			}

			//case state selected
			if($state['az'] == 'on'){
				if($i==0) {
					$query = $query." where (strsprotaz is not null";
					$i++;
				}else{
					$query = $query." {$sel} strsprotaz is not null";
				}
			}
			if($state['co'] == 'on'){
				if($i==0) {
					$query = $query." where (strsprotco is not null";
					$i++;
				}else{
					$query = $query." {$sel} strsprotco is not null";
				}
			}

			if($state['nm'] == 'on'){
				if($i==0) {
					$query = $query." where (strsprotnm is not null";
					$i++;
				}else{
					$query = $query." {$sel} strsprotnm is not null";
				}
			}
			if($state['nv'] == 'on'){
				if($i==0) {
					$query = $query." where (strsprotnv is not null";
					$i++;
				}else{
					$query = $query." {$sel} strsprotnv is not null";
				}
			}
			if($state['ut'] == 'on'){
				if($i==0) {
					$query = $query." where (strsprotut is not null";
					$i++;
				}else{
					$query = $query." {$sel} strsprotut is not null";
				}
			}

			/*
			//case gap selected
			if($gap == 'on'){
			if($i==0) {
			$query = $query." where gap_p_all2 is not null";
			$i++;
			}else{
			$query = $query." or gap_p_all2 is not null";
			}
			}*/

			//case nsglobal selected
			if($nsglobal == 'on'){
				if($i==0) {
					$query = $query." where (strgrank2 is not null";
					$i++;
				}else{
					$query = $query." {$sel} strgrank2 is not null";
				}
			}

			//case nsstate selected
			if($nsstate['az'] == 'on'){
				if($i==0) {
					$query = $query." where (strsrankaz2 is not null";
					$i++;
				}else{
					$query = $query." {$sel} strsrankaz2 is not null";
				}
			}
			if($nsstate['co'] == 'on'){
				if($i==0) {
					$query = $query." where (strsrankco2 is not null";
					$i++;
				}else{
					$query = $query." {$sel} strsrankco2 is not null";
				}
			}
			if($nsstate['nm'] == 'on'){
				if($i==0) {
					$query = $query." where (strsranknm2 is not null";
					$i++;
				}else{
					$query = $query." {$sel} strsranknm2 is not null";
				}
			}
			if($nsstate['nv'] == 'on'){
				if($i==0) {
					$query = $query." where (strsranknv2 is not null";
					$i++;
				}else{
					$query = $query." {$sel} strsranknv2 is not null";
				}
			}
			if($nsstate['ut'] == 'on'){
				if($i==0) {
					$query = $query." where (strsrankut2 is not null";
					$i++;
				}else{
					$query = $query." {$sel} strsrankut2 is not null";
				}
			}

			//case sgcn
			if($sgcn['az'] == 'on'){
				if($i==0) {
					$query = $query." where (strsgcnaz is not null";
					$i++;
				}else{
					$query = $query." {$sel} strsgcnaz is not null";
				}
			}
			if($sgcn['co'] == 'on'){
				if($i==0) {
					$query = $query." where (strsgcnco is not null";
					$i++;
				}else{
					$query = $query." {$sel} strsgcnco is not null";
				}
			}
			if($sgcn['nm'] == 'on'){
				if($i==0) {
					$query = $query." where (strsgcnnm is not null";
					$i++;
				}else{
					$query = $query." {$sel} strsgcnnm is not null";
				}
			}
			if($sgcn['nv'] == 'on'){
				if($i==0) {
					$query = $query." where (strsgcnnv is not null";
					$i++;
				}else{
					$query = $query." {$sel} strsgcnnv is not null";
				}
			}
			if($sgcn['ut'] == 'on'){
				if($i==0) {
					$query = $query." where (strsgcnut is not null";
					$i++;
				}else{
					$query = $query." {$sel} strsgcnut is not null";
				}
			}

			//case pif selected
			if($pif['gbas'] == 'on'){
				if($i==0) {
					$query = $query." where (strpif09 is not null";
					$i++;
				}else{
					$query = $query." {$sel} strpif09 is not null";
				}
			}
			if($pif['nrock'] == 'on'){
				if($i==0) {
					$query = $query." where (strpif10 is not null";
					$i++;
				}else{
					$query = $query." {$sel} strpif10 is not null";
				}
			}
			if($pif['srock'] == 'on'){
				if($i==0) {
					$query = $query." where (strpif16 is not null";
					$i++;
				}else{
					$query = $query." {$sel} strpif16 is not null";
				}
			}
			if($pif['sgrass'] == 'on'){
				if($i==0) {
					$query = $query." where (strpif18 is not null";
					$i++;
				}else{
					$query = $query." {$sel} strpif18 is not null";
				}
			}
			if($pif['sonora'] == 'on'){
				if($i==0) {
					$query = $query." where (strpif33 is not null";
					$i++;
				}else{
					$query = $query." {$sel} strpif33 is not null";
				}
			}
			if($pif['smadre'] == 'on'){
				if($i==0) {
					$query = $query." where (strpif34 is not null";
					$i++;
				}else{
					$query = $query." {$sel} strpif34 is not null";
				}
			}
			if($pif['chihua'] == 'on'){
				if($i==0) {
					$query = $query." where (strpif35 is not null";
					$i++;
				}else{
					$query = $query." {$sel} strpif35 is not null";
				}
			}
			if($i>0)$query .=")";
		}

		//get numbers for avian, mammal, rept and amph for all species
		$avian = $mammal = $rept = $amph =  0;
		$result = pg_query($swdbcon, $query);
		while ($row = pg_fetch_array($result)){
			if ($this->itiscodes[$row['stritiscode']] == 1){
				if($row['strtaxclas'] == 'AMPHIBIA') $amph++;
				if($row['strtaxclas'] == 'AVES') $avian++;
				if($row['strtaxclas'] == 'MAMMALIA') $mammal++;
				if($row['strtaxclas'] == 'REPTILIA') $rept++;

			}
		}
		// assign to class variable and return values as associative array
		$this->query = $query;
		$this->tot_class['amph'] = $amph;
		$this->tot_class['avian'] = $avian;
		$this->tot_class['mammal'] = $mammal;
		$this->tot_class['rept'] = $rept;
		return $this->tot_class;
	}
	/////////////////////////////////////////////////////////////////////////////////
	// end function num_class
	////////////////////////////////////////////////////////////////////////////////

	//get list of selected species for select box
	function get_species($avian, $mammal, $reptile, $amphibian, $language){

		global $swdbcon;
		$query = $this->query;
		$query = $query." order by strelcode_sort";
		$itiscodes = $this->itiscodes;
		$result = pg_query($swdbcon, $query);
		while (($row = pg_fetch_array($result))!==FALSE){
			if ($itiscodes[$row['stritiscode']] == 1){
				switch ($language){
					case "strscomnam":
						$display = strtolower($row[$language]);
						break;
					case "strgname":
						$display = ucfirst($row[$language]);
						break;
				}
				if($row['strtaxclas'] == 'AMPHIBIA' && $amphibian == 'on') {
					echo "<option value=\"".$row['strscomnam']."\">".$display."</option>";
				}
				if($row['strtaxclas'] == 'AVES' && $avian == 'on'){
					echo "<option value=\"".$row['strscomnam']."\">".$display."</option>";
				}
				if($row['strtaxclas'] == 'MAMMALIA' && $mammal == 'on') {
					echo "<option value=\"".$row['strscomnam']."\">".$display."</option>";
				}
				if($row['strtaxclas'] == 'REPTILIA' && $reptile) {
					echo "<option value=\"".$row['strscomnam']."\">".$display."</option>";
				}
			}
		}
	}

	function get_species_search($avian, $mammal, $reptile, $amphibian, $language, $search){

		global $swdbcon;
		$query = $this->query;
		if(strpos($query, "where") === false){
			$query .= " where strscomnam ilike '%{$search}%'";
			$query .= " or strgname ilike '%{$search}%'" ;
		} else {
			$query .= " and (strscomnam ilike '%{$search}%'";
			$query .= " or strgname ilike '%{$search}%')" ;
		}
		$query = $query." order by strelcode_sort";
		$itiscodes = $this->itiscodes;
		$result = pg_query($swdbcon, $query);
		while (($row = pg_fetch_array($result))!==FALSE){
			if ($itiscodes[$row['stritiscode']] == 1){
				switch ($language){
					case "strscomnam":
						$display = strtolower($row[$language]);
						break;
					case "strgname":
						$display = ucfirst($row[$language]);
						break;
				}
				if($row['strtaxclas'] == 'AMPHIBIA' && $amphibian == 'on') {
					echo "<option value=\"".$row['strscomnam']."\">".$display."</option>";
				}
				if($row['strtaxclas'] == 'AVES' && $avian == 'on'){
					echo "<option value=\"".$row['strscomnam']."\">".$display."</option>";
				}
				if($row['strtaxclas'] == 'MAMMALIA' && $mammal == 'on') {
					echo "<option value=\"".$row['strscomnam']."\">".$display."</option>";
				}
				if($row['strtaxclas'] == 'REPTILIA' && $reptile) {
					echo "<option value=\"".$row['strscomnam']."\">".$display."</option>";
				}
			}
		}
	}


	function get_species_ss($avian, $mammal, $reptile, $amphibian, $search, $protcats){
		$report_name = "report".rand(0,999999).".xls";
		global $swdbcon;

      $protcat_text = array(
            "fed" => "Federally listed",
            "stateaz" => "AZ state listed",
            "stateco" => "CO state listed",
            "statenm" => "NM state listed",
            "statenv" => "NV state listed",
            "stateut" => "UT state listed",
            "nsglobal" => "NS Global priority",
            "nsaz" => "NS AZ priority",
            "nsco" => "NS CO priority",
            "nsnm" => "NS NM priority",
            "nsnv" => "NS NV priority",
            "nsut" => "NS UT priority",
            "sgcnaz" => "AZ SGCN",
            "sgcnco" => "CO SGCN",
            "sgcnnm" => "NM SGCN",
            "sgcnnv" => "NV SGCN",
            "sgcnut" => "UT SGCN",
            "pifgbas" => "Great Basin PIF",
            "pifnrock" => "N. Rockies PIF",
            "pifsrock" => "S. Rockies/ Co. Plateau PIF",
            "pifsgrass" => "Shortgrass Prairie PIF",
            "pifsonora" => "Sonoran and Mohave Deserts PIF",
            "pifsmadre" => "Sierra Madre Occidental PIF",
            "pifchihua" => "Chihuahuan Desert PIF"
                           );



		//open file for writing and write column headers
		$handle = fopen("/pub/server_temp/{$report_name}", "w+");

		$somecontent = "elcode \t itiscode \t scientific name \t commom name \t";
      foreach(json_decode($protcats) as $protcat) {
         $somecontent .= $protcat_text[$protcat]."\t";
      }
      $somecontent .= "\n";
      fwrite($handle, $somecontent);

		$query = $this->query;
		if(strpos($query, "where") === false){
			$query .= " where strscomnam ilike '%{$search}%'";
			$query .= " or strgname ilike '%{$search}%'" ;
		} else {
			$query .= " and (strscomnam ilike '%{$search}%'";
			$query .= " or strgname ilike '%{$search}%')" ;
		}
		$query = $query." order by strelcode_sort";
		$itiscodes = $this->itiscodes;

      function write_row($handle2, $elcode, $itiscode, $gname, $comnam, $protcats2) {
         global $swdbcon;
            $infospp_cols = array(
                "fed" => "strusesa",
            "stateaz" => "strsprotaz",
            "stateco" => "strsprotco",
            "statenm" => "strsprotnm",
            "statenv" => "strsprotnv",
            "stateut" => "strsprotut",
            "nsglobal" => "strgrank2",
            "nsaz" => "strsrankaz2",
            "nsco" => "strsrankco2",
            "nsnm" => "strsranknm2",
            "nsnv" => "strsranknv2",
            "nsut" => "strsrankut2",
            "sgcnaz" => "strsgcnaz",
            "sgcnco" => "strsgcnco",
            "sgcnnm" => "strsgcnnm",
            "sgcnnv" => "strsgcnnv",
            "sgcnut" => "strsgcnut",
            "pifgbas" => "strpif09",
            "pifnrock" => "strpif10",
            "pifsrock" => "strpif16",
            "pifsgrass" => "strpif18",
            "pifsonora" => "strpif33",
            "pifsmadre" => "strpif34",
            "pifchihua" => "strpif35"
                            );

         $somecontent =  $elcode."\t".$itiscode."\t".$gname."\t".$comnam;
         fwrite($handle2, $somecontent);
         foreach(json_decode($protcats2) as $protct2) {
            $query = "select {$infospp_cols[$protct2]} from info_spp where  stritiscode ilike '%{$itiscode}%'";
            $result = pg_query($swdbcon, $query);
            $row = pg_fetch_row($result);
            fwrite($handle2, "\t".$row[0]);
            //fwrite($handle2, "\t".$query);
         }
         fwrite($handle2, "\n");
      }

		$result = pg_query( $swdbcon, $query);
		while (($row = pg_fetch_array($result))!==FALSE){
			if ($itiscodes[$row['stritiscode']] == 1){
				if($row['strtaxclas'] == 'AMPHIBIA' && $amphibian == 'on') {
               write_row($handle, $row['strelcode'], $row['stritiscode'], $row['strgname'], $row['strscomnam'], $protcats);
				}
				if($row['strtaxclas'] == 'AVES' && $avian == 'on'){
                write_row($handle, $row['strelcode'], $row['stritiscode'], $row['strgname'], $row['strscomnam'], $protcats);
				}
				if($row['strtaxclas'] == 'MAMMALIA' && $mammal == 'on') {
                write_row($handle, $row['strelcode'], $row['stritiscode'], $row['strgname'], $row['strscomnam'], $protcats);
				}
				if($row['strtaxclas'] == 'REPTILIA' && $reptile) {
					//$somecontent = $row['strelcode']."\t".$row['stritiscode']."\t".$row['strgname']."\t".$row['strscomnam']."\n";
					//fwrite($handle, $somecontent);
                write_row($handle, $row['strelcode'], $row['stritiscode'], $row['strgname'], $row['strscomnam'], $protcats);
				}
			}
		}

		fclose($handle);
		return $report_name;
	}

	//get list of selected species for select box
	function get_species_dnld($avian, $mammal, $reptile, $amphibian, $search){

		global $swdbcon;
		$query = $this->query;
		if(strpos($query, "where") === false){
			$query .= " where strscomnam ilike '%{$search}%'";
			$query .= " or strgname ilike '%{$search}%'" ;
		} else {
			$query .= " and (strscomnam ilike '%{$search}%'";
			$query .= " or strgname ilike '%{$search}%')" ;
		}
		$query = $query." order by strelcode";
		$itiscodes = $this->itiscodes;
		$result = pg_query($swdbcon, $query);
		while (($row = pg_fetch_array($result))!==FALSE){
			if ($itiscodes[$row['stritiscode']] == 1){
				if($row['strtaxclas'] == 'AMPHIBIA' && $amphibian == 'on') {
					echo "<tr><td><input type='checkbox' onclick='poll();' name='pds' value='".$row['stritiscode']."' /></td><td>".$row['strscomnam']."</td></tr>";
				}
				if($row['strtaxclas'] == 'AVES' && $avian == 'on'){
					echo "<tr><td><input type='checkbox' onclick='poll();' name='pds' value='".$row['stritiscode']."'/></td><td>".$row['strscomnam']."</td></tr>";
				}
				if($row['strtaxclas'] == 'MAMMALIA' && $mammal == 'on') {
					echo "<tr><td><input type='checkbox' onclick='poll();' name='pds' value='".$row['stritiscode']."'/></td><td>".$row['strscomnam']."</td></tr>";
				}
				if($row['strtaxclas'] == 'REPTILIA' && $reptile) {
					echo "<tr><td><input type='checkbox' onclick='poll();' name='pds' value='".$row['stritiscode']."'/></td><td>".$row['strscomnam']."</td></tr>";
				}
			}
		}
	}

	//tester function
	function test1(){
		var_dump($this->range);
		var_dump($this->strelcodes);
		var_dump($this->num_species);
	}
}

?>