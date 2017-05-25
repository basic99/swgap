<?php
require('sw_range_class.php');
session_start();
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<title>select species</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" href="../styles/custom-theme/jquery-ui-1.8.6.custom.css" />
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js" ></script>
<script type="text/javascript" src="../javascript/jquery-ui-1.8.6.custom.min.js" ></script>
<style type="text/css">
/* <![CDATA[ */

#btns {/*position:relative; padding-top: 5;*/
		  position: absolute;
		  bottom: 16px;
		  height: 25px;
		  margin-left: 15px;
		  font-size: 10px;}
#select {width: 100%;}
img { margin: 0px; padding: 0px;}
body { margin: 0px; padding-left: 5px;}
#cont2 {font-size: 11px;
}
#cont2 p {font-size: 16px;}
#cont2 button {margin: 6px;}
select {font-size: 12px;}
#msg {color: red;
		  font-size: 16px;}

/* ]]> */
</style>
<script language="javascript" type="text/javascript">
/* <![CDATA[ */

$(document).ready(function(){
	// Your code here
	$("button").button();
	$("#cont2").hide();
	$("#srch").click(function(event){
		event.preventDefault();
		$("#cont1").hide();
		$("#cont2").show();
	});
	$("#slct").click(function(event){
		event.preventDefault();
		var action = parent.functions.location.pathname;
      document.forms.fm2.action = action;
      document.forms.fm2.submit();
	});
	$("#svlist").click(function(event){
		event.preventDefault();
		if(navigator.appName.indexOf('Microsoft') != -1){
		  document.forms.f3.target = "_blank";
      }
      document.forms.f3.submit();
	});
	$("#rst").click(function(event){
		event.preventDefault();
		$("#search").val('');
      $("#fm1").submit();
	});
	$("#srchcncl").click(function(event){
		event.preventDefault();
		document.getElementById('search').value='';
		document.forms.fm1.submit();
	});
	$("#srchsbmt").click(function(event){
		event.preventDefault();
		document.forms.search_form.submit();
	});
	$('input:radio').click(function(event){
		$("#fm1").submit();
	});
	
	var lang = $("#lang").val();
	if(lang == "strscomnam"){
		$('input:radio:eq(0)').attr("checked","checked");
	}
	if(lang == "strgname"){
		$('input:radio:eq(1)').attr("checked","checked");
	}
	if($('#select option').size() == 0){
		$("#cont1").hide();
		$("#cont2").show();
		
		$('#msg').html("No search results returned.");
	}
});

function form_submit(){
	var action = parent.functions.location.pathname;
	document.forms.fm2.action = action;
	document.forms.fm2.submit();
}
function get_list(){
	if(navigator.appName.indexOf('Microsoft') != -1){
		document.forms.f3.target = "_blank";
	}
	document.forms.f3.submit();
}
function form_reset(){
	$("#search").val('');
	$("#fm1").submit();
}

/* ]]> */
</script>
</head>
<body>
<?php
require("sw_config.php");
pg_connect($pg_connect);

$avian = $_POST['avian'];
$mammal = $_POST['mammal'];
$reptile = $_POST['reptile'];
$amphibian = $_POST['amphibian'];
$aoi_name = $_POST['aoi_name'];
$language = $_POST['language'];
$search = $_POST['search'];

$protcats = $_POST['protcats'];
//var_dump(json_decode($protcats));

if(!isset($_POST['language'])){
	$language="strscomnam";
}
$rclass = $_SESSION["range".$aoi_name];

?>
<div id="cont1">
<form method="post" action="select_species.php" target="_self" id="fm1">
<input type="hidden" name="avian" value="<?php echo $avian; ?>" />
<input type="hidden" name="mammal" value="<?php echo $mammal; ?>" />
<input type="hidden" name="reptile" value="<?php echo $reptile; ?>" />
<input type="hidden" name="amphibian" value="<?php echo $amphibian; ?>" />
<input type="hidden" name="aoi_name" value="<?php echo $aoi_name; ?>" />
<input type="hidden" name="language" id="lang" value="<?php echo $language; ?>" />
<input type="hidden" name="search" id="search" value="<?php echo $search; ?>" />

<table>
<tr >
<td><input type="radio" name="language"  value="strscomnam"/></td>
<td> English</td>
<td><input type="radio" name="language"  value="strgname"/></td>
<td> Scientific</td>
</tr>
</table>

</form>

<form action="single.php" method="post" name="fm2" target="functions">
<input type="hidden" name="prev_sel" value=""  />

<div style=" position: absolute; top: 25px; width: 95%;">
<select size="7" name="species[]" id="select" multiple="multiple">
<?php  
$rclass->get_species_search($avian, $mammal, $reptile, $amphibian, $language, $search);
$report_name = $rclass->get_species_ss($avian, $mammal, $reptile, $amphibian, $search, $protcats);
?>
</select>
</div>

<div id="btns" >

<button id="slct">Select</button>
<button id="svlist">Save list</button>
<button id="srch">Search</button>
<button id="rst">Reset</button>
	
</div>
</form>
</div>


<div id="cont2">
<form method="post" action="select_species.php" target="_self" name="search_form">
<input type="hidden" name="avian" value="<?php echo $avian; ?>" />
<input type="hidden" name="mammal" value="<?php echo $mammal; ?>" />
<input type="hidden" name="reptile" value="<?php echo $reptile; ?>" />
<input type="hidden" name="amphibian" value="<?php echo $amphibian; ?>" />
<input type="hidden" name="aoi_name" value="<?php echo $aoi_name; ?>" />
<input type="hidden" name="language" id="lang" value="<?php echo $language; ?>" />

<div>
<p>Enter full or partial common name or scientific name:</p>
<input type="text"  name="search" size="30" />
</div>

<button id="srchcncl">Cancel</button>
<button id="srchsbmt">Submit</button>

</form>
<div id="msg"></div>
</div>


<form action="<?php echo '/server_temp/'.$report_name; ?>" target="_self" method="post" name="f3">
</form>


<form action="../data_download.php" method="post" name="fm4" target="_blank" >
<input type="hidden" name="avian" value="<?php echo $avian; ?>" />
<input type="hidden" name="mammal" value="<?php echo $mammal; ?>" />
<input type="hidden" name="reptile" value="<?php echo $reptile; ?>" />
<input type="hidden" name="amphibian" value="<?php echo $amphibian; ?>" />
<input type="hidden" name="aoi_name" value="<?php echo $aoi_name; ?>" />
<input type="hidden" name="search"  value="<?php echo $search; ?>" />
<input type="hidden" name="itis"  />
<input type="hidden" name="richness_map" />
<input type="hidden" name="richness_species" />
</form>

</body>
</html>
