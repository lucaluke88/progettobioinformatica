<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
require_once('autoload.php');

function randomEntry($id, $pathway, $type)
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

function randomRelation($entry1, $entry2, $type, $subType, $pathway)
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

function makeRandomPathway($id, $org)
{
    return new \Mithril\Pathway\Pathway([
        'id'       => 'path:' . $org . '' . $id,
        'organism' => $org,
        'title'    => 'Pathway di prova ' . $id,
        'image'    => 'http://www.pathway.pa/hsa.prova.' . $id,
    ]);
}

//Costruisco le repository
$entryTypeRepo = new \Mithril\Pathway\Repository\Entry\Type();              //Repository dei tipi di entry
$entryRepo = new \Mithril\Pathway\Repository\Entry\Entry();                 //Repository delle entry
$relationTypeRepo = new \Mithril\Pathway\Repository\Relation\Type();        //Repository dei tipi di relazione
$relationSubTypeRepo = new \Mithril\Pathway\Repository\Relation\SubType();  //Repository dei sottotipi di relazione
$relationRepo = new \Mithril\Pathway\Repository\Relation\Relation();        //Repository delle relazioni
$pathwayRepo = new \Mithril\Pathway\Repository\Pathway();                   //Repository delle pathway
//Imposto le repository delle entry e delle pathway collegandole alle altre
$entryRepo->setRelationsRepository($relationRepo);
$pathwayRepo->setRelationsRepository($relationRepo)->setEntriesRepository($entryRepo);
//costruisco un tipo di entry di esempio
$exampleEntryType = new \Mithril\Pathway\Entry\Type(['name' => 'Gene']);
//e lo aggiungo alla repository dei tipi di entry
$entryTypeRepo->add($exampleEntryType);
//costriusco un tipo di relazione di esempio
$exampleRelationType = new \Mithril\Pathway\Relation\Type(['name' => 'Test']);
//e lo aggiungo alla relativa repository
$relationTypeRepo->add($exampleRelationType);
//costruisco un sottotipo di relazione di esempio
$exampleRelationSubType = new \Mithril\Pathway\Relation\SubType(['name' => 'TestSubType', 'value' => 'XXX']);
//e lo aggiungo alla relativa repository
$relationSubTypeRepo->add($exampleRelationSubType);

$t = 1;
foreach (['human', 'prova1', 'prova2'] as $org) {
    for ($i = 1; $i <= 5; $i++) {
        //costruisco una pathway random
        $path = makeRandomPathway($i, $org);
        //e la aggiungo alla repository delle pathway
        $pathwayRepo->add($path);
        for ($j = $t; $j <= $t + 10; $j++) {
            //costruisco una entry random
            $entry = randomEntry($j, $path, $entryTypeRepo->get("Gene"));
            //e la aggiungo alla repository delle entry
            $entryRepo->add($entry);
        }
        $allEntries = $path->getEntries();
        for ($j = 0; $j < 10; $j++) {
            //Scelgo 2 entry a caso
            $entry1 = $allEntries[mt_rand(0, count($allEntries) - 1)];
            $entry2 = $allEntries[mt_rand(0, count($allEntries) - 1)];
            //costruisco una relazione di esempio tra le due entry casuali
            $relation = randomRelation($entry1, $entry2, $relationTypeRepo->get("Test"),
                $relationSubTypeRepo->get("TestSubType"), $path);
            //aggiungo la relazione alla repository
            $relationRepo->add($relation);
        }
        $t += 10;
    }
}