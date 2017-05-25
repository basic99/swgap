<?php 
require('sw_range_class.php');
session_start();
require("sw_config.php");
pg_connect($pg_connect);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<title>controls3_php</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" href="../styles/aqtree3clickable.css" />
<link rel="stylesheet" href="../styles/custom-theme/jquery-ui-1.8.6.custom.css" />
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js" ></script>
<script type="text/javascript" src="../javascript/jquery-ui-1.8.6.custom.min.js" ></script>
<script type="text/javascript" src="../javascript/aqtree3clickable.js"></script>
<script type="text/javascript" src="../javascript/controls_tab1.js"></script>
<script type="text/javascript" src="../javascript/controls234.js"></script>


<style type="text/css">
/* <![CDATA[ */
body {
		  padding: 0px;
		  margin: 2px;}
#tabs {
		  font-size: 11px;
		  width: 315px;}
#tabs-1 {
		  width: 270px;
		  font-size: 16px;}
#tabs-2{
		  padding: 16px 3px 16px 3px;
		  width: 308px;
		  font-size: 11px;
		  overflow: scroll;}
#tabs-2 td{
		  font-size: 14px;
		  text-align: center;}
#tabs-3 {
		  overflow: scroll;
		  width: 270px;
		  font-size: 16px;}
button {
		  margin: 10px 0px 0px 100px;
		  width: 100px;}
span.desc {
		  font-size: 16px;
		  line-height: 2;}
h2 {
		  text-align: center;}
/* ]]> */
</style>
<script language="javascript" type="text/javascript">
/* <![CDATA[ */
$(document).ready(function(){
load_selections();		  
$("#tabs").tabs();
$("#opentab").click();
$("button").button();
var win_h = $(window).height();
$("#tabs-1,#tabs-2,#tabs-3").height(win_h - 78);

$("#sub").click(function(evt) {
	document.forms[1].submit();	  
});

});


function categories(){
	//alert('hello');
	if(document.forms.fm2.fed.checked
	|| document.forms.fm2.stateaz.checked
	|| document.forms.fm2.stateco.checked
	|| document.forms.fm2.statenm.checked
	|| document.forms.fm2.statenv.checked
	|| document.forms.fm2.stateut.checked
	|| document.forms.fm2.nsglobal.checked
	|| document.forms.fm2.nsaz.checked
	|| document.forms.fm2.nsco.checked
	|| document.forms.fm2.nsnm.checked
	|| document.forms.fm2.nsnv.checked
	|| document.forms.fm2.nsut.checked
	|| document.forms.fm2.sgcnaz.checked
	|| document.forms.fm2.sgcnco.checked
	|| document.forms.fm2.sgcnnm.checked
	|| document.forms.fm2.sgcnnv.checked
	|| document.forms.fm2.sgcnut.checked
	|| document.forms.fm2.pifgbas.checked
	|| document.forms.fm2.pifnrock.checked
	|| document.forms.fm2.pifsrock.checked
	|| document.forms.fm2.pifsgrass.checked
	|| document.forms.fm2.pifsonora.checked
	|| document.forms.fm2.pifsmadre.checked
	|| document.forms.fm2.pifchihua.checked) {
		document.forms.fm2.species[1].checked = true;
	}else{
		document.forms.fm2.species[0].checked = true;
	}
}

