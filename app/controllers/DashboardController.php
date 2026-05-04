<?php
/**
 * DashboardController.php - Dashboard Controller
 * 
 * Handles user dashboard and profile overview.
 * 
 * @author Senior Full Stack Developer
 * @version 1.0
 */

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Models\Course;
use App\Models\Post;
use App\Models\Notification;

class DashboardController extends Controller
{
    private $userModel;
    private $courseModel;
    private $postModel;
    private $notificationModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new User();
        $this->courseModel = new Course();
        $this->postModel = new Post();
        $this->notificationModel = new Notification();
    }

    /**
     * Display user dashboard
     */
    public function index()
    {
        $this->requireAuth();
        
        $user = $this->getCurrentUser();
        $userId = $user['id'];
        $userRole = $user['role'];

        $data = [
            'user' => $this->userModel->find($userId),
            'notifications' => $this->notificationModel->getUserNotifications($userId, 10),
            'unreadCount' => $this->notificationModel->countUnread($userId),
        ];

        // Load role-specific data
        if ($userRole === 'teacher') {
            $data['courses'] = $this->courseModel->getTeacherCourses($userId);
            $data['recentPosts'] = $this->postModel->getUserPosts($userId, 5);
            $this->render('dashboard.teacher', $data);
        } elseif ($userRole === 'student') {
            $data['courses'] = $this->courseModel->getStudentCourses($userId);
            $data['recentPosts'] = $this->postModel->getUserPosts($userId, 5);
            $this->render('dashboard.student', $data);
        } else {
            $this->render('dashboard.index', $data);
        }
    }
}
