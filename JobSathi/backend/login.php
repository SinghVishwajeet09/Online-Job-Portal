<?php
require 'db.php';
session_start();

$data = json_decode(file_get_contents("php://input"));

// --- VALIDATION ---
if (empty($data->email) || empty($data->password)) {
    json_response(['success' => false, 'message' => 'Email and password are required.'], 400);
}

try {
    // Find user by email
    $stmt = $pdo->prepare("SELECT id, fullname, email, phone, password FROM users WHERE email = ?");
    $stmt->execute([$data->email]);
    $user = $stmt->fetch();

    // Verify user and password
    if ($user && password_verify($data->password, $user['password'])) {
        // Successful login
        $_SESSION['user_id'] = $user['id']; // Store user ID in session
        unset($user['password']); // Do not send password hash to client
        json_response([
            'success' => true, 
            'message' => 'Login successful!',
            'user' => $user
        ]);
    } else {
        // Failed login
        json_response(['success' => false, 'message' => 'Invalid email or password.'], 401); // 401 Unauthorized
    }

} catch (PDOException $e) {
    json_response(['success' => false, 'message' => 'Database error: ' . $e->getMessage()], 500);
}
?>
