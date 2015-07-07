<?php
/**
 * MITHrIL: miRNA enriched pathway impact analysis
 * REST Web Service
 *
 * @author S. Alaimo (alaimos at dmi.unict.it)
 */

namespace Mithril\Pathway\Writer;

use Mithril\Data\ElementWriter;

/**
 * Class Pathway Writer
 * @property \Mithril\Pathway\Pathway $element
 * @package Mithril\Pathway\Writer
 */
class Pathway extends ElementWriter
{

    /**
     * @return string
     */
    public function write()
    {
        $data = $this->element->toArray();
        $data['links'] = json_encode($this->element->links);
        return $this->writeArray($data, null, "\t");
    }
}