<?PHP
define('SysConst', TRUE);
require_once("./config.php");
require_once("./header.php");

if(isset($_POST['title']))
{
	$time = date('U');
	
	$hashedPW = hash('sha512', $_POST['password']);
	if($_POST['password'] == "")
		$hashedPW = "";
	
	$sql = "INSERT INTO `trip` (`time`, `name`, `password`) VALUES (?, ?, ?)";
	$stmt = $mysqli->prepare($sql);
	$stmt->bind_param('iss', $time, $_POST['title'], $hashedPW);
	$stmt->execute();
	$stmt->store_result();
	$stmt->close();
	
	$sql = "SELECT `trip_id` FROM `trip` WHERE `time` = ?";
	$stmt = $mysqli->prepare($sql);
	$stmt->bind_param('i', $time);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($trip_id);
	
	if($stmt->fetch()){
		echo "trip=".$trip_id."<br>";
		
		foreach( $_POST['participants'] as $participant => $n ) {
			echo $trip_id.", ".$n."<br>";
			$sql1 = "INSERT INTO `trip_participant` (`trip_id`, `person_id`) VALUES (?, ?)";
			$stmt1 = $mysqli->prepare($sql1);
			$stmt1->bind_param('ii', $trip_id, $n);
			$stmt1->execute();
		}
	}
	$stmt1->close();
	$stmt->close();
	
	header('Location: https://charliechan.cc/trip/');
}
else
{
	?>
	<p><button type="submit" class="btn btn-primary" onclick="location.href = './person_new.php';">Add a new person</button></p>

	<form method="post">
	<table class="table">
	
	<tr>
		<td>Trip Title:</td>
		<td><input class="form-control" type="text" name="title" required></td>
	</tr>
	<tr>
		<td>Password</td>
		<td><input class="form-control" type="password" name="password"></td>
	</tr>
	<tr>
		<td>Participant:</td>
		<td>
			<?PHP
			$sql = "SELECT * FROM `people`";
			$stmt = $mysqli->prepare($sql);
			//$stmt->bind_param('i',$sn);
			$stmt->execute();
			$stmt->bind_result($person_id, $name);
			$sn=0;
			while($stmt->fetch()){
				echo "<div class=\"custom-control custom-checkbox\">";
				echo "<input class=\"custom-control-input\" type=\"checkbox\" name=\"participants[]\" id=\"checkbox".$sn."\" value=\"".$person_id."\">";
				echo "<label class=\"custom-control-label\" for=\"checkbox".$sn."\">".$name."</label>";
				echo "</div>";
				
				$sn++;
			}
			echo "</table>";
			$stmt->close();
			?>
		</td>

	</tr>
	<input class="btn btn-primary"  type="submit">
	</form>

<?PHP
}
require_once("./footer.php");
?>