<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<title>Listing Status</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="StyleSheet" href="styles/popups.css" type="text/css" />
<style type="text/css">
/* <![CDATA[ */
td {width: 200px;}
h3 {text-align: left;}
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

//var_dump($_POST);

$itiscode = $_POST['itiscode'];
$species = stripslashes($_POST['species']);
$query = "select * from info_spp where stritiscode = '{$itiscode}'";
$result = pg_query($query);
$row = pg_fetch_array($result);

//var_dump($row);

?>
<input type="hidden" id="taxclas" value="<?php echo $row['strtaxclas']; ?>" />
<h3><?php echo $row['strscomnam']; ?><br /><i><?php echo $row['strgname']; ?></i></h3>
<hr />
<table>
<tr>
<td><a href="/listcodes/FederalStatusCodes.html" target="fedcodes" onclick="window.open('', 'fedcodes', 'menubar=no,height=150,width=520')"><b>Federal Status</b></a></td> 

<td><?php 
if(strlen($row['strusesa']) == 0) {
   echo "---";
}else{
   echo $row['strusesa']; 
} 
?></td>
<tr>

<td colspan="2"><a href="/listcodes/SWStateStatusCodes.html" target="statecodes" onclick="window.open('', 'statecodes', 'menubar=no,height=400,width=720')"><b>State Status</b></a></td>
</tr>
<tr>
<td>&nbsp;&nbsp;Arizona</td>
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
<td>&nbsp;&nbsp;Colorado</td>
<td>
<?php 
if(strlen($row['strsprotco']) == 0) {
   echo "---";
}else{
   echo $row['strsprotco']; 
}

?>
</td>
</tr>
<tr>
<td>&nbsp;&nbsp;New Mexico</td>
<td>
<?php  
if(strlen($row['strsprotnm']) == 0) {
   echo "---";
}else{
   echo $row['strsprotnm']; 
}

?>
</td>
</tr>
<tr>
<td>&nbsp;&nbsp;Nevada</td>

<td>
<?php 
if(strlen($row['strsprotnv']) == 0) {
   echo "---";
}else{
   echo $row['strsprotnv']; 
}

?>
</td>
</tr>
<tr>
<td>&nbsp;&nbsp;Utah</td>
<td>
<?php 
if(strlen($row['strsprotut']) == 0) {
   echo "---";
}else{
   echo $row['strsprotut']; 
}

?>
</td>
</tr>
<tr>
<td colspan="2"><a href="http://www.natureserve.org/explorer/ranking.htm" target="nserv" onclick="window.open('', 'nserv', 'menubar=no,scrollbars=yes,width=800')"><b>Nature Serve Rank</b></a></td>
</tr>
<tr>
<td>&nbsp;&nbsp;Global Rank</td>
<td>
<?php 
if(strlen($row['strgrank']) == 0) {
   echo "---";
}else{
   echo $row['strgrank']; 
}
?>
</td>

</tr>
<tr>
<td>&nbsp;&nbsp;AZ State Rank</td>
<td>
<?php 
if(strlen($row['strsrankaz']) == 0) {
   echo "---";
}else{
   echo $row['strsrankaz']; 
}
?>
</td>
</tr>
<tr>
<td>&nbsp;&nbsp;CO State Rank</td>
<td>
<?php  
if(strlen($row['strsrankco']) == 0) {
   echo "---";
}else{
   echo $row['strsrankco']; 
}
?>
</td>
</tr>
<tr>
<td>&nbsp;&nbsp;NM State Rank</td>

<td>
<?php  
if(strlen($row['strsranknm']) == 0) {
   echo "---";
}else{
   echo $row['strsranknm']; 
}
?>
</td>
</tr>
<tr>
<td>&nbsp;&nbsp;NV State Rank</td>
<td>
<?php
if(strlen($row['strsrankaz']) == 0) {
   echo "---";
}else{
   echo $row['strsrankaz']; 
}
?>
</td>
</tr>
<tr>
<td>&nbsp;&nbsp;UT State Rank</td>
<td>
<?php 
if(strlen($row['strsrankut']) == 0) {
   echo "---";
}else{
   echo $row['strsrankut']; 
}
?>
</td>

