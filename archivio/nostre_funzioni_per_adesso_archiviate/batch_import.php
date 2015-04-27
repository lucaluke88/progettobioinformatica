<?php
	/**
	 * MITHrIL: miRNA enriched pathway impact analysis
	 * REST Web Service
	 *
	 * @author Illuminato Luca Costantino - Daniela Ramo
	 */
	 
	 /*
	  *	Cerco di ragionare in una maniera più semplice: devo partire da questo esempio per creare il metodo
	  * 
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

	
?>

