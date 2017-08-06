<?php
//copy file to sw_config.php and make changes as necessary

//location of GRASS raster data for webserver
$grass_raster = "/data/southwest/webserv/cellhd/";

//location of GRASS raster data for permanent
$grass_raster_perm = "/data/southwest/PERMANENT/cellhd/";

$GISBASE = "/usr/local/grass-6.4.0svn";

// copy .grassrc6 from /home/webserv
$GISRC = "/var/www/html/swgap/grassrc";

$PATH = "/usr/local/grass-6.4.0svn/bin:/usr/local/grass-6.4.0svn/scripts:/usr/local/bin:/usr/bin:/bin";

//set max aoi and large bb area in square meters
$max_aoi_area = 950000000000;
$large_aoi_area = 100000000000;

$pg_connect = "host=localhost dbname=swgap user=postgres";

$mspath = "/pub/server_temp/";

ini_set("log_errors", 1);
?>