/* ]]> */
</script>
</head>
<body>
<div id="tabs">
<ul>
<li><a href="#tabs-1">View Layers</a></li>
<li><a id="opentab" href="#tabs-2">Select Species</a></li>
<li><a id="legendtab" href="#tabs-3">Legends</a></li>
</ul>
<div id="tabs-1">
<form action="map.php" method="post" target="map">
<ul class="aqtree3clickable">
<li  class="aq3open"><a href="#" class="no_link">Foreground</a>
<ul>
<li><input type="checkbox" name="states"   onclick="loadlayers();" /><a>States</a></li>
<li><input type="checkbox" name="cities"  onclick="loadlayers();" /><a>Cities</a></li>
<li><input type="checkbox" name="counties"  onclick="loadlayers();" /><a>Counties</a></li>
<li><input type="checkbox" name="roads"  onclick="loadlayers();" /><a>Roads</a></li>
<li><input type="checkbox" name="basins_river"  onclick="loadlayers();" /><a>Watersheds</a></li>
<li><input type="checkbox" name="hydro"  onclick="loadlayers();" /><a>Rivers</a></li>
<li><input type="checkbox" name="bcr"  onclick="loadlayers();" /><a>BCR</a></li>
</ul>
</li>
<li><a href="#" class="no_link">Stewardship</a>
<ul>
<li><input type="radio" name="steward" value="gapown"  onclick="loadlayers();" /><a href="#own" onclick="show_lgnd();" >Ownership</a></li>
<li><input type="radio" name="steward" value="gapman"  onclick="loadlayers();" /><a href="#manage" onclick="show_lgnd();" >Management</a></li>
<li><input type="radio" name="steward" value="gapsta"  onclick="loadlayers();" /><a href="#status" onclick="show_lgnd();" >Status</a></li>
<li><input type="radio" name="steward" value="none"  onclick="loadlayers();" /><a>none</a></li>
</ul>
</li>
<li><a href="#" class="no_link">Background</a>
<ul>
<li><input type="radio" name="background" value="landcover"  onclick="loadlayers();" /><a href="#lcov" onclick="show_lgnd();" >Land Cover</a></li>
<li><input type="radio" name="background" value="elevation"  onclick="loadlayers();" /><a href="#elev" onclick="show_lgnd();" >Elevation</a></li>
<li><input type="radio" name="background" value="none"  onclick="loadlayers();" /><a>none</a></li>
</ul>
</li>
</ul>
</form>
</div>
<div id="tabs-2">
<?php



$aoi_name = $_POST['aoi_name'];
$type = $_POST['type'];
/*
$owner_aoi = $_POST['owner'];
$manage_aoi = $_POST['manage'];
$county_aoi = $_POST['county'];
$basin_aoi = $_POST['basin'];
$state_aoi = $_POST['state'];
$bcr_aoi = $_POST['bcr'];
*/
$rclass_ser = $_POST['rclass'];

if (!isset($_SESSION["range".$aoi_name]) ) {

	$_SESSION["range".$aoi_name] = new sw_range_class($aoi_name);
}
$rclass = $_SESSION["range".$aoi_name];
?>

<form action="controls4.php" method="post" target="controls" id="fm2" >
<input  type="hidden" name="aoi_name" value="<?php echo $aoi_name; ?>" /> 
<table style="border-collapse:collapse;" id="cntrls3">

<tr>
<th></th><th style="width: 60px;">Species Count</th><th colspan="2">Select Category</th>
</tr>

<tr>
<td style="width:15px;"><input type="radio" name="species" value="all" checked="checked" /></td>
<td style="border: solid black 1px; border-right: white;"><?php echo $rclass->num_species['all_species']; ?></td>
<td colspan="2" style="border: solid black 1px; border-left: white;" >all species in selection area</td>
</tr>

<tr><td colspan="4" style="height: 5px; border-right:  solid 1px white; "></td></tr>

<tr>
<td></td>
<td style="border: solid black 1px; border-right: white; border-bottom: white"><?php echo $rclass->num_species['fed_species']; ?></td>
<td style="border-top: solid black 1px;"><input type="checkbox" name="fed" onclick="categories();" /></td>
<td style="border: solid black 1px; border-bottom: white; border-left: white;"> Federally listed species</td>
</tr>

<tr>
<td></td>
<td style="border-left: solid black 1px;"><?php echo $rclass->num_species['prot_az']; ?></td>
<td><input type="checkbox" name="stateaz" onclick="categories();"/></td>
<td style="border-right: solid black 1px;"> AZ state listed species</td>
</tr>

<tr>
<td></td>
<td style="border-left: solid black 1px;"><?php echo $rclass->num_species['prot_co']; ?></td>
<td><input type="checkbox" name="stateco" onclick="categories();"/></td>
<td style="border-right: solid black 1px;"> CO state listed species</td>
</tr>

<tr>
<td></td>
<td style="border-left: solid black 1px;"><?php echo $rclass->num_species['prot_nm']; ?></td>
<td><input type="checkbox" name="statenm" onclick="categories();"/></td>
<td style="border-right: solid black 1px;"> NM state listed species</td>
</tr>

