<?php

namespace mtoolkit\cache\test\transformer;

use mtoolkit\cache\MCacheItem;
use mtoolkit\cache\transformer\ItemTransformer;

class ItemTransformerTest extends \PHPUnit_Framework_TestCase
{
    public function testToArray()
    {
        $array = array(
            'key' => 'key',
            'value' => 'value',
            'ttl' => 1
        );

        $item = new MCacheItem();
        $item->setValue( 'value' )
            ->setTtl( 1 )
            ->setKey( 'key' );

        $t = ItemTransformer::toArray( $item );
        unset( $t['endValidation'] );
        $this->assertEquals( $array, $t );
    }

    public function testFromArray()
    {
        $array = array(
            'key' => 'key',
            'value' => 'value',
            'ttl' => 1
        );

        $item = ItemTransformer::fromArray( $array );

        $this->assertEquals( 'key', $item->getKey() );
        $this->assertEquals( 'value', $item->getValue() );
        $this->assertEquals( 1, $item->getTtl() );
    }
}
