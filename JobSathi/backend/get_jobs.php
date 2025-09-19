<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'db.php';

// Comment out the setup_tables call to avoid duplicate executions
// setup_tables($pdo);

try {
    // Base SQL query
    $sql = "SELECT id, title, company, location, experience, salary, category FROM jobs WHERE 1=1";
    $params = [];

    // Filter by category - THIS WORKS
    if (!empty($_GET['category']) && $_GET['category'] !== 'all') {
        $sql .= " AND category = :category";
        $params[':category'] = $_GET['category'];
    }

    // Filter by search term (title, company) - THIS IS FIXED
    if (!empty($_GET['search'])) {
        $searchTerm = '%' . $_GET['search'] . '%';
        // $sql .= " AND (title LIKE :search OR company LIKE :search)";
        // $params[':search'] = $searchTerm;
          $sql .= " AND (title LIKE :searchTitle OR company LIKE :searchCompany)";
$params[':searchTitle'] = $searchTerm;
$params[':searchCompany'] = $searchTerm;
    }
  


    $sql .= " ORDER BY id DESC";

    // Debug logging
    error_log("SQL: " . $sql);
    error_log("Params: " . print_r($params, true));

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $jobs = $stmt->fetchAll();

    json_response(['success' => true, 'jobs' => $jobs]);

} catch (PDOException $e) {
    json_response(['success' => false, 'message' => 'Error fetching jobs: ' . $e->getMessage()], 500);
}

// Keep the setup_tables function but don't call it automatically
function setup_tables($pdo) {
    // Check if jobs table exists, create if not
    $tableCheck = $pdo->query("SHOW TABLES LIKE 'jobs'");
    if ($tableCheck->rowCount() == 0) {
        // Create jobs table
        $pdo->exec("CREATE TABLE jobs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            company VARCHAR(255) NOT NULL,
            location VARCHAR(255) NOT NULL,
            experience VARCHAR(100) NOT NULL,
            salary VARCHAR(100) NOT NULL,
            category VARCHAR(100) NOT NULL
        )");
        
        // Insert sample data
        $pdo->exec("INSERT INTO jobs (title, company, location, experience, salary, category) VALUES 
            ('Web Developer', 'Tech Solutions Inc', 'Mumbai', '2-3 years', '₹6-8 LPA', 'tech'),
            ('Marketing Manager', 'Global Marketing', 'Delhi', '4-6 years', '₹12-15 LPA', 'marketing'),
            ('Data Analyst', 'Data Insights', 'Bengaluru', '1-3 years', '₹5-7 LPA', 'tech')");
    }
}
?>
