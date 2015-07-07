<?php
/**
 * MITHrIL: miRNA enriched pathway impact analysis
 * REST Web Service
 *
 * @author S. Alaimo (alaimos at dmi.unict.it)
 */

namespace Mithril\Pathway\Writer\Relation;

use Mithril\Data\ElementWriter;
use Mithril\Pathway\Writer\Contained\Pathway as PathwayContainedWriter;

/**
 * Class Relation Type Writer
 * @property \Mithril\Pathway\Relation\Type $element
 * @package Mithril\Pathway\Writer\Relation
 */
class Type extends ElementWriter
{

    /**
     * @return string
     */
    public function write()
    {
        return $this->element->name;
    }
}