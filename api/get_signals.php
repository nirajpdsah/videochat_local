<?php
/**
 * API Endpoint: Get pending WebRTC signals for current user
 * Retrieves unread signals sent to this user
 */
require_once '../config.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['user_id'];

// Get unread signals for this user
$stmt = $conn->prepare("
    SELECT s.id, s.from_user_id, s.signal_type, s.signal_data, s.call_type, 
           u.username, u.profile_picture
    FROM signals s
    JOIN users u ON s.from_user_id = u.id
    WHERE s.to_user_id = ? AND s.is_read = 0
    ORDER BY s.created_at ASC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$signals = [];
$signal_ids = [];

while ($row = $result->fetch_assoc()) {
    $signals[] = [
        'id' => $row['id'],
        'from_user_id' => $row['from_user_id'],
        'from_username' => $row['username'],
        'from_profile_picture' => $row['profile_picture'],
        'signal_type' => $row['signal_type'],
        'signal_data' => json_decode($row['signal_data'], true),
        'call_type' => $row['call_type']
    ];
    $signal_ids[] = $row['id'];
}

// Mark signals as read
if (!empty($signal_ids)) {
    $ids_string = implode(',', $signal_ids);
    $conn->query("UPDATE signals SET is_read = 1 WHERE id IN ($ids_string)");
}

echo json_encode([
    'success' => true,
    'signals' => $signals
]);
?>