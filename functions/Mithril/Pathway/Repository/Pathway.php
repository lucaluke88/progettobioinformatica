<?php
/**
 * MITHrIL: miRNA enriched pathway impact analysis
 * REST Web Service
 *
 * @author S. Alaimo (alaimos at dmi.unict.it)
 */

namespace Mithril\Pathway\Repository;

use \Mithril\Data\AbstractRepository;

/**
 * Class Pathway
 * @method \Mithril\Pathway\Pathway|\Mithril\Pathway\Pathway[] get($what, $index = null)
 * @method \Mithril\Pathway\Pathway getByTitle($title = "")
 * @method \Mithril\Pathway\Pathway byTitle($title = "")
 * @method \Mithril\Pathway\Pathway[] getByOrganism($organism = "")
 * @method \Mithril\Pathway\Pathway[] byOrganism($organism = "")
 * @package Mithril\Pathway\Repository
 */
class Pathway extends AbstractRepository
{
    protected $className = '\Mithril\Pathway\Pathway';

    protected $indexes = [
        'byTitle'    => 'title',
        'byOrganism' => 'organism'
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
     * @return \Mithril\Pathway\Repository\Entry\Entry
     */
    public function getEntriesRepository()
    {
        return $this->entriesRepository;
    }

    /**
     * @param \Mithril\Pathway\Repository\Entry\Entry $entriesRepository
     *
     * @return $this
     */
    public function setEntriesRepository($entriesRepository)
    {
        $this->entriesRepository = $entriesRepository;
        return $this;
    }

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
        if ($this->entriesRepository) {
            $this->entriesRepository->setRelationsRepository($relationsRepository);
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
            if (method_exists($data, 'setRelationsRepository')) {
                $data->setRelationsRepository($this->relationsRepository);
            }
            if (method_exists($data, 'setEntriesRepository')) {
                $data->setEntriesRepository($this->entriesRepository);
            }
            parent::add($data);
        }
        return $this;
    }


}