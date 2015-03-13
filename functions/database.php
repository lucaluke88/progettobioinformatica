<?php
	ob_start();
	// per il firebug
	require "./vendor/autoload.php";
	use PhpOrient\PhpOrient;
	// i namespace vanno usati nello scope più esterno altrimenti danno errore!
	$db_name = 'UnifiedPathwayDB';
	
	function writeIntoDB($jsonentry)
	{
		$arrjson = json_decode($jsonentry, true);
		$attributes = $arrjson['@attributes']; // array associativo con i dati del pathway (titolo, ecc)
		return $attributes;
	}




?>