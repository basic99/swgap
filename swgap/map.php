<?php 
/**
 * One of 2 pages that displays map in frame map.
 * 
 * Page is initially loaded from loader.html in same frame that gets frame size.
 * Toolbar has buttons that call javascript functions in javascript/buttons.js.
 * Create new map object from $map = ms_newMapObj($mapfile) using swgap.map.
 * Get layers string created from selections in control frame and use to turn on selected layers.
 * Use setFilter  to display red cross hatch over selected predefined AOI selections.
 * Use javascript library by  Walter Zorn to draw custom AOI and zoom box.
 * To submit new AOI to map2.php change action of form fm1 from map.php to map2.php.
 * 
 * @package swgap
 */

session_start();

if(!isset($_SESSION['username'])){
	$_SESSION['username'] = "visitor";
}
$user_name = $_SESSION['username'];


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<title>map page</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /> 
<meta http-equiv="imagetoolbar" content="no" />
<link rel="StyleSheet" href="/swgap/styles/map.css" type="text/css" />

<style type="text/css">

</style>

<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js" ></script>
<script type="text/javascript" src="../javascript/wz_jsgraphics.js"></script>
<script type="text/javascript" src="../javascript/drag_box.js"></script>
<script type="text/javascript" src="../javascript/dragging.js"></script>
<script type="text/javascript" src="../javascript/draw_aoi.js"></script>
<script type="text/javascript" src="../javascript/buttons.js"></script>
<script type="text/javascript" src="../javascript/onmapctls.js"></script>


<script language="javascript" type="text/javascript">
/* <![CDATA[ */

var curr_user_win;
$(function() {
	start_map();
setmapctls();
	pan();
	send_ajax();
	$(window).unload( function () {
		if(curr_user_win){
			curr_user_win.close();
		}
	});
});

function start_map(){
	var win_h = $(window).height() - 69;
	var win_w = $(window).width();
	if (win_h%2 == 1) win_h = win_h -1;
	if (win_w%2 == 1) win_w = win_w -1;
	$('#winh_ajax,#win_h,#winh_pdf').val(win_h);
	$('#winw_ajax,#win_w,#winw_pdf').val(win_w);
	$('#clkx_ajax').val(win_w/2);
	$('#clky_ajax').val(win_h/2);

}

function send_ajax(){
	//alert("hello");
	//$('#tmpimg').remove();
	var job_id = Math.floor(Math.random() * 1000000000);

	var extent = $('#extent_ajax').val();
	var zoom = $('#zoom_ajax').val();
	var layers = $('#layers_ajax').val();
	var win_h = $('#winh_ajax').val();
	var win_w = $('#winw_ajax').val();
	var click_x = $('#clkx_ajax').val();
	var click_y = $('#clky_ajax').val();

	var county = $('#county_ajax').val();
	var manage = $('#manage_ajax').val();
	var owner = $('#owner_ajax').val();
	var status = $('#status_ajax').val();
	var basin = $('#basin_ajax').val();
	var bird_consv = $('#bird_consv_ajax').val();
	var ecosys = $('#ecosys_ajax').val();
	var state = $('#state_ajax').val();

	$('.mapimage').hide();
	$('#loader_gif').show();

	$.ajax({
		type: "POST",
		url: "map_ajax.php",
		data: {
			extent: extent,
			zoom: zoom,
			layers: layers,
			winw: win_w,
			winh: win_h,
			clickx: click_x,
			clicky: click_y,
			county_aoi: county,
			manage_aoi: manage,
			owner_aoi: owner,
			status_aoi: status,
			basin_aoi: basin,
			bird_consv_aoi: bird_consv,
			ecosys_aoi: ecosys,
			state_aoi: state,
			job_id: job_id

		},
		dataType: "json",
		success: function(data){
			//alert(data);
			if(data.error){
				alert(data.error); //not currently inplemented
			} else {
				$(parent.refmap.document.images[0]).attr("src", data.refname);
				$('#extent_ajax').val(data.extent);
				$('#extent').val(data.extent);
				$('#extent_pdf').val(data.extent);
				//$('#loadingarea').append("<img id='tmpimg' src=/server_temp/" + data.mapname + " />");
				$('.mapimage').attr("src", "/server_temp/" + data.mapname);
				$('.mapimage').load(function(){
					$('.mapimage').fadeIn(300);
					$('#loader_gif').fadeOut(300);
				});
				
				$('#dragMap').css("top", "69px");
				$('#dragMap').css("left", "0px");
				$('#clkx_ajax').val(win_w/2);
				$('#clky_ajax').val(win_h/2);
			}
		}
	});
	//check for mapobj create failure after 2 seconds and if fail resubmit ajax
	setTimeout(function(){
		$.ajax({
			type: "POST",
			url: "chk_mapobj.php",
			data: {job_id: job_id},
			dataType: "json",
			success: function(data){
				if(data.check == "failure"){
					send_ajax();
				}
			}
		});
	}, 2000);
}



