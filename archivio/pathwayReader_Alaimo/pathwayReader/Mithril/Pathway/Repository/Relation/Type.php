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
 * Class Type
 * @method \Mithril\Pathway\Relation\Type|\Mithril\Pathway\Relation\Type[] get($what)
 * @package Mithril\Pathway\Repository\Relation
 */
class Type extends AbstractRepository
{
    protected $className = '\Mithril\Pathway\Relation\Type';

    protected $indexes = [];
}