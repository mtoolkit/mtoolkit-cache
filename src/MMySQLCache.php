<?php
namespace mtoolkit\core\cache;

/*
 * This file is part of MToolkit.
 *
 * MToolkit is free software: you can redistribute it and/or modify
 * it under the terms of the LGNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * MToolkit is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * LGNU Lesser General Public License for more details.
 *
 * You should have received a copy of the LGNU Lesser General Public License
 * along with MToolkit.  If not, see <http://www.gnu.org/licenses/>.
 * 
 * @author  Michele Pagnin
 */
use mtoolkit\core\MObject;
use mtoolkit\model\sql\MDbConnection;


/**
 * MMySQLCache class extends MAbstractCache.<br />
 * This class stores cache records into a MySQL database.
 */
class MMySQLCache extends MAbstractCache
{
    private $connection;
    private $cacheTableName;

    /**
     * @param \PDO $connection
     * @param string $cacheTableName
     * @param \MToolkit\Core\MObject $parent
     * @throws \Exception
     */
    public function __construct( \PDO $connection, $cacheTableName = 'MToolkitCache', MObject $parent = null )
    {
        parent::__construct( $parent );

        if( $connection->getAttribute( \PDO::ATTR_DRIVER_NAME )!='mysql' )
        {
            throw new \Exception( 'Invalid database connection, required mysql, passed ' . $connection->getAttribute( \PDO::ATTR_DRIVER_NAME ) );
        }

        $this->connection = $connection;
        $this->cacheTableName = $cacheTableName;
        $this->init();
    }

    private function init()
    {
        $query = "CREATE TABLE IF NOT EXISTS `" . $this->cacheTableName . "`
            (
                `Key` VARCHAR(255) PRIMARY KEY,
                `Value` LONGTEXT,
                `Expired` BIGINT
            );
        ";
        /* @var $connection \PDO */ $connection = MDbConnection::getDbConnection();
        /* @var $stmt \PDOStatement */ $stmt = $connection->prepare( $query );
        /* @var $result bool */ $result = $stmt->execute();

        if( $result===false )
        {
            throw new \Exception( json_encode( $stmt->errorInfo() ) );
        }

        $stmt->closeCursor();
    }

    /**
     * Remove a single cache record.
     * 
     * @param string $key The key of the record.
     * @throws \Exception
     */
    public function delete( $key )
    {
        $query = "DELETE FROM `" . $this->cacheTableName . "`
            WHERE `Key`=?;
        ";
        /* @var $connection \PDO */ $connection = MDbConnection::getDbConnection();
        /* @var $stmt \PDOStatement */ $stmt = $connection->prepare( $query );
        /* @var $result bool */ $result = $stmt->execute( array( $key ) );

        if( $result===false )
        {
            throw new \Exception( json_encode( $stmt->errorInfo() ) );
        }

        $stmt->closeCursor();
    }

    /**
     * Remove all cache records.
     * 
     * @throws \Exception
     */
    public function flush()
    {
        $query = "TRUNCATE TABLE `" . $this->cacheTableName . "`;";
        /* @var $connection \PDO */ $connection = MDbConnection::getDbConnection();
        /* @var $stmt \PDOStatement */ $stmt = $connection->prepare( $query );
        /* @var $result bool */ $result = $stmt->execute();

        if( $result===false )
        {
            throw new \Exception( json_encode( $stmt->errorInfo() ) );
        }

        $stmt->closeCursor();
    }

    /**
     * Store a <i>$value</i> in a cache record with <i>$key</i>.
     * Time To Live; seconds. After the ttl has
     * passed, the stored variable will be expunged from the cache (on the next
     * request). If no ttl is supplied (or if the ttl is 0), the value will
     * persist until it is removed from the cache manually, or otherwise fails
     * to exist in the cache (clear, restart, etc.).
     *
     * @param string $key
     * @param string $value
     * @param int $ttl
     * @return bool
     * @throws \Exception
     */
    public function store( $key, $value, $ttl = -1 )
    {
        $this->delete( $key );

        $expired = time()+$ttl;

        $query = "INSERT INTO `" . $this->cacheTableName . "` (`Key`, `Value`, `Expired`)
            VALUES(?, ?, ?)
        ;";
        /* @var $connection \PDO */ $connection = MDbConnection::getDbConnection();
        /* @var $stmt \PDOStatement */ $stmt = $connection->prepare( $query );
        /* @var $result bool */ $result = $stmt->execute( array( $key, serialize( $value ), $expired ) );

        if( $result===false )
        {
            throw new \Exception( json_encode( $stmt->errorInfo() ) );
        }

        $stmt->closeCursor();

        return true;
    }

    /**
     * Return the content of a cache record with <i>$key</i>.
     * 
     * @param string $key
     * @return string|null
     * @throws \Exception
     */
    public function fetch( $key )
    {
        $query = "SELECT `Key`, `Value`, `Expired` FROM `" . $this->cacheTableName . "` WHERE `key`=?;";
        /* @var $connection \PDO */ $connection = MDbConnection::getDbConnection();
        /* @var $stmt \PDOStatement */ $stmt = $connection->prepare( $query );
        /* @var $result bool */ $result = $stmt->execute( array( $key ) );

        if( $result===false )
        {
            throw new \Exception( json_encode( $stmt->errorInfo() ) );
        }

        $rows = $stmt->fetchAll( \PDO::FETCH_ASSOC );
        $stmt->closeCursor();

        if( count( $rows )<=0 )
        {
            return null;
        }

        $key = $rows[0]['Key'];
        $value = $rows[0]['Value'];
        $expired = $rows[0]['Expired'];

        if( $expired==-1||$expired>time() )
        {
            return unserialize( $value );
        }

        $this->delete( $key );
        return null;
    }

}

