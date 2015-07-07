<?php
/**
 * MITHrIL: miRNA enriched pathway impact analysis
 * REST Web Service
 *
 * @author S. Alaimo (alaimos at dmi.unict.it)
 */

namespace Mithril\Data;


class RepositoryWriter implements WriterInterface
{

    /**
     * @var \Mithril\Data\AbstractRepository
     */
    protected $repository;

    /**
     * @var string
     */
    protected $writerClass;

    /**
     * @var \Mithril\Data\ElementWriter
     */
    protected $writerObject;

    /**
     * @param \Mithril\Data\AbstractRepository $repository
     * @param string                           $writerClass
     */
    function __construct(AbstractRepository $repository, $writerClass)
    {
        $this->repository = $repository;
        $this->writerClass = $writerClass;
    }

    /**
     * @return \Mithril\Data\AbstractRepository
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * @param \Mithril\Data\AbstractRepository $repository
     *
     * @return $this
     */
    public function setRepository(AbstractRepository $repository)
    {
        $this->repository = $repository;
        return $this;
    }

    /**
     * @return string
     */
    public function getWriterClass()
    {
        return $this->writerClass;
    }

    /**
     * @param string $writerClass
     *
     * @return $this
     */
    public function setWriterClass($writerClass)
    {
        $this->writerClass = $writerClass;
        $this->writerObject = null;
        return $this;
    }

    /**
     * @return \Mithril\Data\ElementWriter
     */
    public function getWriterObject()
    {
        if ($this->writerObject === null) {
            $className = $this->writerClass;
            if (!class_exists($className)) {
                throw new \RuntimeException("Unable to find writer class: {$className}");
            }
            $this->writerObject = new $className();
        }
        return $this->writerObject;
    }

    /**
     * @param \Mithril\Data\AbstractElement $element
     *
     * @return string
     */
    public function writeSingleElement(AbstractElement $element)
    {
        $writer = $this->getWriterObject()->setElement($element);
        return $writer->write();
    }

    /**
     * @return string
     */
    public function write()
    {
        $result = '';
        foreach ($this->repository as $element) {
            $result .= $this->writeSingleElement($element) . "\n";
        }
        return $result;
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
}