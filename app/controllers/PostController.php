<?php
/**
 * PostController.php - Post Management Controller
 * 
 * Handles all post-related operations (assignments, announcements, lessons).
 * 
 * @author Senior Full Stack Developer
 * @version 1.0
 */

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Post;
use App\Models\Course;
use App\Models\Notification;

class PostController extends Controller
{
    private $postModel;
    private $courseModel;
    private $notificationModel;

    public function __construct()
    {
        parent::__construct();
        $this->postModel = new Post();
        $this->courseModel = new Course();
        $this->notificationModel = new Notification();
    }

    /**
     * Show post creation form
     */
    public function create()
    {
        $this->requireRole('teacher');
        
        $courseId = intval($_GET['course_id'] ?? 0);
        $course = $this->courseModel->find($courseId);

        if (!$course || $course['teacher_id'] !== $this->getCurrentUser()['id']) {
            http_response_code(403);
            die('Unauthorized');
        }

        $this->render('posts.create', ['course' => $course]);
    }

    /**
     * Store new post
     */
    public function store()
    {
        $this->requireRole('teacher');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            die('Method Not Allowed');
        }

        $courseId = intval($_POST['course_id'] ?? 0);
        $course = $this->courseModel->find($courseId);

        if (!$course || $course['teacher_id'] !== $this->getCurrentUser()['id']) {
            http_response_code(403);
            die('Unauthorized');
        }

        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $type = trim($_POST['type'] ?? 'discussion');
        $dueDate = !empty($_POST['due_date']) ? $_POST['due_date'] : null;

        $errors = [];

        if (empty($title)) {
            $errors['title'] = 'Title is required';
        }

        if (empty($content)) {
            $errors['content'] = 'Content is required';
        }

        if (!in_array($type, ['assignment', 'announcement', 'lesson', 'discussion'])) {
            $errors['type'] = 'Invalid post type';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old_input'] = [
                'title' => $title,
                'content' => $content,
                'type' => $type,
            ];
            $this->redirect('/student-hub/public/index.php?route=posts/create&course_id=' . $courseId);
        }

        try {
            $postId = $this->postModel->create([
                'user_id' => $this->getCurrentUser()['id'],
                'course_id' => $courseId,
                'title' => $title,
                'content' => $content,
                'type' => $type,
                'due_date' => $dueDate,
            ]);

            // Notify enrolled students
            $this->notifyStudents($courseId, $postId, $title);

            $_SESSION['success'] = 'Post created successfully!';
            $this->redirect('/student-hub/public/index.php?route=posts/view&id=' . $postId);
        } catch (\Exception $e) {
            $_SESSION['errors'] = ['general' => 'Post creation failed: ' . $e->getMessage()];
            $this->redirect('/student-hub/public/index.php?route=posts/create&course_id=' . $courseId);
        }
    }

    /**
     * View post details
     */
    public function view()
    {
        $postId = intval($_GET['id'] ?? 0);
        $post = $this->postModel->getPostWithDetails($postId);

        if (!$post) {
            http_response_code(404);
            die('Post not found');
        }

        // Increment views
        $this->postModel->incrementViews($postId);

        $this->render('posts.view', ['post' => $post]);
    }

    /**
     * Show post edit form
     */
    public function edit()
    {
        $this->requireRole('teacher');
        
        $postId = intval($_GET['id'] ?? 0);
        $post = $this->postModel->find($postId);

        if (!$post || $post['user_id'] !== $this->getCurrentUser()['id']) {
            http_response_code(403);
            die('Unauthorized');
        }

        $this->render('posts.edit', ['post' => $post]);
    }

    /**
     * Update post
     */
    public function update()
    {
        $this->requireRole('teacher');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            die('Method Not Allowed');
        }

        $postId = intval($_POST['id'] ?? 0);
        $post = $this->postModel->find($postId);

        if (!$post || $post['user_id'] !== $this->getCurrentUser()['id']) {
            http_response_code(403);
            die('Unauthorized');
        }

        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $dueDate = !empty($_POST['due_date']) ? $_POST['due_date'] : null;

        try {
            $this->postModel->update($postId, [
                'title' => $title,
                'content' => $content,
                'due_date' => $dueDate,
            ]);

            $_SESSION['success'] = 'Post updated successfully!';
            $this->redirect('/student-hub/public/index.php?route=posts/view&id=' . $postId);
        } catch (\Exception $e) {
            $_SESSION['errors'] = ['general' => 'Update failed: ' . $e->getMessage()];
            $this->redirect('/student-hub/public/index.php?route=posts/edit&id=' . $postId);
        }
    }

    /**
     * Delete post
     */
    public function delete()
    {
        $this->requireRole('teacher');
        
        $postId = intval($_GET['id'] ?? 0);
        $post = $this->postModel->find($postId);

        if (!$post || $post['user_id'] !== $this->getCurrentUser()['id']) {
            http_response_code(403);
            die('Unauthorized');
        }

        $courseId = $post['course_id'];

        try {
            $this->postModel->delete($postId);
            $_SESSION['success'] = 'Post deleted successfully!';
            $this->redirect('/student-hub/public/index.php?route=courses/view&id=' . $courseId);
        } catch (\Exception $e) {
            $_SESSION['errors'] = ['general' => 'Deletion failed: ' . $e->getMessage()];
            $this->redirect('/student-hub/public/index.php?route=posts/view&id=' . $postId);
        }
    }

    /**
     * Notify students about new post
     * 
     * @param int $courseId
     * @param int $postId
     * @param string $title
     */
    private function notifyStudents($courseId, $postId, $title)
    {
        // Get enrolled students
        $sql = "SELECT DISTINCT cu.user_id FROM course_users cu 
                WHERE cu.course_id = ? AND cu.role = 'student'";
        $result = $this->postModel->raw($sql, [$courseId]);

        foreach ($result as $row) {
            $this->notificationModel->notify(
                $row['user_id'],
                'post',
                'New Post in Course',
                'A new post has been added: ' . $title,
                $postId
            );
        }
    }
}