function help(){
	window.open("/howto/","h1","");
}

function set_query(){
	var qitem = $('#query_layer').val();
	$('#query_item').val(qitem);
}

function clear_aois(){
	$(".aoikeys").val("");
	send_ajax();
}


function login2(){
	curr_user_win = window.open("/ncgap/login/login.php","w2","menubar=no,scrollbars,width=600,height=300,top=150"); //parent.curr_user_win =

}

function zoomcoord(){
	window.open("","zoomcoord","menubar=no,scrollbars,width=500,height=300");
	document.forms.fm6.submit();
}

/* ]]> */
</script>
</head>
<body >

<img id="loader_gif" alt="loading icon" style="position: absolute; top: 100px; left: 100px; display: block; z-index: 8;" src="/graphics/swgap/ajax-loader.gif" />

<script type="text/javascript">
document.getElementById('loader_gif').style.top = (find_height()/2 + 24) +"px";
document.getElementById('loader_gif').style.left = (find_width()/2 -110) +"px";
</script>



<div id="toolbar" style="display: block;" >
<table>
<tr> 
<td><input  type="image" src="/graphics/swgap/mag_plus_up.png" id="zmin" title="Zoom In" onclick="zoom_in()" /> </td>
<td><input  type="image" src="/graphics/swgap/mag_minus_up.png" id="zmout" title="Zoom Out" onclick="zoom_out()" /> </td>
<td><input  type="image" src="/graphics/swgap/pan_up.png" id="pn" title="Pan" onclick="pan()" /> </td>
<td><input type="image" src="/graphics/swgap/draw_up.png" id="draw" title="draw custom" onclick="draw()"/> </td>
<td><input  type="image" src="/graphics/swgap/info_up.png" id="qry" title="Query" onclick="query()" /> </td>

<td colspan="3"><input id="message1" type="text"  style="width: 212px;"  readonly="readonly" /></td>
<td><input type="image" src="/graphics/swgap/edit_user_up.png" title="login" onclick="login2();" /> </td>
<td >User: </td>
<td ><input type="text" size="15" id="visitor" value="<?php echo $user_name; ?>" readonly='readonly' /></td>

</tr>

<tr>
<td><input type="image" src="/graphics/swgap/sw_full_up.png" title="Zoom to Full Extent" onclick="fullview()" /> </td>
<td><input type="image" src="/graphics/swgap/resize_up.png" style="visibility: hidden" /> </td>
<td><input type="image" src="/graphics/swgap/bullseye_up.png" title="zoom to co-ordinates" onclick="zoomcoord();" /> </td>
<td><input type="image" src="/graphics/swgap/resize_up.png" title="reload frame size of map" onclick="resize();" /> </td>
<td><input type="image" src="/graphics/swgap/aoi_up.png" title="Use AOI" onclick="login();" /> </td>
<td><input type="image" src="/graphics/swgap/pdf_up.png" title="export pdf" onclick="export_pdf();" /> </td>
<td><input type="image" src="/graphics/swgap/help_up.png" title="Help" onclick="help();" /> </td>
<td colspan="1"> 
<select name="query" id="query_item"   style="width: 150px;">
<option value="gap_status" >Parcel-GAP Status</option>
<option value="owner_desc">Parcel-Ownership</option>
<option value="manager_de">Parcel-Gen. Manage</option>
<option value="man_desc_condense">Parcel-Spec. Manage</option>
<option value="parcelname" >Parcel-Name</option>
<option value="basin">River Basin</option>
<option value="bcr">Bird Cons. Region</option>
<option value="city">City</option>
<option value="county">County</option>
<option value="landcover">landcover (raster)</option>
</select>
</td>

