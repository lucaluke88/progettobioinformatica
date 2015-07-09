<?php
/**
 * MITHrIL: miRNA enriched pathway impact analysis
 * REST Web Service
 *
 * @author S. Alaimo (alaimos at dmi.unict.it)
 */

namespace Mithril\Pathway\Entry;

use \Mithril\Data\AbstractElement;
use Mithril\Pathway\Pathway;
use Mithril\Pathway\Relation\Relation;

/**
 * Class Entry
 * @property string                                  $id
 * @property array                                $aliases
 * @property string                               $name
 * @property \Mithril\Pathway\Entry\Type          $type
 * @property array                                $links
 * @property \Mithril\Pathway\Contained\Pathway[] $contained
 * @method array getAliases()
 * @method \Mithril\Pathway\Entry\Entry   addAliases($aliases)
 * @method \Mithril\Pathway\Entry\Entry   setAliases($aliases)
 * @method \Mithril\Pathway\Entry\Entry   clearAliases()
 * @method array getLinks()
 * @method \Mithril\Pathway\Entry\Entry   addLinks($links)
 * @method \Mithril\Pathway\Entry\Entry   setLinks($links)
 * @method \Mithril\Pathway\Entry\Entry   clearLinks()
 * @method \Mithril\Pathway\Contained\Pathway[] getContained()
 * @method \Mithril\Pathway\Entry\Entry   addContained($contained)
 * @method \Mithril\Pathway\Entry\Entry   setContained($contained)
 * @method \Mithril\Pathway\Entry\Entry   clearContained()
 * @package Mithril\Pathway
 */
class Entry extends AbstractElement
{

    protected $idField = 'id';

    protected $config = [
        'id'        => [
            'type'       => AbstractElement::TYPE_SINGLE,
            'class'      => null,
            'default'    => null,
            'serializer' => null,
            'parser'     => null,
            'output'     => null,
        ],
        'aliases'   => [
            'type'       => AbstractElement::TYPE_MULTI,
            'class'      => null,
            'default'    => [],
            'serializer' => null,
            'parser'     => null,
            'output'     => null,
        ],
        'name'      => [
            'type'       => AbstractElement::TYPE_SINGLE,
            'class'      => null,
            'default'    => null,
            'serializer' => null,
            'parser'     => null,
            'output'     => null,
        ],
        'type'      => [
            'type'       => AbstractElement::TYPE_SINGLE_RELATIONSHIP,
            'class'      => '\Mithril\Pathway\Entry\Type',
            'default'    => [],
            'serializer' => null,
            'parser'     => null,
            'output'     => null,
        ],
        'links'     => [
            'type'       => AbstractElement::TYPE_MULTI,
            'class'      => null,
            'default'    => null,
            'serializer' => null,
            'parser'     => null,
            'output'     => null,
        ],
        'contained' => [
            'type'       => AbstractElement::TYPE_MULTI_RELATIONSHIP,
            'class'      => '\Mithril\Pathway\Contained\Pathway',
            'default'    => [],
            'serializer' => null,
            'parser'     => null,
            'output'     => null,
        ],
    ];

    /**
     * @var \Mithril\Pathway\Repository\Relation\Relation
     */
    protected $relationsRepository;

    /**
     * @return \Mithril\Pathway\Repository\Relation\Relation
     */
    public function getRelationsRepository()
    {
        return $this->relationsRepository;
    }

    /**
     * @param \Mithril\Pathway\Repository\Relation\Relation $relationsRepository
     *
     * @return $this
     */
    public function setRelationsRepository($relationsRepository)
    {
        $this->relationsRepository = $relationsRepository;
        return $this;
    }

    /**
     * @param \Mithril\Pathway\Pathway $pathway
     *
     * @return \Mithril\Pathway\Relation\Relation[]
     */
    public function outgoingRelations(Pathway $pathway = null)
    {
        $list = $this->relationsRepository->byEntry1($this);
        if (!is_array($list) && !is_null($list) && ($list instanceof Relation)) {
            $list = [$list];
        } elseif (!is_array($list)) {
            $list = [];
        }
        if ($pathway !== null) {
            $list = array_filter($list, function (Relation $r) use ($pathway) {
                return (in_array($pathway, $r->pathways));
            });
        }
        return $list;
    }

    /**
     * @param \Mithril\Pathway\Pathway $pathway
     *
     * @return \Mithril\Pathway\Relation\Relation[]
     */
    public function ingoingRelations(Pathway $pathway = null)
    {
        $list = $this->relationsRepository->byEntry2($this);
        if (!is_array($list) && !is_null($list) && ($list instanceof Relation)) {
            $list = [$list];
        } elseif (!is_array($list)) {
            $list = [];
        }
        if ($pathway !== null) {
            $list = array_filter($list, function (Relation $r) use ($pathway) {
                return (in_array($pathway, $r->pathways));
            });
        }
        return $list;
    }

}