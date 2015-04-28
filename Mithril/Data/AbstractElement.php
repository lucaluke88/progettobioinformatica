<?php
/**
 * MITHrIL: miRNA enriched pathway impact analysis
 * REST Web Service
 *
 * @author S. Alaimo (alaimos at dmi.unict.it)
 */

namespace Mithril\Data;

/**
 * Class AbstractElement
 * @package Mithril\Data
 */
abstract class AbstractElement implements \ArrayAccess, \JsonSerializable
{
    const TYPE_SINGLE              = 'single';
    const TYPE_MULTI               = 'multi';
    const TYPE_SINGLE_RELATIONSHIP = 'singleRelationship';
    const TYPE_MULTI_RELATIONSHIP  = 'multiRelationship';

    protected $idField = null;

    protected $config = [
        /*'example' => [
            'type'       => 'single/multi/singleRelationship/multiRelationship',
            'class'      => 'ClassName', //if relationship
            'default'    => null,
            'serializer' => null, //serializer function
            'parser'     => null, //parser function on set
            'output'     => null, //output function on get
        ]*/
    ];

    protected $data = [];

    public function __construct($data = [])
    {
        $this->set($data);
    }

    /**
     * Add a value to a variable
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return $this
     */
    public function add($name, $value)
    {
        if (array_key_exists($name, $this->config)) {
            if (is_callable($this->config[$name]['parser'])) {
                $value = call_user_func($this->config[$name]['parser'], $value);
            }
            $type = $this->config[$name]['type'];
            $className = $this->config[$name]['class'];
            if ($type == self::TYPE_MULTI_RELATIONSHIP) {
                if (!is_array($value) && $value instanceof $className) {
                    $this->data[$name][] = $value;
                } elseif (is_array($value)) {
                    foreach ($value as $v) {
                        if ($v instanceof $className) {
                            $this->data[$name][] = $v;
                        }
                    }
                }
            } elseif ($type == self::TYPE_MULTI) {
                if (!is_array($value) && $value !== null) {
                    $this->data[$name][] = $value;
                } elseif (is_array($value)) {
                    foreach ($value as $v) {
                        if ($v !== null) {
                            $this->data[$name][] = $v;
                        }
                    }
                }
            }
        }
        return $this;
    }

    /**
     * Clear value of a variable
     *
     * @param string $name the name of the variable
     *
     * @return $this
     */
    public function clear($name)
    {
        return $this->set($name, null);
    }

    /**
     * Set the value of a variable
     *
     * @param string|array $name
     * @param mixed|null   $value
     *
     * @return $this
     */
    public function set($name, $value = null)
    {
        if (is_array($name) && $value === null) {
            foreach ($name as $key => $value) {
                $this->set($key, $value);
            }
        } elseif (is_string($name)) {
            if (array_key_exists($name, $this->config)) {
                $type = $this->config[$name]['type'];
                $className = $this->config[$name]['class'];
                if ($type == self::TYPE_SINGLE_RELATIONSHIP && $value !== null && !($value instanceof $className)) {
                    return $this;
                } elseif ($type == self::TYPE_MULTI_RELATIONSHIP) {
                    if (!is_array($value) && $value instanceof $className) {
                        $value = [$value];
                    } elseif (!is_array($value)) {
                        $value = [];
                    } elseif (is_array($value)) {
                        $value = array_filter($value, function ($v) use ($className) {
                            return ($v instanceof $className);
                        });
                    } else {
                        return $this;
                    }
                } elseif ($type == self::TYPE_MULTI) {
                    if (!is_array($value) && $value !== null) {
                        $value = [$value];
                    } elseif (!is_array($value) && $value === null) {
                        $value = [];
                    } else {
                        $value = array_filter($value, function ($v) {
                            return ($v !== null);
                        });
                    }
                }
                if (is_callable($this->config[$name]['parser'])) {
                    $value = call_user_func($this->config[$name]['parser'], $value);
                }
                $this->data[$name] = $value;
            }
        }
        return $this;
    }

    /**
     * Get the value of a variable
     *
     * @param string $name
     *
     * @return mixed
     */
    public function get($name)
    {
        if (array_key_exists($name, $this->config)) {
            $tmp = (isset($this->data[$name])) ? $this->data[$name] : $this->config[$name]['default'];
            if (is_callable($this->config[$name]['output'])) {
                $tmp = call_user_func($this->config[$name]['output'], $tmp);
            }
            return $tmp;
        }
        throw new \RuntimeException("Undefined field {$name}.");
    }

