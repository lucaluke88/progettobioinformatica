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
	
	//<name="path:hsa00010" 
	// org="hsa" 
	// number="00010" 
	// title="Glycolysis / Gluconeogenesis" 
	// image="http://www.kegg.jp/kegg/pathway/hsa/hsa00010.png" 
	// link="http://www.kegg.jp/kegg-bin/show_pathway?hsa00010">
	
	$client -> command('create class Pathway extends V');
	$client -> command('create property Pathway.org string');
	$client -> command('create property Pathway.number string');
	$client -> command('create property Pathway.title string');
	$client -> command('create property Pathway.image string');
	$client -> command('create property Pathway.link string');
?>