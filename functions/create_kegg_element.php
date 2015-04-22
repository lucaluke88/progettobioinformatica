<?php
    error_reporting(E_ALL);
	ini_set('display_errors', '1');
	require_once('autoload_mithril.php');
	
	function createPathway($id, $org, $title, $image, $link)
	{
	    return new \Mithril\Pathway\Pathway([
	        'id'       => $id,
	        'organism' => $org,
	        'title'    => $title;
	        'image'    => $link;
	    ]);
	}
	
	// per adesso diamo come input sia gli attributi di entry, sia di graphics (da rivedere)
	function randomEntry($pathway, $id, $name, $type, $link, $graph_name, $graph_ /* continuare da qui */ )
	{
	    return new \Mithril\Pathway\Entry\Entry([
	        'id'        => $id,
	        'aliases'   => [],
	        'name'      => 'Gene ' . $id,
	        'type'      => $type,
	        'links'     => ["a", "b", "c"],
	        'contained' => [
	            new \Mithril\Pathway\Contained\Pathway([
	                'pathway' => $pathway,
	                'graphic' => new \Mithril\Pathway\Graphic([
	                    'name'    => 'Gene ' . $id,
	                    'x'       => 0.1,
	                    'y'       => 0.2,
	                    'coords'  => 'xxxxxxx',
	                    'type'    => 'rectangle',
	                    'width'   => 100,
	                    'height'  => 100,
	                    'fgcolor' => '#ffffff',
	                    'bgcolor' => '#000000'
	                ])
	            ])
	        ]
	    ]);
	}
?>