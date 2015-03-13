<?php
	ob_start();
	// per il firebug
	require "./vendor/autoload.php";
	use PhpOrient\PhpOrient;
	// i namespace vanno usati nello scope più esterno altrimenti danno errore!
	$db_name = 'UnifiedPathwayDB';
	$new_cluster_id = $client -> dbCreate($db_name, PhpOrient::STORAGE_TYPE_MEMORY, # optional, default: STORAGE_TYPE_PLOCAL
	PhpOrient::DATABASE_TYPE_GRAPH # optional, default: DATABASE_TYPE_GRAPH
	);
	$ClusterMap = $client -> dbOpen($db_name, 'root', '061288');
	
	// PATHWAY
	$client -> command('create class Pathway extends V');
	$client -> command('create property Pathway.title string'); // titolo pathway
	$client -> command('create property Pathway.urlimage string'); // url immagine
	$client -> command('create property Pathway.number string'); // numero
	$client -> command('create property Pathway.link string'); // url pathway
?>