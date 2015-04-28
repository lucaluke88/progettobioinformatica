<?php
/**
 * MITHrIL: miRNA enriched pathway impact analysis
 * REST Web Service
 *
 * @author S. Alaimo (alaimos at dmi.unict.it)
 */

namespace Mithril\Pathway;

use \Mithril\Data\AbstractElement;

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
            'default'    => null,
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
        return $this->entriesRepository->byPathway($this);
    }

    /**
     * @return \Mithril\Pathway\Relation\Relation[]
     */
    public function getRelations()
    {
        return $this->relationsRepository->byPathway($this);
    }
}