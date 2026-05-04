<?php
/**
 * UserController.php - User Profile Controller
 * 
 * Handles user profile viewing and editing.
 * 
 * @author Senior Full Stack Developer
 * @version 1.0
 */

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;

class UserController extends Controller
{
    private $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new User();
    }

    /**
     * View user profile
     */
    public function profile()
    {
        $userId = intval($_GET['id'] ?? 0);
        
        if ($userId <= 0) {
            http_response_code(404);
            die('User not found');
        }

        $user = $this->userModel->getUserWithRole($userId);

        if (!$user) {
            http_response_code(404);
            die('User not found');
        }

        $this->render('users.profile', ['profile' => $user]);
    }

    /**
     * Show profile edit form
     */
    public function edit()
    {
        $this->requireAuth();
        
        $user = $this->userModel->find($this->getCurrentUser()['id']);
        $this->render('users.edit', ['user' => $user]);
    }

    /**
     * Update user profile
     */
    public function update()
    {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            die('Method Not Allowed');
        }

        $userId = $this->getCurrentUser()['id'];
        $name = trim($_POST['name'] ?? '');
        $bio = trim($_POST['bio'] ?? '');

        $errors = [];

        if (empty($name)) {
            $errors['name'] = 'Name is required';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $this->redirect('/student-hub/public/index.php?route=users/edit');
        }

        try {
            $this->userModel->update($userId, [
                'name' => $name,
                'bio' => $bio,
            ]);

            // Update session
            $_SESSION['user']['name'] = $name;

            $_SESSION['success'] = 'Profile updated successfully!';
            $this->redirect('/student-hub/public/index.php?route=users/profile&id=' . $userId);
        } catch (\Exception $e) {
            $_SESSION['errors'] = ['general' => 'Update failed: ' . $e->getMessage()];
            $this->redirect('/student-hub/public/index.php?route=users/edit');
        }
    }
}
