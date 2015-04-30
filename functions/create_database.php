<?php

	/*
	 * 	inserire nel db i subtypes
	 * /


	/**
	 * MITHrIL: miRNA enriched pathway impact analysis
	 * REST Web Service
	 *
	 * @author Illuminato Luca Costantino - Daniela Ramo
	 */

	/*
	 * 	inserire nel db i subtypes
	 * /


	/**
	 * MITHrIL: miRNA enriched pathway impact analysis
	 * REST Web Service
	 *
	 * @author Illuminato Luca Costantino - Daniela Ramo
	 */
	// i namespace vanno usati nello scope più esterno altrimenti danno errore!
	use PhpOrient\PhpOrient;
	// inizializzazione connessione
	$client = new PhpOrient();
	$client -> hostname = $_SESSION['hostname'];
	$client -> port = $_SESSION['port'];
	$client -> username = $_SESSION['user'];
	$client -> password = $_SESSION['passwd'];
	$db_name = $_SESSION['dbname'];
	$client -> connect();
	// creo il db
	$client -> dbCreate($db_name, PhpOrient::STORAGE_TYPE_MEMORY, PhpOrient::DATABASE_TYPE_GRAPH);
	$client -> dbOpen($db_name, $_SESSION['user'], $_SESSION['passwd']);

	// Classe Pathway
	// -------------------------------------------------------------------
	// link pathway: http://www.kegg.jp/dbget-bin/www_bget?pathway+hsa00010
	/*attributi classe Pathway
	 * @property string $id
	 * @property string $organism
	 * @property string $title
	 * @property string $image
	 * @property array  $links*/
	$client -> command('CREATE CLASS Pathway extends V');
	$client -> command('CREATE PROPERTY Pathway.id STRING');
	//id pathway
	$client -> command('CREATE PROPERTY Pathway.title STRING');
	// Titolo della Pathway
	$client -> command('CREATE PROPERTY Pathway.organism STRING');

	$client -> command('CREATE PROPERTY Pathway.image STRING');
	// immagine pathway
	$client -> command('CREATE PROPERTY Pathway.links EMBEDDEDLIST');
	
	// Classe Entry
	// -----------------------------------------------------------------------

	// link entry: http://www.kegg.jp/dbget-bin/www_bget?hsa:130589
	/* attributi della tabella entry
	 * @property int                                  $id
	 * @property array                                $aliases
	 * @property string                               $name
	 * @property \Mithril\Pathway\Entry\Type          $type
	 * @property array                                $links
	 * @property \Mithril\Pathway\Contained\Pathway[] $contained*/
	$client -> command('CREATE CLASS Entry extends V');
	// questi campi li recuperiamo dalla pathway <entry>
	$client -> command('CREATE PROPERTY Entry.pathway_id INTEGER');
	// pathway id
	$client -> command('CREATE PROPERTY Entry.kegg_id STRING');
	// kegg id di quel entry
	$client -> command('CREATE PROPERTY Entry.name STRING');
	//nome entry
	$client -> command('CREATE PROPERTY Entry.type STRING');
	//tipo della entry

	$client -> command('CREATE PROPERTY Entry.aliases EMBEDDEDLIST');
	//altri nomi dell'entry
	$client -> command('CREATE PROPERTY Entry.links EMBEDDEDLIST');
	// link kegg
	$client -> command('CREATE PROPERTY Entry.contained EMBEDDEDLIST');
	//nome entry
	// (non stiamo riportando qui le informazioni su come disegnare gli elementi)
	//$client -> command('CREATE PROPERTY Gene.name STRING');
	// link kegg
	// questi campi li prendiamo dal gene entry
	//$client -> command('CREATE PROPERTY Gene.orthology_id INTEGER');
	// orthology id
	// dobbiamo chiedere cosa importare (se tutte le proprietÃ , se anche le
	// proprietÃ  da altri db in questa fase, o possiamo escludere qualcosa)
	

	// imposto gli archi come non leggeri (commento perchÃ¨ fa impallare tutto)
	//$client -> command('ALTER DATABASE CUSTOM useLightweightEdges=true');
	//Classe Relation
	$client -> command('CREATE CLASS Relation extends E');
	/*attributi della classe Relation
	 * @property \Mithril\Pathway\Entry\Entry        $entry1
	 * @property \Mithril\Pathway\Entry\Entry        $entry2
	 * @property \Mithril\Pathway\Relation\Type      $type
	 * @property \Mithril\Pathway\Relation\SubType[] $subTypes*/
	$client -> command('CREATE PROPERTY Relation.entry1 STRING');
	$client -> command('CREATE PROPERTY Relation.entry2 STRING');
	$client -> command('CREATE PROPERTY Relation.type STRING');
	$client -> command('CREATE PROPERTY Relation.subtype EMBEDDEDLIST');

	//Classe Graphic della Pathway
	/*attributi della Graphic
	 * @property string $name
	 * @property float  $x
	 * @property float  $y
	 * @property string $coords
	 * @property string $type
	 * @property float  $width
	 * @property float  $height
	 * @property string $fgcolor
	 * @property string $bgcolor*/
	$client -> command('CREATE CLASS Graphic extends V');
	// questi campi li recuperiamo dalla pathway <entry>
	$client -> command('CREATE PROPERTY Graphic.name STRING');
	$client -> command('CREATE PROPERTY Graphic.x FLOAT');
	$client -> command('CREATE PROPERTY Graphic.y FLOAT');
	$client -> command('CREATE PROPERTY Graphic.coords STRING');
	$client -> command('CREATE PROPERTY Graphic.type STRING');
	$client -> command('CREATE PROPERTY Graphic.width FLOAT');
	$client -> command('CREATE PROPERTY Graphic.height FLOAT');
	$client -> command('CREATE PROPERTY Graphic.fgcolor STRING');
	$client -> command('CREATE PROPERTY Graphic.bgcolor FLOAT');
?>