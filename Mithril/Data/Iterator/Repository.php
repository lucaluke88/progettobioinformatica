<?php
/**
 * MITHrIL: miRNA enriched pathway impact analysis
 * REST Web Service
 *
 * @author S. Alaimo (alaimos at dmi.unict.it)
 */

namespace Mithril\Data\Iterator;


use Mithril\Data\AbstractRepository;

class Repository extends \ArrayIterator
{

    /**
     * @var \Mithril\Data\AbstractRepository
     */
    protected $repository;

    /**
     * @var null|string
     */
    protected $index = null;

    public function __construct(AbstractRepository $repository, $index = null)
    {
        $this->repository = $repository;
        $this->index = $index;
        $tmp = &$repository->getAll($index);
        if ($tmp !== null) {
            parent::__construct($tmp);
        } else {
            parent::__construct([]);
        }
    }

    public function offsetExists($offset)
    {
        return $this->repository->has($offset, $this->index);
    }

    public function offsetGet($offset)
    {
        return $this->repository->get($offset, $this->index);
    }

    public function offsetSet($offset, $value)
    {
        $this->repository->add($value);
    }

    public function offsetUnset($offset)
    {
        $this->repository->remove($this->offsetGet($offset));
    }

    public function count()
    {
        return $this->repository->count($this->index);
    }

}