</tr>
<tr>
<td colspan="2"><a href="/listcodes/SGCNStatusCodes.html" target="sgcncodes" onclick="window.open('', 'sgcncodes', 'menubar=no,height=150,width=720')"><b>Species of Greatest Conservation Need</b></a></td>
</tr>
<tr>
<td>&nbsp;&nbsp;AZ SGCN</td>
<td>
<?php 
if(strlen($row['strsgcnaz']) == 0) {
   echo "---";
}else{
   echo $row['strsgcnaz']; 
}
?>
</td>
</tr>
<tr>
<td>&nbsp;&nbsp;CO SGCN</td>
<td>
<?php 
if(strlen($row['strsgcnco']) == 0) {
   echo "---";
}else{
   echo $row['strsgcnco']; 
}
?>
</td>
</tr>
<tr>
<td>&nbsp;&nbsp;NM SGCN</td>

<td>
<?php 
if(strlen($row['strsgcnnm']) == 0) {
   echo "---";
}else{
   echo $row['strsgcnnm']; 
}
?>
</td>
</tr>
<tr>
<td>&nbsp;&nbsp;NV SGCN</td>
<td>
<?php 
if(strlen($row['strsgcnnv']) == 0) {
   echo "---";
}else{
   echo $row['strsgcnnv']; 
}
?>
</td>
</tr>
<tr>
<td>&nbsp;&nbsp;UT SGCN</td>
<td>
<?php 
if(strlen($row['strsgcnut']) == 0) {
   echo "---";
}else{
   echo $row['strsgcnut']; 
}
?>
</td>

</tr>
</table>

<div id="pif">
<table>
<tr><td colspan="2"><a href="/listcodes/PIFStatusCodes.html" target="pifcodes" onclick="window.open('', 'pifcodes', 'menubar=no,height=550,width=720')"><b>Partners-In-Flight</b></a></td></tr>
<tr>
<td>&nbsp;&nbsp;Great Basin</td>
<td>
<?php 
if(strlen($row['strpif09']) == 0) {
   echo "---";
}else{
   echo $row['strpif09']; 
}
?>
</td>
</tr>
<tr>
<td>&nbsp;&nbsp;N. Rockies</td>

<td>
<?php  
if(strlen($row['strpif10']) == 0) {
   echo "---";
}else{
   echo $row['strpif10']; 
}
?>
</td>
</tr>
<tr>
<td>&nbsp;&nbsp;S. Rockies / CO Plateau</td>
<td>
<?php  
if(strlen($row['strpif16']) == 0) {
   echo "---";
}else{
   echo $row['strpif16']; 
}
?>
</td>
</tr>
<tr>
<td>&nbsp;&nbsp;Shortgrass Prairie</td>
<td>
<?php 
if(strlen($row['strpif18']) == 0) {
   echo "---";
}else{
   echo $row['strpif18']; 
}
?>
</td>

</tr>
<tr>
<td>&nbsp;&nbsp;Sonoran and Mohave Dst.</td>
<td>
<?php  
if(strlen($row['strpif33']) == 0) {
   echo "---";
}else{
   echo $row['strpif33']; 
}
?>
</td>
</tr>
<tr>
<td>&nbsp;&nbsp;Sierra Madre Occidental</td>
<td>
<?php 
if(strlen($row['strpif34']) == 0) {
   echo "---";
}else{
   echo $row['strpif34']; 
}
?>
</td>
</tr>
<tr>
<td>&nbsp;&nbsp;Chihuahuan Desert</td>

<td>
<?php 
if(strlen($row['strpif35']) == 0) {
   echo "---";
}else{
   echo $row['strpif35']; 
}
?>
</td>
</tr>
</table>
</div>

</body>
</html>
