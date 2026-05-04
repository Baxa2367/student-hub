<?php
/**
 * AuthController.php - Authentication Controller
 * 
 * Handles user registration, login, and logout.
 * 
 * @author Senior Full Stack Developer
 * @version 1.0
 */

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;

class AuthController extends Controller
{
    private $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new User();
    }

    /**
     * Show registration form
     */
    public function register()
    {
        if ($this->isAuthenticated()) {
            $this->redirect('/student-hub/public/index.php?route=dashboard');
        }
        $this->render('auth.register');
    }

    /**
     * Handle registration form submission
     */
    public function postRegister()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            die('Method Not Allowed');
        }

        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $password_confirm = $_POST['password_confirm'] ?? '';
        $role = $_POST['role'] ?? 'student';

        // Validation
        $errors = [];

        if (empty($name)) {
            $errors['name'] = 'Name is required';
        }

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Valid email is required';
        } elseif ($this->userModel->emailExists($email)) {
            $errors['email'] = 'Email already registered';
        }

        if (empty($password) || strlen($password) < 6) {
            $errors['password'] = 'Password must be at least 6 characters';
        }

        if ($password !== $password_confirm) {
            $errors['password_confirm'] = 'Passwords do not match';
        }

        if (!in_array($role, ['student', 'teacher'])) {
            $errors['role'] = 'Invalid role';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old_input'] = [
                'name' => $name,
                'email' => $email,
                'role' => $role,
            ];
            $this->redirect('/student-hub/public/index.php?route=auth/register');
        }

        // Register user
        try {
            $role_id = $role === 'teacher' ? 2 : 3;
            
            $userId = $this->userModel->create([
                'name' => $name,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_BCRYPT),
                'role_id' => $role_id,
                'is_active' => true,
            ]);

            $_SESSION['success'] = 'Registration successful! Please login.';
            $this->redirect('/student-hub/public/index.php?route=auth/login');
        } catch (\Exception $e) {
            $_SESSION['errors'] = ['general' => 'Registration failed: ' . $e->getMessage()];
            $this->redirect('/student-hub/public/index.php?route=auth/register');
        }
    }

    /**
     * Show login form
     */
    public function login()
    {
        if ($this->isAuthenticated()) {
            $this->redirect('/student-hub/public/index.php?route=dashboard');
        }
        $this->render('auth.login');
    }

    /**
     * Handle login form submission
     */
    public function postLogin()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            die('Method Not Allowed');
        }

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        // Validation
        $errors = [];

        if (empty($email)) {
            $errors['email'] = 'Email is required';
        }

        if (empty($password)) {
            $errors['password'] = 'Password is required';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $this->redirect('/student-hub/public/index.php?route=auth/login');
        }

        // Find user
        $user = $this->userModel->findByEmail($email);

        if (!$user || !$this->userModel->verifyPassword($password, $user['password'])) {
            $_SESSION['errors'] = ['general' => 'Invalid credentials'];
            $this->redirect('/student-hub/public/index.php?route=auth/login');
        }

        if (!$user['is_active']) {
            $_SESSION['errors'] = ['general' => 'Account is inactive'];
            $this->redirect('/student-hub/public/index.php?route=auth/login');
        }

        // Get user with role
        $userWithRole = $this->userModel->getUserWithRole($user['id']);

        // Set session
        $_SESSION['user'] = [
            'id' => $userWithRole['id'],
            'name' => $userWithRole['name'],
            'email' => $userWithRole['email'],
            'role' => $userWithRole['role'],
            'avatar' => $userWithRole['avatar'],
        ];

        $_SESSION['success'] = 'Welcome back, ' . $userWithRole['name'];
        $this->redirect('/student-hub/public/index.php?route=dashboard');
    }

    /**
     * Handle logout
     */
    public function logout()
    {
        session_destroy();
        $this->redirect('/student-hub/public/index.php?route=home');
    }
}
