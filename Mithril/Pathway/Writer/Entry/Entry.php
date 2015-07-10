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
 * Class Entry Writer
 * @property \Mithril\Pathway\Entry\Entry $element
 * @package Mithril\Pathway\Writer\Entry
 */
class Entry extends ElementWriter
{

    /**
     * @var \Mithril\Pathway\Writer\Contained\Pathway
     */
    protected $containedWriter = null;

    /**
     * Write contained objects
     *
     * @return string
     */
    protected function writeContained()
    {
        if ($this->containedWriter === null) {
            $this->containedWriter = new PathwayContainedWriter();
        }
        $contained = array_map(function (\Mithril\Pathway\Contained\Pathway $e) {
            return json_decode($this->containedWriter->setElement($e)->write(), true);
        }, $this->element->contained);
        return json_encode($contained);
    }

    /**
     * @return string
     */
    public function write()
    {
        $data = $this->element->toArray();
		print_r($this->element->type);
        $data['type'] = $this->element->type->name;
        $data['aliases'] = $this->writeArray($this->element->aliases);
        $data['links'] = $this->writeArray($this->element->links);
        $data['contained'] = $this->writeContained($this->element->contained);
        return $this->writeArray($data, null, "\t");
    }
}