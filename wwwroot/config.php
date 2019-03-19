<?PHP
if(!defined('SysConst')) {
   die('Direct access not permitted');
}
error_reporting(0);

//change MySQL Account here:
$DBHost = "127.0.0.1";
$DBUser = "tripExpenditure";
$DBName = "username";
$DBPass = "password";

//Change Currenry Here (ISO 4217 Standard)
$currencyArray = array("HKD", "JPY", "CNY");

//force HTTPS (comment out if not using it)
if(!isset($_SERVER["HTTPS"]) || $_SERVER["HTTPS"] != "on" )
{
    header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
    exit;
}






//Connect to MySQL
$mysqli = new mysqli($DBHost, $DBUser, $DBPass, $DBName);
$mysqli->query("SET NAMES utf8"); 

//Start session cookies
session_start();

header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
?>