</tr>
</table>
</div>

<?php



$query_layer = 'county';
//set zoom
$zoom=1;


//get form variables if from zoomcoord.php
$user_x = $_POST['user_x'];
$user_y = $_POST['user_y'];
$layer = $_POST['layers'];

//set layer if from zoomcoord.php
if(!isset($layer)){
	$layer = "elevation states";
}

//set extent if from zoomcoord.php, or default
$old_extent =  ms_newRectObj();
if(isset($user_x)){
	$user_x_min = $user_x - 4500;
	$user_x_max = $user_x + 4500;
	$user_y_min = $user_y - 4500;
	$user_y_max = $user_y + 4500;
	$old_extent->setextent($user_x_min, $user_y_min, $user_x_max, $user_y_max);
}else {
	$old_extent->setextent(-2.09608e+06, 809571, -349280, 2.46251e+06);
}

//set extent
$extent = 	sprintf("%3.6f",$old_extent->minx)." ".
sprintf("%3.6f",$old_extent->miny)." ".
sprintf("%3.6f",$old_extent->maxx)." ".
sprintf("%3.6f",$old_extent->maxy);



?>

<!--
<div id="loadingarea" style="display: none;">
</div>
-->

<div id="myMap" style="position:absolute; top:69px; left:0px; " >
<img alt="1"  class="mapimage" src="" /> 
</div>

<div id="dragMap" style="position:absolute; top:69px; left:0px;"     >
<img alt="2"  class="mapimage" src="" /> 
</div>


<div id="ctls" style="position:absolute; top:69px; left:0px; z-index: 999;" >
		  
<div id="panup" style="position:absolute; left: 13px; top: 4px; width: 18px; height: 18px; z-index: 1004;">
<img src="/graphics/openlayers/north-mini.png" />
</div>

<div id="panleft" style="position:absolute; left: 4px; top: 22px; width: 18px; height: 18px;">
<img src="/graphics/openlayers/west-mini.png" />  
</div>

<div id="panright" style="position:absolute; left: 22px; top: 22px; width: 18px; height: 18px;">
<img src="/graphics/openlayers/east-mini.png" />  
</div>

<div id="pandown" style="position:absolute; left: 13px; top: 40px; width: 18px; height: 18px;">
<img src="/graphics/openlayers/south-mini.png" />  
</div>

<div id="zoomin" style="position:absolute; left: 13px; top: 63px; width: 18px; height: 18px;">
<img src="/graphics/openlayers/zoom-plus-mini.png" />  
</div>

<div id="zoommax" style="position:absolute; left: 13px; top: 81px; width: 18px; height: 18px;">
<img src="/graphics/openlayers/zoom-world-mini.png" />  
</div>

<div id="zoomout" style="position:absolute; left: 13px; top: 99px; width: 18px; height: 18px;">
<img src="/graphics/openlayers/zoom-minus-mini.png" />  
</div>

</div>

