<?php
/**
 * MITHrIL: miRNA enriched pathway impact analysis
 * REST Web Service
 *
 * @author S. Alaimo (alaimos at dmi.unict.it)
 */

namespace Mithril\Pathway\Repository\Entry;

use \Mithril\Data\AbstractRepository;

/**
 * Class Entry
 * @method \Mithril\Pathway\Entry\Entry|\Mithril\Pathway\Entry\Entry[] get($what, $index = null)
 * @method \Mithril\Pathway\Entry\Entry getByName($title = "")
 * @method \Mithril\Pathway\Entry\Entry byName($title = "")
 * @method \Mithril\Pathway\Entry\Entry[] getByAlias($alias = "")
 * @method \Mithril\Pathway\Entry\Entry[] byAlias($alias = "")
 * @method \Mithril\Pathway\Entry\Entry[] getByType($type = "")
 * @method \Mithril\Pathway\Entry\Entry[] byType($type = "")
 * @method \Mithril\Pathway\Entry\Entry[] getByPathway($pathway = "")
 * @method \Mithril\Pathway\Entry\Entry[] byPathway($pathway = "")
 * @package Mithril\Pathway\Repository
 */
class Entry extends AbstractRepository
{
    protected $className = '\Mithril\Pathway\Entry\Entry';

    protected $indexes = [
        'byName'  => 'name',
        'byAlias' => 'aliases',
        'byType'  => 'type',
    ];

    /**
     * @var \Mithril\Pathway\Repository\Relation\Relation
     */
    protected $relationsRepository;

    public function __construct()
    {
        $this->indexes['byPathway'] = function (\Mithril\Pathway\Entry\Entry $e) {
            $ps = [];
            foreach ($e->contained as $p) {
                /** @var \Mithril\Pathway\Contained\Pathway $p */
                $ps[] = $p->pathway;
            }
            return $ps;
        };
        parent::__construct();
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
            parent::add($data);
        }
        return $this;
    }
}