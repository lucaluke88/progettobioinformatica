<?php
/**
 * MITHrIL: miRNA enriched pathway impact analysis
 * REST Web Service
 *
 * @author S. Alaimo (alaimos at dmi.unict.it)
 */

namespace Mithril\Pathway\Relation;

use \Mithril\Data\AbstractElement;

/**
 * Class Relation
 * @property \Mithril\Pathway\Entry\Entry        $entry1
 * @property \Mithril\Pathway\Entry\Entry        $entry2
 * @property \Mithril\Pathway\Relation\Type      $type
 * @property \Mithril\Pathway\Relation\SubType[] $subTypes
 * @method \Mithril\Pathway\Relation\SubType[] getSubTypes()
 * @method \Mithril\Pathway\Relation\Relation addSubTypes($subTypes)
 * @method \Mithril\Pathway\Relation\Relation setSubTypes($subTypes)
 * @method \Mithril\Pathway\Relation\Relation clearSubTypes($subTypes)
 * @package Mithril\Pathway
 */
class Relation extends AbstractElement
{

    protected $config = [
        'entry1'   => [
            'type'       => AbstractElement::TYPE_SINGLE_RELATIONSHIP,
            'class'      => '\Mithril\Pathway\Entry\Entry',
            'default'    => null,
            'serializer' => null,
            'parser'     => null,
            'output'     => null,
        ],
        'entry2'   => [
            'type'       => AbstractElement::TYPE_SINGLE_RELATIONSHIP,
            'class'      => '\Mithril\Pathway\Entry\Entry',
            'default'    => null,
            'serializer' => null,
            'parser'     => null,
            'output'     => null,
        ],
        'type'     => [
            'type'       => AbstractElement::TYPE_SINGLE_RELATIONSHIP,
            'class'      => '\Mithril\Pathway\Relation\Type',
            'default'    => [],
            'serializer' => null,
            'parser'     => null,
            'output'     => null,
        ],
        'subTypes' => [
            'type'       => AbstractElement::TYPE_MULTI_RELATIONSHIP,
            'class'      => '\Mithril\Pathway\Relation\SubType',
            'default'    => [],
            'serializer' => null,
            'parser'     => null,
            'output'     => null,
        ],
        'pathways' => [
            'type'       => AbstractElement::TYPE_MULTI_RELATIONSHIP,
            'class'      => '\Mithril\Pathway\Pathway',
            'default'    => [],
            'serializer' => null,
            'parser'     => null,
            'output'     => null,
        ]
    ];

}