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
		// creo il db
		$client -> dbCreate($db_name, PhpOrient::STORAGE_TYPE_MEMORY, PhpOrient::DATABASE_TYPE_GRAPH);
		echo $client -> dbOpen($db_name, $_SESSION['user'], $_SESSION['passwd']);
		
		// imposto gli archi come non leggeri (commento perchè fa impallare tutto)
		//$client -> command('ALTER DATABASE custom useLightweightEdges=false');
		
		// Classe Pathway -------------------------------------------------------------------
		
		// link pathway: http://www.kegg.jp/dbget-bin/www_bget?pathway+hsa00010
		$client -> command('CREATE CLASS Pathway extends V');
		$client -> command('CREATE PROPERTY Pathway.id STRING');
		// codice pathway
		$client -> command('CREATE PROPERTY Pathway.title STRING');
		// Titolo della Pathway
		$client -> command('CREATE PROPERTY Pathway.image STRING');
		// immagine pathway
		$client -> command('CREATE PROPERTY Pathway.link STRING');
		// url file kegg originale
		
		// Classe Gene -----------------------------------------------------------------------
		
		// link gene: http://www.kegg.jp/dbget-bin/www_bget?hsa:130589
		
		$client -> command('CREATE CLASS Gene extends V');
		// questi campi li recuperiamo dalla pathway entry
		$client -> command('CREATE PROPERTY Gene.pathway_id INTEGER');
		// pathway id
		$client -> command('CREATE PROPERTY Gene.kegg_id STRING');
		// kegg id di quel gene
		$client -> command('CREATE PROPERTY Gene.link STRING');
		// link kegg
		// (non stiamo riportando qui le informazioni su come disegnare gli elementi)
		$client -> command('CREATE PROPERTY Gene.name STRING');
		// link kegg
		// questi campi li prendiamo dal gene entry
		$client -> command('CREATE PROPERTY Gene.orthology_id INTEGER');
		// orthology id
		// dobbiamo chiedere cosa importare (se tutte le proprietà, se anche le proprietà da altri db in questa fase, o possiamo escludere qualcosa)
		session_destroy();	
	}
	catch (Exception $e) 
	{
		echo 'Caught exception: ',  $e->getMessage(), "\n";
	}
?>