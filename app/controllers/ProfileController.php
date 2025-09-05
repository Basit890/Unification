<?php

require_once 'app/helpers/Session.php';
require_once 'app/helpers/FileUpload.php';

class ProfileController {
    private $userModel;
    private $fileUpload;

    public function __construct($userModel, $fileUpload = null) {
        $this->userModel = $userModel;
        $this->fileUpload = $fileUpload ?: new FileUpload();
    }

    public function index() {
        if (!Session::isLoggedIn()) {
            $this->redirect('index.php?page=login');
        }

        $user = $this->userModel->findById(Session::getUserId());
        
        $userTypeLabels = [
            'admin' => 'Administrator',
            'donor' => 'Donor',
            'fundraiser' => 'Fundraiser'
        ];
        
        $data = [
            'user' => $user,
            'userType' => $userTypeLabels[$user['user_type']] ?? 'User',
            'userEmail' => $user['email'] ?? 'Not available',
            'userUsername' => $user['username'] ?? 'Not available'
        ];

        return $data;
    }

    public function updateProfile() {
        // Suppress all output and start buffering
        ob_start();
        error_reporting(0);
        ini_set('display_errors', 0);
        
        if (!Session::isLoggedIn()) {
            ob_clean();
            $this->jsonResponse(['success' => false, 'message' => 'Not logged in']);
        }

        $userId = Session::getUserId();
        $user = $this->userModel->findById($userId);

        if (!$user) {
            $this->jsonResponse(['success' => false, 'message' => 'User not found']);
        }

        $errors = [];
        $updateData = [];

        // Validate and update first name
        if (isset($_POST['first_name']) && !empty(trim($_POST['first_name']))) {
            $firstName = trim($_POST['first_name']);
            if (strlen($firstName) < 2) {
                $errors[] = 'First name must be at least 2 characters long';
            } elseif (strlen($firstName) > 50) {
                $errors[] = 'First name must be less than 50 characters';
            } else {
                $updateData['first_name'] = $firstName;
            }
        }

        // Validate and update last name
        if (isset($_POST['last_name']) && !empty(trim($_POST['last_name']))) {
            $lastName = trim($_POST['last_name']);
            if (strlen($lastName) < 2) {
                $errors[] = 'Last name must be at least 2 characters long';
            } elseif (strlen($lastName) > 50) {
                $errors[] = 'Last name must be less than 50 characters';
            } else {
                $updateData['last_name'] = $lastName;
            }
        }

        // Validate and update username
        if (isset($_POST['username']) && !empty(trim($_POST['username']))) {
            $username = trim($_POST['username']);
            if (strlen($username) < 3) {
                $errors[] = 'Username must be at least 3 characters long';
            } elseif (strlen($username) > 30) {
                $errors[] = 'Username must be less than 30 characters';
            } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
                $errors[] = 'Username can only contain letters, numbers, and underscores';
            } else {
                // Check if username is already taken by another user
                $existingUser = $this->userModel->findByUsername($username);
                if ($existingUser && $existingUser['id'] != $userId) {
                    $errors[] = 'Username is already taken';
                } else {
                    $updateData['username'] = $username;
                }
            }
        }

        // Validate and update religion
        if (isset($_POST['religion']) && !empty(trim($_POST['religion']))) {
            $religion = trim($_POST['religion']);
            $allowedReligions = ['islam', 'christianity', 'hinduism', 'buddhism', 'other'];
            if (in_array(strtolower($religion), $allowedReligions)) {
                $updateData['religion'] = strtolower($religion);
            } else {
                $errors[] = 'Invalid religion selection';
            }
        }

        // Handle profile picture upload
        if (isset($_FILES['profile_picture'])) {
            $file = $_FILES['profile_picture'];
            
            // Check for upload errors
            if ($file['error'] !== UPLOAD_ERR_OK) {
                $uploadErrors = [
                    UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize directive',
                    UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE directive',
                    UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
                    UPLOAD_ERR_NO_FILE => 'No file was uploaded',
                    UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
                    UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
                    UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload'
                ];
                $errorMessage = $uploadErrors[$file['error']] ?? 'Unknown upload error';
                $errors[] = 'Upload error: ' . $errorMessage;
            } else {
                try {
                    $uploadResult = $this->fileUpload->uploadImage($file);
                    
                    if ($uploadResult['success']) {
                        // Delete old profile picture if exists
                        if (!empty($user['profile_picture']) && file_exists($user['profile_picture'])) {
                            unlink($user['profile_picture']);
                        }
                        $updateData['profile_picture'] = $uploadResult['filepath'];
                    } else {
                        $errors[] = 'Upload failed: ' . $uploadResult['error'];
                    }
                } catch (Exception $e) {
                    $errors[] = 'Upload error: ' . $e->getMessage();
                }
            }
        }

        if (!empty($errors)) {
            $this->jsonResponse(['success' => false, 'message' => implode(', ', $errors)]);
        }

        if (empty($updateData)) {
            $this->jsonResponse(['success' => false, 'message' => 'No changes to update']);
        }

        // Update user data
        $result = $this->userModel->update($userId, $updateData);

        if ($result) {
            // Update session data if name was changed
            if (isset($updateData['first_name']) || isset($updateData['last_name'])) {
                $updatedUser = $this->userModel->findById($userId);
                Session::setFirstName($updatedUser['first_name']);
                Session::setLastName($updatedUser['last_name']);
            }
            
            $this->jsonResponse(['success' => true, 'message' => 'Profile updated successfully']);
        } else {
            $this->jsonResponse(['success' => false, 'message' => 'Failed to update profile']);
        }
    }

    public function deleteProfilePicture() {
        // Suppress all output and start buffering
        ob_start();
        error_reporting(0);
        ini_set('display_errors', 0);
        
        if (!Session::isLoggedIn()) {
            ob_clean();
            $this->jsonResponse(['success' => false, 'message' => 'Not logged in']);
        }

        $userId = Session::getUserId();
        $user = $this->userModel->findById($userId);

        if (!$user) {
            $this->jsonResponse(['success' => false, 'message' => 'User not found']);
        }

        if (empty($user['profile_picture'])) {
            $this->jsonResponse(['success' => false, 'message' => 'No profile picture to delete']);
        }

        // Delete the file
        if (file_exists($user['profile_picture'])) {
            unlink($user['profile_picture']);
        }

        // Update database
        $result = $this->userModel->update($userId, ['profile_picture' => null]);

        if ($result) {
            $this->jsonResponse(['success' => true, 'message' => 'Profile picture deleted successfully']);
        } else {
            $this->jsonResponse(['success' => false, 'message' => 'Failed to delete profile picture']);
        }
    }

    private function jsonResponse($data) {
        // Clean any output that might have been generated
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        // Set proper headers
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: 0');
        
        // Output JSON and exit
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit();
    }

    private function redirect($url) {
        header("Location: $url");
        exit();
    }
}
