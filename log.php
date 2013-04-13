<html>
<link rel="stylesheet" type="text/css" href="rmng.css">
<head>
</head>
<body class="logfile">
<?php
// Includes
include __DIR__."/rmng_conf.php";

// Internal vars
$rmng_link = mysql_connect($rmng_mysql_host,$rmng_mysql_user , $rmng_mysql_pass);
mysql_select_db("rmng",$rmng_link);
$selected_server= $_GET["servername"];
$selected_time= $_GET["time"];

$rmng_query="select rmng_crashinfo from rmng_job_statistic where rmng_servername='".$selected_server."' and rmng_job_start='".$selected_time."' ";
$result = mysql_query($rmng_query,$rmng_link);
while($row = mysql_fetch_array($result)){
	$array = unserialize($row['rmng_crashinfo']);
	foreach($array as $arow){
	echo $arow."<br>";
	}
}
echo "EOF";
?>
</body>
</html>
