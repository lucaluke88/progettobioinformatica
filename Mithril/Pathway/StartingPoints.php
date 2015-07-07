<?php
/**
 * MITHrIL: miRNA enriched pathway impact analysis
 * REST Web Service
 *
 * @author S. Alaimo (alaimos at dmi.unict.it)
 */

namespace Mithril\Pathway;

class StartingPoints
{

    /**
     * @var \Mithril\Pathway\Repository\Pathway
     */
    protected $pathwayRepository;

    /**
     * @var \Mithril\Pathway\Entry\Entry[]
     */
    protected $startingPoints = [];

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
     * Find all starting points from node $node in pathway $pathway <br>
     * $visited is used to make a dfs traversal. $endpoints is the array of all endpoints already found
     *
     * @param \Mithril\Pathway\Pathway     $pathway
     * @param \Mithril\Pathway\Entry\Entry $node
     * @param array                        $visited
     * @param array                        $startingPoints
     *
     * @return void
     */
    protected function findStartingPointsByPathwayAndNode(Pathway $pathway, Entry\Entry $node, array &$visited,
                                                          array &$startingPoints)
    {
        $pathway->runUpstream($node, function (Entry\Entry $n, Pathway $pathway) use (&$visited, &$startingPoints) {
            if (!array_key_exists($n->id, $visited)) {
                $visited[$n->id] = null;
                if (!count($n->ingoingRelations($pathway))) {
                    if (count($n->outgoingRelations($pathway)) > 0) { // se è un nodo isolato lo ignoro
                        $startingPoints[$n->id] = $n;
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
    protected function findStartingPointsByPathway(Pathway $pathway)
    {
        $visited = [];
        $startingPoints = [];
        foreach ($pathway->getEntries() as $entry) {
            /** @var \Mithril\Pathway\Entry\Entry $entry */
            $this->findStartingPointsByPathwayAndNode($pathway, $entry, $visited, $startingPoints);
        }
        return array_values($startingPoints);
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
            $this->startingPoints[$pathway->id] = $this->findStartingPointsByPathway($pathway);
        }
        return $this;
    }

    /**
     * Get all endpoints found
     *
     * @return \Mithril\Pathway\Entry\Entry[]
     */
    public function getStartingPoints()
    {
        return $this->startingPoints;
    }

    /**
     * @return bool
     */
    public function hasRun()
    {
        return $this->run;
    }

}