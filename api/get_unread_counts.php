<?php
/**
 * API Endpoint: Get Unread Message Counts
 * Returns the count of unread messages from each user
 */
require_once '../config.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$current_user_id = $_SESSION['user_id'];

// Get unread message counts grouped by sender
$stmt = $conn->prepare("
    SELECT from_user_id, COUNT(*) as unread_count
    FROM messages
    WHERE to_user_id = ? AND is_read = 0
    GROUP BY from_user_id
");
$stmt->bind_param("i", $current_user_id);
$stmt->execute();
$result = $stmt->get_result();

$unread_counts = [];
while ($row = $result->fetch_assoc()) {
    $unread_counts[$row['from_user_id']] = (int) $row['unread_count'];
}

echo json_encode([
    'success' => true,
    'unread_counts' => $unread_counts
]);
?>