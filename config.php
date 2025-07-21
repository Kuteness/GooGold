<?php
ob_start();

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";

$host = $_SERVER['HTTP_HOST'];

define('SITE_URL', $protocol . '://' . $host);

$dbname = "database";
$dbhost = "localhost";
$dbuser = "user";
$dbpass = "password";

try 
{
	$con = new PDO("mysql:dbname=$dbname;host=$dbhost", "$dbuser", "$dbpass");
	$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
}
catch(PDOExeption $e) 
{
	echo "Connection failed: " . $e->getMessage();
}
?>
