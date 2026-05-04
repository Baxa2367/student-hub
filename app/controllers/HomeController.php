<?php
/**
 * HomeController.php - Home Page Controller
 * 
 * Handles home page and general information.
 * 
 * @author Senior Full Stack Developer
 * @version 1.0
 */

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Course;
use App\Models\User;
use App\Models\Post;

class HomeController extends Controller
{
    private $courseModel;
    private $userModel;
    private $postModel;

    public function __construct()
    {
        parent::__construct();
        $this->courseModel = new Course();
        $this->userModel = new User();
        $this->postModel = new Post();
    }

    /**
     * Display home page
     */
    public function index()
    {
        // Get published courses
        $courses = $this->courseModel->getPublishedCourses();
        
        // Get teachers count
        $teachers = $this->userModel->getUsersByRole('teacher');
        
        // Get trending posts
        $trendingPosts = $this->postModel->getTrendingPosts(5);

        $this->render('home.index', [
            'courses' => $courses,
            'teachersCount' => count($teachers),
            'coursesCount' => count($courses),
            'trendingPosts' => $trendingPosts,
        ]);
    }
}
