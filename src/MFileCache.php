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
 * MFileCache class extends MAbstractCache.<br />
 * This class stores cache records into files (in a chosen <i>path</i>).
 */
class MFileCache extends MAbstractCache
{
    /**
     * MFileCache constructor.
     * @param MObject|null $parent
     */
    public function __construct( MObject $parent = null )
    {
        parent::__construct( $parent );
    }

    /**
     * @var string 
     */
    const CACHE_FILE_PREFIX = "cache_";
    const DELIMITER = "$$$$";
    const FILE_EXTENSION = "mcache";

    /**
     * @var string
     */
    private $path = null;

    /**
     * Return the path of the cache.
     * 
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set the path of the cache.
     * 
     * @param string $path
     * @return MFileCache
     */
    public function setPath( $path )
    {
        $this->path = $path;
        $len = strlen( $this->path );

        if( $this->path{$len - 1} != DIRECTORY_SEPARATOR )
        {
            $this->path.=DIRECTORY_SEPARATOR;
        }

        return $this;
    }

    /**
     * Remove a single cache file.
     * 
     * @param string $key
     */
    public function delete( $key )
    {
        if( file_exists( $this->getFileName( $key ) ) === true )
        {
            unlink( $this->getFileName( $key ) );
        }
    }

    /**
     * Remove all cache files.
     */
    public function flush()
    {
        $files = glob( $this->path . MFileCache::CACHE_FILE_PREFIX . '*' ); // get all file names
        foreach( $files as $file )
        {
            if( is_file( $file ) )
                unlink( $file );
        }
    }

    /**
     * Return the content of a cache file with <i>$key</i>.
     * 
     * @param string $key
     * @return string|null
     */
    public function fetch( $key )
    {
        if( file_exists( $this->getFileName( $key ) ) === false )
        {
            return null;
        }

        $fileContent = file_get_contents( $this->getFileName( $key ) );

        $separatorPosition = strrpos( $fileContent, MFileCache::DELIMITER );
        $expired = substr( $fileContent, 0, $separatorPosition );
        $cache = substr( $fileContent, $separatorPosition + strlen( MFileCache::DELIMITER ) );
        
        if( $expired==-1 || $expired > time() )
        {
            return unserialize($cache);
        }
        
        $this->delete($key);
        return null;
    }

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
    public function store( $key, $value, $ttl = -1 )
    {
        $this->delete($key);
        
        $expired=time()+$ttl;
        
        $success = file_put_contents( $this->getFileName( $key ), $expired . MFileCache::DELIMITER . serialize($value) );
        return ($success != false);
    }

    private function getFileName( $key )
    {
        return $this->path . MFileCache::CACHE_FILE_PREFIX . $key . '.' . MFileCache::FILE_EXTENSION;
    }

}
