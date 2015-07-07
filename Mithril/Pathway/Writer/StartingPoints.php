<?php
/**
 * MITHrIL: miRNA enriched pathway impact analysis
 * REST Web Service
 *
 * @author S. Alaimo (alaimos at dmi.unict.it)
 */

namespace Mithril\Pathway\Writer;

use Mithril\Data\WriterInterface;

/**
 * Class Starting Points Writer
 * @package Mithril\Pathway\Writer
 */
class StartingPoints implements WriterInterface
{

    /**
     * @var \Mithril\Pathway\StartingPoints
     */
    protected $element;

    /**
     * @param \Mithril\Pathway\StartingPoints $element
     */
    public function __construct(\Mithril\Pathway\StartingPoints $element = null)
    {
        if (!is_null($element)) $this->element = $element;
    }

    /**
     * @return \Mithril\Pathway\StartingPoints
     */
    public function getElement()
    {
        return $this->element;
    }

    /**
     * @param \Mithril\Pathway\StartingPoints $element
     *
     * @return $this
     */
    public function setElement(\Mithril\Pathway\StartingPoints $element)
    {
        $this->element = $element;
        return $this;
    }

    /**
     * @param string $filename
     *
     * @return int
     */
    public function writeAndSave($filename)
    {
        return file_put_contents($filename, $this->write());
    }

    /**
     * Serialize an array to string
     *
     * @param array         $a
     * @param null|callable $mapper
     * @param string        $separator
     *
     * @return string
     */
    protected function writeArray(array $a, $mapper = null, $separator = ",")
    {
        if ($mapper !== null && is_callable($mapper)) {
            $a = array_map($mapper, $a);
        }
        return implode($separator, $a);
    }

    /**
     * @return string
     */
    public function write()
    {
        if (!$this->element->hasRun()) {
            $this->element->find();
        }
        $result = '';
        $startingPoints = $this->element->getStartingPoints();
        foreach ($startingPoints as $pathway => $nodes) {
            /** @var \Mithril\Pathway\Entry\Entry[] $nodes */
            $result .= $pathway . "\t" . $this->writeArray($nodes, function (\Mithril\Pathway\Entry\Entry $n) {
                    return $n->id;
                }, ",") . "\n";
        }
        return $result;
    }

}