<?php
	/**
	 * MITHrIL: miRNA enriched pathway impact analysis
	 * REST Web Service
	 *
	 * @author Illuminato Luca Costantino - Daniela Ramo
	 */

	require_once "vendor/autoload.php";
	// composer
	require_once "functions/autoload_mithril.php";
	// autoloader class mithril

	use PhpOrient\PhpOrient;

	$xmlstring = file_get_contents("file/hsa00010.xml");
	// è testo
	$xmlobj = simplexml_load_string($xmlstring);
	// è un oggetto XML

	if ($xmlobj -> getName() == "pathway")
	{
		// attributi della root (la pathway)
		//METODO_COSTRUZIONE_PATHWAY -> ($xmlobj['number'], $xmlobj['org'],
		// $xmlobj['title'],$xmlobj['image'],$xmlobj['link']);
		// ora esploriamo i figli
		foreach ($xmlobj->children() as $xmlobj_tmp)
		{
			if ($xmlobj_tmp -> getName() == "entry")
			{
				// attributi del tag entry
				//CREA_ENTRY -> ($xmlobj_tmp['id'], $xmlobj_tmp['name'], $xmlobj_tmp['type'],
				// $xmlobj_tmp['link']);
				// recupero le informazioni di <graphics>
				//$xml_graphics = $xmlobj_tmp -> children();
				//CREA_ENTRY_GRAPHICS -> ($xml_graphics['name'], $xml_graphics['fgcolor'],
				// $xml_graphics['bgcolor'], $xml_graphics['type'], $xml_graphics['x'],
				// $xml_graphics['y'], $xml_graphics['width'], $xml_graphics['height']);
				// controllare la classe di Alaimo per la grafica
				// in particolare x e y ma aggiunge anche COORD
			}
			
			else if ($xmlobj_tmp -> getName() == "relation")
			{
				// attributi del tag relation
				//CREA_RELATION -> ($xmlobj_tmp['entry1'], $xmlobj_tmp['entry2'],
				// $xmlobj_tmp['type'], /* ['subtype'] */);
				//$xml_subtype = $xmlobj_tmp -> children();
				//CREA_ENTRY_SUBTYPE -> ($xml_subtype['name'],$xml_subtype['value']);
			}
		}

	}
?>

