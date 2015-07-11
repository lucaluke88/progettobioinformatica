<?php

/**
 * MITHrIL: miRNA enriched pathway impact analysis
 * REST Web Service
 *
 * @author Illuminato Luca Costantino - Daniela Ramo
 */

error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('max_execution_time', 300);
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
	        'image'    => $image,
	        'link' => $link,
	    ]);
		
	}

function createEntry($pathway, $id, $name, $type, $link, $graph_child)
	{
		$typename = print_r($type->name,TRUE);
		return new \Mithril\Pathway\Entry\Entry([
					        'id'        => $id,
					        'aliases'   => $name,
					        'name'      => $typename . $id, // qui va inserito il secondo pezzo rest.kegg.jp/list/hsa
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

 // creazione repo
$entryRepo = new \Mithril\Pathway\Repository\Entry\Entry();
$entryTypeRepo = new Mithril\Pathway\Repository\Entry\Type();  
$relationTypeRepo = new \Mithril\Pathway\Repository\Relation\Type();
$relationSubTypeRepo = new \Mithril\Pathway\Repository\Relation\SubType();
$relationRepo = new \Mithril\Pathway\Repository\Relation\Relation();
$pathwayRepo = new \Mithril\Pathway\Repository\Pathway();                   
$entryRepo->setRelationsRepository($relationRepo);
$pathwayRepo->setRelationsRepository($relationRepo)->setEntriesRepository($entryRepo);

// creazione tipi per le entry
$geneEntryType = new \Mithril\Pathway\Entry\Type(['name' => 'gene']);
$mirnaEntryType = new \Mithril\Pathway\Entry\Type(['name' => 'mirna']); // entry MIRNA
$orthologEntryType = new \Mithril\Pathway\Entry\Type(['name' => 'ortholog']);
$enzymeEntryType = new \Mithril\Pathway\Entry\Type(['name' => 'enzyme']);
$groupEntryType = new \Mithril\Pathway\Entry\Type(['name' => 'group']);
$compoundEntryType = new \Mithril\Pathway\Entry\Type(['name' => 'compound']);
$mapEntryType = new \Mithril\Pathway\Entry\Type(['name' => 'map']);

// aggiunta tipi al repo ai tipi
$entryTypeRepo->add($geneEntryType);
$entryTypeRepo->add($orthologEntryType);
$entryTypeRepo->add($enzymeEntryType);
$entryTypeRepo->add($groupEntryType);
$entryTypeRepo->add($mapEntryType);
$entryTypeRepo->add($mirnaEntryType); // mirna
$entryTypeRepo->add($compoundEntryType);

// creazione tipi relazioni
$maplinkRelationType = new \Mithril\Pathway\Relation\Type(['name' => 'maplink']);
$ecrellinkRelationType = new \Mithril\Pathway\Relation\Type(['name' => 'ECrel']);
$pprellinkRelationType = new \Mithril\Pathway\Relation\Type(['name' => 'PPrel']);
$gerellinkRelationType = new \Mithril\Pathway\Relation\Type(['name' => 'GErel']);
$pcrellinkRelationType = new \Mithril\Pathway\Relation\Type(['name' => 'PCrel']);
$mgrellinkRelationType = new \Mithril\Pathway\Relation\Type(['name' => 'MGrel']);
$mirnaRelationType = new \Mithril\Pathway\Relation\Type(['name' => 'mirna']);

// aggiunta tipi relazione al repo dei tipi
$relationTypeRepo->add($maplinkRelationType);
$relationTypeRepo->add($ecrellinkRelationType);
$relationTypeRepo->add($pprellinkRelationType);
$relationTypeRepo->add($gerellinkRelationType);
$relationTypeRepo->add($pcrellinkRelationType);
$relationTypeRepo->add($mirnaRelationType);

// nuova subtype relation mirna->gene target
$mirnaRelationSubType = new \Mithril\Pathway\Relation\SubType(['name' => 'inhibition', 'value' => 0]);
$relationSubTypeRepo->add($mirnaRelationSubType);

echo "Lettura pathway hsa da KEGG...</br>";
$pathwaylist = explode("\n", file_get_contents('http://rest.kegg.jp/list/pathway/hsa'));

foreach ($pathwaylist as $p) 
{
		echo "Lettura pathway ".$cont." </br>";
		$url = ("http://rest.kegg.jp/get/path:".substr($p, 5, 8)."/kgml");
		$xmlstring = file_get_contents($url); // il contenuto della pagina inserito in questa variabile
		$xml_pathway_obj = simplexml_load_string($xmlstring);
		
		if ($xml_pathway_obj -> getName() == "pathway")
		{
			 // casting da XMLObject a string
			$mypathway = createPathway(substr($p, 5, 8), (string) $xml_pathway_obj['org'], (string) $xml_pathway_obj['title'], (string) $xml_pathway_obj['image'], (string) $xml_pathway_obj['link']);
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
							$entry_id_split = explode(':', $alias);
							$entry_id = $entry_id_split[1];
							$typeaux = (string) $xml_item['type'][0]; // casting da XMLObject a string
							$type = $entryTypeRepo->get($typeaux);
							$entry = createEntry($mypathway,$entry_id,$alias,$type, $xml_item['link'][0],$graph_child); //entry di tipo gene
				            $entryRepo->add($entry);
				            $coppie_id_xml_id_db["".$xml_item['id']] = $entry;
				            $entrezid_to_mithrilid[$entry_id] = $entry;
							if(!(isset($pathways_for_this_gene[$entry_id])))
								$pathways_for_this_gene[$entry_id] = array();
							array_push($pathways_for_this_gene[$entry_id],$mypathway); // aggiungiamo la pathway
						}
					}
				}
				else if ($xml_item -> getName() == "relation")
				{
					$entry1 = $coppie_id_xml_id_db["".$xml_item['entry1']];
					$entry2 = $coppie_id_xml_id_db["".$xml_item['entry2']];
					
					foreach ($xml_item->children() as $subtype_entry)
					{
						$name = (string) $subtype_entry['name'];  // casting da XMLObject a string
						$type = (string) $xml_item['type'];  // casting da XMLObject a string
						$relation = createRelation($entry1, $entry2, $relationTypeRepo->get($type),
			                $relationSubTypeRepo->get($name), $mypathway);
						$relationRepo->add($relation);
						// qui mi indica come creare una relation subtype (mi serve per l'inibizione)
						$value = (string) $subtype_entry['value'];  // casting da XMLObject a string
						$compoundRelationSubType = new \Mithril\Pathway\Relation\SubType(['name' => $name, 'value' => $value]);
						$relationSubTypeRepo->add($compoundRelationSubType);
					}
				}
				
			} 
		}
}


