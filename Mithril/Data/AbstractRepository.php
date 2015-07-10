<?php
/**
 * MITHrIL: miRNA enriched pathway impact analysis
 * REST Web Service
 *
 * @author S. Alaimo (alaimos at dmi.unict.it)
 */

namespace Mithril\Data;

/**
 * Class AbstractRepository
 * @package Mithril\Data
 */
abstract class AbstractRepository implements \JsonSerializable, \IteratorAggregate, \Countable
{

    protected $className = '';

    protected $data = [];

    protected $indexedData = [];

    protected $indexes = [
        // 'indexName' => 'fieldName',
    ];

    public function __construct()
    {
        $this->initIndexes();
    }

    /**
     * Returns the key used to add an element to the index
     *
     * @param \Mithril\Data\AbstractElement $data
     * @param                               $index
     *
     * @return mixed
     */
    protected function indexKey(AbstractElement $data, $index)
    {
        $fn = $this->indexes[$index];
        if (is_callable($fn)) {
            return call_user_func($fn, $data);
        } else {
            return $data->get($fn);
        }
    }

    /**
     * Returns the position of an element in a array
     *
     * @param \Mithril\Data\AbstractElement $needle
     * @param array                         $haystack
     *
     * @return bool|int
     */
    protected function arraySearch(AbstractElement $needle, array $haystack)
    {
        $i = 0;
        foreach ($haystack as $hay) {
            if ($needle->isEqual($hay)) {
                return $i;
            }
            $i++;
        }
        return false;
    }

    /**
     * Checks if an element is contained in a array
     *
     * @param \Mithril\Data\AbstractElement $needle
     * @param array                         $haystack
     *
     * @return bool
     */
    protected function inArray(AbstractElement $needle, array $haystack)
    {
        return $this->arraySearch($needle, $haystack) !== false;
    }

    /**
     * Add an element to an index
     *
     * @param string                        $indexName
     * @param mixed                         $value
     * @param \Mithril\Data\AbstractElement $data
     */
    protected function realIndex($indexName, $value, AbstractElement $data)
    {
    	if ($value instanceof AbstractElement) {
            if ($value->getIdField() !== null) {
                $value = $value->get($value->getIdField());
			} else {
                throw new \RuntimeException("Unsupported object in index");
            }
        }
		if (array_key_exists($value, $this->indexedData[$indexName])) 
        {
        	if (is_array($this->indexedData[$indexName][$value]) &&
                !$this->inArray($data, $this->indexedData[$indexName][$value])
            ) {
                $this->indexedData[$indexName][$value][] = $data;
            } elseif (!is_array($this->indexedData[$indexName][$value]) &&
                !$data->isEqual($this->indexedData[$indexName][$value])
            ) {
                $this->indexedData[$indexName][$value] = [$this->indexedData[$indexName][$value], $data];
            }
        } 
        else 
        {
            $this->indexedData[$indexName][$value] = $data;
        }
    }

    /**
     * Add an element to all indexes
     *
     * @param \Mithril\Data\AbstractElement $data
     *
     * @return $this
     */
    protected function index(AbstractElement $data)
    {
        foreach ($this->indexes as $indexName => $fieldName) {
            $values = $this->indexKey($data, $indexName);
            if (is_array($values)) {
                foreach ($values as $v) {
                	$this->realIndex($indexName, $v, $data);
                }
            } else {
            	$this->realIndex($indexName, $values, $data);
            }
        }
        return $this;
    }

