<?php
namespace mtoolkit\cache;

/**
 * Interface MCacheFactory
 *
 * @package mtoolkit\cache
 */
interface MCacheFactory
{
    /**
     * @param MCacheConfiguration $configuration
     * @return MCacheManager
     */
    static function getManager( MCacheConfiguration $configuration );
}