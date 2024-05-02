<?php
ini_set('session.cache_limiter','public');
session_cache_limiter(false);
// ini_set('display_errors', '1');
// error_reporting(E_ALL);
if (!isset($thispath)) {
	ob_start();
	session_start();

	//set timezone
	date_default_timezone_set('America/New_York');

	//database credentials
	define('DBHOST','localhost');
	define('DBUSER','root');
	define('DBPASS','root.');
	define('DBNAME','adamisac_cbpdata');

	//application address
	define('DIR','https://cbpdata.adamisacson.com/');
	define('SITEEMAIL','thisisadamsmail@gmail.com');

	$thispath = "/cbpdata/";
	$sitename = "CBP Data Search";

	try {

		//create PDO connection
		$pdo = new PDO("mysql:host=".DBHOST.";dbname=".DBNAME, DBUSER, DBPASS);
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	} catch(PDOException $e) {
		//show error
	    echo '<p>'.$e->getMessage().'</p>';
	    exit;
	}
}
?>
