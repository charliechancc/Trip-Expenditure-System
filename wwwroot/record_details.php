<?PHP
define('SysConst', TRUE);
require_once("./config.php");
require_once("./header.php");

$sql = "SELECT `id`, `trip`, `time`, `description`, `currency` , `waived`, `IP`, `UserAgent` FROM `pay_record` WHERE `id` = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param('i',$_GET['id']);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($pay_record_id, $trip, $time, $description, $currency, $waived, $IP, $UserAgent);
$stmt->fetch();

$sql = "SELECT `name`, `password` FROM `trip` WHERE `trip_id` = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param('i', $trip);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($tripName, $tripPassword);
$stmt->fetch();

if(isset($_POST['password']) && $tripPassword == hash('sha512', $_POST['password']))
{
	$_SESSION['trip'][$trip] = true;
}

if(isset($_POST['logout']))
{
	unset($_SESSION['trip'][$trip]);
}

if(isset($_SESSION['trip'][$trip]))
{
	?>
	<p>
	<form method="post">
	<input class="btn btn-success" name="logout" value="Logout" type="submit">
	</form>
	</p>
	<?
}

if($tripPassword != "" && $_SESSION['trip'][ $trip ] != true)
{
	?>
	<form method="post">
	Enter Password:<br>
	<input class="form-control" type="password" name="password" required><br>
	<input class="btn btn-primary" type="submit">
	</form>
	<?
}
else
{
	
	$sql = "SELECT trip_participant.person_id , people.name FROM trip_participant, people WHERE people.person_id = trip_participant.person_id AND trip_participant.trip_id = ?";
	$stmt = $mysqli->prepare($sql);
	$stmt->bind_param('i',$trip);
	$stmt->execute();
	$stmt->bind_result($person_id, $name);
	while($stmt->fetch()){
		$participant[$person_id] = $name;
	}

	?>

	<table class="table">
	<tr>
		<td>
		Reference ID:
		</td>
		<td>
		<?PHP echo $pay_record_id?>
		</td>
	</tr>
	<tr>
		<td>
		Inserted at:
		</td>
		<td>
		<?PHP echo date("Y-m-d H:i", $time)?>
		</td>
	</tr>
	<tr>
		<td>
		Trip:
		</td>
		<td>
		<?PHP echo $tripName?>
		</td>
	</tr>
	<tr>
		<td>
		IP Address:
		</td>
		<td>
		<?PHP echo $IP?>
		</td>
	</tr>
	<tr>
		<td>
		Device:
		</td>
		<td>
		<?PHP echo $UserAgent?>
		</td>
	</tr>
	<tr>
		<td>
		Description:
		</td>
		<td>
		<?PHP echo $description?>
		</td>
	</tr>
	<tr>
		<td>
		Currency:
		</td>
		<td>
		<?PHP echo $currency?>
		</td>
	</tr>
	<tr>
		<td>
		Paid By:
		</td>
		<td>
		<?
			$sql = "SELECT `payer`, `amount` FROM `payer` WHERE `pay_record` = ?";
			$stmt = $mysqli->prepare($sql);
			$stmt->bind_param('i',$_GET['id']);
			$stmt->execute();
			$stmt->bind_result($payer, $amount);
			while($stmt->fetch()){
				echo $participant[ $payer ].": ".$amount."<br>";
			}
		?>
		</td>
	</tr>
	<tr>
		<td>
		Paid For:
		</td>
		<td>
		<?
			$sql = "SELECT `payee` FROM `pay_for` WHERE `pay_record` = ?";
			$stmt = $mysqli->prepare($sql);
			$stmt->bind_param('i',$_GET['id']);
			$stmt->execute();
			$stmt->bind_result($payee);
			while($stmt->fetch()){
				echo $participant[ $payee ]."<br>";
			}
		?>
		</td>
	</tr>
	</table>

<?PHP 
}
require_once("./footer.php");
?>