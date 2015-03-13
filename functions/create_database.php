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
	
	$client -> command('create class Gene extends V');
	$client -> command('create class Protein extends V');
	$client -> command('create class Interation extends E');
	
?>