<?php
require 'db.php';

$data = json_decode(file_get_contents("php://input"));

// --- VALIDATION ---
if (empty($data->job_id) || empty($data->user_id) || empty($data->fullname) || empty($data->email) || empty($data->phone)) {
    json_response(['success' => false, 'message' => 'Missing required application data.'], 400);
}

try {
    // Check if user already applied for this job
    $stmt = $pdo->prepare("SELECT id FROM applications WHERE user_id = ? AND job_id = ?");
    $stmt->execute([$data->user_id, $data->job_id]);
    if ($stmt->fetch()) {
        json_response(['success' => false, 'message' => 'You have already applied for this job.'], 409); // 409 Conflict
    }

    // Insert application
    $stmt = $pdo->prepare("INSERT INTO applications (job_id, user_id, fullname, email, phone, cover_letter) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $data->job_id, 
        $data->user_id, 
        $data->fullname, 
        $data->email, 
        $data->phone, 
        $data->cover_letter ?? ''
    ]);

    json_response(['success' => true, 'message' => 'Application submitted successfully!']);

} catch (PDOException $e) {
    json_response(['success' => false, 'message' => 'Database error: ' . $e->getMessage()], 500);
}
?>