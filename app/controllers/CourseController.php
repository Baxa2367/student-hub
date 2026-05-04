<?php
/**
 * CourseController.php - Course Management Controller
 * 
 * Handles all course-related operations.
 * 
 * @author Senior Full Stack Developer
 * @version 1.0
 */

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Course;
use App\Models\Post;

class CourseController extends Controller
{
    private $courseModel;
    private $postModel;

    public function __construct()
    {
        parent::__construct();
        $this->courseModel = new Course();
        $this->postModel = new Post();
    }

    /**
     * Display all courses
     */
    public function index()
    {
        $courses = $this->courseModel->getPublishedCourses();
        
        $this->render('courses.index', [
            'courses' => $courses,
        ]);
    }

    /**
     * Show course creation form
     */
    public function create()
    {
        $this->requireRole('teacher');
        $this->render('courses.create');
    }

    /**
     * Store new course
     */
    public function store()
    {
        $this->requireRole('teacher');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            die('Method Not Allowed');
        }

        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $is_published = isset($_POST['is_published']) ? 1 : 0;

        $errors = [];

        if (empty($name)) {
            $errors['name'] = 'Course name is required';
        }

        if (empty($description)) {
            $errors['description'] = 'Course description is required';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old_input'] = [
                'name' => $name,
                'description' => $description,
                'category' => $category,
            ];
            $this->redirect('/student-hub/public/index.php?route=courses/create');
        }

        try {
            $courseId = $this->courseModel->create([
                'name' => $name,
                'description' => $description,
                'teacher_id' => $this->getCurrentUser()['id'],
                'category' => $category,
                'is_published' => $is_published,
            ]);

            $_SESSION['success'] = 'Course created successfully!';
            $this->redirect('/student-hub/public/index.php?route=courses/view&id=' . $courseId);
        } catch (\Exception $e) {
            $_SESSION['errors'] = ['general' => 'Course creation failed: ' . $e->getMessage()];
            $this->redirect('/student-hub/public/index.php?route=courses/create');
        }
    }

    /**
     * View course details
     */
    public function view()
    {
        $courseId = intval($_GET['id'] ?? 0);
        
        if ($courseId <= 0) {
            http_response_code(404);
            die('Course not found');
        }

        $course = $this->courseModel->getCourseStats($courseId);

        if (!$course) {
            http_response_code(404);
            die('Course not found');
        }

        $posts = $this->postModel->getCoursePost($courseId);
        
        // Check if current user is enrolled (for students)
        $isEnrolled = false;
        if ($this->isAuthenticated() && $this->isStudent()) {
            $isEnrolled = $this->courseModel->isStudentEnrolled($this->getCurrentUser()['id'], $courseId);
        }

        $this->render('courses.view', [
            'course' => $course,
            'posts' => $posts,
            'isEnrolled' => $isEnrolled,
        ]);
    }

    /**
     * Enroll student in course
     */
    public function enroll()
    {
        $this->requireRole('student');
        
        $courseId = intval($_GET['id'] ?? 0);
        
        if ($courseId <= 0) {
            http_response_code(404);
            die('Course not found');
        }

        try {
            $success = $this->courseModel->enrollStudent($this->getCurrentUser()['id'], $courseId);
            
            if ($success) {
                $_SESSION['success'] = 'Enrolled in course successfully!';
            } else {
                $_SESSION['errors'] = ['general' => 'Already enrolled in this course'];
            }
        } catch (\Exception $e) {
            $_SESSION['errors'] = ['general' => 'Enrollment failed: ' . $e->getMessage()];
        }

        $this->redirect('/student-hub/public/index.php?route=courses/view&id=' . $courseId);
    }

    /**
     * Show course edit form
     */
    public function edit()
    {
        $this->requireRole('teacher');
        
        $courseId = intval($_GET['id'] ?? 0);
        $course = $this->courseModel->find($courseId);

        if (!$course || $course['teacher_id'] !== $this->getCurrentUser()['id']) {
            http_response_code(403);
            die('Unauthorized');
        }

        $this->render('courses.edit', ['course' => $course]);
    }

    /**
     * Update course
     */
    public function update()
    {
        $this->requireRole('teacher');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            die('Method Not Allowed');
        }

        $courseId = intval($_POST['id'] ?? 0);
        $course = $this->courseModel->find($courseId);

        if (!$course || $course['teacher_id'] !== $this->getCurrentUser()['id']) {
            http_response_code(403);
            die('Unauthorized');
        }

        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $is_published = isset($_POST['is_published']) ? 1 : 0;

        try {
            $this->courseModel->update($courseId, [
                'name' => $name,
                'description' => $description,
                'category' => $category,
                'is_published' => $is_published,
            ]);

            $_SESSION['success'] = 'Course updated successfully!';
            $this->redirect('/student-hub/public/index.php?route=courses/view&id=' . $courseId);
        } catch (\Exception $e) {
            $_SESSION['errors'] = ['general' => 'Update failed: ' . $e->getMessage()];
            $this->redirect('/student-hub/public/index.php?route=courses/edit&id=' . $courseId);
        }
    }

    /**
     * Delete course
     */
    public function delete()
    {
        $this->requireRole('teacher');
        
        $courseId = intval($_GET['id'] ?? 0);
        $course = $this->courseModel->find($courseId);

        if (!$course || $course['teacher_id'] !== $this->getCurrentUser()['id']) {
            http_response_code(403);
            die('Unauthorized');
        }

        try {
            $this->courseModel->delete($courseId);
            $_SESSION['success'] = 'Course deleted successfully!';
            $this->redirect('/student-hub/public/index.php?route=dashboard');
        } catch (\Exception $e) {
            $_SESSION['errors'] = ['general' => 'Deletion failed: ' . $e->getMessage()];
            $this->redirect('/student-hub/public/index.php?route=courses/view&id=' . $courseId);
        }
    }
}
