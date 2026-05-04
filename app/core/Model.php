<?php
/**
 * Model.php
 * 
 * Base Model class - all models inherit from this.
 * Provides common database operations.
 * 
 * @author Senior Full Stack Developer
 * @version 1.0
 */

namespace App\Core;

use App\Core\Database;

class Model
{
    /**
     * @var Database Database instance
     */
    protected $db;

    /**
     * @var string Table name for the model
     */
    protected $table;

    /**
     * Constructor - initialize database connection
     */
    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Find by ID
     * 
     * @param int $id
     * @return array|null
     */
    public function find($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = ? AND deleted_at IS NULL";
        return $this->db->fetchOne($sql, [$id]);
    }

    /**
     * Find all records
     * 
     * @return array
     */
    public function all()
    {
        $sql = "SELECT * FROM {$this->table} WHERE deleted_at IS NULL ORDER BY created_at DESC";
        return $this->db->fetchAll($sql);
    }

    /**
     * Find by column
     * 
     * @param string $column
     * @param mixed $value
     * @return array|null
     */
    public function findBy($column, $value)
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$column} = ? AND deleted_at IS NULL";
        return $this->db->fetchOne($sql, [$value]);
    }

    /**
     * Find all by column
     * 
     * @param string $column
     * @param mixed $value
     * @return array
     */
    public function findAllBy($column, $value)
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$column} = ? AND deleted_at IS NULL ORDER BY created_at DESC";
        return $this->db->fetchAll($sql, [$value]);
    }

    /**
     * Create a new record
     * 
     * @param array $data
     * @return int Last inserted ID
     */
    public function create($data)
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        
        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
        $this->db->query($sql, array_values($data));
        
        return $this->db->lastInsertId();
    }

    /**
     * Update a record
     * 
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        $set = [];
        $values = [];
        
        foreach ($data as $key => $value) {
            $set[] = "{$key} = ?";
            $values[] = $value;
        }
        
        $values[] = $id;
        
        $sql = "UPDATE {$this->table} SET " . implode(', ', $set) . " WHERE id = ?";
        $this->db->query($sql, $values);
        
        return true;
    }

    /**
     * Soft delete a record
     * 
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        $sql = "UPDATE {$this->table} SET deleted_at = NOW() WHERE id = ?";
        $this->db->query($sql, [$id]);
        return true;
    }

    /**
     * Hard delete a record (permanent)
     * 
     * @param int $id
     * @return bool
     */
    public function forceDelete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        $this->db->query($sql, [$id]);
        return true;
    }

    /**
     * Count records
     * 
     * @param string $where Optional WHERE clause
     * @param array $params Optional parameters
     * @return int
     */
    public function count($where = '', $params = [])
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE deleted_at IS NULL";
        
        if (!empty($where)) {
            $sql .= " AND {$where}";
        }
        
        $result = $this->db->fetchOne($sql, $params);
        return $result['total'] ?? 0;
    }

    /**
     * Execute raw query
     * 
     * @param string $sql
     * @param array $params
     * @return array
     */
    public function raw($sql, $params = [])
    {
        return $this->db->fetchAll($sql, $params);
    }
}
