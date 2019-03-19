<?PHP
	define('SysConst', TRUE);
	require_once("./config.php");
	require_once("./header.php");

echo "<p><button type=\"submit\" class=\"btn btn-primary\" onclick=\"location.href = './trip_new.php';\">Add a new trip</button></p>";
echo "
<table class=\"table\">
<tr>
    <th>Trip</th>
    <th>Created</th>
  </tr>";
  
$sql = "SELECT `trip_id`, `time`, `name` FROM `trip`";
$stmt = $mysqli->prepare($sql);
//$stmt->bind_param('i',$sn);
$stmt->execute();
$stmt->bind_result($trip_id, $time, $name);
while($stmt->fetch()){
    echo "<tr><td><a href=\"trip.php?id=".$trip_id."\">".$name."</a></td><td>".date("Y-m-d H:i", $time)."</td></tr>";
}

echo "</table>";

require_once("./footer.php");
?>
