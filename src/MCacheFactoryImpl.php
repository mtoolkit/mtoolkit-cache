<?php
namespace mtoolkit\cache;

use mtoolkit\cache\file\MFileCacheManager;
use mtoolkit\cache\apc\MAPCCacheManager;
use mtoolkit\cache\mysql\MMySQLCacheManager;

/**
 * Class MCacheFactoryImpl
 *
 * @package mtoolkit\cache
 */
class MCacheFactoryImpl implements MCacheFactory
{
    /**
     * @param MCacheConfiguration $configuration
     * @return MCacheManager
     */
    static function getManager( MCacheConfiguration $configuration )
    {
        switch( $configuration->getType() )
        {
            case MCacheType::MYSQL:
                return new MMySQLCacheManager( $configuration->getOption() );
            case MCacheType::FILE:
                return new MFileCacheManager( $configuration->getOption() );
            case MCacheType::APC:
                return new MAPCCacheManager( $configuration->getOption() );
        }

        return null;
    }
}