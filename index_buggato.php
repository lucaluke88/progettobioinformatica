<?php
	require "vendor/autoload.php";
	use PhpOrient\PhpOrient; // i namespace vanno usati nello scope più esterno altrimenti danno errore!
?>
<html>
	<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>DB Unificato per Pathways - UNICT (Costantino-Ramo)</title>
		<link rel="stylesheet" href="stile.css"/>
		
	</head>
	<body>
	
		<?php
		try 
		{
			// inizializzazione
			$client = new PhpOrient( 'localhost', 2424 );
			$client->connect( 'root', 'root' );
			$db_name = 'UnifiedPathwayDB';
			//
			if(!($client->dbExists($db_name,PhpOrient::DATABASE_TYPE_GRAPH)))
				{
					include('functions/create_database.php');
				}
			else 
			{
				
				$client->dbOpen($db_name, 'root', 'root' );
				echo $client->dbList();
				
				if (isset($_POST['action']) && (isset($_POST['action']=="submit") {
					include ('functions/database.php');
				}
				elseif (isset($_POST['action']) && (isset($_POST['action']=="reset") {
					$client->dbDrop($db_name, 'root', 'root' );
				}
				elseif (isset($_POST['action']) && (isset($_POST['action']=="dblist") {
				echo $dblist=$client->dbList();
				}
				
			}
			*/
		}
		catch (Exeption $e)
		{
			echo 'Caught exception: ',  $e->getMessage(), "\n";
		}
			
		?>
			
	</body>
</html>