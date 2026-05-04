<?php
/**
 * Post.php - Post Model
 * 
 * Handles all post-related database operations.
 * 
 * @author Senior Full Stack Developer
 * @version 1.0
 */

namespace App\Models;

use App\Core\Model;

class Post extends Model
{
    protected $table = 'posts';

    /**
     * Get post with author and course details
     * 
     * @param int $id Post ID
     * @return array|null
     */
    public function getPostWithDetails($id)
    {
        $sql = "SELECT p.*, u.name as author_name, u.avatar, c.name as course_name
                FROM posts p
                JOIN users u ON p.user_id = u.id
                JOIN courses c ON p.course_id = c.id
                WHERE p.id = ? AND p.deleted_at IS NULL";
        
        return $this->db->fetchOne($sql, [$id]);
    }

    /**
     * Get all posts for a course
     * 
     * @param int $course_id
     * @param int $limit
     * @return array
     */
    public function getCoursePost($course_id, $limit = 10)
    {
        $sql = "SELECT p.*, u.name as author_name, u.avatar,
                       COUNT(cm.id) as comment_count
                FROM posts p
                JOIN users u ON p.user_id = u.id
                LEFT JOIN comments cm ON p.id = cm.post_id AND cm.deleted_at IS NULL
                WHERE p.course_id = ? AND p.deleted_at IS NULL
                GROUP BY p.id
                ORDER BY p.is_pinned DESC, p.created_at DESC
                LIMIT ?";
        
        return $this->db->fetchAll($sql, [$course_id, $limit]);
    }

    /**
     * Get assignments for a course
     * 
     * @param int $course_id
     * @return array
     */
    public function getCourseAssignments($course_id)
    {
        $sql = "SELECT p.*, u.name as author_name,
                       COUNT(cm.id) as submission_count
                FROM posts p
                JOIN users u ON p.user_id = u.id
                LEFT JOIN comments cm ON p.id = cm.post_id AND cm.deleted_at IS NULL
                WHERE p.course_id = ? AND p.type = 'assignment' AND p.deleted_at IS NULL
                GROUP BY p.id
                ORDER BY p.due_date ASC, p.created_at DESC";
        
        return $this->db->fetchAll($sql, [$course_id]);
    }

    /**
     * Get announcements for a course
     * 
     * @param int $course_id
     * @return array
     */
    public function getCourseAnnouncements($course_id)
    {
        $sql = "SELECT p.*, u.name as author_name
                FROM posts p
                JOIN users u ON p.user_id = u.id
                WHERE p.course_id = ? AND p.type = 'announcement' AND p.deleted_at IS NULL
                ORDER BY p.created_at DESC";
        
        return $this->db->fetchAll($sql, [$course_id]);
    }

    /**
     * Get user's recent posts
     * 
     * @param int $user_id
     * @param int $limit
     * @return array
     */
    public function getUserPosts($user_id, $limit = 10)
    {
        $sql = "SELECT p.*, c.name as course_name,
                       COUNT(cm.id) as comment_count
                FROM posts p
                JOIN courses c ON p.course_id = c.id
                LEFT JOIN comments cm ON p.id = cm.post_id AND cm.deleted_at IS NULL
                WHERE p.user_id = ? AND p.deleted_at IS NULL
                GROUP BY p.id
                ORDER BY p.created_at DESC
                LIMIT ?";
        
        return $this->db->fetchAll($sql, [$user_id, $limit]);
    }

    /**
     * Increment post views
     * 
     * @param int $id Post ID
     * @return void
     */
    public function incrementViews($id)
    {
        $sql = "UPDATE posts SET views = views + 1 WHERE id = ?";
        $this->db->query($sql, [$id]);
    }

    /**
     * Search posts by keyword
     * 
     * @param string $keyword
     * @param int $course_id Optional course filter
     * @return array
     */
    public function search($keyword, $course_id = null)
    {
        $sql = "SELECT p.*, u.name as author_name, c.name as course_name,
                       COUNT(cm.id) as comment_count
                FROM posts p
                JOIN users u ON p.user_id = u.id
                JOIN courses c ON p.course_id = c.id
                LEFT JOIN comments cm ON p.id = cm.post_id AND cm.deleted_at IS NULL
                WHERE (MATCH(p.title, p.content) AGAINST(? IN BOOLEAN MODE) 
                       OR p.title LIKE ? OR p.content LIKE ?) 
                AND p.deleted_at IS NULL";
        
        $params = [$keyword, "%{$keyword}%", "%{$keyword}%"];
        
        if ($course_id) {
            $sql .= " AND p.course_id = ?";
            $params[] = $course_id;
        }
        
        $sql .= " GROUP BY p.id ORDER BY p.created_at DESC";
        
        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Get trending posts
     * 
     * @param int $limit
     * @return array
     */
    public function getTrendingPosts($limit = 5)
    {
        $sql = "SELECT p.*, u.name as author_name, c.name as course_name,
                       COUNT(cm.id) as comment_count
                FROM posts p
                JOIN users u ON p.user_id = u.id
                JOIN courses c ON p.course_id = c.id
                LEFT JOIN comments cm ON p.id = cm.post_id AND cm.deleted_at IS NULL
                WHERE p.deleted_at IS NULL
                GROUP BY p.id
                ORDER BY (p.views + COUNT(cm.id)) DESC
                LIMIT ?";
        
        return $this->db->fetchAll($sql, [$limit]);
    }
}
