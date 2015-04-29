<?php

/**
 * MITHrIL: miRNA enriched pathway impact analysis
 * REST Web Service
 *
 * @author Illuminato Luca Costantino - Daniela Ramo
 */

error_reporting(E_ALL);
ini_set('display_errors', '1');

 /*
 * 	INIZIALIZZAZIONE REPOSITORY ======================================================================
 */ 


//Repository delle entry         
$entryRepo = new \Mithril\Pathway\Repository\Entry\Entry();

//Repository dei TIPI di entry
$entryTypeRepo = new Mithril\Pathway\Repository\Entry\Type();  

//Repository dei tipi di relazione             
$relationTypeRepo = new \Mithril\Pathway\Repository\Relation\Type();
//Repository dei sottotipi di relazione   
$relationSubTypeRepo = new \Mithril\Pathway\Repository\Relation\SubType();
//Repository delle relazioni  
$relationRepo = new \Mithril\Pathway\Repository\Relation\Relation();
//Repository delle pathway        
$pathwayRepo = new \Mithril\Pathway\Repository\Pathway();                   
//Imposto le repository delle entry e delle pathway collegandole alle altre
$entryRepo->setRelationsRepository($relationRepo);
$pathwayRepo->setRelationsRepository($relationRepo)->setEntriesRepository($entryRepo);
//costruisco il tipo di entry GENE e il tipo ORTHOLOG
$geneEntryType = new \Mithril\Pathway\Entry\Type(['name' => 'gene']);
$orthologEntryType = new \Mithril\Pathway\Entry\Type(['name' => 'ortholog']);
//e li aggiungi alla repository dei tipi di entry
$entryTypeRepo->add($geneEntryType);
$entryTypeRepo->add($orthologEntryType);
//costruisco i tipi di relazione maplink, ecrel 
$maplinkRelationType = new \Mithril\Pathway\Relation\Type(['name' => 'maplink']);
$ecrellinkRelationType = new \Mithril\Pathway\Relation\Type(['name' => 'ECrel']);
//e li aggiungo alla relativa repository
$relationTypeRepo->add($maplinkRelationType);
$relationTypeRepo->add($ecrellinkRelationType);

/*
 * OPERAZIONI DI LETTURA =============================================================================
 */

// elenco di tutte le pathway umane
// http://rest.kegg.jp/list/pathway/hsa
$pathwaylist = explode("\n", file_get_contents('http://rest.kegg.jp/list/pathway/hsa'));
foreach ($pathwaylist as $p) {
	echo "pathway n".$cont;
	echo "</br>";
	$url = ("http://www.kegg.jp/kegg-bin/download?entry=".substr($p, 5, 8)."&format=kgml");
	// leggo dall'url il mio xml, lo analizzo e per ogni elemento, creo l'entry o la relation più opportuna
	readElementsFromXML($url);
}

 
/*
 *	MIE FUNZIONI ================================================================================================
 */
 
function createRelation($entry1, $entry2, $type, $subType, $pathway)
{
    return new \Mithril\Pathway\Relation\Relation([
        'entry1'   => $entry1,
        'entry2'   => $entry2,
        'type'     => $type,
        'subTypes' => $subType,
        'pathways' => [
            $pathway
        ]
    ]);
}

