<?php
/**
 * API Endpoint: User Registration
 * Alternative API-based registration (can be used by mobile apps or AJAX)
 * Returns JSON response
 */
require_once '../config.php';

header('Content-Type: application/json');

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

// Get JSON input or form data
$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    $input = $_POST; // Fallback to form data
}

$username = isset($input['username']) ? cleanInput($input['username']) : '';
$email = isset($input['email']) ? cleanInput($input['email']) : '';
$password = isset($input['password']) ? $input['password'] : '';

// Validation
if (empty($username) || empty($email) || empty($password)) {
    echo json_encode([
        'success' => false, 
        'message' => 'All fields are required'
    ]);
    exit();
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        'success' => false, 
        'message' => 'Invalid email format'
    ]);
    exit();
}

// Validate password length
if (strlen($password) < 6) {
    echo json_encode([
        'success' => false, 
        'message' => 'Password must be at least 6 characters'
    ]);
    exit();
}

// Check if username or email already exists
$check_stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
$check_stmt->bind_param("ss", $username, $email);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows > 0) {
    echo json_encode([
        'success' => false, 
        'message' => 'Username or email already exists'
    ]);
    exit();
}

// Hash password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Handle profile picture (default)
$profile_picture = 'default-avatar.png';

// Insert new user
$insert_stmt = $conn->prepare("INSERT INTO users (username, email, password, profile_picture) VALUES (?, ?, ?, ?)");
$insert_stmt->bind_param("ssss", $username, $email, $hashed_password, $profile_picture);

if ($insert_stmt->execute()) {
    $user_id = $conn->insert_id;
    
    echo json_encode([
        'success' => true,
        'message' => 'Registration successful',
        'user_id' => $user_id,
        'username' => $username,
        'email' => $email
    ]);
} else {
    echo json_encode([
        'success' => false, 
        'message' => 'Registration failed. Please try again'
    ]);
}
?>