<?php
require 'db.php'; // Your PDO connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $job_id = $_POST['job_id'];
    $name = $_POST['applicantName'];
    $email = $_POST['applicantEmail'];
    $phone = $_POST['applicantPhone'];
    $cover_letter = $_POST['coverLetter'];

    // Handle file upload
    if (isset($_FILES['resume']) && $_FILES['resume']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['resume']['tmp_name'];
        $fileName = $_FILES['resume']['name'];
        $fileContent = file_get_contents($fileTmpPath);

        $stmt = $pdo->prepare("INSERT INTO applications 
            (job_id, applicant_name, applicant_email, applicant_phone, resume_path, resume, resume_filename, cover_letter) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $resume_path = ''; // You can still save the path if you want, or leave blank
        $stmt->execute([
            $job_id, $name, $email, $phone, $resume_path, $fileContent, $fileName, $cover_letter
        ]);
        echo "Application submitted successfully!";
    } else {
        echo "Resume upload failed!";
    }
}
?>