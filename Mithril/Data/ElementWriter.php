<?php
/**
 * MITHrIL: miRNA enriched pathway impact analysis
 * REST Web Service
 *
 * @author S. Alaimo (alaimos at dmi.unict.it)
 */

namespace Mithril\Data;


abstract class ElementWriter implements WriterInterface
{

    /**
     * @var \Mithril\Data\AbstractElement
     */
    protected $element;

    /**
     * @param \Mithril\Data\AbstractElement $element
     */
    public function __construct(AbstractElement $element = null)
    {
        if (!is_null($element)) $this->element = $element;
    }

    /**
     * @return \Mithril\Data\AbstractElement
     */
    public function getElement()
    {
        return $this->element;
    }

    /**
     * @param \Mithril\Data\AbstractElement $element
     *
     * @return $this
     */
    public function setElement(AbstractElement $element)
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

}