<?php

/**
 * MITHrIL: miRNA enriched pathway impact analysis
 * REST Web Service
 *
 * @author Illuminato Luca Costantino - Daniela Ramo
 */

error_reporting(E_NOTICE);
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
	        'image'    => $link
	    ]);
		
	}

/* MIRNA ENTRY
 * $pathway = NULL (per adesso)
 * $id lo prendiamo da mirtarbase col. 1
 * $name lo prendiamo da mirtarbase col.2
 * $type si passa col get->
 * $link è null
 * $graph_child è null
 */
function createEntry($pathway, $id, $name, $type, $link, $graph_child)
	{
		if(is_null($graph_child)) // MIRNA ENTRY
		{
			echo "entry null</br>";
			return new \Mithril\Pathway\Entry\Entry([
			        'id'        => $id,
			        'aliases'   => $name,
			        'name'      => $type . $id, // qui va inserito il secondo pezzo rest.kegg.jp/list/hsa
			        'type'      => $type,
			        'links'     => $link,
			        'contained' => NULL
			    ]);
			}
			else { // ALTRE ENTRY
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
$mirnaEntryType = new \Mithril\Pathway\Entry\Type(['name' => 'mirna']); // entry MIRNA
$orthologEntryType = new \Mithril\Pathway\Entry\Type(['name' => 'ortholog']);
$enzymeEntryType = new \Mithril\Pathway\Entry\Type(['name' => 'enzyme']);
$groupEntryType = new \Mithril\Pathway\Entry\Type(['name' => 'group']);
$mapEntryType = new \Mithril\Pathway\Entry\Type(['name' => 'map']);


$entryTypeRepo->add($geneEntryType);
$entryTypeRepo->add($orthologEntryType);
$entryTypeRepo->add($enzymeEntryType);
$entryTypeRepo->add($groupEntryType);
$entryTypeRepo->add($mapEntryType);
$entryTypeRepo->add($mirnaEntryType); // mirna

$maplinkRelationType = new \Mithril\Pathway\Relation\Type(['name' => 'maplink']);
$ecrellinkRelationType = new \Mithril\Pathway\Relation\Type(['name' => 'ECrel']);
$pprellinkRelationType = new \Mithril\Pathway\Relation\Type(['name' => 'PPrel']);
$gerellinkRelationType = new \Mithril\Pathway\Relation\Type(['name' => 'GErel']);
$pcrellinkRelationType = new \Mithril\Pathway\Relation\Type(['name' => 'PCrel']);
$mgrellinkRelationType = new \Mithril\Pathway\Relation\Type(['name' => 'MGrel']);
$mirnaRelationType = new \Mithril\Pathway\Relation\Type(['name' => 'mirna']);

$relationTypeRepo->add($maplinkRelationType);
$relationTypeRepo->add($ecrellinkRelationType);
$relationTypeRepo->add($pprellinkRelationType);
$relationTypeRepo->add($gerellinkRelationType);
$relationTypeRepo->add($pcrellinkRelationType);

// nuova subtype relation mirna->gene target
$mirnaRelationSubType = new \Mithril\Pathway\Relation\SubType(['name' => 'inhibition', 'value' => 0]); // la chiamiamo così perchè non è detto che siano tutte di inibizione
$relationSubTypeRepo->add($mirnaRelationSubType);

echo "Lettura pathway da KEGG...</br>";
$pathwaylist = explode("\n", file_get_contents('http://rest.kegg.jp/list/pathway/hsa'));
$cont = 1;

$nIterazioni = 30; // quante pathway vogliamo leggere
foreach ($pathwaylist as $p) 
{
	if ($cont<=$nIterazioni) // levando il commento qui
	{											// e qui possiamo limitare le letture (ai fini di test)
		echo "</br>"."Pathway n".$cont." </br>";
		
		$url = ("http://rest.kegg.jp/get/path:".substr($p, 5, 8)."/kgml");
		$xmlstring = file_get_contents($url); // il contenuto della pagina inserito in questa variabile
		$xml_pathway_obj = simplexml_load_string($xmlstring);
		
		if ($xml_pathway_obj -> getName() == "pathway")
		{
			$mypathway = createPathway(substr($p, 5, 8), $xml_pathway_obj['org'], $xml_pathway_obj['title'], $xml_pathway_obj['image'], $xml_pathway_obj['link']);
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
							$type = $xml_item['type'][0];
							$entry = createEntry($xml_pathway_obj['name'],$entry_id,$alias,$entryTypeRepo->get($type),$xml_item['link'][0],$graph_child); //entry di tipo gene
				            $entryRepo->add($entry); // entryRepo aggiornato correttamente
				            $coppie_id_xml_id_db["".$xml_item['id']] = $entry; // viene correttamente creato l'array associativo
				            $entrezid_to_mithrilid[$entry_id] = $entry; // cerca i target col mirna
				            
				            echo "Entry ID: ".$entry->id."</br>";
				            if (is_null($pathwayfinderbygene[$entry] )) 
				            {
				            	echo "creazione </br>";
								$pathwayfinderbygene[$entry] = array($mypathway);
							} 
							else 
							{
								echo "inserimento... </br>";
								$pathwayfinderbygene[$entry] = array_push($pathwayfinderbygene[$entry],$mypathway); // cerca pathway col target
							}
							
				            echo "Numero pathway per entry ".count($pathwayfinderbygene[$entry])."</br>";
						}
					}
				}
				else if ($xml_item -> getName() == "relation")
				{
					// ora funzionano
					$entry1 = $coppie_id_xml_id_db["".$xml_item['entry1']];
					$entry2 = $coppie_id_xml_id_db["".$xml_item['entry2']];
					
					foreach ($xml_item->children() as $subtype_entry)
					{
						$relation = createRelation($entry1, $entry2, $relationTypeRepo->get($xml_item['type']),
			                $relationSubTypeRepo->get($subtype_entry['name']), $mypathway);
						$relationRepo->add($relation);
						// qui mi indica come creare una relation subtype (mi serve per l'inibizione)
						$compoundRelationSubType = new \Mithril\Pathway\Relation\SubType(['name' => $subtype_entry['name'], 'value' => $subtype_entry['value']]);
						$relationSubTypeRepo->add($compoundRelationSubType);
					}
				}
				
			} 
		}
	}

	$cont = $cont + 1;
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

