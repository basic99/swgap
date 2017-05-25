<?php  echo '<?xml version="1.0" encoding="UTF-8"?>';  ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<title>single species</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js" ></script>
<link rel="stylesheet" href="../styles/custom-theme-wslider/jquery-ui-1.8.14.custom.css" />
<script type="text/javascript" src="../javascript/jquery-ui-1.8.14.custom.min.js" ></script>
<style type="text/css">
/* <![CDATA[ */

.lbl {font-size: 16px;}
body {margin: 0px;}
#spname {width: 350px;}

ul {
	padding: 0px;
	margin: 0px;
}
li {
	list-style: none;
	font-size: 14px;
	padding-bottom: 12px;
	padding-top: 2px;
}
#cont {
	width: 520px;
	margin: 0px auto 0px;
	font-size: 11px;
}


#col1 {
	width: 235px;
	height: 135px;
	float: left;
}
#col2 {
	width: 280px;
	height: 135px;
	float: right;
}
#sprep,#liststat {
	clear: both;	
	margin: 0px 70px 0px;
}

#col1 button, #col2 button {
	float: right;
   clear: both;
	margin: 2px;
}
#slider1, #slider2 {
   margin: 3px;
   width: 110px;
   float: right;
}

/* ]]> */
</style>
<script language="javascript" type="text/javascript">
/* <![CDATA[ */
$(document).ready(function(){
	
	<?php
   if(!isset($_GET['species'])){
      echo "pred_dist();";    
   } 
   ?>
	
   $('#slider1').slider({
      range: false,
      values: [ 75],
      stop: function() {
         var val1 = $('#slider1').slider("values", 0);
         document.forms.fm1.pred_transp.value = val1;
         var selctd = $("input[name|='functions']:checked").val();
         if(selctd.indexOf("dist")  == 0)pred_dist();         
      }
   }); 
  $('#slider2').slider({
      range: false,
      values: [ 25],
      stop: function() {
         var val2 = $('#slider2').slider("values", 0);
         range_transp = val2;
         var selctd = $("input[name|='functions']:checked").val();
         if(selctd.indexOf("range")  == 0){
            //range();
         } else {
            if(document.getElementById("rngchkbox").checked){
               switch (selctd){
                  case "dist":
                     pred_dist();
                     break;
                  case "veg":
                     lc_map();
                     break;
                  case "owner":
                     ownership_map();
                     break;
                  case "protection":
                     status_map();
                     break;
                  case "management":
                     manage_map();
                     break;              
               }
            }
         }
      }
   });
	
	$("button").button();
	$("#pred").click(function(event){
		event.preventDefault();
		predicted();
	});
	$("#lc").click(function(event){
		event.preventDefault();
		lc_report();
	});
	$("#own").click(function(event){
		event.preventDefault();
		owner_report();
	});
	$("#stat").click(function(event){
		event.preventDefault();
		status_report();
	});
	$("#man").click(function(event){
		event.preventDefault();
		manage_report();
	});
	$("#sprep").click(function(event){
		event.preventDefault();
		species_report();
	});
	$("#liststat").click(function(event){
		event.preventDefault();
		list_stat();
	});
});

var showrange = "range";
var range_transp = 25;
//add range to other maps
function add_range(){
    if(document.getElementById("rngchkbox").checked){      
      showrange = "range";
   } else {
      showrange = "";
   }
   var selctd = $("input[name|='functions']:checked").val();
   switch (selctd){
                  case "dist":
                     pred_dist();
                     break;
                  case "veg":
                     lc_map();
                     break;
                  case "owner":
                     ownership_map();
                     break;
                  case "protection":
                     status_map();
                     break;
                  case "management":
                     manage_map();
                     break;              
               }
}

