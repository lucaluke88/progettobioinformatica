<?php

	/**
	 * MITHrIL: miRNA enriched pathway impact analysis
	 * REST Web Service
	 *
	 * @author Illuminato Luca Costantino - Daniela Ramo
	 */
	error_reporting(E_ALL);
	use PhpOrient\PhpOrient;
	//compara singola entry con gli id hsa del file txt
	
	//function compareHsa ($entry) {
	$txt_file = file_get_contents("../file/kegg_hsa.txt");
	//print_r($txt_file);
	
	$row        = explode("\n", $txt_file);
	for($i=0;$row[$i]!=null;$i=$i+1)
	{
		$rowarray=explode("\t",$rows);
		for($j=0;$rowarray[j]!=null;$j=$j+1)
		{
			if($i==0) {
			$hsaname = $rowarray[0];
			}
			else {
				$aliases= $rowarray[$j];
		}
	}
	}
	//print_r($element);

	/*
	//scorro la prima riga
	for($i=0;$rowarray[$i]!=null;$i=$i+1) {
		
		print_r("il mio nome =".$hsaname);
		$j=0;
		//if ($hsaname==($entry->$id)) {
			print_r("elemento di rowarray =".$aliases[$j]);
			$aliases[$j]= $rowarray[i];
			
			//$entry -> $aliases [$j] = $string[i];
			$j=$j+1;
		}
	for($j=0;$aliases[j]!=null;$j=$j+1) {
		print_r($aliases[j]);
	}
	//}*/
	//scorro il resto del file
	/*
	foreach ($rows as $row => $tmp ) 
	{
		for(i=0;$string[i]!=null;i++) {
		$hsaname = $string[0];
		$j=0;
		if ($hsaname==($entry->$id)) {
			
			$entry -> $aliases [$j] = $string[i];
			$j++;
		}
	}
	}
	
	}*/
	
	
	
	 ?>	