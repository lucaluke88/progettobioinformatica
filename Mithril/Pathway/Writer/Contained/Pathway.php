<?php
/**
 * MITHrIL: miRNA enriched pathway impact analysis
 * REST Web Service
 *
 * @author S. Alaimo (alaimos at dmi.unict.it)
 */

namespace Mithril\Pathway\Writer\Contained;

use Mithril\Data\ElementWriter;


/**
 * Class Pathway Contained Writer
 * @property \Mithril\Pathway\Contained\Pathway $element
 * @package Mithril\Pathway\Writer\Contained
 */
class Pathway extends ElementWriter
{

    /**
     * @return string
     */
    public function write()
    {
        $graphic = $this->element->graphic->toArray();
        $graphic['pathway'] = $this->element->pathway->id;
        return json_encode($graphic);
    }
}