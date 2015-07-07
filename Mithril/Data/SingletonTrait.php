<?php
/**
 * MITHrIL: miRNA enriched pathway impact analysis
 * REST Web Service
 *
 * @author S. Alaimo (alaimos at dmi.unict.it)
 */

namespace Mithril\Data;

trait SingletonTrait
{
    /**
     * @var \Mithril\Data\SingletonTrait
     */
    private static $instance = null;

    /**
     * @return \Mithril\Data\SingletonTrait
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }


    private function __construct()
    {
    }
}