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
 * Class SubType
 * @method \Mithril\Pathway\Relation\SubType|\Mithril\Pathway\Relation\SubType[] get($what)
 * @package Mithril\Pathway\Repository\Relation
 */
class SubType extends AbstractRepository
{
    protected $className = '\Mithril\Pathway\Relation\SubType';

    protected $indexes = [];
}