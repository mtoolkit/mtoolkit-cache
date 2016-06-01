<?php
namespace mtoolkit\cache\test\apc;

use mtoolkit\cache\apc\MAPCCacheManager;
use mtoolkit\cache\MCacheConfiguration;
use mtoolkit\cache\MCacheFactoryImpl;
use mtoolkit\cache\MCacheItem;
use mtoolkit\cache\MCacheType;

class MAPCCacheManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MAPCCacheManager
     */
    private $cacheManager;

    public function testStoreAndRetrieveItemByKey()
    {
        $cacheItem = new MCacheItem();
        $cacheItem->setKey( 'key' )
            ->setValue( 'value' )
            ->setTtl( 100000 );

        $this->assertTrue( $this->cacheManager->addItem( $cacheItem ) );
        $dbCacheItems = $this->cacheManager->getItems( array( 'key' ) );

        $this->assertEquals( $cacheItem, $dbCacheItems[0] );
    }

    protected function setUp()
    {
        parent::setUp();
        $this->cacheManager = MCacheFactoryImpl::getManager( new MCacheConfiguration(
            MCacheType::APC,
            array()
        ) );
    }


    protected function tearDown()
    {
        parent::tearDown();
    }


}
