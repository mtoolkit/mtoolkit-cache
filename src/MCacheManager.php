<?php
namespace mtoolkit\cache;

/**
 * Class MCacheManager is the base class for the implementations which provide a kind of caching.
 *
 * @package mtoolkit\cache
 */
abstract class MCacheManager
{
    /**
     * @var array
     */
    private $options;

    /**
     * MCacheManager constructor.
     *
     * @param array $options
     */
    public function __construct( array $options )
    {
        $this->options = $options;
    }

    /**
     * @param array $keys
     * @return MCacheItem[]
     */
    public abstract function getItems( array $keys );

    /**
     * @param string $key
     * @return MCacheItem[]
     */
    public function getItemsByKey( $key )
    {
        return $this->getItems( array( $key ) );
    }

    /**
     * @param array $keys
     * @return MCacheItem[]
     */
    public function getItemsByKeys( array $keys )
    {
        return $this->getItems( $keys );
    }

    /**
     * @param array $keys
     */
    public abstract function deleteItems( array $keys );

    /**
     * @param string $key
     */
    public function deleteItemsByKey( $key )
    {
        $this->deleteItems( array( $key ) );
    }

    /**
     * @param array $keys
     */
    public function deleteItemsByKeys( array $keys )
    {
        $this->deleteItems( $keys );
    }

    /**
     * @param MCacheItem $item
     * @return bool
     */
    public abstract function addItem( MCacheItem $item );

    /**
     * @param array $items
     */
    public function addItems( array $items )
    {
        array_map( array( $this, 'addItem' ), $items );
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @return void
     */
    public abstract function flush(  );

}