<?php

// IMPORTANT! RENAME OR COPY THIS FILE AS "functions.php" THEN ADD YOUR PERSONAL USERNAME, PASSWORD, ETC. TO IT. THIS IS A DUMMY FILE TO SHARE ON GITHUB, WITH NO PRIVATE INFORMATION IN IT.

if (!isset($thispath)) {
	ob_start();
	session_start();

	//set timezone -- you want to change this if you're not in US Eastern Time
	date_default_timezone_set('America/New_York');
	
	/*
	If you're just hosting this on your own computer using a web server not exposed to the internet, like MAMP or WAMP, these settings are probably fine:
	
	//database credentials
	define('DBHOST','localhost:8889');
	define('DBUSER','root');
	define('DBPASS','root');
	define('DBNAME','cbp_data');

	*/

	//database credentials
	define('DBHOST','YOUR HOSTNAME - MAY BE localhost');
	define('DBUSER','YOUR USERNAME');
	define('DBPASS','YOUR PASSWORD');
	define('DBNAME','YOUR DATABASE\'s NAME');

	//application address - this stuff is optional
	//	define('DIR','THE DIRECTORY WHERE THESE FILES LIVE');
	//	define('SITEEMAIL','YOUR EMAIL ADDRESS, THIS ISN\'T REALLY NECESSARY');

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
