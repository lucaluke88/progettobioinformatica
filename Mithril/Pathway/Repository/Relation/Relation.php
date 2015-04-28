<?php
/**
 * MITHrIL: miRNA enriched pathway impact analysis
 * REST Web Service
 *
 * @author S. Alaimo (alaimos at dmi.unict.it)
 */

namespace Mithril\Pathway\Repository\Relation;

use \Mithril\Data\AbstractRepository;

/**
 * Class Relation
 * @method \Mithril\Pathway\Relation\Relation|\Mithril\Pathway\Relation\Relation[] get($what, $index = null)
 * @method \Mithril\Pathway\Relation\Relation[] getByEntry1($entry1 = "")
 * @method \Mithril\Pathway\Relation\Relation[] byEntry1($entry1 = "")
 * @method \Mithril\Pathway\Relation\Relation[] getByEntry2($entry2 = "")
 * @method \Mithril\Pathway\Relation\Relation[] byEntry2($entry2 = "")
 * @method \Mithril\Pathway\Relation\Relation[] getByType($type = "")
 * @method \Mithril\Pathway\Relation\Relation[] byType($type = "")
 * @method \Mithril\Pathway\Relation\Relation[] getBySubType($subType = "")
 * @method \Mithril\Pathway\Relation\Relation[] bySubType($subType = "")
 * @method \Mithril\Pathway\Relation\Relation[] getByPathway($pathway = "")
 * @method \Mithril\Pathway\Relation\Relation[] byPathway($pathway = "")
 * @package Mithril\Pathway\Repository
 */
class Relation extends AbstractRepository
{
    protected $className = '\Mithril\Pathway\Relation\Relation';

    protected $indexes = [
        'byEntry1'  => 'entry1',
        'byEntry2'  => 'entry2',
        'byType'    => 'type',
        'bySubType' => 'subTypes',
        'byPathway' => 'pathways',
    ];
}