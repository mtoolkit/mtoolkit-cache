<?php
namespace mtoolkit\cache\test\file;

use mtoolkit\cache\file\MFileCacheManager;
use mtoolkit\cache\MCacheConfiguration;
use mtoolkit\cache\MCacheFactoryImpl;
use mtoolkit\cache\MCacheItem;
use mtoolkit\cache\MCacheType;

class MFileCacheManagerTest extends \PHPUnit_Framework_TestCase
{
    private $path;

    /**
     * @var MFileCacheManager
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
        $this->path = '/tmp';
        $this->cacheManager = MCacheFactoryImpl::getManager( new MCacheConfiguration(
            MCacheType::FILE,
            array(
                'path' => $this->path
            )
        ) );
    }


    protected function tearDown()
    {
        parent::tearDown();
    }
}
