<?php
    require "./vendor/autoload.php"; // Composer
	// i namespace vanno usati nello scope più esterno altrimenti danno errore!
	use PhpOrient\PhpOrient;
	session_start();
	try 
	{
		// inizializzazione connessione
		$client = new PhpOrient();
		echo $client->hostname = $_SESSION['hostname'];
		echo $client->port     = $_SESSION['port'];
		echo $client->username = $_SESSION['user'];
		echo $client->password = $_SESSION['passwd'];
		echo $db_name = $_SESSION['dbname'];
		$client -> connect();
		$client->command($_POST['manual_command']);
	}
	catch (Exception $e) 
	{
		echo 'Caught exception: ',  $e->getMessage(), "\n";
	}
?>