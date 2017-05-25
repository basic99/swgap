<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" href="../styles/custom-theme/jquery-ui-1.8.6.custom.css" />
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js" ></script>
<script type="text/javascript" src="../javascript/jquery-ui-1.8.6.custom.min.js" ></script>
<style type="text/css">
/* <![CDATA[ */
#cont {
	width: 520px;
	margin: 0px auto 0px;
	font-size: 11px;
}
body{
   font-family:sans-serif;
	font-size: 11px;
}
button {width: 120px;
	float: right;
   clear: both;
	margin: 2px;}
textarea {
	width: 380px;
	height: 120px;}
/* ]]> */
</style>
<script language="javascript" type="text/javascript">
/* <![CDATA[ */

$(document).ready(function(){
	$("button").button();
	var species = document.getElementById('prev_sel').value;
	parent.data.document.forms.fm2.prev_sel.value = species;
	
	$("#rmap").click(function() {
		submit_multi();
		});
	$("#rst").click(function() {
		clear_sel();
		});
	$("#calc").click(function() {
		richnessreport();
		});
});
function set_species(){
	var species = document.getElementById('prev_sel').value;
	parent.data.window.document.fm2.prev_sel.value = species;

}

function submit_multi(){
	var species = document.forms[0].species.value;
	//var sub_name = document.forms[0].sub_name.value;
	parent.map.document.getElementById('itiscode_ajax').value = '';
	parent.map.document.getElementById('richness_species_ajax').value = species;
	//parent.map.document.getElementById('species_layer_ajax').value = sub_name;
	parent.map.document.getElementById('species_layer_ajax').value = "richness";
	//update here to prevent reuse of old richness map
	parent.map.document.forms.ajaxform.species_layer_prev.value = 'old map';
	parent.map.document.getElementById('species_layer_pdf').value = "richness";
	parent.map.document.getElementById('zoom_ajax').value = 1;
	parent.map.send_ajax();
/*
	parent.map.document.forms.fm1.richness_species.value = species;
	parent.map.document.forms.fm1.species_layer.value = sub_name;
	parent.map.document.getElementById('zoom').value = '1';
*/
}

function clear_sel(){
	parent.data.document.forms.fm2.prev_sel.value = "";
	document.getElementById('selected').value = '';
}

function richnessreport(){
	var species = document.forms[0].species.value;
	if (species.length == 0){alert('must select species');
	}else{
		window.open("","report","toolbar=no,menubar=no,scrollbars,resizable");
		parent.map.document.forms.fm2.species.value = species;
		parent.map.document.forms.fm2.target = 'report';
		parent.map.document.forms.fm2.report.value = 'richness_report';
		parent.map.document.forms.fm2.submit();
	}
}
/* ]]> */
</script>
</head>
<body onload="set_species()">
	<div id="cont">
<button id="rmap">Richness Map</button>
<button id="rst">Reset</button>
<button id="calc">Calculate</button>

<?php
require("sw_config.php");
pg_connect($pg_connect);

$species = $_POST['species'];
for ($i=0; $i<sizeof($species); $i++){
	$species[$i] = $species[$i];
}

$prev_sel = $_POST['prev_sel'];
$prev_sel = explode(":", $prev_sel);
if (strlen($prev_sel[0])!==0) {
	$species = array_unique(array_merge($species,$prev_sel));
}

echo '<textarea name="species" id="selected" rows="6" cols="40" readonly="readonly">';
$num_species = sizeof($species);
if(sizeof($species)!==0){
	$species_ser = (implode(":",$species));
	foreach ($species as $v){
		$sqlvar = pg_escape_string($v);
		$query = "select * from specieslist where swregap_common_name  = '{$sqlvar}'";
		$result = pg_query($query);
		$row = pg_fetch_array($result);
		$itiscode = $row['itiscode'];
		$scname = ucfirst($row['swregap_scientific_name']);
		$comname = strtolower($v);
		echo $comname."/".$scname."\n";
	}
}
echo '</textarea>';
$sub_name = "richness".rand(0,9999);
?>
<form action="map2.php" method="post" target="map" >
<input type="hidden" name="species" value="<?php echo $species_ser?>" id="prev_sel" />
<input type="hidden" name="sub_name" value="<?php echo $sub_name; ?>" />

 </form>
	</div>
</body>
</html>
