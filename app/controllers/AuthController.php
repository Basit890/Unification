<?php

class AuthController {
    private $userModel;
    
    public function __construct($userModel) {
        $this->userModel = $userModel;
    }
    
    public function register($data) {
        if (empty($data['first_name']) || empty($data['last_name']) || empty($data['email']) || empty($data['password']) || empty($data['religion']) || empty($data['user_type'])) {
            return ['success' => false, 'message' => 'All fields are required'];
        }
        
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Invalid email format'];
        }
        
        if (strlen($data['password']) < 6) {
            return ['success' => false, 'message' => 'Password must be at least 6 characters'];
        }
        
        $allowedReligions = ['islam', 'hindu', 'christian', 'buddhist', 'other'];
        if (!in_array($data['religion'], $allowedReligions)) {
            return ['success' => false, 'message' => 'Invalid religion selection'];
        }
        
        $allowedUserTypes = ['admin', 'donor', 'fundraiser'];
        if (!in_array($data['user_type'], $allowedUserTypes)) {
            return ['success' => false, 'message' => 'Invalid user type selection'];
        }
        
        $isAdmin = ($data['user_type'] === 'admin');
        
        if ($this->userModel->create($data['first_name'], $data['last_name'], $data['email'], $data['password'], $data['religion'], $data['user_type'], $isAdmin)) {
            return ['success' => true, 'message' => 'Registration successful! Please login.'];
        } else {
            return ['success' => false, 'message' => 'Registration failed. Email may already exist.'];
        }
    }
    
    public function login($data) {
        if (empty($data['email']) || empty($data['password'])) {
            return ['success' => false, 'message' => 'Email and password are required'];
        }
        
        $user = $this->userModel->authenticate($data['email'], $data['password']);
        
        if ($user) {
            Session::login($user);
            return ['success' => true, 'message' => 'Login successful!'];
        } else {
            return ['success' => false, 'message' => 'Invalid credentials.'];
        }
    }
    
    public function logout() {
        Session::logout();
        return ['success' => true, 'message' => 'Logged out successfully'];
    }
} 