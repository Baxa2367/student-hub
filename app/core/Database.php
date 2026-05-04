<?php
/**
 * Database.php - PDO Database Connection Class
 * 
 * Implements Singleton pattern for single database instance.
 * Provides methods for query execution with prepared statements.
 * 
 * @author Senior Full Stack Developer
 * @version 1.0
 */

namespace App\Core;

use PDO;
use PDOException;

class Database
{
    /**
     * @var Database Singleton instance
     */
    private static $instance = null;

    /**
     * @var PDO Database connection
     */
    private $pdo;

    /**
     * @var array Database configuration
     */
    private $config;

    /**
     * Private constructor - prevents direct instantiation
     */
    private function __construct()
    {
        // Load configuration
        $this->config = require(__DIR__ . '/../../config/config.php');

        $dsn = "mysql:host=" . $this->config['db_host'] . ";dbname=" . $this->config['db_name'] . ";charset=utf8mb4";
        
        try {
            $this->pdo = new PDO(
                $dsn,
                $this->config['db_user'],
                $this->config['db_pass'],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        } catch (PDOException $e) {
            die("Database Connection Error: " . $e->getMessage());
        }
    }

    /**
     * Get database instance (Singleton pattern)
     * 
     * @return Database
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Get PDO connection object
     * 
     * @return PDO
     */
    public function getConnection()
    {
        return $this->pdo;
    }

    /**
     * Execute a query with parameters
     * 
     * @param string $sql SQL query
     * @param array $params Parameters for prepared statement
     * @return \PDOStatement
     */
    public function query($sql, $params = [])
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            throw new \Exception("Query Error: " . $e->getMessage());
        }
    }

    /**
     * Fetch a single row
     * 
     * @param string $sql SQL query
     * @param array $params Parameters
     * @return array|null
     */
    public function fetchOne($sql, $params = [])
    {
        $stmt = $this->query($sql, $params);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Fetch all rows
     * 
     * @param string $sql SQL query
     * @param array $params Parameters
     * @return array
     */
    public function fetchAll($sql, $params = [])
    {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Count rows affected by query
     * 
     * @param string $sql SQL query
     * @param array $params Parameters
     * @return int
     */
    public function count($sql, $params = [])
    {
        $stmt = $this->query($sql, $params);
        return $stmt->rowCount();
    }

    /**
     * Get last inserted ID
     * 
     * @return string
     */
    public function lastInsertId()
    {
        return $this->pdo->lastInsertId();
    }

    /**
     * Begin transaction
     * 
     * @return bool
     */
    public function beginTransaction()
    {
        return $this->pdo->beginTransaction();
    }

    /**
     * Commit transaction
     * 
     * @return bool
     */
    public function commit()
    {
        return $this->pdo->commit();
    }

    /**
     * Rollback transaction
     * 
     * @return bool
     */
    public function rollback()
    {
        return $this->pdo->rollBack();
    }

    /**
     * Prevent cloning
     */
    private function __clone()
    {
    }

    /**
     * Prevent unserialization
     */
    private function __wakeup()
    {
    }
}