<tr>
<td></td>
<td style="border-left: solid black 1px;"><?php echo $rclass->num_species['prot_nv']; ?></td>
<td><input type="checkbox" name="statenv" onclick="categories();"/></td>
<td style="border-right: solid black 1px;"> NV state listed species</td>
</tr>

<tr>
<td></td>
<td style="border-left: solid black 1px; "><?php echo $rclass->num_species['prot_ut']; ?></td>
<td ><input type="checkbox" name="stateut" onclick="categories();"/></td>
<td style="border-right: solid black 1px; border-bottom:solid black 1px;"> UT state listed species</td>
</tr>

<tr>
<td><input type="radio" name="species" value="prot" /></td>
<td style="border-left: solid black 1px;"><?php echo $rclass->num_species['ns_global']; ?></td>
<td><input type="checkbox" name="nsglobal" onclick="categories();"/></td>
<td style="border-right: solid black 1px;"> NS Global priority species</td>
</tr>

<tr>
<td></td>
<td style="border-left: solid black 1px;"><?php echo $rclass->num_species['ns_az']; ?></td>
<td><input type="checkbox" name="nsaz" onclick="categories();"/></td>
<td style="border-right: solid black 1px;">NS AZ priority species</td>
</tr>

<tr>
<td></td>
<td style="border-left: solid black 1px;"><?php echo $rclass->num_species['ns_co']; ?></td>
<td><input type="checkbox" name="nsco" onclick="categories();"/></td>
<td style="border-right: solid black 1px;">NS CO priority species</td>
</tr>

<tr>
<td></td>
<td style="border-left: solid black 1px;"><?php echo $rclass->num_species['ns_nm']; ?></td>
<td><input type="checkbox" name="nsnm" onclick="categories();"/></td>
<td style="border-right: solid black 1px;">NS NM priority species</td>
</tr>

<tr>
<td></td>
<td style="border-left: solid black 1px;"><?php echo $rclass->num_species['ns_nv']; ?></td>
<td><input type="checkbox" name="nsnv" onclick="categories();"/></td>
<td style="border-right: solid black 1px;">NS NV priority species</td>
</tr>

<tr>
<td></td>
<td style="border-left: solid black 1px; "><?php echo $rclass->num_species['ns_ut']; ?></td>
<td ><input type="checkbox" name="nsut" onclick="categories();"/></td>
<td style="border-right: solid black 1px; border-bottom:solid black 1px;">NS UT priority species</td>
</tr>

<tr>
<td></td>
<td style="border-left: solid black 1px;"><?php echo $rclass->num_species['sgcn_az']; ?></td>
<td><input type="checkbox" name="sgcnaz" onclick="categories();"/></td>
<td style="border-right: solid black 1px;">AZ SGCN Species</td>
</tr>

<tr>
<td></td>
<td style="border-left: solid black 1px;"><?php echo $rclass->num_species['sgcn_co']; ?></td>
<td><input type="checkbox" name="sgcnco" onclick="categories();"/></td>
<td style="border-right: solid black 1px;">CO SGCN Species</td>
</tr>

<tr>
<td></td>
<td style="border-left: solid black 1px;"><?php echo $rclass->num_species['sgcn_nm']; ?></td>
<td><input type="checkbox" name="sgcnnm" onclick="categories();"/></td>
<td style="border-right: solid black 1px;">NM SGCN Species</td>
</tr>

<tr>
<td></td>
<td style="border-left: solid black 1px;"><?php echo $rclass->num_species['sgcn_nv']; ?></td>
<td><input type="checkbox" name="sgcnnv" onclick="categories();"/></td>
<td style="border-right: solid black 1px;">NV SGCN Species</td>
</tr>

<tr>
<td></td>
<td style="border-left: solid black 1px; "><?php echo $rclass->num_species['sgcn_ut']; ?></td>
<td ><input type="checkbox" name="sgcnut" onclick="categories();"/></td>
<td style="border-right: solid black 1px; border-bottom:solid black 1px;">UT SGCN Species</td>
</tr>

<tr>
<td></td>
<td style="border-left: solid black 1px; "><?php echo $rclass->num_species['pif_g_basin']; ?></td>
<td><input type="checkbox" name="pifgbas" onclick="categories();"/></td>
<td style="border-right: solid black 1px; ">Great Basin PIF</td>
</tr>

