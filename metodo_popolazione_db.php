<?php

/**
 * MITHrIL: miRNA enriched pathway impact analysis
 * REST Web Service
 *
 * @author Illuminato Luca Costantino - Daniela Ramo
 */

error_reporting(E_ERROR);
ini_set('display_errors', '1');
require_once('autoload.php');

/*
 *	ANNOTAZIONI 
 *  
 * 
 */
 
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
        'pathways' => [$pathway]
    ]);
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


function createEntry($pathway, $id, $name, $type, $link, $graph_child)
	{
		
		
		return new \Mithril\Pathway\Entry\Entry([
		        'id'        => $id,
		        'aliases'   => $name,
		        'name'      => $type . $id, // qui va inserito il secondo pezzo rest.kegg.jp/list/hsa
		        'type'      => $type,
		        'links'     => $link,
		        'contained' => [
		            new \Mithril\Pathway\Contained\Pathway([
		                'pathway' => $pathway,
		                'graphic' => new \Mithril\Pathway\Graphic([
		                    'name'    => $graph_child -> name, // name contenuto in graph_child
		                    'x'       => $graph_child -> x,
		                    'y'       => $graph_child -> y,
		                    'coords'  => $graph_child -> x.','.$graph_child -> y, // oggetto coords (se esiste, lo troviamo nel xml della pathway)
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

 /*
 * 	INIZIALIZZAZIONE REPOSITORY ======================================================================
 */ 

$entryRepo = new \Mithril\Pathway\Repository\Entry\Entry();
$entryTypeRepo = new Mithril\Pathway\Repository\Entry\Type();  
$relationTypeRepo = new \Mithril\Pathway\Repository\Relation\Type();
$relationSubTypeRepo = new \Mithril\Pathway\Repository\Relation\SubType();
$relationRepo = new \Mithril\Pathway\Repository\Relation\Relation();
$pathwayRepo = new \Mithril\Pathway\Repository\Pathway();                   
$entryRepo->setRelationsRepository($relationRepo);
$pathwayRepo->setRelationsRepository($relationRepo)->setEntriesRepository($entryRepo);
$geneEntryType = new \Mithril\Pathway\Entry\Type(['name' => 'gene']);
$orthologEntryType = new \Mithril\Pathway\Entry\Type(['name' => 'ortholog']);
$enzymeEntryType = new \Mithril\Pathway\Entry\Type(['name' => 'enzyme']);
$groupEntryType = new \Mithril\Pathway\Entry\Type(['name' => 'group']);
$mapEntryType = new \Mithril\Pathway\Entry\Type(['name' => 'map']);

$entryTypeRepo->add($geneEntryType);
$entryTypeRepo->add($orthologEntryType);
$entryTypeRepo->add($enzymeEntryType);
$entryTypeRepo->add($groupEntryType);
$entryTypeRepo->add($mapEntryType);

$maplinkRelationType = new \Mithril\Pathway\Relation\Type(['name' => 'maplink']);
$ecrellinkRelationType = new \Mithril\Pathway\Relation\Type(['name' => 'ECrel']);
$pprellinkRelationType = new \Mithril\Pathway\Relation\Type(['name' => 'PPrel']);
$gerellinkRelationType = new \Mithril\Pathway\Relation\Type(['name' => 'GErel']);
$pcrellinkRelationType = new \Mithril\Pathway\Relation\Type(['name' => 'PCrel']);

$relationTypeRepo->add($maplinkRelationType);
$relationTypeRepo->add($ecrellinkRelationType);
$relationTypeRepo->add($pprellinkRelationType);
$relationTypeRepo->add($gerellinkRelationType);
$relationTypeRepo->add($pcrellinkRelationType);

$pathwaylist = explode("\n", file_get_contents('http://rest.kegg.jp/list/pathway/hsa'));
$cont = 1;
foreach ($pathwaylist as $p) 
{
	if ($cont<=2) // due sole iterazioni
	{
		echo "</br>"."Iterazione ".$cont." </br>";	
		$url = ("http://rest.kegg.jp/get/path:".substr($p, 5, 8)."/kgml");
		$xmlstring = file_get_contents($url); // il contenuto della pagina inserito in questa variabile
		$xml_pathway_obj = simplexml_load_string($xmlstring); // oggetto xml di tutta la pathway
		
		if ($xml_pathway_obj -> getName() == "pathway")
		{
			// root: informazioni e tag della pathway
			echo "Nome pathway: ".$xml_pathway_obj['name']."</br>";
			
			$mypathway = createPathway($xml_pathway_obj['name'], $xml_pathway_obj['org'], $xml_pathway_obj['title'], $xml_pathway_obj['image'], $xml_pathway_obj['link']);
			print_r($mypathway);
			echo "</br>";
			
			$pathwayRepo->add($mypathway);
			
	        foreach ($xml_pathway_obj->children() as $xml_item)
			{
				if ($xml_item -> getName() == "entry")
				{
					foreach ($xml_item->children() as $graph_child) 
					{
						$aliases_list = explode(" ", $xml_item['name']);
						foreach ($aliases_list as $alias) 
						{
							$entry_id = substr($alias, 4);
							$entry = createEntry($xml_pathway_obj['name'],$entry_id,$xml_item['name'],$xml_item['type'],$xml_item['link'],$graph_child);
			            	$entryRepo->add($entry);
						}
					}
				}
				else if ($xml_item -> getName() == "relation")
				{
					/*
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
					 */
				}
				//$allEntries = $path->getEntries();
			} 
		} 
	}
	$cont = $cont + 1;
}

if(is_null($pathwayRepo->getEntries()))
{
	echo "</br>";
	echo "Non ci sono pathway";
}
else 
{
	
	echo "Repository aggiunti";
}
 



// dopo di questo, dobbiamo aggiungere altri tipi e altre entità
//aggiungere interazioni microRNA-geni (sempre umane)
// microrna gene targets (ci interessano solo queste validate)
// abbiamo a disposizione l'id di entrez, che abbiamo anche in kegg nel formato hsa:id_entrez
// MIMAT collegati al gene (interazioni di tipo gene)
// relation type: mgrel
// relation subtype: ~inibition
// sito mirwalk tabella
// sito mirtarbase excel
// mappare il nome del maturo con l'entry del db mirbase

// aggiungere entry "microrna"
