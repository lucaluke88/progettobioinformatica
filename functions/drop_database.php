<?php
/**
 * MITHrIL: miRNA enriched pathway impact analysis
 * REST Web Service
 *
 * @author Illuminato Luca Costantino - Daniela Ramo
 */
	// i namespace vanno usati nello scope più esterno altrimenti danno errore!
	use PhpOrient\PhpOrient;
	//session_start();
	// Mi recupero le variabili di sessione
	
	try 
	{
		// inizializzazione connessione
		$client = new PhpOrient();
		$client->hostname = $_SESSION['hostname'];
		$client->port     = $_SESSION['port'];
		$client->username = $_SESSION['user'];
		$client->password = $_SESSION['passwd'];
		$db_name = $_SESSION['dbname'];
		$client -> connect();
		
		if($client->dbExists($db_name,PhpOrient::DATABASE_TYPE_GRAPH))
		{
			$client->dbDrop($db_name,PhpOrient::STORAGE_TYPE_MEMORY);
		}
		else {
			echo "Non ho cancellato nulla perchè il database non esiste";
		}
	}
	catch (Exception $e) 
	{
		echo 'Caught exception: ',  $e->getMessage(), "\n";
	}
?>