<?php
/**
 * MITHrIL: miRNA enriched pathway impact analysis
 * REST Web Service
 *
 * @author S. Alaimo (alaimos at dmi.unict.it)
 */

namespace Mithril\Data;


interface WriterInterface
{

    /**
     * @return string
     */
    public function write();

    /**
     * @param string $filename
     *
     * @return int
     */
    public function writeAndSave($filename);

}