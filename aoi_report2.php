<?php
require('sw_aoi_class.php');
require("sw_config.php");
session_start();
//ob_implicit_flush();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<title>AOI GRASS Report</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style type="text/css">
/* <![CDATA[ */
@media print{
  .prn {display: none; }
  }
  
  body {font-family: sans-serif;}
/* ]]> */
</style>
<script language="javascript" type="text/javascript">
/* <![CDATA[ */
function spreadsheet(){
	//window.open("aoi_report_ss.php","ssrept","height=100,width=300")
	var pretag = document.getElementsByTagName("pre");
	var content = pretag[0].innerHTML;
	document.forms[0].content.value = content;
	document.forms[0].submit();
}
/* ]]> */
</script>
</head>
<body>

<?php
$aoi_name = $_POST['aoi_name'];
$a = $_SESSION[$aoi_name];

$report = $_POST['report'];
$itiscode = $_POST['itiscode'];
$species = stripslashes($_POST['species']);

pg_connect($pg_connect);
$query = "select aoi_data from aoi where name = '$aoi_name'";
//echo $query;
$result = pg_query($query);
$row = pg_fetch_all($result);

$aoi_data = unserialize($row['aoi_data']);



if ($report == 'landcover'){
	echo "<h1>AOI Land Cover Report</h1>";	
	$a->aoi_landcover();
}

if ($report == 'management') {
	echo "<h1>AOI Management Report</h1>";
	$a->aoi_management();
}

if ($report == 'owner') {
	echo "<h1>AOI Ownership Report</h1>";
	$a->aoi_ownership();
}

if ($report == 'status') {
	echo "<h1>AOI GAP Status Report</h1>";
	$a->aoi_status();
}

if ($report == 'status_sp') {
	echo "<h1>Species GAP Status Report</h1>";
	echo "<h3>{$species}</h3>";
	$a->species_status($itiscode);
}

if ($report == 'landcover_sp') {
	echo "<h1>Species Land Cover Report</h1>";
	echo "<h3>{$species}</h3>";
	$a->species_landcover($itiscode);
}

if ($report == 'management_sp') {
	echo "<h1>Species Management Report</h1>";
	echo "<h3>{$species}</h3>";
	$a->species_management($itiscode);
}

if ($report == 'owner_sp') {
	echo "<h1>Species Ownership Report</h1>";
	echo "<h3>{$species}</h3>";
	$a->species_ownership($itiscode);
}
if ($report == 'predicted') {
	echo "<h1>Predicted Distribution Report</h1>";
	echo "<h3>{$species}</h3>";
	$a->predicted($itiscode);
}
if ($report == 'richness_report') {
	echo "<h1>Richness Report</h1>";
	//echo "<h3>{$species}</h3>";
	$a->richnessreport($species);
}

?>


<img src="/graphics/swgap/b21_up.png" alt="b21" id="b21" class="prn" onclick="window.print();" 
   onmousedown="document.getElementById('b21').src='/graphics/swgap/b21_dn.png';"
   onmouseup="document.getElementById('b21').src='/graphics/swgap/b21_up.png';"/>

<img src="/graphics/swgap/b22_up.png" alt="b22" id="b22" class="prn" onclick="spreadsheet();" 
   onmousedown="document.getElementById('b22').src='/graphics/swgap/b22_dn.png';"
   onmouseup="document.getElementById('b22').src='/graphics/swgap/b22_up.png';"/>

<form action="aoi_report_ss.php" target="_self" method="post">
<input  type="hidden" name="aoi_name" value="<?php echo $aoi_name; ?>" /> 
<input type="hidden" name="report" value="<?php echo $report; ?>" />
<input type="hidden" name="species" value="<?php echo $species ?>" />
<input type="hidden" name="itiscode" value="<?php echo $itiscode ?>" />
<input type="hidden" name="content"  />
</form>

</body>
</html>
