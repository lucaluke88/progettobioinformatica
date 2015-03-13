<?php
	function getPathwayInfoFromItsCode($code)
	{
		// http://rest.kegg.jp/get/hsa00010
		//http://rest.kegg.jp/get/eco00020/kgml
		$content = file_get_contents('http://rest.kegg.jp/get/hsa'.$code.'/kgml');
		return xml2json($content);
	}
	
	function xml2json($fileContents)
	{
		$fileContents = str_replace(array("\n", "\r", "\t"), '', $fileContents);
		$fileContents = trim(str_replace('"', "'", $fileContents));
		$simpleXml = simplexml_load_string($fileContents);
		$json = json_encode($simpleXml);
		return $json;
	}
	
	


	
?>