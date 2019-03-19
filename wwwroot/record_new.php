<?PHP
define('SysConst', TRUE);
require_once("./config.php");
require_once("./header.php");

$sql = "SELECT `password` FROM `trip` WHERE `trip_id` = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param('i', $_GET['trip']);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($tripPassword);
$stmt->fetch();

if(isset($_POST['password']) && $tripPassword == hash('sha512', $_POST['password']))
{
	$_SESSION['trip'][$_GET['trip']] = true;
}

if(isset($_POST['logout']))
{
	unset($_SESSION['trip'][$_GET['id']]);
}

if(isset($_SESSION['trip'][$_GET['id']]))
{
	?>
	<p>
	<form method="post">
	<input class="btn btn-success" name="logout" value="Logout" type="submit">
	</form>
	</p>
	<?
}

if($tripPassword != "" && $_SESSION['trip'][ $_GET['trip'] ] != true)
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
	if(isset($_POST['desc']) && isset($_GET['trip']))
	{
		$payer_weight=0;
		$payers=0;
		foreach($_POST['payer'] as $payer => $n ) {
			if($n != 0)
			{
				//echo $pay_record_id.", ".$payer.", ".$n."<br>";
				$payer_weight += $payer;
				$payers++;
				
				$payers_input[$payer]=$n;
			}
		}
		
		$payee_weight=0;
		$payees=0;
		foreach( $_POST['participants'] as $participant => $n ) {
			//echo $pay_record_id.", ".$n."<br>";
			$payee_weight += $n;
			$payees++;
			
			$payees_input[$n]=1;
		}
		
		$valid = true;
		
		if($payer_weight == 0 || $payee_weight ==0)
			$valid = false;
		if($payees == 1 && $payer_weight == $payee_weight)
			$valid = false;

		if($valid)
		{
			$time = date('U');
			$sql = "INSERT INTO `pay_record` (`trip`, `time`, `description`, `currency`, `IP`, `UserAgent`) VALUES (?, ?, ?, ?, ?, ?)";
			$stmt = $mysqli->prepare($sql);
			$stmt->bind_param('iissss', $_GET['trip'], $time, $_POST['desc'], $_POST['currency'], $_SERVER["REMOTE_ADDR"], $_SERVER['HTTP_USER_AGENT']);
			$stmt->execute();
			$stmt->store_result();
			$stmt->close();
			
			$sql = "SELECT `id` FROM `pay_record` WHERE `time` = ?";
			$stmt = $mysqli->prepare($sql);
			$stmt->bind_param('i', $time);
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($pay_record_id);
			
			if($stmt->fetch())
			{
				echo $pay_record_id."<br>";
				foreach($_POST['payer'] as $payer => $n ) {
					if($n != 0)
					{
						echo $pay_record_id.", ".$payer.", ".$n."<br>";
						$sql1 = "INSERT INTO `payer` (`pay_record`, `payer`, `amount`) VALUES (?, ?, ?)";
						$stmt1 = $mysqli->prepare($sql1);
						$stmt1->bind_param('iid', $pay_record_id, $payer, $n);
						$stmt1->execute();
					}
				}
				
				foreach( $_POST['participants'] as $participant => $n ) {
					echo $pay_record_id.", ".$n."<br>";
					$sql1 = "INSERT INTO `pay_for` (`pay_record`, `payee`) VALUES (?, ?)";
					$stmt1 = $mysqli->prepare($sql1);
					$stmt1->bind_param('ii', $pay_record_id, $n);
					$stmt1->execute();
				}
			}
			header('Location: https://charliechan.cc/trip/trip.php?id='.$_GET['trip']);
		}
		else
		{?>
			<div class="alert alert-danger" role="alert">
			  Invalid input
			</div>
			<?PHP
		}
		
	}
	?>

	<form method="post">
	<table class="table">
	<tr>
		<td>Description:</td>
		<td><input class="form-control" type="text" name="desc" value="<?PHP if(isset($_POST['desc'])){echo $_POST['desc'];}?>"required></td>
	</tr>


	<tr>
		<td>Currency:</td>
		<td>
			 <select class="form-control" name="currency">
				<?
				for($x = 0; $x < count($currencyArray); $x++) {
					echo "<option value=\"".$currencyArray[$x]."\">".$currencyArray[$x]."</option>\n";
				}
				?>
			</select> 
		</td>
	</tr>
		
	<tr>
		<td>Paid By:</td>
		<td>
		<?PHP
			$sql = "SELECT trip_participant.person_id , people.name FROM trip_participant, people WHERE people.person_id = trip_participant.person_id AND trip_participant.trip_id = ?";
			$stmt = $mysqli->prepare($sql);
			$stmt->bind_param('i',$_GET['trip']);
			$stmt->execute();
			$stmt->bind_result($person_id, $name);
			while($stmt->fetch()){
				echo $name."<input class=\"form-control\" type=\"number\" step=\"0.01\" min=\"0\" max=\"9999999999\" name=\"payer[".$person_id."]\" ";
				
				if(isset($payers_input[$person_id]))
					echo "value=\"".$payers_input[$person_id]."\">";
				
				echo "<br>";
			}
		?>
		</td>
	</tr>

	<tr>
	<br>
		<td>Paid For:</td>
		<td>
		<?PHP
			$sql = "SELECT trip_participant.person_id , people.name FROM trip_participant, people WHERE people.person_id = trip_participant.person_id AND trip_participant.trip_id = ?";
			$stmt = $mysqli->prepare($sql);
			$stmt->bind_param('i',$_GET['trip']);
			$stmt->execute();
			$stmt->bind_result($person_id, $name);
			$sn=0;
			while($stmt->fetch()){
				echo "<div class=\"custom-control custom-checkbox\">";
					echo "<input class=\"custom-control-input\" type=\"checkbox\" name=\"participants[]\" id=\"checkbox".$sn."\" value=\"".$person_id."\"";
					
					if(isset($payees_input[$person_id]))
						echo "checked";
				
					echo ">";
					echo "<label class=\"custom-control-label\" for=\"checkbox".$sn."\">".$name."</label>";
				echo "</div>";
				
				$sn++;
			}
		?>
		</td>
	</tr>
	</table>
	<input class="btn btn-primary"  type="submit">

	</form>

<?PHP 
}
require_once("./footer.php");
?>