    /**
     * Check if a variable exists
     *
     * @param string $name
     *
     * @return bool
     */
    public function exists($name)
    {
        return array_key_exists($name, $this->config);
    }

    /**
     * Automatic implementation of getters as array: isset($this['something'])
     *
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists($offset)
    {
        return $this->exists($offset);
    }

    /**
     * Automatic implementation of getters as array: echo $this['something']
     *
     * @param mixed $offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * Automatic implementation of setters as array: $this['something'] = somethingelse;
     *
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    /**
     * Automatic implementation of setters as array: unset($this['something'])
     *
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        $this->clear($offset);
    }

    /**
     * Automatic implementation of getters and setters
     *
     * @param string $name
     * @param array  $arguments
     *
     * @return $this|mixed
     */
    function __call($name, $arguments)
    {
        $action = strtolower(substr($name, 0, 3));
        $field = lcfirst(substr($name, 3));
        if ($action == 'cle') {
            $action = strtolower(substr($name, 0, 5));
            $field = lcfirst(substr($name, 5));
        }
        switch ($action) {
            case 'add':
                if (!count($arguments)) {
                    throw new \RuntimeException("{$name}: you must specify one parameter");
                }
                return $this->add($field, $arguments[0]);
                break;
            case 'get':
                return $this->get($field);
                break;
            case 'set':
                if (!count($arguments)) {
                    throw new \RuntimeException("{$name}: you must specify one parameter");
                }
                return $this->set($field, $arguments[0]);
                break;
            case 'clear':
                return $this->clear($field);
                break;
        }
        throw new \RuntimeException("Undefined method {$name}.");
    }

    /**
     * Automatic implementation of getters as properties: echo $this->something
     *
     * @param $name
     *
     * @return mixed
     */
    function __get($name)
    {
        return $this->get($name);
    }

    /**
     * Automatic implementation of setters as properties: $this->something = somethingelse
     *
     * @param $name
     *
     * @return mixed
     */
    function __set($name, $value)
    {
        $this->set($name, $value);
    }

    /**
     * Automatic implementation of getters as properties: isset($this->something)
     *
     * @param $name
     *
     * @return mixed
     */
    function __isset($name)
    {
        return $this->exists($name);
    }

    /**
     * Automatic implementation of setters as properties: unset($this->something)
     *
     * @param $name
     *
     * @return mixed
     */
    function __unset($name)
    {
        $this->clear($name);
    }

    /**
     * Allow usage of this function as a method
     *
     * @param string|array $name
     * @param mixed|null   $value
     *
     * @return $this|mixed
     */
    function __invoke($name, $value = null)
    {
        if ($value === null && is_string($name)) {
            return $this->get($name);
        } else if ($value === null && is_array($name)) {
            return $this->set($name);
        } else {
            return $this->set($name, $value);
        }
    }

    /**
     * Allow json serialization of this object
     * @return array
     */
    function jsonSerialize()
    {
        $data = $this->data;
        foreach ($this->config as $name => $c) {
            if (is_callable($c['serializer'])) {
                $data[$name] = call_user_func($c['serializer'], $data['name']);
            }
        }
        return $data;
    }

    /**
     * Get the id field
     *
     * @return string
     */
    public function getIdField()
    {
        return $this->idField;
    }

    /**
     * Get the id
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->get($this->idField);
    }

    /**
     * Check if this object is equal to another one
     *
     * @param \Mithril\Data\AbstractElement $e
     *
     * @return bool
     */
    public function isEqual(AbstractElement $e)
    {
        if (get_class($this) != get_class($e)) return false;
        if ($this == $e) return true;
        if ($this->idField !== null) {
            return ($this->get($this->idField) == $e->get($this->idField));
        } else {
            $result = true;
            foreach ($this->config as $field => $c) {
                if (!isset($c['doNotCompare']) || !$c['doNotCompare']) {
                    if (is_object($this->data[$field]) && method_exists($this->data[$field], 'isEqual')) {
                        if (is_null($this->data[$field]) && is_null($e->data[$field])) {
                            $result = $result && true;
                        } else if (is_null($this->data[$field]) || is_null($e->data[$field])) {
                            $result = false;
                        } else {
                            $result = $result && $this->data[$field]->isEqual($e->data[$field]);
                        }
                    } else {
                        $result = $result && $this->data[$field] == $e->data[$field];
                    }
                }
            }
            return $result;
        }
    }

}