function readElementsFromXML($url)
{
	$xmlstring = file_get_contents($url); // il contenuto della pagina inserito in questa variabile
	echo $xmlstring;
	$xml_pathway_obj = simplexml_load_string($xmlstring); // oggetto xml di tutta la pathway
	
	
	/* NOMI VARIABILI
	 * $xml_pathway_obj = è il nodo root dell'xml, contiene le info sulla pathway
	 * $xml_item = identifica tutti i nodi di primo livello (cioè entries
	 * e relations)
	 */
	
	
	if ($xml_pathway_obj -> getName() == "pathway")
	{
		// root: informazioni e tag della pathway
		
		$mypathway = createPathway($xml_pathway_obj['name'], $xml_pathway_obj['org'], $xml_pathway_obj['title'], $xml_pathway_obj['image'], $xml_pathway_obj['link']);
		$pathwayRepo->add($mypathway); // con questo comando aggiungiamo la pathway al repo
        
        // guardiamo tutti i figli (che sono gene/orthology o relation) 
        foreach ($xml_pathway_obj->children() as $xml_item)
		{
			if ($xml_item -> getName() == "entry")
			{
				// uso il foreach perchè altrimenti non mi legge il figlio ma fa una sola iterazione
				foreach ($xml_item->children() as $graph_child) 
				{
					/* 	Il tag name contiene gli alias, il nome da memorizzare va preso da
				 		hsa:UN_NUMERO il numero contenuto va posto come id nell'oggetto entry.
				 		Ogni alias è nel formato hsa:ID, dobbiamo creare 3 entry o fare una lista di 3 id?
					*/
				
					// Qui mi ricavo gli N id (gli alias) e creo N entry nel db
					$aliases_list = explode(" ", $xml_item['name']);
					foreach ($aliases_list as $alias) 
					{
						$entry_id = substr($alias, 4);
						$entry = createEntry($xml_pathway_obj['name'],$entry_id,$xml_item['name'],$xml_item['type'],$xml_item['link'],$graph_child);
		            	$entryRepo->add($entry);
					}
					
					//$entry_id = metodo_per_ricavare_id($entry);
					//$entry = createEntry($xml_pathway_obj['name'],$entry_id,$xml_item['name'],$xml_item['type'],$xml_item['link'],$graph_child);
		            //$entryRepo->add($entry);
				}
				
				
			}
			else if ($xml_item -> getName() == "relation")
			{
				foreach ($xml_item->children() as $subtype_entry)
				{
					// leggo i campi di relation dall'xml
					$entry1 = $allEntries[$xml_item['entry1']];
		            $entry2 = $allEntries[$xml_item['entry2']];
					//costruisco la relazione tra le due entry lette
		            $relation = createRelation($entry1, $entry2, $relationTypeRepo->get($xml_item['type']),
		                $relationSubTypeRepo->get($subtype_entry['name']), $path);
		            $relationRepo->add($relation);
					//costruisco un sottotipo di relazione di esempio
					$compoundRelationSubType = new \Mithril\Pathway\Relation\SubType(['name' => $subtype_entry['name'], 'value' => $subtype_entry['value']]);
					$relationSubTypeRepo->add($compoundRelationSubType);
				}
			}
			//$allEntries = $path->getEntries();
		} 
	} 
}

function createPathway($id, $org, $title, $image, $link)
	{
	    return new \Mithril\Pathway\Pathway([
	        'id'       => $id,
	        'organism' => $org,
	        'title'    => $title,
	        'image'    => $link
	    ]);
	}


// $id va ricavato incrociando la ricerca; $name è la lista degli alias del'attributo "name" del tag "entry"
function createEntry($pathway, $id, $name, $type, $link, $graph_child)
	{
		return new \Mithril\Pathway\Entry\Entry([
		        'id'        => $id,
		        'aliases'   => $name,
		        'name'      => $type . $id,
		        'type'      => $type,
		        'links'     => $link,
		        'contained' => [
		            new \Mithril\Pathway\Contained\Pathway([
		                'pathway' => $pathway,
		                'graphic' => new \Mithril\Pathway\Graphic([
		                    'name'    => $type . $id,
		                    'x'       => $graph_child -> x,
		                    'y'       => $graph_child -> y,
		                    'coords'  => $graph_child -> x.','.$graph_child -> y, // coords = x,y
		                    'type'    => $graph_child -> type,
		                    'width'   => $graph_child -> width,
		                    'height'  => $graph_child -> height,
		                    'fgcolor' => $graph_child -> fgcolor,
		                    'bgcolor' => $graph_child -> bgcolor
		                ])
		            ])
		        ]
		    ]);
	}