function hab_leg(){
	parent.controls.show_lgnd();
	parent.controls.location.hash = '#lcov';
}
function sta_leg(){
	parent.controls.show_lgnd();
	parent.controls.location.hash = '#status';
}
function man_leg(){
	parent.controls.show_lgnd();
	parent.controls.location.hash = '#manage';
}
function own_leg(){
	parent.controls.show_lgnd();
	parent.controls.location.hash = '#own';
}
function ran_leg(){
	parent.controls.show_lgnd();
	parent.controls.location.hash = '#range';
}
function pred_dist(){
	var itiscode = document.forms.fm1.itiscode.value;
	var pred_transp = document.forms.fm1.pred_transp.value;
	if (itiscode.length == 0){
		parent.map.document.getElementById('itiscode_ajax').value = '';
	}else{
		parent.map.document.getElementById('itiscode_ajax').value = itiscode;
		parent.map.document.getElementById('pred_transp_ajax').value = pred_transp;
		parent.map.document.getElementById('range_transp_ajax').value = range_transp;
		parent.map.document.getElementById('itiscode_pdf').value = itiscode;
		parent.map.document.getElementById('species_layer_ajax').value = 'predicted ' + showrange;
		parent.map.document.getElementById('species_layer_pdf').value = 'predicted ' + showrange;
		parent.map.document.getElementById('zoom_ajax').value = 1;
		parent.map.send_ajax();
	}
}
function predicted(){
	var itiscode = document.forms.fm1.itiscode.value;
	//alert(itiscode);
	if (itiscode.length == 0){alert('must select a species');
	}else{
		window.open("","report","toolbar=no,menubar=no,scrollbars,resizable");
		parent.map.document.forms.fm2.itiscode.value = itiscode;
		var species = document.forms.fm1.species.value;
		parent.map.document.forms.fm2.species.value = species;
		parent.map.document.forms.fm2.target = 'report';
		parent.map.document.forms.fm2.report.value = 'predicted';
		parent.map.document.forms.fm2.submit();
	}
}
function lc_report(){
	var itiscode = document.forms.fm1.itiscode.value;
	if (itiscode.length == 0){alert('must select a species');
	}else{
		window.open("","report","toolbar=no,menubar=no,scrollbars,resizable");
		parent.map.document.forms.fm2.itiscode.value = itiscode;
		var species = document.forms.fm1.species.value;
		parent.map.document.forms.fm2.species.value = species;
		parent.map.document.forms.fm2.target = 'report';
		parent.map.document.forms.fm2.report.value = 'landcover_sp';
		parent.map.document.forms.fm2.submit();
	}
}
function manage_report(){
	var itiscode = document.forms.fm1.itiscode.value;
	if (itiscode.length == 0){alert('must select a species');
	}else{
		window.open("","report","toolbar=no,menubar=no,scrollbars,resizable");
		parent.map.document.forms.fm2.itiscode.value = itiscode;
		var species = document.forms.fm1.species.value;
		parent.map.document.forms.fm2.species.value = species;
		parent.map.document.forms.fm2.target = 'report';
		parent.map.document.forms.fm2.report.value = 'management_sp';
		parent.map.document.forms.fm2.submit();
	}
}
function owner_report(){

	var itiscode = document.forms.fm1.itiscode.value;
	if (itiscode.length == 0){alert('must select a species');
	}else{
		window.open("","report","toolbar=no,menubar=no,scrollbars,resizable");
		parent.map.document.forms.fm2.itiscode.value = itiscode;
		var species = document.forms.fm1.species.value;
		parent.map.document.forms.fm2.species.value = species;
		parent.map.document.forms.fm2.target = 'report';
		parent.map.document.forms.fm2.report.value = 'owner_sp';
		parent.map.document.forms.fm2.submit();
	}
}
function status_report(){

	var itiscode = document.forms.fm1.itiscode.value;
	if (itiscode.length == 0){alert('must select a species');
	}else{
		window.open("","report","toolbar=no,menubar=no,scrollbars,resizable");
		parent.map.document.forms.fm2.itiscode.value = itiscode;
		var species = document.forms.fm1.species.value;
		parent.map.document.forms.fm2.species.value = species;
		parent.map.document.forms.fm2.target = 'report';
		parent.map.document.forms.fm2.report.value = 'status_sp';
		parent.map.document.forms.fm2.submit();
	}
}
function lc_map(){
	//alert('lcmap');
	var itiscode = document.forms.fm1.itiscode.value;
	if (itiscode.length == 0){alert('must select a species');
	}else{
		parent.map.document.getElementById('itiscode_ajax').value = itiscode;
		parent.map.document.getElementById('itiscode_pdf').value = itiscode;
		parent.map.document.getElementById('range_transp_ajax').value = range_transp;
		
		parent.map.document.getElementById('species_layer_ajax').value = 'habitat ' + showrange;
		parent.map.document.getElementById('species_layer_pdf').value = 'habitat ' + showrange;
		parent.map.document.getElementById('zoom_ajax').value = 1;
		parent.map.send_ajax();

	}
}
function ownership_map(){
	var itiscode = document.forms.fm1.itiscode.value;
	if (itiscode.length == 0){alert('must select a species');
	}else{
		parent.map.document.getElementById('itiscode_ajax').value = itiscode;
		parent.map.document.getElementById('itiscode_pdf').value = itiscode;
		parent.map.document.getElementById('range_transp_ajax').value = range_transp;
		parent.map.document.getElementById('species_layer_ajax').value = 'ownership ' + showrange;
		parent.map.document.getElementById('species_layer_pdf').value = 'ownership ' + showrange;
		parent.map.document.getElementById('zoom_ajax').value = 1;
		parent.map.send_ajax();


	}
}
function status_map(){
	var itiscode = document.forms.fm1.itiscode.value;
	if (itiscode.length == 0){alert('must select a species');
	}else{
		parent.map.document.getElementById('itiscode_ajax').value = itiscode;
		parent.map.document.getElementById('itiscode_pdf').value = itiscode;
		parent.map.document.getElementById('range_transp_ajax').value = range_transp;
		parent.map.document.getElementById('species_layer_ajax').value = 'status ' + showrange;
		parent.map.document.getElementById('species_layer_pdf').value = 'status ' + showrange;
		parent.map.document.getElementById('zoom_ajax').value = 1;
		parent.map.send_ajax();
	}
}
function manage_map(){
	var itiscode = document.forms.fm1.itiscode.value;
	if (itiscode.length == 0){alert('must select a species');
	}else{
		parent.map.document.getElementById('itiscode_ajax').value = itiscode;
		parent.map.document.getElementById('itiscode_pdf').value = itiscode;
		parent.map.document.getElementById('range_transp_ajax').value = range_transp;
		parent.map.document.getElementById('species_layer_ajax').value = 'manage ' + showrange;
		parent.map.document.getElementById('species_layer_pdf').value = 'manage ' + showrange;
		parent.map.document.getElementById('zoom_ajax').value = 1;
		parent.map.send_ajax();
	}
}
/*
function range(){
	var itiscode = document.forms.fm1.itiscode.value;
	if (itiscode.length == 0){alert('must select a species');
	}else{
		parent.map.document.getElementById('itiscode_ajax').value = itiscode;
		parent.map.document.getElementById('itiscode_pdf').value = itiscode;
		parent.map.document.getElementById('species_layer_ajax').value = 'range';
		parent.map.document.getElementById('species_layer_pdf').value = 'range';
		parent.map.document.getElementById('zoom_ajax').value = 1;
		parent.map.send_ajax();
	}
}
*/
function list_stat(){
	document.forms[0].action = "../SW_ListStatus.php";
	//document.forms[0].action = "/swgap/list_stat.php";
	window.open('','liststat','toolbar=no,menubar=no,resizable,scrollbars=yes,height=800,width=450');
	document.forms[0].target = "liststat";
	document.forms[0].submit();
}
function species_report(){
	var host = "metacomet.zo.ncsu.edu";
	//var host = window.location.host;
	var page = document.forms[0].itiscode.value+"_SppReport.pdf";
	var url = 'http://'+host+"/sw_sppreport/"+page;
	window.open(url,'','toolbar=no,menubar=no,resizable,scrollbars=yes');
}

