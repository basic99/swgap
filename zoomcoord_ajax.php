<?php
//get data
$proj = $_POST['projection'];
$x= $_POST['user_x'];
$y = $_POST['user_y'];

//if albers return
if ($proj == "albers") {
	echo json_encode(array("x"=>$x, "y"=>$y)); die();
}

//create layer name, file name of input csv file
$csvlayer = "zoomdata".rand(1000,9999);
$csvfilename = "/pub/server_temp/{$csvlayer}.csv";

//create input csv file
$fp = fopen("$csvfilename", "w");
$csvfile[] = "x,y";
$csvfile[] = "{$x},{$y}";
foreach ($csvfile as $line){
	fputcsv($fp, explode(",", $line));
}
fclose($fp );

//load vrt file and update values, remove first line of xml file
$xml1 = simplexml_load_file("grass/geocs.vrt");
$xml1->OGRVRTLayer[0]->SrcDataSource = $csvfilename;
$xml1->OGRVRTLayer[0]->SrcLayer = $csvlayer;
$xml1str = $xml1->asXML();
$xml1str = trim(strstr($xml1str,"\n"));
//echo $xml1str;

$ogc_wkt = 'PROJCS["USA_Contiguous_Albers_Equal_Area_Conic_USGS_version",GEOGCS["GCS_North_American_1983",DATUM["North_American_Datum_1983",SPHEROID["GRS_1980",6378137.0,298.257222101]],PRIMEM["Greenwich",0.0],UNIT["Degree",0.0174532925199433]],PROJECTION["Albers_Conic_Equal_Area"],PARAMETER["False_Easting",0.0],PARAMETER["False_Northing",0.0],PARAMETER["longitude_of_center",-96.0],PARAMETER["Standard_Parallel_1",29.5],PARAMETER["Standard_Parallel_2",45.5],PARAMETER["latitude_of_center",23.0],UNIT["Meter",1.0]]';

//create ogr command and run
$xmlfilename = "xml".rand(1000,9999);
$ogrcmd1 = "/usr/local/bin/ogr2ogr -f \"GML\" -t_srs $ogc_wkt /pub/server_temp/{$xmlfilename}   '{$xml1str}'";
system($ogrcmd1);

//load gml file and get coords as array
$xml2 = simplexml_load_file("/pub/server_temp/{$xmlfilename}");
$val = $xml2->xpath('/ogr:FeatureCollection/gml:featureMember/ogr:zoom/ogr:geometryProperty/gml:Point/gml:coordinates');
$planecoords = explode(",", $val[0]);
echo json_encode(array("x"=>$planecoords[0], "y"=>$planecoords[1]));
?>