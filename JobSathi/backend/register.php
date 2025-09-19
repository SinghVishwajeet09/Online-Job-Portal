<?php
require 'db.php';

$data = json_decode(file_get_contents("php://input"));

// --- VALIDATION ---
if (empty($data->fullname) || empty($data->email) || empty($data->password) || empty($data->phone)) {
    json_response(['success' => false, 'message' => 'All fields are required.'], 400);
}
if (!filter_var($data->email, FILTER_VALIDATE_EMAIL)) {
    json_response(['success' => false, 'message' => 'Invalid email format.'], 400);
}
if (strlen($data->password) < 6) {
    json_response(['success' => false, 'message' => 'Password must be at least 6 characters.'], 400);
}


try {
    // Check if email already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$data->email]);
    if ($stmt->fetch()) {
        json_response(['success' => false, 'message' => 'Email is already registered.'], 409); // 409 Conflict
    }

    // Hash the password
    $hashed_password = password_hash($data->password, PASSWORD_DEFAULT);

    // Insert new user
    $stmt = $pdo->prepare("INSERT INTO users (fullname, email, phone, password) VALUES (?, ?, ?, ?)");
    $stmt->execute([$data->fullname, $data->email, $data->phone, $hashed_password]);
    
    json_response(['success' => true, 'message' => 'Registration successful! Please log in.']);

} catch (PDOException $e) {
    json_response(['success' => false, 'message' => 'Database error: ' . $e->getMessage()], 500);
}
?>
