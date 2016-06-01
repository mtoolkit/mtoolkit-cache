<?php
namespace mtoolkit\cache\test;

use mtoolkit\cache\file\MFileCacheManager;
use mtoolkit\cache\MCacheConfiguration;
use mtoolkit\cache\MCacheFactoryImpl;
use mtoolkit\cache\MCacheType;
use mtoolkit\cache\mysql\MMySQLCacheManager;

class MCacheFactoryImplTest extends \PHPUnit_Framework_TestCase
{
    public function testGetManager()
    {
        $class = MMySQLCacheManager::class;
        $this->assertTrue( MCacheFactoryImpl::getManager(
                new MCacheConfiguration(
                    MCacheType::MYSQL,
                    array(
                        'db' => new \PDO( 'mysql:host=localhost;dbname=test', 'root', '' ),
                        'table' => ''
                    )
                )
            ) instanceof $class
        );

        $class = MFileCacheManager::class;
        $this->assertTrue( MCacheFactoryImpl::getManager(
                new MCacheConfiguration(
                    MCacheType::FILE,
                    array(
                        'path' => '/tmp'
                    )
                )
            ) instanceof $class
        );
    }
}
