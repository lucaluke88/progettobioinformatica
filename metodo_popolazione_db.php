<?php

/**
 * MITHrIL: miRNA enriched pathway impact analysis
 * REST Web Service
 *
 * @author Illuminato Luca Costantino - Daniela Ramo
 */

error_reporting(E_ERROR);
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
$mirnaRelationType = new \Mithril\Pathway\Relation\Type(['name' => 'MIRNA']);

$relationTypeRepo->add($maplinkRelationType);
$relationTypeRepo->add($ecrellinkRelationType);
$relationTypeRepo->add($pprellinkRelationType);
$relationTypeRepo->add($gerellinkRelationType);
$relationTypeRepo->add($pcrellinkRelationType);

// nuova subtype relation mirna->gene target
$mirnaRelationSubType = new \Mithril\Pathway\Relation\SubType(['name' => 'mirna_relation', 'value' => 0]); // la chiamiamo così perchè non è detto che siano tutte di inibizione
$relationSubTypeRepo->add($mirnaRelationSubType);

$pathwaylist = explode("\n", file_get_contents('http://rest.kegg.jp/list/pathway/hsa'));
$cont = 1;

$global_graph_child;

foreach ($pathwaylist as $p) 
{
	if ($cont<=30) // 2 iterazioni
	{
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
							// dovrebbe essere automaticamente gestito
							//cpd:C00033
							//path:hsa00030
							//ko:K01568
							//hsa:10327
							$entry_id_split = explode(':', $alias);
							$entry_id = $entry_id_split[1];
							if($entry_id_split[0]=="hsa")
							{
								//echo "hsa";
							}
							
							$type = $xml_item['type'][0];
							$entry = createEntry($xml_pathway_obj['name'],$entry_id,$alias,$entryTypeRepo->get($type),$xml_item['link'][0],$graph_child);
			            	$entryRepo->add($entry); // entryRepo aggiornato correttamente
			            	// Array associativo [id_locale|id_nuovo_vero]
			            	$coppie_id_xml_id_db["".$xml_item['id']] = $entry; // viene correttamente creato l'array associativo
			            	// SICCOME NON TROVO UNA RICERCA DEL TIPO 'ENTREZ_ID'-->OGGETTO GENE, mi faccio un array associativo per usarlo con i mirna
			            	$entrezid_to_mithrilid[$entry_id] = $entry;
			          
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
			                $relationSubTypeRepo->get($subtype_entry['name']), $path);
						$relationRepo->add($relation);
						// qui mi indica come creare una relation subtype (mi serve per l'inibizione)
						$compoundRelationSubType = new \Mithril\Pathway\Relation\SubType(['name' => $subtype_entry['name'], 'value' => $subtype_entry['value']]);
						$relationSubTypeRepo->add($compoundRelationSubType);
					}
				}
				
			} 
		}
		echo "</br>";
		echo "EntryRepoCount: ".$entryRepo->count()."</br>";
		echo "EntryTypeRepoCount: ".$entryTypeRepo->count()."</br>";
		echo "RelationRepoCount: ".$relationRepo->count()."</br>";
		echo "RelationTypeRepoCount: ".$relationTypeRepo->count()."</br>";
	}

	$cont = $cont + 1;
}

echo "PathwayRepo: ".$pathwayRepo->count()."</br>";


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

//echo "OK Scrittura su File </br>";

 
//	dopo di questo, dobbiamo aggiungere altri tipi e altre entità
//	aggiungere interazioni microRNA-geni (sempre umane)
//	microrna gene targets (ci interessano solo queste validate)
//	abbiamo a disposizione l'id di entrez, che abbiamo anche in kegg nel formato hsa:id_entrez
//	MIMAT collegati al gene (interazioni di tipo gene)

//	relation subtype: ~inibition
//	sito mirwalk tabella
//	sito mirtarbase excel
//	mappare il nome del maturo con l'entry del db mirbase

// XLS PARSING DI MIRTARBASE


require_once 'excel_reader2.php';

$data = new Spreadsheet_Excel_Reader("mirtarbase/hsa_MTI.xls",false);

//echo $data->rowcount($sheet_index=0); // count righe
//echo $data->colcount($sheet_index=0); // count colonne
//echo $data->val($row,$col); // accediamo ad un valore
echo "OK EXCEL</br>";
// gli elementi partono dalla seconda riga
// colonna 2 : nome mirna
// colonna 5: id gene (entrez)

for ($i=2; $i < 1000 ; $i++) // mettiamo 10 per rapidità, in realtà è $i < rowcount($sheet_index=0)
{
	echo "riga".$i."</br>";
	
	/* Vogliamo creare la relazione tra ID_GENE_TARGET e ID_MIRNA
		 Il primo lo recuperiamo dalle EntryRepo avendo a disposizione l'id entrez
		Il secondo lo abbiamo già dall'istruzione di creazione del mirna
	*/
	 
		$mirna_entry_id = createEntry(NULL,$data->val($i,1),$data->val($i,2),'mirna',NULL,NULL);
		$target_entry_id = $entrezid_to_mithrilid[$data->val($i,5)];
		// assumendo che sia corretto, possiamo procedere nel creare le relazioni
		if(!is_null($target_entry_id))
		{ // creiamo la relazione
			$relation = createRelation($mirna_entry_id, $target_entry_id, $mirnaRelationType,$mirnaRelationSubType, NULL);
			echo "Trovato gene target nelle pathway analizzate!</br>";
		}
}
