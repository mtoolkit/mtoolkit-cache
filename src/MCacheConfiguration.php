<?php
namespace mtoolkit\cache;

/**
 * Class MCacheConfiguration
 *
 * @package mtoolkit\cache
 */
class MCacheConfiguration
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var array
     */
    private $option = array();

    /**
     * CacheConfiguration constructor.
     *
     * @param string $type
     * @param array $option
     */
    public function __construct( $type, array $option )
    {
        $this->type = $type;
        $this->option = $option;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return array
     */
    public function getOption()
    {
        return $this->option;
    }


}