<form id="fm1" action="map2.php" method="post" target="_self">
<input  type="hidden" name="aoi_name_saved" />
<input id="mode" type="hidden" name="mode"  />
<input id="layers" type="hidden" name="layers" value="<?php  echo $layer; ?>" />
<input id="aoi_type" type="hidden" name="type" />
<input  type="hidden" name="zoom" id="zoom"  />
<input type="hidden" name="posix" id="click_val_x" />
<input type="hidden" name="posiy" id="click_val_y" />
<input type="hidden" name="win_w" id="win_w" value="<?php  echo $win_w; ?>" />
<input type="hidden" name="win_h" id="win_h" value="<?php  echo $win_h; ?>" />
<input type="hidden" name="extent" id="extent" value='<?php  echo $extent; ?>' />
<input type="hidden" class="aoikeys" name="manage" id="manage_aoi"  />
<input type="hidden" class="aoikeys" name="owner" id="owner_aoi" />
<input type="hidden" class="aoikeys" name="status"  id="status_aoi" />
<input type="hidden" class="aoikeys" name="county" id="county_aoi" />
<input type="hidden" class="aoikeys" name="topo"  id="topo_aoi" />
<input type="hidden" class="aoikeys" name="basin" id="basin_aoi" />
<input type="hidden" class="aoikeys" name="bird_consv" id="bird_consv_aoi" />
<input type="hidden" class="aoikeys" name="state" id="state_aoi" />
<input type="hidden" class="aoikeys" name="ecosys"  id="ecosys_aoi" />
</form>

<form name='fm4' method="post" target="w2" action="../curr_user_aois.php">
</form>

<form name="fm5" action="../pdf_report.php" target="_blank" method="post">
<input type="hidden" name="layers2" id="layers_pdf" value="<?php  echo $layer; ?>" />
<input type="hidden" name="extent" id="extent_pdf" />
<input type="hidden" name="zoom" value="<?php  echo $zoom; ?>" />
<input type="hidden" name="win_w" id="winw_pdf" value="<?php  echo $win_w; ?>" />
<input type="hidden" name="win_h" id="winh_pdf" value="<?php  echo $win_h; ?>" />
<input type="hidden" name="manage" class="aoikeys" id="manage_pdf"  />
<input type="hidden" name="owner" class="aoikeys" id="owner_pdf"  />
<input type="hidden" name="status"  class="aoikeys" id="status_pdf" />
<input type="hidden" name="county" class="aoikeys" id="county_pdf" />
<input type="hidden" name="topo"  class="aoikeys" id="topo_pdf" />
<input type="hidden" name="basin" class="aoikeys" id="basin_pdf" />
<input type="hidden" name="bird_consv"  class="aoikeys" id="bird_consv_pdf" />
<input type="hidden" name="state"  class="aoikeys" id="state_pdf" />
<input type="hidden" name="dpi"  />
<input type="hidden" name="desc"  />
</form>

<form action="" >
<input type="hidden" id="username" value="<?php echo $user_name; ?>" />
<input type="hidden" id="clkx_ajax" value="<?php echo $click_x; ?>" />
<input type="hidden" id="clky_ajax" value="<?php echo $click_y; ?>" />
<input type="hidden" id="extent_ajax" value='<?php  echo $extent; ?>' />
<input type="hidden" id="zoom_ajax" value='<?php  echo $zoom; ?>'/>
<input type="hidden" id="layers_ajax" value='<?php  echo $layer; ?>' />
<input type="hidden" id="winh_ajax" value='<?php  echo $win_h; ?>' />
<input type="hidden" id="winw_ajax" value='<?php  echo $win_w; ?>' />
<input type="hidden" class="aoikeys" id="county_ajax"  />
<input type="hidden" class="aoikeys" id="manage_ajax"  />
<input type="hidden" class="aoikeys" id="owner_ajax" />
<input type="hidden" class="aoikeys" id="status_ajax" />
<input type="hidden" class="aoikeys" id="topo_ajax" />
<input type="hidden" class="aoikeys" id="basin_ajax" />
<input type="hidden" class="aoikeys" id="bird_consv_ajax" />
<input type="hidden" class="aoikeys" id="state_ajax" />
<input type="hidden" class="aoikeys" id="ecosys_ajax" />

</form>

<form action="../zoomcoord.php" target="zoomcoord" name="fm6" method="POST">
<input type="hidden" name="layers" id="layers_zoom" value="<?php  echo $layer; ?>" />
</form>

<script type="text/javascript">
var jg_box = new jsGraphics("dragMap");
document.getElementById('dragMap').ondragstart = function() {return false;};
</script>

</body>
</html>
