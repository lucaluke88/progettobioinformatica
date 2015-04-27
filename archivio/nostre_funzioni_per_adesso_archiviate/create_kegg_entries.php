<?php
    error_reporting(E_ALL);
	ini_set('display_errors', '1');
	require_once('autoload_mithril.php');
	
	function repoInit()
	{
		//Costruisco le repository
		$entryTypeRepo = new \Mithril\Pathway\Repository\Entry\Type();              //Repository dei tipi di entry
		$entryRepo = new \Mithril\Pathway\Repository\Entry\Entry();                 //Repository delle entry
		$relationTypeRepo = new \Mithril\Pathway\Repository\Relation\Type();        //Repository dei tipi di relazione
		$relationSubTypeRepo = new \Mithril\Pathway\Repository\Relation\SubType();  //Repository dei sottotipi di relazione
		$relationRepo = new \Mithril\Pathway\Repository\Relation\Relation();        //Repository delle relazioni
		$pathwayRepo = new \Mithril\Pathway\Repository\Pathway();                   //Repository delle pathway
		//Imposto le repository delle entry e delle pathway collegandole alle altre
		$entryRepo->setRelationsRepository($relationRepo);
		$pathwayRepo->setRelationsRepository($relationRepo)->setEntriesRepository($entryRepo);
	}
	
	
	// Costruiamo una pathway (inizialmente vuota)
	function createPathway($id, $org, $title, $image, $link)
	{
	    return new \Mithril\Pathway\Pathway([
	        'id'       => $id,
	        'organism' => $org,
	        'title'    => $title;
	        'image'    => $link;
	    ]);
	}
	
	// Costruiamo un oggetto di tipo "entry" che può essere di tipo Gene o Orthology
	// una volta creato, viene anche creato l'oggetto grafico associato e inserito nelle repository
	function createEntry($pathway, $id, $name, $type, $link, $graph_entry)
	{
		return new \Mithril\Pathway\Entry\Entry([ // crea una nuova entry nell'apposito repo
		        'id'        => $id,
		        'aliases'   => [],
		        'name'      => $type . $id,
		        'type'      => $type,
		        'links'     => $link,
		        'contained' => [
		            new \Mithril\Pathway\Contained\Pathway([
		                'pathway' => $pathway,
		                'graphic' => new \Mithril\Pathway\Graphic([
		                    'name'    => 'Gene ' . $id,
		                    'x'       => $graph_entry -> x,
		                    'y'       => $graph_entry -> y,
		                    'coords'  => $graph_entry -> x.','.$graph_entry -> y, // da rivedere x,y
		                    'type'    => $graph_entry -> type,
		                    'width'   => $graph_entry -> width,
		                    'height'  => $graph_entry -> height,
		                    'fgcolor' => $graph_entry -> fgcolor,
		                    'bgcolor' => $graph_entry -> bgcolor
		                ])
		            ])
		        ]
		    ]);
	}
?>