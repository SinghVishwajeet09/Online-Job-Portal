<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'db.php';

try {
    // Add tech jobs
    $techJobs = [
        [
            'title' => 'Frontend Developer',
            'company' => 'TechCorp Solutions',
            'location' => 'Bengaluru',
            'experience' => '2-4 years',
            'salary' => '₹8-12 LPA',
            'category' => 'tech'
        ],
        [
            'title' => 'Software Engineer',
            'company' => 'InnovateTech',
            'location' => 'Hyderabad',
            'experience' => '3-5 years',
            'salary' => '₹10-15 LPA',
            'category' => 'tech'
        ],
        [
            'title' => 'Full Stack Developer',
            'company' => 'Digital Solutions',
            'location' => 'Pune',
            'experience' => '4-6 years',
            'salary' => '₹12-18 LPA',
            'category' => 'tech'
        ],
        [
            'title' => 'DevOps Engineer',
            'company' => 'CloudTech Systems',
            'location' => 'Mumbai',
            'experience' => '3-5 years',
            'salary' => '₹14-18 LPA',
            'category' => 'tech'
        ]
    ];

    // Insert tech jobs
    $stmt = $pdo->prepare("INSERT INTO jobs (title, company, location, experience, salary, category) 
                          VALUES (:title, :company, :location, :experience, :salary, :category)");

    foreach ($techJobs as $job) {
        $stmt->execute($job);
    }

    echo "Successfully added tech jobs!";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>