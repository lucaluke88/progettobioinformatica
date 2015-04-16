<?php
	require "./vendor/autoload.php";
	use PhpOrient\PhpOrient;
	// i namespace vanno usati nello scope più esterno altrimenti danno errore!
	session_start();
	$db_name = $_SESSION['$db_name'];
	// creo il db
	$_SESSION['client'] -> connect();
	$_SESSION['client'] -> dbCreate($db_name, PhpOrient::STORAGE_TYPE_MEMORY,PhpOrient::DATABASE_TYPE_GRAPH);
	$_SESSION['client'] -> dbOpen($db_name, $_SESSION['user'], $_SESSION['passwd']);
	
	// imposto gli archi come non leggeri (commento perchè fa impallare tutto)
	//$_SESSION['client'] -> command('ALTER DATABASE custom useLightweightEdges=false');
	
	// Classe Pathway -------------------------------------------------------------------
	
	// link pathway: http://www.kegg.jp/dbget-bin/www_bget?pathway+hsa00010
	$_SESSION['client'] -> command('CREATE CLASS Pathway extends V');
	$_SESSION['client'] -> command('CREATE PROPERTY Pathway.id STRING'); // codice pathway
	$_SESSION['client'] -> command('CREATE PROPERTY Pathway.title STRING'); // Titolo della Pathway
	$_SESSION['client'] -> command('CREATE PROPERTY Pathway.image STRING'); // immagine pathway
	$_SESSION['client'] -> command('CREATE PROPERTY Pathway.link STRING'); // url file kegg originale
	
	// Classe Gene -----------------------------------------------------------------------
	
	// link gene: http://www.kegg.jp/dbget-bin/www_bget?hsa:130589
	
	$_SESSION['client'] -> command('CREATE CLASS Gene extends V');
	// questi campi li recuperiamo dalla pathway entry
	$_SESSION['client'] -> command('CREATE PROPERTY Gene.pathway_id NUMBER'); // pathway id
	$_SESSION['client'] -> command('CREATE PROPERTY Gene.kegg_id STRING'); // kegg id di quel gene
	$_SESSION['client'] -> command('CREATE PROPERTY Gene.link STRING'); // link kegg
	// (non stiamo riportando qui le informazioni su come disegnare gli elementi)
	$_SESSION['client'] -> command('CREATE PROPERTY Gene.name STRING'); // link kegg
	// questi campi li prendiamo dal gene entry
	$_SESSION['client'] -> command('CREATE PROPERTY Gene.orthology_id NUMBER'); // orthology id
	// dobbiamo chiedere cosa importare (se tutte le proprietà, se anche le proprietà da altri db in questa fase, o possiamo escludere qualcosa)
?>