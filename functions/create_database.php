<?php
	require "./vendor/autoload.php";
	use PhpOrient\PhpOrient;
	// i namespace vanno usati nello scope piÃ¹ esterno altrimenti danno errore!
	$db_name = 'UnifiedPathwayDB';
	$new_cluster_id = $client -> dbCreate($db_name, PhpOrient::STORAGE_TYPE_MEMORY, # optional, default: STORAGE_TYPE_PLOCAL
	PhpOrient::DATABASE_TYPE_GRAPH # optional, default: DATABASE_TYPE_GRAPH
	);
	$ClusterMap = $client -> dbOpen($db_name, 'root', 'root');
	
	// PATHWAY
	
	//<name="path:hsa00010" 
	// org="hsa" 
	// number="00010" 
	// title="Glycolysis / Gluconeogenesis" 
	// image="http://www.kegg.jp/kegg/pathway/hsa/hsa00010.png" 
	// link="http://www.kegg.jp/kegg-bin/show_pathway?hsa00010">
	
	// Classe Pathway
	$client -> command('create class Pathway extends V');
	$client -> command('create property Pathway.number string'); // codice pathway
	$client -> command('create property Pathway.title string'); // Titolo della Pathway
	$client -> command('create property Pathway.image string'); // immagine pathway
	$client -> command('create property Pathway.link string'); // url file kegg originale
	
	// Classe Gene
	
		// le proprietà vanno prese da NCBI
		// in pratica, noi importiamo da NCBI solo i geni richiesti da qualche pathway di KEGG
		
	
	// Classe Enzyme
	
	
?>