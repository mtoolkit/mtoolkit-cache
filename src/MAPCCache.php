<?php
namespace mtoolkit\core\cache;

/*
 * This file is part of MToolkit.
 *
 * MToolkit is free software: you can redistribute it and/or modify
 * it under the terms of the LGNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * MToolkit is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * LGNU Lesser General Public License for more details.
 *
 * You should have received a copy of the LGNU Lesser General Public License
 * along with MToolkit.  If not, see <http://www.gnu.org/licenses/>.
 * 
 * @author  Michele Pagnin
 */

use mtoolkit\core\MObject;

class MAPCCache extends MAbstractCache
{
    public function __construct( MObject $parent = null )
    {
        parent::__construct( $parent );
    }

    /**
     * Removes a stored variable from the cache.<br />
     * Returns TRUE on success or FALSE on failure.
     * 
     * @param string $key
     * @return boolean
     */
    public function delete( $key )
    {
        return apc_delete($key);
    }

    /**
     * Fetchs a stored variable from the cache.<br />
     * The stored variable or array of variables on success; null on failure
     * 
     * @param string $key
     * @return mixed
     */
    public function fetch( $key )
    {
        $return= apc_fetch($key);
        
        if( $return===false )
        {
            return null;
        }
        
        return $return;
    }

    /**
     * Clears the APC user cache.
     * Returns TRUE always.
     * 
     * @return boolean
     */
    public function flush()
    {
        return apc_clear_cache('user');
    }
    
    /**
     * Clears the APC cache.<br />
     * If cache_type is "user", the user cache will be cleared; otherwise, the 
     * system cache (cached files) will be cleared.<br />
     * Returns TRUE always.
     * 
     * @param string $cacheType
     * @return boolean
     */
    public function clearCache($cacheType = "")
    {
        return apc_clear_cache($cacheType);
    }

    /**
     * cache a variable in the data store.<br />
     * Returns TRUE on success or FALSE on failure, on an array with error keys.
     * 
     * @param string $key
     * @param mixed $value
     * @param int $ttl
     * @return mixed
     */
    public function store( $key, $value, $ttl = 0 )
    {
        return apc_store( $key, $value, $ttl );
    }

}
