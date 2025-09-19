<?php
require 'db.php';
session_start();

header('Content-Type: application/json');

// Get user_id from session (ensure user is logged in)
$user_id = $_SESSION['user_id'] ?? null;
$data = json_decode(file_get_contents('php://input'), true);
$job_id = $data['job_id'] ?? null;

if (!$user_id) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}
if (!$job_id) {
    echo json_encode(['success' => false, 'message' => 'No job specified']);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT IGNORE INTO favourites (user_id, job_id) VALUES (?, ?)");
    $stmt->execute([$user_id, $job_id]);
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
?>