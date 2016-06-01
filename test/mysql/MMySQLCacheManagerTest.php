<?php
namespace mtoolkit\cache\test\mysql;

use mtoolkit\cache\MCacheConfiguration;
use mtoolkit\cache\MCacheFactoryImpl;
use mtoolkit\cache\MCacheItem;
use mtoolkit\cache\MCacheType;
use mtoolkit\cache\mysql\MMySQLCacheManager;

class MMySQLCacheManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PDO
     */
    private $connection;

    private $tableName;

    /**
     * @var MMySQLCacheManager
     */
    private $cacheManager;

    public function testCreateTable()
    {
        $this->assertTrue( $this->cacheManager->createTable() );
    }

    public function testStoreAndRetrieveItemByKey()
    {
        $cacheItem = new MCacheItem();
        $cacheItem->setKey( 'key' )
            ->setValue( 'value' )
            ->setTtl( 100000 );

        $this->cacheManager->createTable();
        $this->cacheManager->addItem( $cacheItem );
        $dbCacheItems = $this->cacheManager->getItems( array( 'key' ) );

        $this->assertEquals( $cacheItem, $dbCacheItems[0] );
    }

    protected function setUp()
    {
        parent::setUp();
        $this->connection = new \PDO( 'mysql:host=localhost;dbname=test', 'root', '' );
        $this->tableName = 'mcache';
        $this->cacheManager = MCacheFactoryImpl::getManager( new MCacheConfiguration(
            MCacheType::MYSQL,
            array(
                'db' => $this->connection,
                'table' => $this->tableName
            )
        ) );
    }


    protected function tearDown()
    {
        parent::tearDown();
        $this->connection->exec( sprintf( 'DROP TABLE %s', $this->tableName ) );
    }


}
