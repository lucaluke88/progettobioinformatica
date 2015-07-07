<?php
/**
 * MITHrIL: miRNA enriched pathway impact analysis
 * REST Web Service
 *
 * @author S. Alaimo (alaimos at dmi.unict.it)
 */

namespace Mithril\Pathway;

class Endpoints
{

    /**
     * @var \Mithril\Pathway\Repository\Pathway
     */
    protected $pathwayRepository;

    /**
     * @var \Mithril\Pathway\Entry\Entry[]
     */
    protected $endpoints = [];

    /**
     * @var bool
     */
    protected $run = false;

    /**
     * @param \Mithril\Pathway\Repository\Pathway $pathwayRepository
     */
    public function __construct(Repository\Pathway $pathwayRepository)
    {
        $this->pathwayRepository = $pathwayRepository;
    }

    /**
     * Find all endpoints starting from node $node in pathway $pathway <br>
     * $visited is used to make a dfs traversal. $endpoints is the array of all endpoints already found
     *
     * @param \Mithril\Pathway\Pathway     $pathway
     * @param \Mithril\Pathway\Entry\Entry $node
     * @param array                        $visited
     * @param array                        $endpoints
     *
     * @return void
     */
    protected function findEndpointsByPathwayAndNode(Pathway $pathway, Entry\Entry $node, array &$visited,
                                                     array &$endpoints)
    {
        $pathway->runDownstream($node, function (Entry\Entry $n, Pathway $pathway) use (&$visited, &$endpoints) {
            if (!array_key_exists($n->id, $visited)) {
                $visited[$n->id] = null;
                if (!count($n->outgoingRelations($pathway))) {
                    if (count($n->ingoingRelations($pathway)) > 0) { // se è un nodo isolato lo ignoro
                        $endpoints[$n->id] = $n;
                    }
                    return Pathway::PRUNE;
                } else {
                    return Pathway::NEXT;
                }
            }
            return Pathway::PRUNE;
        });
    }

    /**
     * Find all endpoints
     *
     * @param \Mithril\Pathway\Pathway $pathway
     *
     * @return array
     */
    protected function findEndpointsByPathway(Pathway $pathway)
    {
        $visited = [];
        $endpoints = [];
        foreach ($pathway->getEntries() as $entry) {
            /** @var \Mithril\Pathway\Entry\Entry $entry */
            $this->findEndpointsByPathwayAndNode($pathway, $entry, $visited, $endpoints);
        }
        return array_values($endpoints);
    }

    /**
     * Find all endpoints
     *
     * @return $this
     */
    public function find()
    {
        $this->run = true;
        foreach ($this->pathwayRepository as $pathway) {
            /** @var \Mithril\Pathway\Pathway $pathway */
            $this->endpoints[$pathway->id] = $this->findEndpointsByPathway($pathway);
        }
        return $this;
    }

    /**
     * Get all endpoints found
     *
     * @return \Mithril\Pathway\Entry\Entry[]
     */
    public function getEndpoints()
    {
        return $this->endpoints;
    }

    /**
     * @return bool
     */
    public function hasRun()
    {
        return $this->run;
    }

}