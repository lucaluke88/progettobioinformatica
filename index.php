<?php
	ob_start();
	// per il firebug
	require "vendor/autoload.php";
	use PhpOrient\PhpOrient; // i namespace vanno usati nello scope più esterno altrimenti danno errore!
?>
<html>
	<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>DB Unificato per Pathways - UNICT (Costantino-Ramo)</title>
		<link rel="stylesheet" href="stile.css"/>
		<?php
		try 
		{
			// inizializzazione
			$client = new PhpOrient();
			$client->configure( array(
			    'username' => 'root',
			    'password' => '061288',
			    'hostname' => 'localhost',
			    'port'     => 2424,
			) );
			$db_name = 'UnifiedPathwayDB';
			
			$client->connect();
			
			if(!($client->dbExists($db_name,PhpOrient::DATABASE_TYPE_GRAPH)))
			{
				include('functions/create_database.php');
			}
			
			// connettiamoci al db che ora è sicuramente esistente
			$ClusterMap = $client->dbOpen($db_name, 'root', '061288' );
		}
		catch (Exception $e) 
		{
		    echo 'Caught exception: ',  $e->getMessage(), "\n";
		}
		?>
	</head>
		<body>
			<div id="container">
				<h1> Unified Database for Pathway Analysis </h1>
				<div id="searchform"> 
					<form action="#" method="POST">
						<input id="searchbox" type="text" name="query"/>
						</br></br>
						<input type="submit"/> <input type="reset"/>
					</form>
				</div>
				<div id="output">
					<?php
						echo 'Dimensione del db corrente: </br>';
						echo ($client->dbCountRecords());
					?>
				</div>
				<div id="footer">
					Developed by: Illuminato Luca Costantino & Daniela Ramo
				</div>
			</div>
			
		</body>
</html>