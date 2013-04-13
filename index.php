<html>
<link rel="stylesheet" type="text/css" href="rmng.css">
<head>
<script type="text/javascript">
<!--
function logPopup(parameters) {
window.open( "log.php" + parameters,"fenster", "width=600,height=300,resizable=yes,scrollbars=yes,location=no,toolbars=no,menubar=no" )
}
//-->
</script>
</head>
<body><script type="text/javascript" src="wz_tooltip.js"></script>
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
echo "</select>";

//Submit Button
echo "<input type=\"submit\" value=\"Select Server\"><p></form>";



if (!($_POST["server"])) {

	//get maximums for last 100 jobs
	$rmng_query="select max(rmng_size_total) from rmng_job_statistic ORDER BY rmng_job_start DESC LIMIT 100";
	$result = mysql_query($rmng_query,$rmng_link);
	while($row = mysql_fetch_array($result)){  $max_total=$row[0];}
	$rmng_query="select max(rmng_size_transferred) from rmng_job_statistic ORDER BY rmng_job_start DESC LIMIT 100";
        $result = mysql_query($rmng_query,$rmng_link);
        while($row = mysql_fetch_array($result)){  $max_trans=$row[0];}
	$rmng_query="select max(rmng_job_stop-rmng_job_start) from rmng_job_statistic ORDER BY rmng_job_start DESC LIMIT 100";
        $result = mysql_query($rmng_query,$rmng_link);
        while($row = mysql_fetch_array($result)){  $max_time=$row[0];}


		
	//create the table for last 100 jobs
        $rmng_query="select * from rmng_job_statistic ORDER BY rmng_job_start DESC LIMIT 100";
        $result = mysql_query($rmng_query,$rmng_link);
        echo "<table><tr><th class='servername'>Servername</th><th class='jobstart'>Job start</th><th class='duration'>Duration</th><th class='total'>Bytes total</th><th class='trans'>Bytes transferred</th><th class='over'>Overview</th><th class='status'>Status</th></tr>";
	while($row = mysql_fetch_array($result)){

        	$rmng_servername=$row['rmng_servername'];
        	$rmng_duration=round(($row["rmng_job_stop"]-$row["rmng_job_start"])/60,2);
        	$rmng_size_total=number_format($row["rmng_size_total"],0,",",".");
        	$rmng_size_transferred=number_format($row["rmng_size_transferred"],0,",",".");
        	$rmng_status=intval($row['rmng_status']);
        	$rmng_job_start=$row["rmng_job_start"];
		//Calculate the div-container-with in table
		$percentage_total=($row["rmng_size_total"]/$max_total)*40;
		$percentage_trans=($row["rmng_size_transferred"]/$max_trans)*40;
		$percentage_time=(($row["rmng_job_stop"]-$row["rmng_job_start"])/$max_time)*40;
		//Correct vision-errors
		if ($percentage_total<="0"){$percentage_total=0;};
		if ($percentage_trans<="0"){$percentage_trans=0;};
		if ($percentage_time<="0"){$percentage_time=0;};
		//Choose the status-image
		if ( $rmng_status==0){
                	$rmng_jobstatus="<img src=\"images/Clear Green Button.png\" class=\"scaled\" onmouseover=\"Tip('Success - no errors')\" onmouseout=\"UnTip()\">";
        	}	
        	else if ($rmng_status==24){
                	$rmng_jobstatus="<img src=\"images/Orange Ball.png\" class=\"scaled\"  onmouseover=\"Tip('Partial transfer due to vanished source files')\" onmouseout=\"UnTip()\">";
        	}
        	else
        	{
                	$rmng_jobstatus="<a href=\"javascript:logPopup('?servername=".$rmng_servername."&time=".$rmng_job_start."')\"><img src=\"images/Mr. Bomb.png\" class=\"scaled\" onmouseover=\"Tip('Click for crashlog-popup')\" onmouseout=\"UnTip()\"></a></form>";
        	}
        	echo "<tr><td>".$rmng_servername."</td><td>".date("Y-m-d H:i:s",$rmng_job_start)."</td><td>".$rmng_duration." minutes </td><td class='total'>".$rmng_size_total."</td><td class='trans'>".$rmng_size_transferred."</td><td><div style='width:".$percentage_time.";background-color:#cc0000;height:15;float:left;'onmouseover=\"Tip('time/max_time')\" onmouseout=\"UnTip()\" ></div><div style='width:".$percentage_total.";background-color:#00FF00;height:15;float:left' onmouseover=\"Tip('total_bytes/max_total_bytes')\" onmouseout=\"UnTip()\"></div><div style='width:".$percentage_trans.";height:15;background-color:#FFFF00;float:left;' onmouseover=\"Tip('transferred_bytes/max_transferred_bytes')\" onmouseout=\"UnTip()\"></div></td><td class='status'>".$rmng_jobstatus."</td> </tr>";
	
        }
        echo "</table>";


}

else {

	//create the table
	$rmng_query="select * from rmng_job_statistic where rmng_servername=\"".$selected_server."\"";
	$result = mysql_query($rmng_query,$rmng_link);

	echo "<table><tr><th class='servername'>Servername</th><th class='duration'>Duration(Minutes)</th><th class='total'>Gibibytes total</th><th class='trans'>Gibibytes transferred</th><th class='status'>Status</th></tr>";
	while($row = mysql_fetch_array($result)){

	$rmng_servername=$row['rmng_servername'];
	$rmng_duration=round(($row["rmng_job_stop"]-$row["rmng_job_start"])/60,2);
	$rmng_size_total=round($row["rmng_size_total"]/1024/1024/1024,3);
	$rmng_size_transferred=round($row["rmng_size_transferred"]/1024/1024/1024,3);
	$rmng_status=intval($row['rmng_status']);
	$rmng_job_start=$row["rmng_job_start"];
	if ( $rmng_status==0){
		$rmng_jobstatus="<img src=\"images/Clear Green Button.png\" class=\"scaled\" onmouseover=\"Tip('Success - no errors')\" onmouseout=\"UnTip()\">";
	}
	else if ($rmng_status==24){
		$rmng_jobstatus="<img src=\"images/Orange Ball.png\" class=\"scaled\"  onmouseover=\"Tip('Partial transfer due to vanished source files')\" onmouseout=\"UnTip()\">";
	}
	else
	{
		$rmng_jobstatus="<a href=\"javascript:logPopup('?servername=".$rmng_servername."&time=".$rmng_job_start."')\"><img src=\"images/Mr. Bomb.png\" class=\"scaled\" onmouseover=\"Tip('Click for crashlog-popup')\" onmouseout=\"UnTip()\"></a></form>";
	}
	echo "<tr><td>".$rmng_servername."</td><td>".$rmng_duration."</td><td>".$rmng_size_total."</td><td>".$rmng_size_transferred."</td><td class='status'>".$rmng_jobstatus."</td></tr>";
	
	}
	echo "</table>";
}

?>
</body>
</html>