echo "Inseriti elementi dalla lettura delle pathway</br>";
echo "Numero Entry: ".$entryRepo->count()."</br>";
echo "Numeri Tipi: ".$entryTypeRepo->count()."</br>";
echo "Numero relazioni: ".$relationRepo->count()."</br>";
echo "Numero tipi relazione: ".$relationTypeRepo->count()."</br>";
echo "Numero pathway lette: ".$pathwayRepo->count()."</br></br>";
echo "----------------------</br>";

echo "Lettura dati MiRna da Mirtarbase...</br>";

require_once 'excel_reader2.php';

file_put_contents("mirtarbase/hsa_MTI.xls", fopen("http://mirtarbase.mbc.nctu.edu.tw/cache/download/4.5/hsa_MTI.xls", 'r'));
$data = new Spreadsheet_Excel_Reader("mirtarbase/hsa_MTI.xls",false);

$nIterazioni = $data->rowcount($sheet_index=0); // leggiamo tutto il file, commentare se vogliamo limitare le letture

// pathway e graph_child dummy per i MIRNA
$dummyPathway = createPathway("mirna_path", "hsa", "mirna", "image", "link");
$dummyGraphChild = new \Mithril\Pathway\Graphic([
					                    'name'    => 'mirna_graph_name', // name contenuto in graph_child
					                    'x'       => '-1',
					                    'y'       => '-1',
					                    'coords'  => "-1,-1", // oggetto coords (se esiste, lo troviamo nel xml della pathway)
					                    'type'    => "mirna",
					                    'width'   => "0",
					                    'height'  => "0",
					                    'fgcolor' => "0",
					                    'bgcolor' => "0"
					                ]);

