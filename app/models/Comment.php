<?php
/**
 * Comment.php - Comment Model
 * 
 * Handles all comment-related database operations.
 * 
 * @author Senior Full Stack Developer
 * @version 1.0
 */

namespace App\Models;

use App\Core\Model;

class Comment extends Model
{
    protected $table = 'comments';

    /**
     * Get all comments for a post with replies
     * 
     * @param int $post_id
     * @return array
     */
    public function getPostComments($post_id)
    {
        // Get parent comments
        $sql = "SELECT c.*, u.name as author_name, u.avatar
                FROM comments c
                JOIN users u ON c.user_id = u.id
                WHERE c.post_id = ? AND c.parent_comment_id IS NULL AND c.deleted_at IS NULL
                ORDER BY c.created_at DESC";
        
        $comments = $this->db->fetchAll($sql, [$post_id]);
        
        // Get replies for each parent comment
        foreach ($comments as &$comment) {
            $replySql = "SELECT c.*, u.name as author_name, u.avatar
                        FROM comments c
                        JOIN users u ON c.user_id = u.id
                        WHERE c.parent_comment_id = ? AND c.deleted_at IS NULL
                        ORDER BY c.created_at ASC";
            
            $comment['replies'] = $this->db->fetchAll($replySql, [$comment['id']]);
        }
        
        return $comments;
    }

    /**
     * Get comment with author details
     * 
     * @param int $id Comment ID
     * @return array|null
     */
    public function getCommentWithAuthor($id)
    {
        $sql = "SELECT c.*, u.name as author_name, u.avatar, u.email
                FROM comments c
                JOIN users u ON c.user_id = u.id
                WHERE c.id = ? AND c.deleted_at IS NULL";
        
        return $this->db->fetchOne($sql, [$id]);
    }

    /**
     * Count comments on a post
     * 
     * @param int $post_id
     * @return int
     */
    public function countPostComments($post_id)
    {
        $sql = "SELECT COUNT(*) as total FROM comments 
                WHERE post_id = ? AND deleted_at IS NULL";
        $result = $this->db->fetchOne($sql, [$post_id]);
        return $result['total'] ?? 0;
    }

    /**
     * Get user's recent comments
     * 
     * @param int $user_id
     * @param int $limit
     * @return array
     */
    public function getUserComments($user_id, $limit = 10)
    {
        $sql = "SELECT c.*, p.title as post_title, cr.name as course_name
                FROM comments c
                JOIN posts p ON c.post_id = p.id
                JOIN courses cr ON p.course_id = cr.id
                WHERE c.user_id = ? AND c.deleted_at IS NULL
                ORDER BY c.created_at DESC
                LIMIT ?";
        
        return $this->db->fetchAll($sql, [$user_id, $limit]);
    }

    /**
     * Add a reply to a comment
     * 
     * @param int $post_id
     * @param int $user_id
     * @param string $content
     * @param int $parent_comment_id
     * @return int Comment ID
     */
    public function reply($post_id, $user_id, $content, $parent_comment_id)
    {
        $data = [
            'post_id' => $post_id,
            'user_id' => $user_id,
            'content' => $content,
            'parent_comment_id' => $parent_comment_id,
        ];
        
        return $this->create($data);
    }
}
