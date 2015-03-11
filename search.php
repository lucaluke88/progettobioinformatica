<?php
	include('PhpOrient.php');
	$query = $_POST['query'];
	echo "Hai scritto ".$query;
	
	echo "<a href='index.php'>Torna indietro</a>";
	
	// connessione al DB
	$client = new PhpOrient('localhost', 2424);
	$db_username = "root";
	$db_password = "061288";
	$db_data = $client->dbOpen('UnifiedPathwayDB', $db_username, $db_password);
	
	//if(isset($db_data)) echo "connessione riuscita!";
	//else echo "connessione al db non riuscita!";
	
	
	
	
?>