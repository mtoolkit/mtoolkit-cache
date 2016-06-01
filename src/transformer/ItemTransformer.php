<?php
namespace mtoolkit\cache\transformer;

use mtoolkit\cache\MCacheItem;

/**
 * Class ItemTransformer
 *
 * @package mtoolkit\cache\transformer
 */
class ItemTransformer
{
    const SERIALIZATION_DATE_FORMAT = 'Y-m-d H:i:s';

    /**
     * @param MCacheItem $item
     * @return array
     */
    public static function toArray( MCacheItem $item )
    {
        return array(
            'key' => $item->getKey(),
            'value' => $item->getValue(),
            'ttl' => $item->getTtl()
        );
    }

    public static function fromArray( array $item )
    {
        $i = new MCacheItem();
        $i->setTtl( $item['ttl'] )
            ->setKey( $item['key'] )
            ->setValue( $item['value'] );

        return $i;
    }

}