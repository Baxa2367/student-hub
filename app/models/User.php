<?php
/**
 * User.php - User Model
 * 
 * Handles all user-related database operations.
 * 
 * @author Senior Full Stack Developer
 * @version 1.0
 */

namespace App\Models;

use App\Core\Model;

class User extends Model
{
    protected $table = 'users';

    /**
     * Register a new user
     * 
     * @param array $data User data
     * @return int User ID
     */
    public function register($data)
    {
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        $data['role_id'] = 3; // Default to student role
        $data['is_active'] = true;
        
        return $this->create($data);
    }

    /**
     * Find user by email
     * 
     * @param string $email
     * @return array|null
     */
    public function findByEmail($email)
    {
        return $this->findBy('email', $email);
    }

    /**
     * Verify password
     * 
     * @param string $password Plain text password
     * @param string $hash Hashed password
     * @return bool
     */
    public function verifyPassword($password, $hash)
    {
        return password_verify($password, $hash);
    }

    /**
     * Get user with role details
     * 
     * @param int $id User ID
     * @return array|null
     */
    public function getUserWithRole($id)
    {
        $sql = "SELECT u.*, r.name as role FROM users u
                LEFT JOIN roles r ON u.role_id = r.id
                WHERE u.id = ? AND u.deleted_at IS NULL";
        
        return $this->db->fetchOne($sql, [$id]);
    }

    /**
     * Get all users with their roles
     * 
     * @return array
     */
    public function getAllWithRoles()
    {
        $sql = "SELECT u.*, r.name as role FROM users u
                LEFT JOIN roles r ON u.role_id = r.id
                WHERE u.deleted_at IS NULL
                ORDER BY u.created_at DESC";
        
        return $this->db->fetchAll($sql);
    }

    /**
     * Get users by role
     * 
     * @param string $role Role name
     * @return array
     */
    public function getUsersByRole($role)
    {
        $sql = "SELECT u.* FROM users u
                JOIN roles r ON u.role_id = r.id
                WHERE r.name = ? AND u.deleted_at IS NULL
                ORDER BY u.created_at DESC";
        
        return $this->db->fetchAll($sql, [$role]);
    }

    /**
     * Get teacher with course count
     * 
     * @return array
     */
    public function getTeachersWithCourseCount()
    {
        $sql = "SELECT u.id, u.name, u.email, COUNT(c.id) as course_count
                FROM users u
                LEFT JOIN courses c ON u.id = c.teacher_id AND c.deleted_at IS NULL
                JOIN roles r ON u.role_id = r.id
                WHERE r.name = 'teacher' AND u.deleted_at IS NULL
                GROUP BY u.id
                ORDER BY u.created_at DESC";
        
        return $this->db->fetchAll($sql);
    }

    /**
     * Check if email exists
     * 
     * @param string $email
     * @return bool
     */
    public function emailExists($email)
    {
        $sql = "SELECT COUNT(*) as count FROM users WHERE email = ? AND deleted_at IS NULL";
        $result = $this->db->fetchOne($sql, [$email]);
        return $result['count'] > 0;
    }
}
