<?php
/**
 * MITHrIL: miRNA enriched pathway impact analysis
 * REST Web Service
 *
 * @author S. Alaimo (alaimos at dmi.unict.it)
 */

namespace Mithril\Pathway\Writer\Entry;

use Mithril\Data\ElementWriter;
use Mithril\Pathway\Writer\Contained\Pathway as PathwayContainedWriter;

/**
 * Class Entry Type Writer
 * @property \Mithril\Pathway\Entry\Type $element
 * @package Mithril\Pathway\Writer\Entry
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