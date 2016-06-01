<?php

namespace mtoolkit\cache\mysql;

use mtoolkit\cache\MCacheItem;
use mtoolkit\cache\MCacheManager;
use mtoolkit\core\MDataType;

/**
 * Class MMySQLCacheManager
 *
 * @package mtoolkit\cache\mysql
 */
class MMySQLCacheManager extends MCacheManager
{
    const MYSQL_DATE_FORMAT = 'Y-m-d H:i:s';

    private static $CREATE_TABLE = '
        CREATE TABLE IF NOT EXISTS %s(
            `key` VARCHAR(200) NOT NULL,
            `value` TEXT,
            `inserted` DATETIME,
            `ttl` int(11),
            `end_validation` DATETIME,
            PRIMARY KEY (`key`)
        )
    ';

    /**
     * @var string
     */
    private static $UPSERT = '
        INSERT INTO %s(
          `key`, `value`, `inserted`, `ttl`, `end_validation`
        )
        VALUES (
          ?,
          ?,
          NOW(),
          ?,
          DATE_ADD(NOW(), INTERVAL ? SECOND)
        )
        ON DUPLICATE KEY UPDATE
          `value`=?,
          `inserted`=NOW(),
          `ttl`=?,
          `end_validation`=DATE_ADD(NOW(), INTERVAL ? SECOND)
    ';

    /**
     * @var string
     */
    private static $DELETE = '
      DELETE FROM %s
      WHERE ( `key`=? OR ? is null )
    ';

    /**
     * @var string
     */
    private static $SELECT = '
        SELECT *
        FROM %s
        WHERE `end_validation` > NOW()
    ';

    /**
     * @var string
     */
    private $tableName = null;

    /**
     * @var \PDO
     */
    private $pdoConnection = null;

    /**
     * MPDOCacheManager constructor.
     *
     * @param array $option Must contains an item called 'db' containing an {@link \PDO} object and an item called
     *     'table' containing the table name of the database to use to store the cache item.
     * @throws \Exception
     */
    public function __construct( array $option )
    {
        parent::__construct( $option );

        if( isset( $option['db'] ) === false || isset( $option['table'] ) === false )
        {
            throw new \Exception( 'Mandatory options are not set!' );
        }

        $this->setPdoConnection( $option['db'] );
        $this->setTableName( $option['table'] );
    }

    /**
     * Create the cache table on the database.
     *
     * @return bool
     */
    public function createTable()
    {
        $stmt = $this->pdoConnection->prepare( sprintf( self::$CREATE_TABLE, $this->tableName ) );

        return $stmt->execute();
    }

    /**
     * @param array $keys
     * @return MCacheItem[]
     */
    public function getItems( array $keys )
    {
        $select = self::$SELECT;

        if( is_null( $keys ) === false && count( $keys ) > 0 )
        {
            $keyClause = ' AND `key` IN (%s) ';
            $keysString = '\'' . implode( '\', \'', $keys ) . '\'';
            $select .= sprintf( $keyClause, $keysString );
        }

        $stmt = $this->pdoConnection->prepare( sprintf( $select, $this->tableName ) );
        $result = $stmt->execute();

        if( $result === false )
        {
            return array();
        }

        $result = $stmt->fetchAll( \PDO::FETCH_ASSOC );

        $items = array();
        foreach( $result as $row )
        {
            $i = new MCacheItem();
            $i->setValue( $row['value'] )
                ->setTtl( (int)$row['ttl'] )
                ->setKey( $row['key'] );

            $items[] = $i;
        }

        return $items;
    }

    /**
     * @param array $keys
     */
    public function deleteItems( array $keys )
    {
        array_map(
            function ( $key )
            {
                $i=1;
                $stmt = $this->pdoConnection->prepare( sprintf( self::$DELETE, $this->tableName ) );
                $stmt->bindParam( $i++, $key, \PDO::PARAM_STR );
                $stmt->bindParam( $i, $key, \PDO::PARAM_STR );

                return $stmt->execute();
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
        $i=0;
        $stmt = $this->pdoConnection->prepare( sprintf( self::$UPSERT, $this->tableName ) );
        $stmt->bindParam( ++$i, $item->getKey(), \PDO::PARAM_STR );
        $stmt->bindParam( ++$i, $item->getValue(), \PDO::PARAM_STR );
        $stmt->bindParam( ++$i, $item->getTtl(), \PDO::PARAM_INT );
        $stmt->bindParam( ++$i, $item->getTtl(), \PDO::PARAM_INT );
        $stmt->bindParam( ++$i, $item->getValue(), \PDO::PARAM_STR );
        $stmt->bindParam( ++$i, $item->getTtl(), \PDO::PARAM_INT );
        $stmt->bindParam( ++$i, $item->getTtl(), \PDO::PARAM_INT );

        return $stmt->execute();
    }

    /**
     * @param string $tableName
     * @return MMySQLCacheManager
     */
    private function setTableName( $tableName )
    {
        MDataType::mustBe( MDataType::STRING );
        $this->tableName = $tableName;

        return $this;
    }

    /**
     * @param \PDO $pdoConnection
     * @return MMySQLCacheManager
     */
    private function setPdoConnection( \PDO $pdoConnection )
    {
        $this->pdoConnection = $pdoConnection;

        return $this;
    }


    /**
     * @return void
     */
    public function flush( )
    {
        $this->deleteItems( null );
    }
}