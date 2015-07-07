<?php
/**
 * MITHrIL: miRNA enriched pathway impact analysis
 * REST Web Service
 *
 * @author S. Alaimo (alaimos at dmi.unict.it)
 */

namespace Mithril\Pathway\Contained;

use \Mithril\Data\AbstractElement;


/**
 * Class Pathway
 * @property \Mithril\Pathway\Pathway $pathway
 * @property \Mithril\Pathway\Graphic $graphic
 * @package Mithril\Pathway\Contained
 */
class Pathway extends AbstractElement
{

    protected $config = [
        'pathway' => [
            'type'       => AbstractElement::TYPE_SINGLE_RELATIONSHIP,
            'class'      => '\Mithril\Pathway\Pathway',
            'default'    => null,
            'serializer' => null,
            'parser'     => null,
            'output'     => null,
        ],
        'graphic' => [
            'type'       => AbstractElement::TYPE_SINGLE_RELATIONSHIP,
            'class'      => '\Mithril\Pathway\Graphic',
            'default'    => [],
            'serializer' => null,
            'parser'     => null,
            'output'     => null,
        ],
    ];



}