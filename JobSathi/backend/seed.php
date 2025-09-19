<?php
// This file is included by get_jobs.php to set up the database.

function setup_tables(PDO $pdo) {
    try {
        // Create users table
        $pdo->exec("CREATE TABLE IF NOT EXISTS `users` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `fullname` VARCHAR(255) NOT NULL,
            `email` VARCHAR(255) NOT NULL UNIQUE,
            `phone` VARCHAR(20) NOT NULL,
            `password` VARCHAR(255) NOT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        // Create jobs table
        $pdo->exec("CREATE TABLE IF NOT EXISTS `jobs` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `title` VARCHAR(255) NOT NULL,
            `company` VARCHAR(255) NOT NULL,
            `location` VARCHAR(255) NOT NULL,
            `experience` VARCHAR(100) NOT NULL,
            `salary` VARCHAR(100) NOT NULL,
            `category` VARCHAR(100) NOT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
        
        // Create applications table
        $pdo->exec("CREATE TABLE IF NOT EXISTS `applications` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `job_id` INT NOT NULL,
            `user_id` INT NOT NULL,
            `fullname` VARCHAR(255) NOT NULL,
            `email` VARCHAR(255) NOT NULL,
            `phone` VARCHAR(20) NOT NULL,
            `cover_letter` TEXT,
            `applied_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        // Check if jobs table is empty
        $stmt = $pdo->query("SELECT id FROM jobs LIMIT 1");
        if ($stmt->fetch()) {
            return; // Table has data, no need to seed.
        }

        // Seed jobs data
        $jobs = [
            ['Software Engineer', 'Tech Mahindra', 'Pune, India', '2-4 years', '₹8-12 LPA', 'software'],
            ['Marketing Manager', 'Growth Solutions', 'Mumbai, India', '5+ years', '₹15-20 LPA', 'marketing'],
            ['Data Analyst', 'Infosys', 'Bengaluru, India', '1-3 years', '₹6-9 LPA', 'software'],
            ['Relationship Manager', 'HDFC Bank', 'Delhi, India', '3-5 years', '₹7-11 LPA', 'banking'],
            ['Registered Nurse', 'Apollo Hospitals', 'Chennai, India', '2+ years', '₹4-6 LPA', 'healthcare'],
            ['Full-Stack Developer', 'Wipro', 'Remote', '4-6 years', '₹12-18 LPA', 'software'],
            ['Digital Marketer', 'Ad-Ventures Inc.', 'Bengaluru, India', '2-3 years', '₹5-8 LPA', 'marketing'],
        ];

        $stmt = $pdo->prepare("INSERT INTO jobs (title, company, location, experience, salary, category) VALUES (?, ?, ?, ?, ?, ?)");
        foreach ($jobs as $job) {
            $stmt->execute($job);
        }

    } catch (PDOException $e) {
        // If table creation fails, it's a critical error.
        json_response(['success' => false, 'message' => 'Database setup failed: ' . $e->getMessage()], 500);
    }
}
?>