<tr>
<td></td>
<td style="border-left: solid black 1px;"><?php echo $rclass->num_species['pif_n_rockies']; ?></td>
<td><input type="checkbox" name="pifnrock" onclick="categories();"/></td>
<td style="border-right: solid black 1px;">N. Rockies PIF</td>
</tr>

<tr>
<td></td>
<td style="border-left: solid black 1px;"><?php echo $rclass->num_species['pif_s_rockies']; ?></td>
<td><input type="checkbox" name="pifsrock" onclick="categories();"/></td>
<td style="border-right: solid black 1px;">S. Rockies/ Co. Plateau PIF</td>
</tr>

<tr>
<td></td>
<td style="border-left: solid black 1px;"><?php echo $rclass->num_species['pif_shortgrass']; ?></td>
<td><input type="checkbox" name="pifsgrass" onclick="categories();"/></td>
<td style="border-right: solid black 1px;">Shortgrass Prairie PIF</td>
</tr>

<tr>
<td></td>
<td style="border-left: solid black 1px;"><?php echo $rclass->num_species['pif_sonoran']; ?></td>
<td><input type="checkbox" name="pifsonora" onclick="categories();"/></td>
<td style="border-right: solid black 1px;">Sonoran and Mohave Deserts PIF</td>
</tr>

<tr>
<td></td>
<td style="border-left: solid black 1px;"><?php echo $rclass->num_species['pif_s_madre']; ?></td>
<td><input type="checkbox" name="pifsmadre" onclick="categories();"/></td>
<td style="border-right: solid black 1px;">Sierra Madre Occidental PIF</td>
</tr>

<tr>
<td ></td>
<td style="border-left: solid black 1px; border-bottom:solid black 1px; "><?php echo $rclass->num_species['pif_chihuahan']; ?></td>
<td style="border-bottom:solid black 1px;" ><input type="checkbox" name="pifchihua" onclick="categories();"/></td>
<td style="border-right: solid black 1px; border-bottom:solid black 1px;" >Chihuahuan Desert PIF</td>
</tr>

<tr>
<td></td>
<td style="border-left: solid black 1px;"><input type="radio" name="sel" value="and" /> </td>
<td colspan="2" style="border-right: solid black 1px;">AND Select only species in all checked categories</td>
</tr>

<tr>
<td ></td>
<td style="border: solid black 1px; border-top: white; border-right: white;"><input type="radio" name="sel" value="or" checked="checked" /></td>
<td colspan="2" style="border-bottom: solid black 1px; border-right: solid black 1px;"> OR Select species in any checked category </td>
</tr>

</table>
<button id="sub">Submit</button>
</form>
</div>
<div id="tabs-3">

<h4><a href="#lcov">GAP Land Cover </a></h4>
<h4><a href="#owner">Ownership (Stewardship)</a></h4>
<h4><a href="#manage">Management (Stewardship)</a></h4>
<h4><a href="#status">GAP Status (Stewardship)</a></h4>

<a name="elev"></a></a><br /><br />
<h4>Elevation (meters)</h4>
<img alt="elevation legend" src="/graphics/swgap/sw_elev_legend.png" /><br />
<br />

<a name="lcov"></a><br /><br />
<h4>GAP Land Cover</h4>
<img alt="landcover legend" src="/graphics/swgap/sw_lc_legend_1_25.png" />
<img alt="landcover legend" src="/graphics/swgap/sw_lc_legend_26_50.png" />
<img alt="landcover legend" src="/graphics/swgap/sw_lc_legend_51_75.png" />
<img alt="landcover legend" src="/graphics/swgap/sw_lc_legend_76_100.png" />
<img alt="landcover legend" src="/graphics/swgap/sw_lc_legend_101_125.png" /><br />
<br />

<a name="own"></a></a><br /><br />
<h4>Ownership (Stewardship)</h4>
<img alt="elevation legend" src="/graphics/swgap/sw_owner_legend.png" /><br />
<br />

<a name="manage"></a></a><br /><br />
<h4>Management (Stewardship)</h4>
<img alt="elevation legend" src="/graphics/swgap/sw_manage_legend.png" /><br />
<br />

<a name="status"></a></a><br /><br />
<h4>GAP Status (Stewardship)</h4>
<img alt="elevation legend" src="/graphics/swgap/sw_status_legend.png" /><br />
<br />

</div>

</div>
</body>
</html>
