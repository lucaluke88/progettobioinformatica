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
	        'id'       => 'path:' . $org . '' . $id,
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

function luca_createEntry($pathway, $id, $name, $type, $link, $graph_child)
	{
		$entry = new \Mithril\Pathway\Entry\Entry([]);
		$entry->add('name',$name);
		$entry->add('id',$id);
		$entry->add('type',$id);
		$entry->add('links',$id);
		return $entry;
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
		$xml_pathway_obj = simplexml_load_string($xmlstring);
		
		if ($xml_pathway_obj -> getName() == "pathway")
		{
			$mypathway = createPathway(substr($p, 5, 8), $xml_pathway_obj['org'], $xml_pathway_obj['title'], $xml_pathway_obj['image'], $xml_pathway_obj['link']);
			$pathwayRepo->add($mypathway);
			$stop = FALSE;
	        foreach ($xml_pathway_obj->children() as $xml_item)
			{
				if ($xml_item -> getName() == "entry")
				{
					foreach ($xml_item->children() as $graph_child) // non lo guardareeeeeeeeeeeee
					{
						$aliases_list = explode(" ", $xml_item['name']);
						foreach ($aliases_list as $alias) 
						{
							$entry_id_split = explode(':', $alias);
							$entry_id = $entry_id_split[1];
							// pathway_id_new,entry_id_new,collezione_alias,type,link,oggetto Graphic
							$type = $xml_item['type'][0];
							$entry = createEntry($xml_pathway_obj['name'],$entry_id,$alias,$entryTypeRepo->get($type),$xml_item['link'][0],$graph_child);
			            	
							echo "Name: ";
			            	echo $entry->get('name')."</br>";
							echo "Id (nuovo): ";
							echo $entry->get('id')."</br>";
							echo "Type: ";
							// $entry->get('type') è un oggetto di tipo entryType
							if(is_null($entry->get('type')))
								echo "hai passato un oggetto type null";
							echo "</br>";
							echo "Links: ";
							$link = $entry->get('links');
							echo $link[0]."</br>";
							echo "/////////////////</br>";
			            	$entryRepo->add($entry);
							$coppie_id_xml_id_db[$entry_id] = $entry;
						}
					}
				}
				else if ($xml_item -> getName() == "relation")
				{
					$allEntries = $mypathway->getEntries();
					$entry1 = $allEntries[$coppie_id_xml_id_db[$xml_item['entry1']]];
					if(!($stop))
					{
						if(!(is_object($entry1)))
						{
							echo "Entry 1 non è un oggetto";
							$stop = TRUE;
						}
					}
					
					$entry2 = $allEntries[$coppie_id_xml_id_db[$xml_item['entry2']]];
					foreach ($xml_item->children() as $subtype_entry)
					{
						$relation = createRelation($entry1, $entry2, $relationTypeRepo->get($xml_item['type']),
			                $relationSubTypeRepo->get($subtype_entry['name']), $path);
						
						$relationRepo->add($relation);
						$compoundRelationSubType = new \Mithril\Pathway\Relation\SubType(['name' => $subtype_entry['name'], 'value' => $subtype_entry['value']]);
						$relationSubTypeRepo->add($compoundRelationSubType);
					}
				}
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
	echo "</br>";
	echo $pathwayRepo->count();
	echo " repository aggiunti";
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
