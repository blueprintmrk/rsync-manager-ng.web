<html>
<link rel="stylesheet" type="text/css" href="rmng.css">
<head>
<script type="text/javascript">
<!--
function logPopup(parameters) {
window.open( "log.php" + parameters,"fenster", "width=400,height=300,resizable=yes,scrollbars=yes" )
}
//-->
</script>
</head>
<?php
// Includes
include __DIR__."/rmng_conf.php";

// Internal vars
$rmng_link = mysql_connect($rmng_mysql_host,$rmng_mysql_user , $rmng_mysql_pass);
mysql_select_db("rmng",$rmng_link);
$selected_server= $_POST["server"];

//Fill Dropdown with Servernames

echo "<form action=\"".$_SERVER["PHP_SELF"]."\" method=\"POST\" ><p><select name=\"server\">";
$rmng_query="select distinct rmng_servername from rmng_job_statistic";
$result = mysql_query($rmng_query,$rmng_link);
while($row = mysql_fetch_array($result)){
$rmng_servername=$row['rmng_servername'];
echo "<option>".$rmng_servername."</option>";
}
echo "</select></p>";



//Submit Button
echo "<input type=\"submit\" value=\"Select Server\"></form>";

//create the table
$rmng_query="select * from rmng_job_statistic where rmng_servername=\"".$selected_server."\"";
$result = mysql_query($rmng_query,$rmng_link);

echo "<table><tr><th>Servername</th><th>Duration(Minutes)</th><th>Gibibytes total</th><th>Gibibytes transferred</th><th>Status</th></tr>";
while($row = mysql_fetch_array($result)){

$rmng_servername=$row['rmng_servername'];
$rmng_duration=round(($row["rmng_job_stop"]-$row["rmng_job_start"])/60,2);
$rmng_size_total=round($row["rmng_size_total"]/1024/1024,3);
$rmng_size_transferred=round($row["rmng_size_transferred"]/1024/1024,3);
$rmng_status=intval($row['rmng_status']);
$rmng_job_start=$row["rmng_job_start"];
if ( $rmng_status==0){
	$rmng_jobstatus="<img src=\"images/Clear Green Button.png\"";
}
else if ($rmng_status==24){
	$rmng_jobstatus="<img src=\"images/Orange Ball.png\"";
}
else
{
	$rmng_jobstatus="<a href=\"javascript:logPopup('?servername=".$rmng_servername."&time=".$rmng_job_start."')\"><img src=\"images/Mr. Bomb.png\"></a></form>";
}
echo "<tr><td>".$rmng_servername."</td><td>".$rmng_duration."</td><td>".$rmng_size_total."</td><td>".$rmng_size_transferred."</td><td>".$rmng_jobstatus."</td></tr>";

}
echo "</table>";


?>

</html>
