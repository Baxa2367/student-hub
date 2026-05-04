<?php
/**
 * Model.php
 * 
 * Base Model class for all application models.
 * Provides common database operations (CRUD).
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
     * @var string Table name
     */
    protected $table = '';

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Find a record by ID
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
     * Get all records
     * 
     * @return array
     */
    public function all()
    {
        $sql = "SELECT * FROM {$this->table} WHERE deleted_at IS NULL ORDER BY created_at DESC";
        return $this->db->fetchAll($sql);
    }

    /**
     * Get records with pagination
     * 
     * @param int $page
     * @param int $limit
     * @return array
     */
    public function paginate($page = 1, $limit = 10)
    {
        $offset = ($page - 1) * $limit;
        $sql = "SELECT * FROM {$this->table} WHERE deleted_at IS NULL ORDER BY created_at DESC LIMIT ? OFFSET ?";
        return $this->db->fetchAll($sql, [$limit, $offset]);
    }

    /**
     * Count all records
     * 
     * @return int
     */
    public function count()
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE deleted_at IS NULL";
        $result = $this->db->fetchOne($sql);
        return $result['total'] ?? 0;
    }

    /**
     * Create a new record
     * 
     * @param array $data
     * @return int Last inserted ID
     */
    public function create($data)
    {
        $columns = array_keys($data);
        $values = array_values($data);
        $placeholders = implode(',', array_fill(0, count($columns), '?'));
        $columnList = implode(',', $columns);

        $sql = "INSERT INTO {$this->table} ({$columnList}) VALUES ({$placeholders})";
        $this->db->query($sql, $values);
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
        $columns = array_keys($data);
        $values = array_values($data);
        $values[] = $id;

        $setClause = implode(',', array_map(fn($col) => "{$col} = ?", $columns));
        $sql = "UPDATE {$this->table} SET {$setClause} WHERE id = ?";
        
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
     * Hard delete (permanent)
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
     * Execute raw SQL query
     * 
     * @param string $sql
     * @param array $params
     * @return array
     */
    public function rawQuery($sql, $params = [])
    {
        return $this->db->fetchAll($sql, $params);
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
        $sql = "SELECT * FROM {$this->table} WHERE {$column} = ? AND deleted_at IS NULL LIMIT 1";
        return $this->db->fetchOne($sql, [$value]);
    }

    /**
     * Find multiple by column
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
}
