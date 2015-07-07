<?php
/**
 * MITHrIL: miRNA enriched pathway impact analysis
 * REST Web Service
 *
 * @author S. Alaimo (alaimos at dmi.unict.it)
 */

namespace Mithril\Pathway\Writer\Relation;

use Mithril\Data\ElementWriter;

/**
 * Class Relation Writer
 * @property \Mithril\Pathway\Relation\Relation $element
 * @package Mithril\Pathway\Writer\Relation
 */
class Relation extends ElementWriter
{

    /**
     * @return string
     */
    public function write()
    {
        $data = [
            $this->element->entry1->id,
            $this->element->entry2->id,
            $this->element->type->name,
            $this->writeArray($this->element->subTypes, function (\Mithril\Pathway\Relation\SubType $e) {
                return $e->value;
            }),
            $this->writeArray($this->element->pathways, function (\Mithril\Pathway\Pathway $e) {
                return $e->id;
            })
        ];
        return $this->writeArray($data, null, "\t");
    }
}