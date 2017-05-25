<?php 
require('sw_range_class.php');
session_start();
require("sw_config.php");
pg_connect($pg_connect);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<title>controls_php</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" href="../styles/aqtree3clickable.css" />
<link rel="stylesheet" href="../styles/custom-theme/jquery-ui-1.8.6.custom.css" />
<link rel="stylesheet" href="../styles/controls.css" />
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js" ></script>
<script type="text/javascript" src="../javascript/jquery-ui-1.8.6.custom.min.js" ></script>
<script type="text/javascript" src="../javascript/aqtree3clickable.js"></script>
<script type="text/javascript" src="../javascript/controls_tab1.js"></script>
<script type="text/javascript" src="../javascript/controls234.js"></script>
<style type="text/css">
/* <![CDATA[ */
body {padding: 0px; margin: 2px;}
#tabs {font-size: 11px; width: 315px;}
#tabs-1 { width: 270px; font-size: 16px;}
#tabs-2{ width: 270px; font-size: 11px;}
#tabs-3 {overflow: scroll; width: 270px; font-size: 16px;}
button { width: 100px; margin: 15px;}
span.desc {font-size: 16px; line-height: 2;}
h2 {text-align: center;}
#tabs-2 li,td {list-style: none; font-size: 16px;}
/* ]]> */
</style>
<script language="javascript" type="text/javascript">
/* <![CDATA[ */
$(document).ready(function(){
   <?php
   if(!isset($_GET['aoiname'])){
     echo "functions_action();";
   }
   ?> 
//functions_action();
document.forms[1].submit();
load_selections();
$("#tabs").tabs();
$("#opentab").click();
$("button").button();
var win_h = $(window).height();
$("#tabs-1,#tabs-2,#tabs-3").height(win_h - 78);

$("#sub").click(function(evt) {
document.forms[1].submit();	  
});
$("#back").click(function(evt) {
		  evt.preventDefault();
		  change_categories();
});

});
function show_lgnd(){
		  $("#legendtab").click();
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
<li><input type="radio" name="steward" value="gapown"  onclick="loadlayers();" /><a href="#own" onclick="show_lgnd();">Ownership</a></li>
<li><input type="radio" name="steward" value="gapman"  onclick="loadlayers();" /><a href="#manage" onclick="show_lgnd();">Management</a></li>
<li><input type="radio" name="steward" value="gapsta"  onclick="loadlayers();" /><a href="#status"onclick="show_lgnd();" >Status</a></li>
<li><input type="radio" name="steward" value="none"  onclick="loadlayers();" /><a>none</a></li>
</ul>
</li>
<li><a href="#" class="no_link">Background</a>
<ul>
<li><input type="radio" name="background" value="landcover"  onclick="loadlayers();" /><a href="#lcov" onclick="show_lgnd();">Land Cover</a></li>
<li><input type="radio" name="background" value="elevation"  onclick="loadlayers();" /><a href="#elev" onclick="show_lgnd();">Elevation</a></li>
<li><input type="radio" name="background" value="none"  onclick="loadlayers();" /><a>none</a></li>
</ul>
</li>
</ul>
</form>
</div>
<div id="tabs-2">

<?php
//var_dump($_GET);
if(isset($_GET['aoiname'])){
   //var_dump($_GET);
   $aoi_name = $_GET['aoiname'];
   //$species_sel = $_GET['species'];
   $species = "all";
   if (!isset($_SESSION["range".$aoi_name]) ) {
      $_SESSION["range".$aoi_name] = new sw_range_class($aoi_name);
   }
   //$rclass = $_SESSION["range".$aoi_name];
} else {
   $species = $_POST['species'];
   $fed = $_POST['fed'];
   $state['az'] = $_POST['stateaz'];
   $state['co'] = $_POST['stateco'];
   $state['nm'] = $_POST['statenm'];
   $state['nv'] = $_POST['statenv'];
   $state['ut'] = $_POST['stateut'];
   $nsglobal = $_POST['nsglobal'];
   $nsstate['az'] = $_POST['nsaz'];
   $nsstate['co'] = $_POST['nsco'];
   $nsstate['nm'] = $_POST['nsnm'];
   $nsstate['nv'] = $_POST['nsnv'];
   $nsstate['ut'] = $_POST['nsut'];
   $sgcn['az'] = $_POST['sgcnaz'];
   $sgcn['co'] = $_POST['sgcnco'];
   $sgcn['nm'] = $_POST['sgcnnm'];
   $sgcn['nv'] = $_POST['sgcnnv'];
   $sgcn['ut'] = $_POST['sgcnut'];
   $pif['gbas'] = $_POST['pifgbas'];
   $pif['nrock'] = $_POST['pifnrock'];
   $pif['srock'] = $_POST['pifsrock'];
   $pif['sgrass'] = $_POST['pifsgrass'];
   $pif['sonora'] = $_POST['pifsonora'];
   $pif['smadre'] = $_POST['pifsmadre'];
   $pif['chihua'] = $_POST['pifchihua'];
   $sel = $_POST['sel'];
   $aoi_name = $_POST['aoi_name'];
}

foreach($_POST as $key=>$foo){
   switch($key){
      case "aoi_name":
         break;
      case "species":
         break;
      case "sel":
         break;
      default:
         $protcats[] = $key; 
   }
}

$rclass = $_SESSION["range".$aoi_name];
$tot_class = $rclass->num_class($species, $sel, $fed, $state, $gap, $nsglobal, $nsstate, $pif, $sgcn);
?>

<form action="select_species.php" method="post" target="data">
<input  type="hidden" name="aoi_name" value="<?php echo $aoi_name; ?>" />
<input type="hidden" name="protcats" value='<?php echo json_encode($protcats); ?>' />
<input type="hidden" name="type" value='reload' />
<table>
<tr><th>Species Group</th><th>Total Count</th><th>Display</th></tr>

<tr>
<td>Avian Species</td>
<td class="cnt"><?php echo $tot_class['avian']; ?></td>
<td class="cnt"><input type="checkbox" name="avian" checked="checked" /></td>
</tr>

<tr>
<td>Mammalian Species</td>
<td class="cnt"><?php  echo  $tot_class['mammal']; ?></td>
<td class="cnt"><input type="checkbox" name="mammal" checked="checked" /></td>
</tr>

<tr>
<td>Reptilian Species</td>
<td class="cnt"><?php echo $tot_class['rept']; ?></td>
<td class="cnt"><input type="checkbox" name="reptile" checked="checked" /></td>
</tr>

<tr>
<td>Amphibian Species</td>
<td class="cnt"><?php echo $tot_class['amph']; ?></td>
<td class="cnt"><input type="checkbox" name="amphibian" checked="checked" /></td>
</tr>
</table>

<button id="back">Back</button>
<button id="sub">Submit</button>

<ul>
<li>
	<input id="modesingle" type="radio" name="mode" checked="checked" value="single" onclick="functions_action();" /><label for="modesingle" > Single&nbsp;species&nbsp;Mode</label>	  
</li>
<li>
	<input id="modemult" type="radio" name="mode"  value="multiple" onclick="functions_action();" /> <label for="modemult" > Multiple&nbsp;Species&nbsp;Mode </label>	  
</li>
</ul>

</div>
<div id="tabs-3">

<h4><a href="#lcov">GAP Land Cover </a></h4>
<h4><a href="#owner">Ownership (Stewardship)</a></h4>
<h4><a href="#manage">Management (Stewardship)</a></h4>
<h4><a href="#status">GAP Status (Stewardship)</a></h4>
<h4><a href="#range">Known range</a></h4>

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



<a name="range"></a><br />
<h4>Known Range</h4>
<img alt="watershed range legend" src="/graphics/swgap/sw_range_legend.png" />
<br /><br /><br />
</div>

</body>
</html>
