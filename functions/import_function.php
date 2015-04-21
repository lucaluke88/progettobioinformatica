<?php
    require_once "../vendor/autoload.php"; // Composer
	// i namespace vanno usati nello scope più esterno altrimenti danno errore!
	use PhpOrient\PhpOrient;
	session_start();
	
	try {
		$xmlstring = file_get_contents("../file/hsa00010.xml");	
		$xmlobj = simplexml_load_string($xmlstring);
		//print_r($xmlobj);
		
		//nodi di primo livello: la pathway
		foreach($xmlobj->children() as $xmlstring) {
			echo $xmlstring->name . ", "; 
    		echo $xmlstring->number . ", "; 
    		echo $xmlstring->title . ", ";
    		echo $xmlstring->image . "<br>"; 
			echo $xmlstring->link . "<br>"; 
		}
	
		$client = new PhpOrient();
		$client->hostname = $_SESSION['hostname'];
		$client->port     = $_SESSION['port'];
		$client->username = $_SESSION['user'];
		$client->password = $_SESSION['passwd'];
		$db_name = $_SESSION['dbname'];
		
		$client -> dbOpen($db_name, $_SESSION['user'], $_SESSION['passwd']);
		$client -> connect();
		
		
	}
	
	catch (Exception $e) 
	{
		echo 'Caught exception: ',  $e->getMessage(), "\n";
	}
?>

