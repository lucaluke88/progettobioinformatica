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
$ecrellinkRelationType = new \Mithril\Pathway\Relation\Type(['name' => 'ecrel']);
//e li aggiungo alla relativa repository
$relationTypeRepo->add($maplinkRelationType);
$relationTypeRepo->add($ecrellinkRelationType);


/*
 * OPERAZIONI DI LETTURA =============================================================================
 */

// elenco di tutte le pathway umane
// http://rest.kegg.jp/list/pathway/hsa
$pathwaylist = file_get_contents('http://rest.kegg.jp/list/pathway/hsa');
while(!feof($pathwaylist)){
    $p = fgets($pathwaylist);
    $p_code = substr($p, 5, 12);
	echo $p_code;
	$url = ("http://www.kegg.jp/kegg-bin/download?entry=".$p_code[1]."&format=kgml");
	echo $url;
	// leggo dall'url il mio xml, lo analizzo e per ogni elemento, creo l'entry o la relation più opportuna
	//readElementsFromXML($url);
}
fclose($file);
 
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

// leggiamo la stringa xml e analizziamo i vari elementi: se è ENTRY, creeremo un gene, altrimenti una relazione
function readElementsFromXML($url)
{
	// $url contiene un url ad una specifica pathway (in formato xml)
	$xmlstring = file_get_contents($url); // il contenuto della pagina inserito in questa variabile
	$xmlobj = simplexml_load_string($xmlstring); // oggetto xml di tutta la pathway
	if ($xmlobj -> getName() == "pathway")
	{
		// root: informazioni e tag della pathway
		$mypathway = createPathway($xmlobj['name'], $xmlobj['org'], $xmlobj['title'], $xmlobj['image'], $xmlobj['link']);
		//e la aggiungo alla repository delle pathway
        $pathwayRepo->add($mypathway); // con questo comando aggiungiamo la pathway al repo
        // -------------------------------------------------------------------------------
        // guardiamo tutti i figli (che sono gene/orthology o relation)    
        foreach ($xmlobj->children() as $xmlobj_child)
		{
			if ($xmlobj_child -> getName() == "entry")
			{
				$graph_entry = $xmlobj_child -> children(); // figlio "graphic"
				// controllare bene $xmlobj_child['name'],$xmlobj_child['name']
				// il tag name contiene gli alias, il nome da memorizzare va preso da
				// hsa:UN_NUMERO il numero contenuto va posto come id nell'oggetto entry
				$entry = createEntry($xmlobj['name'],$xmlobj_child['name'],$xmlobj_child['name'],$xmlobj_child['type'],$xmlobj_child['link'],$graph_entry);
	            //e la aggiungo alla repository delle entry
	            $entryRepo->add($entry);
			}
			else if ($xmlobj_child -> getName() == "relation")
			{
				$subtype_entry = $xmlobj_child -> children(); // figlio "graphic"
				// leggo i campi di relation dall'xml
				$entry1 = $allEntries[$xmlobj_child['entry1']];
	            $entry2 = $allEntries[$xmlobj_child['entry2']];
	            //costruisco la relazione tra le due entry lette
	            $relation = createRelation($entry1, $entry2, $relationTypeRepo->get($xmlobj_child['type']),
	                $relationSubTypeRepo->get($subtype_entry['name']), $path);
	            //aggiungo la relazione alla repository
	            $relationRepo->add($relation);
				//costruisco un sottotipo di relazione di esempio
				// if not exists (subtype_name, subtype_value)
				// {
				$compoundRelationSubType = new \Mithril\Pathway\Relation\SubType(['name' => $subtype_entry['name'], 'value' => $subtype_entry['value']]);
				//e lo aggiungo alla relativa repository
				$relationSubTypeRepo->add($compoundRelationSubType);
				// }
			}
			$allEntries = $path->getEntries();
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
		                    'name'    => $type . $id,
		                    'x'       => $graph_entry -> x,
		                    'y'       => $graph_entry -> y,
		                    'coords'  => $graph_entry -> x.','.$graph_entry -> y, // coords = x,y
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