    /**
     * Remove an element from an index
     *
     * @param string                        $indexName
     * @param mixed                         $value
     * @param \Mithril\Data\AbstractElement $data
     */
    protected function realUnIndex($indexName, $value, $data)
    {
        if ($value instanceof AbstractElement) {
            if ($value->getIdField() !== null) {
                $value = $value->get($value->getIdField());
            } else {
                throw new \RuntimeException("Unsupported object in index");
            }
        }
		if (array_key_exists($value, $this->indexedData[$indexName])) {
            if (is_array($this->indexedData[$indexName][$value])) {
                $idx = $this->arraySearch($data, $this->indexedData[$indexName][$value]);
                if ($idx !== false) {
                    array_splice($this->indexedData[$indexName][$value], $idx, 1);
                }
                if (empty($this->indexedData[$indexName][$value])) {
                    unset($this->indexedData[$indexName][$value]);
                }
            } else {
                unset($this->indexedData[$indexName][$value]);
            }
        }
    }

    /**
     * Remove an element from all indexes
     *
     * @param \Mithril\Data\AbstractElement $data
     *
     * @return $this
     */
    protected function unIndex(AbstractElement $data)
    {
        foreach ($this->indexes as $indexName => $fieldName) {
            $values = $this->indexKey($data, $indexName);
            if (is_array($values)) {
                foreach ($values as $v) {
                    $this->realUnIndex($indexName, $v, $data);
                }
            } else {
                $this->realUnIndex($indexName, $values, $data);
            }
        }
        return $this;
    }

    /**
     * Initializes indexes
     *
     * @return $this
     */
    protected function initIndexes()
    {
        foreach ($this->indexes as $indexName => $fieldName) {
            $this->indexedData[$indexName] = [];
        }
        foreach ($this->data as $data) {
            $this->index($data);
        }
        return $this;
    }

    /**
     * Add an element to this repository
     *
     * @param \Mithril\Data\AbstractElement[]|\Mithril\Data\AbstractElement $data
     *
     * @return $this
     */
    public function add($data)
    {
        if (is_array($data)) {
            foreach ($data as $d) {
                $this->add($d);
            }
        } else {
            $class = $this->className;
            if ($data instanceof $class && $data instanceof AbstractElement) {
                $idf = $data->getIdField();
                if ($idf !== null) {
                    $this->data[$data->get($idf)] = $data;
                } else {
                    $this->data[] = $data;
                }
				$this->index($data);
            }
        }
        return $this;
    }

    /**
     * Remove an element from this repository
     *
     * @param \Mithril\Data\AbstractElement $data
     *
     * @return $this
     */
    public function remove(AbstractElement $data)
    {
        $idf = $data->getIdField();
        if ($idf !== null) {
            unset($this->data[$data->get($idf)]);
        } else {
            $idx = array_search($data, $this->data);
            if ($idx !== false) {
                array_splice($this->data, $idx, 1);
            }
        }
        $this->unIndex($data);
        return $this;
    }

    /**
     * Checks if something is contained in this repository
     *
     * @param mixed       $what
     * @param string|null $index
     *
     * @return bool
     */
    public function has($what, $index = null)
    {
        if ($index === null) {
           return array_key_exists($what, $this->data);
        } else {
            return isset($this->indexes[$index]) && array_key_exists($what, $this->indexedData[$index]);
        }
    }

    /**
     * Get an element from this repository
     *
     * @param mixed       $what
     * @param string|null $index
     *
     * @return \Mithril\Data\AbstractElement|\Mithril\Data\AbstractElement[]
     */
    public function get($what, $index = null)
    {
        if ($what instanceof AbstractElement) {
            if ($what->getIdField() !== null) {
                $what = $what->get($what->getIdField());
            } else {
                throw new \RuntimeException("Unsupported object in index");
            }
        }
        if ($this->has($what, $index)) {
            if ($index === null) {
                return $this->data[$what];
            } else {
                return $this->indexedData[$index][$what];
            }
        }
        return null;
    }

