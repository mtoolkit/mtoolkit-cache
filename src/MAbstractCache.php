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

/**
 * Caching enables you to store data in memory for rapid access. When the data 
 * is accessed again, applications can get the data from the cache instead of 
 * retrieving it from the original source. This can improve performance and 
 * scalability. In addition, caching makes data available when the data source 
 * is temporarily unavailable.<br />
 * The MToolkit Framework provides caching functionality that you can use to 
 * improve the performance and scalability of you PHP project.
 */
abstract class MAbstractCache extends MObject
{
    /**
     * @param \MToolkit\Core\MObject $parent
     */
    public function __construct( MObject $parent = null )
    {
        parent::__construct( $parent );
    }

    /**
     * Remove a single cache record.
     * 
     * @param string $key
     */
    public abstract function delete( $key );

    /**
     * Remove all cache records.
     */
    public abstract function flush();

    /**
     * Return the content of a cache record with <i>$key</i>.
     * 
     * @param string $key
     * @return string|null
     */
    public abstract function fetch( $key );

    /**
     * Store a <i>$value</i> in a cache record with <i>$key</i>.
     * Time To Live; seconds. After the ttl has 
     * passed, the stored variable will be expunged from the cache (on the next 
     * request). If no ttl is supplied (or if the ttl is 0), the value will 
     * persist until it is removed from the cache manually, or otherwise fails 
     * to exist in the cache (clear, restart, etc.).
     * 
     * @param string $key
     * @param string $value
     * @param int $ttl
     * @return bool
     */
    public abstract function store( $key, $value, $ttl = 0 );
}

