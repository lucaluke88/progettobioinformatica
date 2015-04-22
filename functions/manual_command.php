<?php
	/**
	 * MITHrIL: miRNA enriched pathway impact analysis
	 * REST Web Service
	 *
	 * @author Illuminato Luca Costantino - Daniela Ramo
	 */
	require_once "vendor/autoload.php";
	// Composer
	// i namespace vanno usati nello scope più esterno altrimenti danno errore!
	use PhpOrient\PhpOrient;
	session_start();
	try
	{
		echo $_POST['content'];
		// inizializzazione connessione
		$client = new PhpOrient();

		$client -> hostname = $_SESSION['hostname'];
		$client -> port = $_SESSION['port'];
		$client -> username = $_SESSION['user'];
		$client -> password = $_SESSION['passwd'];
		$db_name = $_SESSION['dbname'];
		$client -> dbOpen($db_name, $_username, $_password);
		$client -> connect();

		$client -> command($_POST['manual_command']);
		//echo $_POST['manual_command'];
	}
	catch (Exception $e)
	{
		echo 'Caught exception: ', $e -> getMessage(), "\n";
	}
?>