/* ]]> */
</script>
</head>
<body >
<?php
require("sw_config.php");
pg_connect($pg_connect);
ini_set("variables_order", "PG");
$species = $_REQUEST['species'];
if(is_array($species)){
   $selected = $species[0];
	$sqlvar = pg_escape_string($selected);
	$query = "select * from specieslist where swregap_common_name  = '{$sqlvar}'";
	$result = pg_query($query);
	$row = pg_fetch_array($result);
	$itiscode = $row['itiscode'];
	$scname = ucfirst($row['swregap_scientific_name']);
	$comname = strtolower($selected);
} else {
   $itiscode = $species;
	$query = "select * from specieslist where itiscode = '{$itiscode}'";
	if(@$result = pg_query($query)){
		$row = pg_fetch_array($result);
		$scname = ucfirst($row['swregap_scientific_name']);
		$comname = strtolower($row['swregap_common_name']);
	}
}
//$species = $_POST['species'];
//$selected = $species[0];


?>

<div id="cont">
<span class="lbl">Current Species:</span> <input type="text" name="species" id="spname"  readonly="readonly" value="<?php echo $comname.'/'.$scname; ?>"/>


<div id="col1">
<button id="pred">Calculate</button>
<ul>
<li>
<input type="radio" name="functions" value="dist" onclick="pred_dist();" checked="checked" /><span >Predicted Dist.</span>
</li>
<li>Transparency:
<div id="slider1"></div>
</li>
<li>
	<input type="checkbox" id="rngchkbox" checked="checked" onclick="add_range();" /> <a href="javascript:ran_leg();">Range&nbsp;Map</a>
</li>
<li>Transparency:
<div id="slider2"></div>   
</li>
</ul>
</div>

<div id="col2">
<button id="lc">Calculate</button>
<button id="own">Calculate</button>
<button id="stat">Calculate</button>
<button id="man">Calculate</button>
<ul>
<li>
<input type="radio" name="functions" value="veg" onclick="lc_map();"/> <a href="javascript:hab_leg();">Habitat Types</a>
</li>
<li><input type="radio" name="functions" value="owner" onclick="ownership_map();"/> <a href="javascript:own_leg();">Ownership of Habitat</a></li>
<li><input type="radio" name="functions" value="protection" onclick="status_map();"/> <a href="javascript:sta_leg();">Protection of Habitat</a></li>
<li><input type="radio" name="functions" value="management" onclick="manage_map();"/><a href="javascript:man_leg();">Management of Habitat</a></li>
</ul>
</div>

<button id="sprep">Species Report</button>
<button id="liststat">Listing Status</button>
</div>

<form action="" method="post" name="fm1">
<input type="hidden" name="itiscode" value="<?php echo $itiscode; ?>"/> 
<input type="hidden" name="species" value="<?php echo $selected; ?>"/>
<input type="hidden" name="pred_transp" value="75"/> 
<input type="hidden" name="range_transp" value="25"/> 
</form>
</form>
</body>
</html>
