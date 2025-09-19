<?php
// --- CONFIGURATION ---
define('DB_HOST', '127.0.0.1'); // Or your database host
define('DB_NAME', 'jobportal');
define('DB_USER', 'root');
define('DB_PASS', ''); // Your database password, often empty for local XAMPP
define('DB_CHARSET', 'utf8mb4');

// --- HEADERS ---
// These headers allow cross-origin requests, useful for development.
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Handle pre-flight OPTIONS request (sent by browsers to check permissions)
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

// --- DATABASE CONNECTION (PDO) ---
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Throw exceptions on errors
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Fetch associative arrays
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Use native prepared statements
];

$dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;

try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (\PDOException $e) {
    // If connection fails, stop the script and send a server error response.
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database connection failed. Please check your db.php configuration.',
        'error' => $e->getMessage()
    ]);
    exit(); // Stop script execution
}

// This function can be used to send a standardized JSON response.
function json_response($data, $status = 200) {
    header('Content-Type: application/json');
    http_response_code($status);
    echo json_encode($data);
    exit;
}
?>
