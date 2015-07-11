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
		if(isset($this->element->type->name))
		{
			$data['type'] = $this->element->type->name;
		}
		else
		{
			echo "is not set</br>";
			$data['type'] = 'typeWriteElement';
		}
		
         // è un array void, problema qui in questo file
        $data['aliases'] = $this->writeArray($this->element->aliases);
		$data['links'] = $this->writeArray($this->element->links);
		$data['contained'] = $this->writeContained($this->element->contained); // problema qui riferito nel pathway.php
        return $this->writeArray($data, null, "\t");
    }
}