<?php

namespace mtoolkit\cache;

use mtoolkit\core\MDataType;

/**
 * Class MCacheItem
 *
 * @package mtoolkit\cache
 */
class MCacheItem
{
    /**
     * @var string
     */
    private $key;

    /**
     * @var string
     */
    private $value;

    /**
     * @var int Time to live in seconds
     */
    private $ttl = -1;

    /**
     * CacheItem constructor.
     */
    public function __construct()
    {
    }

    /**
     * @return int
     */
    public function getTtl()
    {
        return $this->ttl;
    }

    /**
     * @param int $ttl
     * @return MCacheItem
     */
    public function setTtl( $ttl )
    {
        MDataType::mustBe( MDataType::INT );
        $this->ttl = $ttl;

        return $this;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param string $key
     * @return MCacheItem
     */
    public function setKey( $key )
    {
        $this->key = $key;

        return $this;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $value
     * @return MCacheItem
     */
    public function setValue( $value )
    {
        $this->value = $value;

        return $this;
    }

}