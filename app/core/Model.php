<?php
/**
 * Model.php - Base Model Class
 * 
 * All models extend this base class.
 * Provides common database operations (CRUD).
 * 
 * @author Senior Full Stack Developer
 * @version 1.0
 */

namespace App\Core;

class Model
{
    /**
     * @var Database Database instance
     */
    protected $db;

    /**
     * @var string Table name for this model
     */
    protected $table = '';

    /**
     * @var string Primary key column
     */
    protected $pk = 'id';

    /**
     * Constructor - Initialize database connection
     */
    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Get all records from table
     * 
     * @param array $where WHERE conditions
     * @param string $order ORDER BY clause
     * @param int $limit LIMIT
     * @return array
     */
    public function findAll($where = [], $order = '', $limit = '')
    {
        $sql = "SELECT * FROM {$this->table} WHERE 1=1";
        $params = [];

        // Build WHERE clause
        foreach ($where as $column => $value) {
            $sql .= " AND {$column} = ?";
            $params[] = $value;
        }

        // Add ORDER BY
        if (!empty($order)) {
            $sql .= " {$order}";
        }

        // Add LIMIT
        if (!empty($limit)) {
            $sql .= " LIMIT {$limit}";
        }

        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Find single record by ID
     * 
     * @param int $id Record ID
     * @return array|null
     */
    public function findById($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->pk} = ?";
        return $this->db->fetchOne($sql, [$id]);
    }

    /**
     * Find single record by column value
     * 
     * @param string $column Column name
     * @param mixed $value Column value
     * @return array|null
     */
    public function findByColumn($column, $value)
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$column} = ?";
        return $this->db->fetchOne($sql, [$value]);
    }

    /**
     * Insert new record
     * 
     * @param array $data Associative array of column => value
     * @return int Last inserted ID
     */
    public function insert($data)
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
        
        $this->db->query($sql, array_values($data));
        return $this->db->lastInsertId();
    }

    /**
     * Update record
     * 
     * @param array $data Data to update
     * @param int $id Record ID
     * @return bool
     */
    public function update($data, $id)
    {
        $set = '';
        $params = [];

        foreach ($data as $column => $value) {
            $set .= "{$column} = ?, ";
            $params[] = $value;
        }

        $set = rtrim($set, ', ');
        $params[] = $id;

        $sql = "UPDATE {$this->table} SET {$set} WHERE {$this->pk} = ?";
        
        return $this->db->count($sql, $params) > 0;
    }

    /**
     * Delete record (hard delete)
     * 
     * @param int $id Record ID
     * @return bool
     */
    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE {$this->pk} = ?";
        return $this->db->count($sql, [$id]) > 0;
    }

    /**
     * Soft delete record (set deleted_at timestamp)
     * 
     * @param int $id Record ID
     * @return bool
     */
    public function softDelete($id)
    {
        $sql = "UPDATE {$this->table} SET deleted_at = NOW() WHERE {$this->pk} = ?";
        return $this->db->count($sql, [$id]) > 0;
    }

    /**
     * Count records
     * 
     * @param array $where WHERE conditions
     * @return int
     */
    public function count($where = [])
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE 1=1";
        $params = [];

        foreach ($where as $column => $value) {
            $sql .= " AND {$column} = ?";
            $params[] = $value;
        }

        $result = $this->db->fetchOne($sql, $params);
        return $result['count'] ?? 0;
    }

    /**
     * Execute raw SQL query
     * 
     * @param string $sql SQL query
     * @param array $params Parameters
     * @return array
     */
    public function query($sql, $params = [])
    {
        return $this->db->fetchAll($sql, $params);
    }
}
