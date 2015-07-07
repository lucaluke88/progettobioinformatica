<?php
/**
 * MITHrIL: miRNA enriched pathway impact analysis
 * REST Web Service
 *
 * @author S. Alaimo (alaimos at dmi.unict.it)
 */

namespace Mithril\Pathway;

use Mithril\Data\AbstractElement;
use Mithril\Pathway\Entry\Entry;
use Mithril\Pathway\Relation\Relation;

/**
 * Class Pathway
 * @property string $id
 * @property string $organism
 * @property string $title
 * @property string $image
 * @property array  $links
 * @method array getLinks()
 * @method \Mithril\Pathway\Pathway addLinks($links)
 * @method \Mithril\Pathway\Pathway setLinks($links)
 * @method \Mithril\Pathway\Pathway clearLinks()
 * @package Mithril\Pathway
 */
class Pathway extends AbstractElement
{

    const PRUNE = 'prune';
    const STOP  = 'stop';
    const NEXT  = 'next';

    protected $idField = 'id';

    protected $config = [
        'id'       => [
            'type'       => AbstractElement::TYPE_SINGLE,
            'class'      => null,
            'default'    => null,
            'serializer' => null,
            'parser'     => null,
            'output'     => null,
        ],
        'organism' => [
            'type'       => AbstractElement::TYPE_SINGLE,
            'class'      => null,
            'default'    => null,
            'serializer' => null,
            'parser'     => null,
            'output'     => null,
        ],
        'title'    => [
            'type'       => AbstractElement::TYPE_SINGLE,
            'class'      => null,
            'default'    => null,
            'serializer' => null,
            'parser'     => null,
            'output'     => null,
        ],
        'image'    => [
            'type'       => AbstractElement::TYPE_SINGLE,
            'class'      => null,
            'default'    => null,
            'serializer' => null,
            'parser'     => null,
            'output'     => null,
        ],
        'links'    => [
            'type'       => AbstractElement::TYPE_MULTI,
            'class'      => null,
            'default'    => [],
            'serializer' => null,
            'parser'     => null,
            'output'     => null,
        ],
    ];

    /**
     * @var \Mithril\Pathway\Repository\Entry\Entry
     */
    protected $entriesRepository;

    /**
     * @var \Mithril\Pathway\Repository\Relation\Relation
     */
    protected $relationsRepository;

    /**
     * @return Repository\Entry\Entry
     */
    public function getEntriesRepository()
    {
        return $this->entriesRepository;
    }

    /**
     * @param Repository\Entry\Entry $entriesRepository
     *
     * @return $this
     */
    public function setEntriesRepository($entriesRepository)
    {
        $this->entriesRepository = $entriesRepository;
        return $this;
    }

    /**
     * @return Repository\Relation\Relation
     */
    public function getRelationsRepository()
    {
        return $this->relationsRepository;
    }

    /**
     * @param Repository\Relation\Relation $relationsRepository
     *
     * @return $this
     */
    public function setRelationsRepository($relationsRepository)
    {
        $this->relationsRepository = $relationsRepository;
        return $this;
    }

    /**
     * @return \Mithril\Pathway\Entry\Entry[]
     */
    public function getEntries()
    {
        $list = $this->entriesRepository->byPathway($this);
        if (!is_array($list) && !is_null($list) && ($list instanceof Entry)) {
            $list = [$list];
        } elseif (!is_array($list)) {
            $list = [];
        }
        return $list;
    }

    /**
     * @return \Mithril\Pathway\Relation\Relation[]
     */
    public function getRelations()
    {
        $list = $this->relationsRepository->byPathway($this);
        if (!is_array($list) && !is_null($list) && ($list instanceof Relation)) {
            $list = [$list];
        } elseif (!is_array($list)) {
            $list = [];
        }
        return $list;
    }

    /**
     * @param \Mithril\Pathway\Entry\Entry $entry
     * @param callable                     $action
     */
    public function runUpstream(Entry $entry, $action)
    {
        if (!is_callable($action)) {
            throw new \RuntimeException("action should be a callback");
        }
        $traversalGuide = [];
        array_push($traversalGuide, $entry);
        while (count($traversalGuide) > 0) {
            $next = array_pop($traversalGuide);
            $result = call_user_func($action, $next, $this);
            if ($result == self::STOP) {
                break;
            } elseif ($result == self::NEXT) {
                foreach ($entry->ingoingRelations($this) as $in) { // for each edge entry1 -> next
                    /** @var \Mithril\Pathway\Relation\Relation $in */
                    array_push($traversalGuide, $in->entry1);
                }
            }
        }
    }

    /**
     * @param \Mithril\Pathway\Entry\Entry $entry
     * @param callable                     $action
     */
    public function runDownstream(Entry $entry, $action)
    {
        if (!is_callable($action)) {
            throw new \RuntimeException("action should be a callback");
        }
        $traversalGuide = [];
        array_push($traversalGuide, $entry);
        while (count($traversalGuide) > 0) {
            $next = array_pop($traversalGuide);
            $result = call_user_func($action, $next, $this);
            if ($result == self::STOP) {
                break;
            } elseif ($result == self::NEXT) {
                foreach ($entry->outgoingRelations($this) as $out) { // for each edge next -> entry2
                    /** @var \Mithril\Pathway\Relation\Relation $out */
                    array_push($traversalGuide, $out->entry2);
                }
            }
        }
    }
}