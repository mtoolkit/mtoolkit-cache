<?php
namespace mtoolkit\cache\file;

use mtoolkit\cache\MCacheItem;
use mtoolkit\cache\MCacheManager;
use mtoolkit\cache\transformer\ItemTransformer;
use mtoolkit\core\MDataType;

/**
 * Class MFileCacheManager
 *
 * @package mtoolkit\cache\file
 */
class MFileCacheManager extends MCacheManager
{
    const CACHE_FOLDER_NAME = 'cache';
    const FILE_DATE_FORMAT = 'Y-m-d H:i:s';

    /**
     * @var string
     */
    private $path;

    /**
     * MPDOCacheManager constructor.
     *
     * @param array $option Must contains an item called 'path' containing the path to use to store the cache item.
     * @throws \Exception
     */
    public function __construct( array $option )
    {
        parent::__construct( $option );

        if( isset( $option['path'] ) === false )
        {
            throw new \Exception( 'Mandatory options are not set!' );
        }

        $this->setPath( $option['path'] );

        if( file_exists( $this->path ) === false )
        {
            throw new \Exception( sprintf( 'Path \'%s\' does not exist!', $this->path ) );
        }

        if( substr( $this->path, strlen( $this->path ) - 1 ) !== DIRECTORY_SEPARATOR )
        {
            $this->path = $this->path . DIRECTORY_SEPARATOR;
        }

        if( file_exists( $this->getBasePath() ) === false )
        {
            mkdir( $this->getBasePath() );
        }
    }

    /**
     * @param array $keys
     * @return MCacheItem[]
     */
    public function getItems( array $keys )
    {
        $array = array();
        foreach( $keys as $key )
        {
            $now = new \DateTime();
            $path = $this->getBackupFilePath( $key );

            if( file_exists( $path ) )
            {
                $json = file_get_contents( $path );
                $array = json_decode( $json, true );
                $item = ItemTransformer::fromArray( $array );
                $endValidation = \DateTime::createFromFormat( self::FILE_DATE_FORMAT, $array['end_validation'] );
                if( $endValidation >= $now )
                {
                    $array[] = $item;
                }
            }
        }

        return $array;
    }

    /**
     * @param array $keys
     */
    public function deleteItems( array $keys )
    {
        array_map(
            function ( $key )
            {
                return unlink( $this->getBackupFilePath( $key ) );
            },
            $keys
        );
    }

    /**
     * @param MCacheItem $item
     * @return bool
     */
    public function addItem( MCacheItem $item )
    {
        $inserted = new \DateTime( 'now' );
        $endValidation = new \DateTime( 'now' );
        $endValidation = $endValidation->add( new \DateInterval( sprintf( 'PT%dS', $item->getTtl() ) ) );

        $array = ItemTransformer::toArray( $item );
        $array['end_validation'] = $endValidation->format( self::FILE_DATE_FORMAT );
        $array['inserted'] = $inserted->format( self::FILE_DATE_FORMAT );
        $json = json_encode( $array );
        $filePath = $this->getBackupFilePath( $item->getKey() );

        if( file_exists( $filePath ) )
        {
            unlink( $this->getBackupFilePath( $item->getKey() ) );
        }

        return (
            file_put_contents(
                $filePath,
                $json
            ) !== false
        );
    }

    private function getBackupFilePath( $key )
    {
        MDataType::mustBe( MDataType::STRING | MDataType::NULL );
        $path = $this->getBasePath();
        $path .= $key . '.backup';

        return $path;
    }

    private function getBasePath()
    {
        return $this->path . self::CACHE_FOLDER_NAME . DIRECTORY_SEPARATOR;
    }

    /**
     * @param string $path
     * @return MFileCacheManager
     */
    private function setPath( $path )
    {
        MDataType::mustBe( MDataType::STRING );
        $this->path = $path;

        return $this;
    }

    /**
     * @return void
     */
    public function flush()
    {
        MDataType::mustBe( MDataType::STRING | MDataType::NULL );

        $this->removeFolder( $this->getBasePath() );
    }

    /**
     * @param string $path
     * @return void
     */
    private function removeFolder( $path )
    {
        MDataType::mustBe( MDataType::STRING );

        if( !file_exists( $path ) )
        {
            return;
        }

        if( !is_dir( $path ) )
        {
            unlink( $path );
        }

        foreach( scandir( $path ) as $item )
        {
            if( $item == '.' || $item == '..' )
            {
                continue;
            }

            $this->removeFolder( $path . DIRECTORY_SEPARATOR . $item );
        }

        rmdir( $path );
    }
}