<?php
/**
 * Notification.php - Notification Model
 * 
 * Handles all notification-related database operations.
 * 
 * @author Senior Full Stack Developer
 * @version 1.0
 */

namespace App\Models;

use App\Core\Model;

class Notification extends Model
{
    protected $table = 'notifications';

    /**
     * Get user's unread notifications
     * 
     * @param int $user_id
     * @return array
     */
    public function getUnreadNotifications($user_id)
    {
        $sql = "SELECT * FROM notifications 
                WHERE user_id = ? AND is_read = FALSE
                ORDER BY created_at DESC";
        
        return $this->db->fetchAll($sql, [$user_id]);
    }

    /**
     * Get all user notifications
     * 
     * @param int $user_id
     * @param int $limit
     * @return array
     */
    public function getUserNotifications($user_id, $limit = 20)
    {
        $sql = "SELECT * FROM notifications 
                WHERE user_id = ?
                ORDER BY created_at DESC
                LIMIT ?";
        
        return $this->db->fetchAll($sql, [$user_id, $limit]);
    }

    /**
     * Count unread notifications
     * 
     * @param int $user_id
     * @return int
     */
    public function countUnread($user_id)
    {
        $sql = "SELECT COUNT(*) as total FROM notifications 
                WHERE user_id = ? AND is_read = FALSE";
        $result = $this->db->fetchOne($sql, [$user_id]);
        return $result['total'] ?? 0;
    }

    /**
     * Mark notification as read
     * 
     * @param int $id Notification ID
     * @return bool
     */
    public function markAsRead($id)
    {
        return $this->update($id, ['is_read' => true]);
    }

    /**
     * Mark all user notifications as read
     * 
     * @param int $user_id
     * @return bool
     */
    public function markAllAsRead($user_id)
    {
        $sql = "UPDATE notifications SET is_read = TRUE 
                WHERE user_id = ? AND is_read = FALSE";
        $this->db->query($sql, [$user_id]);
        return true;
    }

    /**
     * Create a new notification
     * 
     * @param int $user_id
     * @param string $type
     * @param string $title
     * @param string $message
     * @param int $related_id
     * @return int Notification ID
     */
    public function notify($user_id, $type, $title, $message, $related_id = null)
    {
        $data = [
            'user_id' => $user_id,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'related_id' => $related_id,
            'is_read' => false,
        ];
        
        return $this->create($data);
    }
}
