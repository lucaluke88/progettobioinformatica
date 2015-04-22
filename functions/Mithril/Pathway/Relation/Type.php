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
 * Class Type
 * @property string $name
 * @package Mithril\Pathway\Relation
 */
class Type extends AbstractElement
{

    protected $idField = 'name';

    protected $config = [
        'name'  => [
            'type'       => AbstractElement::TYPE_SINGLE,
            'class'      => null,
            'default'    => null,
            'serializer' => null,
            'parser'     => null,
            'output'     => null,
        ],
    ];

}