//file_put_contents("mirtarbase/hsa_MTI.xls", fopen("http://mirtarbase.mbc.nctu.edu.tw/cache/download/4.5/hsa_MTI.xls", 'r'));

$data = new Spreadsheet_Excel_Reader("mirtarbase/hsa_MTI.xls",false);

$nIterazioni = 100;
// $nIterazioni = $data->rowcount($sheet_index=0);

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

for ($i=2; $i < $nIterazioni+2 ; $i++) // gli elementi partono dalla seconda riga
{
	   echo "Iterazione ".$i."</br>";
	   // colonna 2 : nome mirna | ... | colonna 5: id gene (entrez)
	   //function createEntry($pathway, $id, $name, $type, $link, $graph_child)
	   $mirna_id = $data->val($i,2);
	   
		$target_entry = $entrezid_to_mithrilid[$data->val($i,5)];
		// assumendo che sia corretto, possiamo procedere nel creare le relazioni
		if(!is_null($target_entry))
		{ 
			// creiamo la relazione
			$pathways_for_this_target = $pathwayfinderbygene[$target_entry];  //tutte le pathway per questa entry
			echo "Il gene target del miRNA è presente in ".count($pathways_for_this_target)." pathway</br>";
			echo "inserimento mirna</br>";
			$mirna_entry= createEntry($pathways_for_this_target,$mirna_id,$mirna_id,"mirna","NULL",$dummyGraphChild);
			$entryRepo->add($mirna_entry);
			$relation = createRelation($mirna_entry, $target_entry, $mirnaRelationType,$mirnaRelationSubType, NULL);
			$relationRepo->add($relation);
		}
		else 
		{
			// Mirna il cui target non è censito dal sistema, inserimento con dati dummy
			echo "inserimento mirna con dati dummy (target non presente)</br>";
			$mirna_entry= createEntry($dummyPathway,$data->val($i,2),$data->val($i,2),"mirna","NULL",$dummyGraphChild);
			$entryRepo->add($mirna_entry);
		}
}

echo "Inseriti elementi dalla lettura dei miRNA di Mirwalk</br>";
echo "Numero Entry: ".$entryRepo->count()."</br>";
echo "Numeri Tipi: ".$entryTypeRepo->count()."</br>";
echo "Numero relazioni: ".$relationRepo->count()."</br>";
echo "Numero tipi relazione: ".$relationTypeRepo->count()."</br>";
echo "Numero pathway lette: ".$pathwayRepo->count()."</br></br>";
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
