<?php
/**
 * API Endpoint: User Authentication (Login)
 * Alternative API-based login (can be used by mobile apps or AJAX)
 * Returns JSON response and creates session
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
$password = isset($input['password']) ? $input['password'] : '';

// Validation
if (empty($username) || empty($password)) {
    echo json_encode([
        'success' => false, 
        'message' => 'Username and password are required'
    ]);
    exit();
}

// Find user by username or email
$stmt = $conn->prepare("SELECT id, username, email, password, profile_picture FROM users WHERE username = ? OR email = ?");
$stmt->bind_param("ss", $username, $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode([
        'success' => false, 
        'message' => 'Invalid username or password'
    ]);
    exit();
}

$user = $result->fetch_assoc();

// Verify password
if (!password_verify($password, $user['password'])) {
    echo json_encode([
        'success' => false, 
        'message' => 'Invalid username or password'
    ]);
    exit();
}

// Set session variables
$_SESSION['user_id'] = $user['id'];
$_SESSION['username'] = $user['username'];

// Update user status to online
$update_stmt = $conn->prepare("UPDATE users SET status = 'online' WHERE id = ?");
$update_stmt->bind_param("i", $user['id']);
$update_stmt->execute();

// Return success with user data
echo json_encode([
    'success' => true,
    'message' => 'Login successful',
    'user' => [
        'id' => $user['id'],
        'username' => $user['username'],
        'email' => $user['email'],
        'profile_picture' => $user['profile_picture']
    ]
]);
?>