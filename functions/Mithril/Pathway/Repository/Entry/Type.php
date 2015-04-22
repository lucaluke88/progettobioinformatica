<?php
/**
 * MITHrIL: miRNA enriched pathway impact analysis
 * REST Web Service
 *
 * @author S. Alaimo (alaimos at dmi.unict.it)
 */

namespace Mithril\Pathway\Repository\Entry;

use \Mithril\Data\AbstractRepository;

/**
 * Class Type
 * @method \Mithril\Pathway\Entry\Type|\Mithril\Pathway\Entry\Type[] get($what)
 */
class Type extends AbstractRepository
{
    protected $className = '\Mithril\Pathway\Entry\Type';

    protected $indexes = [];
}