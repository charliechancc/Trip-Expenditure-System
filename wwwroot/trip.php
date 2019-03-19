<?PHP
define('SysConst', TRUE);
require_once("./config.php");
require_once("./header.php");

$sql = "SELECT `password` FROM `trip` WHERE `trip_id` = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param('i', $_GET['id']);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($tripPassword);
$stmt->fetch();

if(isset($_POST['password']) && $tripPassword == hash('sha512', $_POST['password']))
{
	$_SESSION['trip'][$_GET['id']] = true;
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

if($tripPassword != "" && $_SESSION['trip'][ $_GET['id'] ] != true)
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

	if(isset($_GET['waive']))
	{	
		$sql = "UPDATE `tripMoney`.`pay_record` SET `waived` = '1' WHERE `pay_record`.`id` = ?";
		$stmt = $mysqli->prepare($sql);
		$stmt->bind_param('i',$_GET['waive']);
		$stmt->execute();
	}

	if(isset($_GET['unwaive']))
	{	
		$sql = "UPDATE `tripMoney`.`pay_record` SET `waived` = '0' WHERE `pay_record`.`id` = ?";
		$stmt = $mysqli->prepare($sql);
		$stmt->bind_param('i',$_GET['unwaive']);
		$stmt->execute();
	}

	echo "<p><button type=\"submit\" class=\"btn btn-primary\" onclick=\"location.href = './record_new.php?trip=".$_GET['id']."';\">Add a new record</button></p>";
	echo "<table class=\"table\">
	<thead>
	<tr>
		<th scope=\"col\">Date</th>
		<th scope=\"col\">Description</th>";

		$sn=0;;
		$sql = "SELECT trip_participant.person_id , people.name FROM trip_participant, people WHERE people.person_id = trip_participant.person_id AND trip_participant.trip_id = ?";
		$stmt = $mysqli->prepare($sql);
		$stmt->bind_param('i',$_GET['id']);
		$stmt->execute();
		$stmt->bind_result($person_id, $name);
		while($stmt->fetch()){
			echo "<th scope=\"col\">".$name."</th>";
			
			
			$participant[$person_id] = $sn;
			$sn++;
		}
		
		echo "<th scope=\"col\">Remarks</th>";
		echo "<th scope=\"col\">Operation</th>";
	echo "</tr>
	</thead>";
	  

	$currency_num=0;
	$sql = "SELECT `currency` FROM `pay_record` WHERE `trip`= ? AND `waived` = 0 GROUP BY `currency` ORDER BY `currency` ASC";
	$stmt = $mysqli->prepare($sql);
	$stmt->bind_param('i',$_GET['id']);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($currencyFetch);
	while($stmt->fetch())
	{
		$currencyName[$currencyFetch] = $currency_num;
		$currencyId[$currency_num] = $currencyFetch;
		$currency_num++;
	}
		

	$sql = "SELECT `id`, `time`, `description`, `currency`, `waived` FROM `pay_record` WHERE `trip` = ?";
	$stmt = $mysqli->prepare($sql);
	$stmt->bind_param('i',$_GET['id']);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($pay_record_id, $time, $description, $currency, $waived);
	while($stmt->fetch()){
		echo "<tr>";
		echo "<td>".date("d/m H:i", $time)."<br>(".$currency.")</td>";
		
		echo "<td>";
		if($waived)
			echo "<strike>";
		
		echo $description;
		
		if($waived)
			echo "</strike>";
		echo"</td>";
		
		//payer
		//echo "<td>";
		$sum=0;
		$sql1 = "SELECT `payer`, `amount` FROM `payer` WHERE `pay_record` = ?";
		$stmt1 = $mysqli->prepare($sql1);
		$stmt1->bind_param('i',$pay_record_id);
		$stmt1->execute();
		$stmt1->store_result();
		$stmt1->bind_result($payer, $amount);
		while($stmt1->fetch()){
			//echo $payer.", ".$amount."<br>";
			$sum+=$amount;
			
			$display_amount[ $participant[$payer] ] += $amount;
		}
		//echo " ;sum=".$sum;
		
		//payee
		$num_of_payee=0;
		$sql1 = "SELECT `payee` FROM `pay_for` WHERE `pay_record` = ?";
		$stmt1 = $mysqli->prepare($sql1);
		$stmt1->bind_param('i',$pay_record_id);
		$stmt1->execute();
		$stmt1->store_result();
		$stmt1->bind_result($payee);
		while($stmt1->fetch()){
			//echo $payee.";";
			$num_of_payee++;
		}
		//echo " ;num_of_payee=".$num_of_payee;
		
		$avg_amount = $sum / $num_of_payee;
		
		//echo " ;avg_amount=".$avg_amount;
		
		//echo "</td>";
		
		
		
		$sql = "SELECT trip_participant.person_id , people.name FROM trip_participant, people WHERE people.person_id = trip_participant.person_id AND trip_participant.trip_id = ? ORDER BY trip_participant.trip_id ASC";
		$stmt1 = $mysqli->prepare($sql1);
		$stmt1->bind_param('i',$pay_record_id);
		$stmt1->execute();
		$stmt1->store_result();
		$stmt1->bind_result($payee);
		while($stmt1->fetch()){
			$display_amount[ $participant[$payee] ] -= $avg_amount;
			
		}
		
		for($x = 0; $x < $sn; $x++)
		{
			echo "<td>";
			
			if($display_amount[ $x ] != 0 || isset($display_amount[ $x ]))
			{
				if(!$waived)
					$sum_of_each[ $currencyName[$currency] ][ $x ] += $display_amount[ $x ];
							
				if($waived)
					echo "<strike>";
			
				if($display_amount[ $x ] >=0)
					echo "<font color=\"green\">".number_format($display_amount[ $x ], 2, '.', '')."</font>";
				else
					echo "<font color=\"red\">".number_format(-$display_amount[ $x ], 2, '.', '')."</font>";
							
				if($waived)
					echo "</strike>";
			}
			echo "</td>";
		}
			
		
		echo "<td>";
			if($waived)
				echo "Waived";
		echo "</td>";
		
		
		echo "<td>";
		echo "<a href=\"./record_details.php?id=".$pay_record_id."\">Details</a><br><br>";
		if(!$waived)
			echo "<a href=\"./trip.php?id=".$_GET['id']."&waive=".$pay_record_id."\">Waive this record</a>";
		else
			echo "<a href=\"./trip.php?id=".$_GET['id']."&unwaive=".$pay_record_id."\">Unwaive this record</a>";
		echo "</td>";
		
		
		echo "</tr>";
		
		unset($display_amount);
	}

	echo "<tr>";
	echo "<td></td>";
	echo "<td align=\"right\"><b>Total: ";
	for($y = 0; $y < $currency_num; $y++)
	{
		echo $currencyId[$y];
		echo "<br>";
	}

	echo "</b></td>";

	for($x = 0; $x < $sn; $x++)
	{
		echo "<td>";
		
		//echo $currency_num;
		for($y = 0; $y < $currency_num; $y++)
		{
			if($sum_of_each[ $y ][$x] >=0)
					echo "<font color=\"green\">".number_format($sum_of_each[ $y ][$x], 2, '.', '')."</font><br>";
				else
					echo "<font color=\"red\">".number_format(-$sum_of_each[ $y ][$x], 2, '.', '')."</font><br>";
		}
		
		
		echo "</td>";
	}

	echo "<td>";

	echo "</td>";
	echo "<td>";
	echo "</td>";
	echo "</tr>";


	echo "</table>";
}
require_once("./footer.php");
?>
