<?php
/**
 * Course.php - Course Model
 * 
 * Handles all course-related database operations.
 * 
 * @author Senior Full Stack Developer
 * @version 1.0
 */

namespace App\Models;

use App\Core\Model;

class Course extends Model
{
    protected $table = 'courses';

    /**
     * Get course with teacher details
     * 
     * @param int $id Course ID
     * @return array|null
     */
    public function getCourseWithTeacher($id)
    {
        $sql = "SELECT c.*, u.name as teacher_name, u.email as teacher_email
                FROM courses c
                JOIN users u ON c.teacher_id = u.id
                WHERE c.id = ? AND c.deleted_at IS NULL";
        
        return $this->db->fetchOne($sql, [$id]);
    }

    /**
     * Get all published courses with teacher info
     * 
     * @return array
     */
    public function getPublishedCourses()
    {
        $sql = "SELECT c.*, u.name as teacher_name, COUNT(cu.id) as student_count
                FROM courses c
                JOIN users u ON c.teacher_id = u.id
                LEFT JOIN course_users cu ON c.id = cu.course_id AND cu.role = 'student'
                WHERE c.is_published = TRUE AND c.deleted_at IS NULL
                GROUP BY c.id
                ORDER BY c.created_at DESC";
        
        return $this->db->fetchAll($sql);
    }

    /**
     * Get teacher's courses
     * 
     * @param int $teacher_id
     * @return array
     */
    public function getTeacherCourses($teacher_id)
    {
        $sql = "SELECT c.*, COUNT(cu.id) as student_count
                FROM courses c
                LEFT JOIN course_users cu ON c.id = cu.course_id AND cu.role = 'student'
                WHERE c.teacher_id = ? AND c.deleted_at IS NULL
                GROUP BY c.id
                ORDER BY c.created_at DESC";
        
        return $this->db->fetchAll($sql, [$teacher_id]);
    }

    /**
     * Get student's enrolled courses
     * 
     * @param int $student_id
     * @return array
     */
    public function getStudentCourses($student_id)
    {
        $sql = "SELECT c.*, u.name as teacher_name, cu.progress, cu.joined_at
                FROM courses c
                JOIN users u ON c.teacher_id = u.id
                JOIN course_users cu ON c.id = cu.course_id
                WHERE cu.user_id = ? AND cu.role = 'student' AND c.deleted_at IS NULL
                ORDER BY cu.joined_at DESC";
        
        return $this->db->fetchAll($sql, [$student_id]);
    }

    /**
     * Get course with enrollment info
     * 
     * @param int $course_id
     * @return array|null
     */
    public function getCourseStats($course_id)
    {
        $sql = "SELECT 
                    c.*,
                    u.name as teacher_name,
                    u.email as teacher_email,
                    (SELECT COUNT(*) FROM course_users WHERE course_id = c.id AND role = 'student') as student_count,
                    (SELECT COUNT(*) FROM posts WHERE course_id = c.id AND deleted_at IS NULL) as post_count,
                    (SELECT COUNT(*) FROM posts WHERE course_id = c.id AND type = 'assignment' AND deleted_at IS NULL) as assignment_count
                FROM courses c
                JOIN users u ON c.teacher_id = u.id
                WHERE c.id = ? AND c.deleted_at IS NULL";
        
        return $this->db->fetchOne($sql, [$course_id]);
    }

    /**
     * Check if student is enrolled in course
     * 
     * @param int $student_id
     * @param int $course_id
     * @return bool
     */
    public function isStudentEnrolled($student_id, $course_id)
    {
        $sql = "SELECT COUNT(*) as count FROM course_users 
                WHERE user_id = ? AND course_id = ? AND role = 'student'";
        $result = $this->db->fetchOne($sql, [$student_id, $course_id]);
        return $result['count'] > 0;
    }

    /**
     * Enroll student in course (transaction-safe)
     * 
     * @param int $student_id
     * @param int $course_id
     * @return bool
     */
    public function enrollStudent($student_id, $course_id)
    {
        try {
            $this->db->beginTransaction();
            
            // Check if already enrolled
            if ($this->isStudentEnrolled($student_id, $course_id)) {
                $this->db->rollback();
                return false;
            }
            
            // Enroll student
            $sql = "INSERT INTO course_users (user_id, course_id, role, joined_at) VALUES (?, ?, 'student', NOW())";
            $this->db->query($sql, [$student_id, $course_id]);
            
            // Create notification
            $course = $this->find($course_id);
            $notificationSql = "INSERT INTO notifications (user_id, type, title, message, related_id) 
                              VALUES (?, 'enrollment', 'Course Enrollment', ?, ?)";
            $this->db->query($notificationSql, [$student_id, "You have been enrolled in: {$course['name']}", $course_id]);
            
            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }
}
