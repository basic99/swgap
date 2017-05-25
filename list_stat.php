<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<title>Listing Status</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<style type="text/css">
/* <![CDATA[ */
body {font-family: sans-serif;}
.hdr {font-weight: bold; font-size: 1.1em;}
td {width: 200px;}
h3 {text-align: center;}
/* ]]> */
</style>
<script language="javascript" type="text/javascript">
/* <![CDATA[ */
function set_view(){
   var taxclass = document.getElementById('taxclas').value;
   //alert(taxclass);
   if(taxclass != 'AVES'){
      document.getElementById('pif').style.display = 'none';
   }
}
/* ]]> */
</script>
</head>
<body onload="set_view();">
<?php
require("sw_config.php");
pg_connect($pg_connect);

$itiscode = $_POST['itiscode'];
$species = stripslashes($_POST['species']);

$query = "select * from info_spp where stritiscode = '{$itiscode}'";

//echo $query;

$result = pg_query($query);
$row = pg_fetch_array($result);
var_dump($row);

?>
<input type="hidden" id="taxclas" value="<?php echo $row['strtaxclas']; ?>" />
<h3><?php echo $species; ?></h3>
<table>
<tr><td class='hdr' colspan="2" class="hdr">Ranking Information</td></tr>
<tr>
<td>Federal</td>
<td>
<?php 
if(strlen($row['strusesa']) == 0) {
   echo "---";
}else{
   echo $row['strusesa']; 
} 
?>
</td>
</tr>
<tr>
<td>AZ State</td>
<td>
<?php 
if(strlen($row['strsprotaz']) == 0) {
   echo "---";
}else{
   echo $row['strsprotaz']; 
}

?></td>
</tr>
<tr>
<td>CO State</td>
<td>
<?php 
if(strlen($row['strsprotco']) == 0) {
   echo "---";
}else{
   echo $row['strsprotco']; 
}

?></td>
</tr>
<tr>
<td>NM State</td>
<td>
<?php  
if(strlen($row['strsprotnm']) == 0) {
   echo "---";
}else{
   echo $row['strsprotnm']; 
}

?></td>
</tr>
<tr>
<td>NV State</td>
<td>
<?php 
if(strlen($row['strsprotnv']) == 0) {
   echo "---";
}else{
   echo $row['strsprotnv']; 
}

?></td>
</tr>
<tr>
<td>UT State</td>
<td>
<?php 
if(strlen($row['strsprotut']) == 0) {
   echo "---";
}else{
   echo $row['strsprotut']; 
}

?></td>
</tr>
<tr>
<td>Nserve Global</td>
<td>
<?php 
if(strlen($row['strgrank']) == 0) {
   echo "---";
}else{
   echo $row['strgrank']; 
}
?></td>
</tr>
<tr>
<td>Nserve (AZ)</td>
<td>
<?php 
if(strlen($row['strsrankaz']) == 0) {
   echo "---";
}else{
   echo $row['strsrankaz']; 
}
?></td>
</tr>
<tr>
<td>Nserve (CO)</td>
<td>
<?php  
if(strlen($row['strsrankco']) == 0) {
   echo "---";
}else{
   echo $row['strsrankco']; 
}
?></td>
</tr>
<tr>
<td>Nserve (NM)</td>
<td>
<?php  
if(strlen($row['strsranknm']) == 0) {
   echo "---";
}else{
   echo $row['strsranknm']; 
}
?></td>
</tr>
<tr>
<td>Nserve (NV)</td>
<td>
<?php
if(strlen($row['strsrankaz']) == 0) {
   echo "---";
}else{
   echo $row['strsrankaz']; 
}
?></td>
</tr>
<tr>
<td>Nserve (UT)</td>
<td>
<?php 
if(strlen($row['strsrankut']) == 0) {
   echo "---";
}else{
   echo $row['strsrankut']; 
}
?></td>
</tr>
<tr>
<td>AZ SGCN</td>
<td>
<?php 
if(strlen($row['strsgcnaz']) == 0) {
   echo "---";
}else{
   echo $row['strsgcnaz']; 
}
?></td>
</tr>
<tr>
<td>CO SGCN</td>
<td>
<?php 
if(strlen($row['strsgcnco']) == 0) {
   echo "---";
}else{
   echo $row['strsgcnco']; 
}
?></td>
</tr>
<tr>
<td>NM SGCN</td>
<td>
<?php 
if(strlen($row['strsgcnnm']) == 0) {
   echo "---";
}else{
   echo $row['strsgcnnm']; 
}
?></td>
</tr>
<tr>
<td>NV SGCN</td>
<td>
<?php 
if(strlen($row['strsgcnnv']) == 0) {
   echo "---";
}else{
   echo $row['strsgcnnv']; 
}
?></td>
</tr>
<tr>
<td>UT SGCN</td>
<td>
<?php 
if(strlen($row['strsgcnut']) == 0) {
   echo "---";
}else{
   echo $row['strsgcnut']; 
}
?></td>
</tr>
</table>

<div id="pif">
<table>
<tr><td class="hdr" colspan="2">Partners-In-Flight Regions</td></tr>
<tr>
<td>Great Basin</td>
<td>
<?php 
if(strlen($row['strpif09']) == 0) {
   echo "---";
}else{
   echo $row['strpif09']; 
}
?></td>
</tr>
<tr>
<td>Northern Rockies</td>
<td>
<?php  
if(strlen($row['strpif10']) == 0) {
   echo "---";
}else{
   echo $row['strpif10']; 
}
?></td>
</tr>
<tr>
<td>Southern Rockies/ Colorado Plateau</td>
<td>
<?php  
if(strlen($row['strpif16']) == 0) {
   echo "---";
}else{
   echo $row['strpif16']; 
}
?></td>
</tr>
<tr>
<td>Shortgrass Prairie</td>
<td>
<?php 
if(strlen($row['strpif18']) == 0) {
   echo "---";
}else{
   echo $row['strpif18']; 
}
?></td>
</tr>
<tr>
<td>Sonoran and Mohave Deserts</td>
<td>
<?php  
if(strlen($row['strpif33']) == 0) {
   echo "---";
}else{
   echo $row['strpif33']; 
}
?></td>
</tr>
<tr>
<td>Sierra Madre Occidental</td>
<td>
<?php 
if(strlen($row['strpif34']) == 0) {
   echo "---";
}else{
   echo $row['strpif34']; 
}
?></td>
</tr>
<tr>
<td>Chihuahuan Desert</td>
<td>
<?php 
if(strlen($row['strpif35']) == 0) {
   echo "---";
}else{
   echo $row['strpif35']; 
}
?></td>
</tr>
</table>
</div>

</body>
</html>
