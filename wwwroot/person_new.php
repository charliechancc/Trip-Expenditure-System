<?PHP
define('SysConst', TRUE);
require_once("./config.php");
require_once("./header.php");

if(isset($_POST['name']))
{
	$sql = "INSERT INTO `people` (`name`) VALUES (?)";
	$stmt = $mysqli->prepare($sql);
	$stmt->bind_param('s', $_POST['name']);
	$stmt->execute();
	$stmt->store_result();
	$stmt->close();
	
	header('Location: https://charliechan.cc/trip/trip_new.php');
}
else
{
	?>

	<form method="post">
	<table class="table">
	
	<tr>
		<td>Name:</td>
		<td><input class="form-control" type="text" name="name" required></td>
	</tr>
	</table>
	<input class="btn btn-primary"  type="submit">
	</form>

<?PHP
}
require_once("./footer.php");
?>