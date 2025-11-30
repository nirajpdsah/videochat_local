<?php
/**
 * API Endpoint: Messages
 * GET: Retrieve messages with a user
 * POST: Send a message to a user
 */
require_once '../config.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$current_user_id = $_SESSION['user_id'];

// Handle POST request (send message)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $to_user_id = isset($input['to_user_id']) ? intval($input['to_user_id']) : 0;
    $message = isset($input['message']) ? trim($input['message']) : '';
    
    if ($to_user_id == 0 || empty($message)) {
        echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
        exit();
    }
    
    // Insert message
    $stmt = $conn->prepare("INSERT INTO messages (from_user_id, to_user_id, message) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $current_user_id, $to_user_id, $message);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message_id' => $conn->insert_id
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to send message']);
    }
    exit();
}

// Handle GET request (retrieve messages)
$other_user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;

if ($other_user_id == 0) {
    echo json_encode(['success' => false, 'message' => 'User ID required']);
    exit();
}

// Get messages between current user and other user
$stmt = $conn->prepare("
    SELECT m.id, m.from_user_id, m.to_user_id, m.message, m.created_at,
           u.username, u.profile_picture
    FROM messages m
    JOIN users u ON (m.from_user_id = u.id)
    WHERE (m.from_user_id = ? AND m.to_user_id = ?) 
       OR (m.from_user_id = ? AND m.to_user_id = ?)
    ORDER BY m.created_at ASC
");
$stmt->bind_param("iiii", $current_user_id, $other_user_id, $other_user_id, $current_user_id);
$stmt->execute();
$result = $stmt->get_result();

$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = [
        'id' => $row['id'],
        'from_user_id' => $row['from_user_id'],
        'to_user_id' => $row['to_user_id'],
        'message' => $row['message'],
        'username' => $row['username'],
        'profile_picture' => $row['profile_picture'],
        'created_at' => $row['created_at']
    ];
}

// Mark messages as read
$update_stmt = $conn->prepare("UPDATE messages SET is_read = 1 WHERE to_user_id = ? AND from_user_id = ?");
$update_stmt->bind_param("ii", $current_user_id, $other_user_id);
$update_stmt->execute();

echo json_encode([
    'success' => true,
    'messages' => $messages
]);
?>