$nMirnaEntry = 1;
for ($i=2; $i < $nIterazioni+1 ; $i++) // gli elementi partono dalla seconda riga
{
	   echo "Iterazione ".$i."</br>";
	   $mirna_id = $data->val($i,2); // id e nome mirna vengono letti dalla seconda colonna del file xls
	   
		if(isset($entrezid_to_mithrilid[$data->val($i,5)])) // se il target del mirna è presente in mithril
		{
			$target_entry = $entrezid_to_mithrilid[$data->val($i,5)]; // la colonna 5 ha l'id entrez del nostro gene 
			// creiamo la relazione
			$pathways_for_this_target = $pathways_for_this_gene[$data->val($i,5)];  //tutte le pathway per questa entry (usando l'ID)
			echo "Il gene target del miRNA è presente in ".count($pathways_for_this_target)." pathway. Inserimento.</br>";
			$mirna_entry= createEntry($pathways_for_this_target,$mirna_id,$mirna_id,"mirna","NULL",$dummyGraphChild);
			$entryRepo->add($mirna_entry);
			$nMirnaEntry = $nMirnaEntry + 1;
			$relation = createRelation($mirna_entry, $target_entry, $mirnaRelationType,$mirnaRelationSubType, NULL);
			$relationRepo->add($relation);
		}
		else 
		{
			// Mirna il cui target non è censito dal sistema, inserimento con dati dummy
			echo "inserimento mirna con pathway e graphic object dummy (target non presente in mithril)</br>";
			$type = $entryTypeRepo->get("mirna");
			$mirna_entry= createEntry($dummyPathway,$data->val($i,2),$data->val($i,2),$type,"NULL",$dummyGraphChild);
			$entryRepo->add($mirna_entry);
			$nMirnaEntry = $nMirnaEntry + 1;
			// non possiamo creare relazioni, non avendo il target nel sistema
		}
}

echo "Letti ".$nMirnaEntry." mirna entry di Mirwalk</br>";
echo "Numero Entry Totale: ".$entryRepo->count()."</br>";
echo "Numeri Tipi Totale: ".$entryTypeRepo->count()."</br>";
echo "Numero relazioni Totale: ".$relationRepo->count()."</br>";
echo "Numero tipi relazione Totale: ".$relationTypeRepo->count()."</br>";
echo "Numero pathway lette Totale: ".$pathwayRepo->count()."</br></br>";
echo "----------------------</br>";


// SCRITTURA SU FILE DEI RISULTATI

echo "Scrittura su file dei risultati... </br>";

$entriesTypeWriter = new \Mithril\Data\RepositoryWriter($entryTypeRepo, '\Mithril\Pathway\Writer\Entry\Type');
$entriesTypeWriter->writeAndSave("tmp/entries.types.txt");
$relationTypeWriter = new \Mithril\Data\RepositoryWriter($relationTypeRepo, '\Mithril\Pathway\Writer\Relation\Type');
$relationTypeWriter->writeAndSave("tmp/relation.types.txt");
$relationSubTypeWriter = new \Mithril\Data\RepositoryWriter($relationSubTypeRepo, '\Mithril\Pathway\Writer\Relation\SubType');
$relationSubTypeWriter->writeAndSave("tmp/relation.subtypes.txt");
$pathwaysWriter = new \Mithril\Data\RepositoryWriter($pathwayRepo, '\Mithril\Pathway\Writer\Pathway');
$pathwaysWriter->writeAndSave("tmp/pathways.txt");

$entriesWriter = new \Mithril\Data\RepositoryWriter($entryRepo, '\Mithril\Pathway\Writer\Entry\Entry');
$entriesWriter->writeAndSave("tmp/entries.txt");

$relationsWriter = new \Mithril\Data\RepositoryWriter($relationRepo, '\Mithril\Pathway\Writer\Relation\Relation');
$relationsWriter->writeAndSave("tmp/relations.txt");
$endpointsContainer = new \Mithril\Pathway\Endpoints($pathwayRepo);
$endpointsWriter = new \Mithril\Pathway\Writer\Endpoints($endpointsContainer);
$endpointsWriter->writeAndSave("tmp/endpoints.txt");
$startingPointsContainer = new \Mithril\Pathway\StartingPoints($pathwayRepo);
$startingPointsWriter = new \Mithril\Pathway\Writer\StartingPoints($startingPointsContainer);
$startingPointsWriter->writeAndSave("tmp/startpoints.txt");
echo "Scrittura su file effettuata!</br>";