    /**
     * Find an element in this repository
     *
     * @param string                 $fieldName
     * @param string|object|callable $needle
     * @param bool                   $caseSensitive
     * @param bool                   $regExp
     *
     * @return \Mithril\Data\AbstractElement[]
     */
    public function find($fieldName, $needle, $caseSensitive = true, $regExp = false)
    {
        $exact = !is_callable($needle) && !$regExp && $caseSensitive;
        $result = [];
        if ($exact && in_array($fieldName, $this->indexedData)) {
            if ($needle instanceof AbstractElement) {
                if ($needle->getIdField() !== null) {
                    $needle = $needle->get($needle->getIdField());
                } else {
                    throw new \RuntimeException("Unsupported needle object");
                }
            }
            if (array_key_exists($needle, $this->indexedData[$fieldName])) {
                $tmp = $this->indexedData[$fieldName][(string)$needle];
                $result = (is_array($tmp)) ? $tmp : [$tmp];
            }
        } else {
            foreach ($this->data as $data) {
                /** @var AbstractElement $data */
                $value = $data->get($fieldName);
                if (is_object($needle) && method_exists($needle, 'isEqual')) {
                    if (($exact && $needle->isEqual($value)) ||
                        (!$exact && is_callable($needle) && call_user_func($needle, $value))
                    ) {
                        $result[] = $data;
                    }
                } else {
                    if (($exact && $value == $needle) ||
                        (!$exact && (
                                (!$caseSensitive && strcasecmp($value, $needle) == 0) ||
                                ($regExp && preg_match($needle, $value)) ||
                                (is_callable($needle) && call_user_func($needle, $value))))
                    ) {
                        $result[] = $data;
                    }
                }
            }
        }
        return $result;
    }

    /**
     * Internal method do not invoke
     *
     * @param null|string $index
     *
     * @return array|null
     */
    public function &getAll($index = null)
    {
        if ($index === null) {
            return $this->data;
        } else {
            if (array_key_exists($index, $this->indexes)) {
                return $this->indexedData[$index];
            }
        }
        return null;
    }

    /**
     * Instantiate an iterator for an index in this object
     *
     * @param null|string $index
     *
     * @return \Mithril\Data\Iterator\Repository
     */
    public function getIterator($index = null)
    {
        return new Iterator\Repository($this, $index);
    }

    /**
     * Use this object ad a function which returns an iterator
     *
     * @param null|string $index
     *
     * @return \Mithril\Data\Iterator\Repository
     */
    function __invoke($index = null)
    {
        return $this->getIterator($index);
    }

    /**
     * Create automatic getters and setters for an index
     *
     * @param $name
     * @param $arguments
     *
     * @return bool|\Mithril\Data\AbstractElement|\Mithril\Data\AbstractElement[]
     */
    function __call($name, $arguments)
    {
        $action = strtolower(substr($name, 0, 3));
        $index = lcfirst(substr($name, 3));
        if (strtolower(substr($name, 0, 6)) == 'findby') {
            $action = 'findby';
            $index = lcfirst(substr($name, 6));
        }
        if ($action == 'get') {
            if (!count($arguments)) {
                return $this->getIterator($name);
            }
            return $this->get($arguments[0], $index);
        } elseif ($action == 'has') {
            if (!count($arguments)) {
                throw new \RuntimeException("{$name}: you must specify one parameter");
            }
            return $this->has($arguments[0], $index);
        } elseif ($action == 'findby') {
            array_unshift($arguments, $index);
            return call_user_func_array([$this, 'find'], $arguments);
        } elseif (array_key_exists($name, $this->indexes)) {
            if (!count($arguments)) {
                return $this->getIterator($name);
            }
            return $this->get($arguments[0], $name);
        }
        throw new \RuntimeException("Undefined method {$name}.");
    }

    /**
     * Allow json serialization of this object
     *
     * @return array
     */
    function jsonSerialize()
    {
        return $this->data;
    }

    /**
     * Count elements of this repository
     *
     * @param null|string $index
     *
     * @return int
     */
    public function count($index = null)
    {
        if ($index === null) {
            return count($this->data);
        } else {
            if (array_key_exists($index, $this->indexes)) {
                return count($this->indexedData[$index]);
            }
        }
        return 0;
    }

}