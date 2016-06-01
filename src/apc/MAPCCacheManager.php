<?php

namespace mtoolkit\cache\apc;

use mtoolkit\cache\MCacheItem;
use mtoolkit\cache\MCacheManager;

/**
 * Class MAPCCacheManager
 *
 * @package mtoolkit\cache\mysql
 */
class MAPCCacheManager extends MCacheManager
{
    /**
     * MPDOCacheManager constructor.
     *
     * @param array $option Unused
     * @throws \Exception
     */
    public function __construct( array $option )
    {
        parent::__construct( $option );

        if( function_exists( "apc_delete" ) === false )
        {
            throw new \Exception( "APC is not installed" );
        }
    }

    /**
     * @param array $keys
     * @return MCacheItem[]
     */
    public function getItems( array $keys )
    {
        $items = [ ];
        foreach( $keys as $key )
        {
            $items[] = apc_fetch( $key );
        }

        return $items;
    }

    /**
     * @param array $keys
     */
    public function deleteItems( array $keys )
    {
        foreach( $keys as $key )
        {
            $items[] = apc_delete( $key );
        }
    }

    /**
     * @param MCacheItem $item
     * @return bool
     */
    public function addItem( MCacheItem $item )
    {
        return apc_store( $item->getKey(), $item, $item->getTtl() );
    }


    /**
     * @return void
     */
    public function flush()
    {
        apc_clear_cache();
    }
}