<?php
session_start();
require 'backend/db.php';

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    echo "You must be logged in to view your profile.";
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

$stmt = $pdo->prepare("SELECT jobs.* FROM jobs
    JOIN favourites ON jobs.id = favourites.job_id
    WHERE favourites.user_id = ?");
$stmt->execute([$user_id]);
$favouriteJobs = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>My Profile - JobSathi</title>
    <link rel="stylesheet" href="styles.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
</head>
<body>
    <header class="navbar">
        <div class="nav-container">
            <div class="logo">
                <span class="job-text">Job</span><span class="sathi-text">Sathi</span>
            </div>
            <nav class="nav-links">
                <a href="index.html#jobs">Jobs</a>
                <a href="index.html#about">About</a>
                <a href="index.html#contact">Contact</a>
                <a href="profile.php" class="active">Profile</a>
            </nav>
            <div class="user-profile" id="userProfile" style="display:block;">
                <div class="user-avatar" id="userAvatar"></div>
                <span class="user-name" id="userName"><?= htmlspecialchars($user['name'] ?? $user['fullname']) ?></span>
                <div class="user-dropdown">
                    <a href="logout.php">Logout</a>
                </div>
            </div>
        </div>
    </header>

    <main>
        <section class="profile-section">
            <div class="container">
                <h2>My Profile</h2>
                <div class="profile-info">
                    <p><strong>Name:</strong> <?= htmlspecialchars($user['name'] ?? $user['fullname']) ?></p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
                    <p><strong>Phone:</strong> <?= htmlspecialchars($user['phone'] ?? '') ?></p>
                </div>
                <h3>My Favourite Jobs</h3>
                <div class="job-container">
                    <?php if (count($favouriteJobs) === 0): ?>
                        <p>No favourite jobs yet.</p>
                    <?php else: ?>
                        <?php foreach ($favouriteJobs as $job): ?>
                            <div class="job-card">
                                <div class="job-header">
                                    <h3 class="job-title"><?= htmlspecialchars($job['title']) ?></h3>
                                    <span class="job-company"><?= htmlspecialchars($job['company']) ?></span>
                                </div>
                                <div class="job-details">
                                    <span class="job-location"><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($job['location']) ?></span>
                                    <span class="job-experience"><i class="fas fa-briefcase"></i> <?= htmlspecialchars($job['experience']) ?></span>
                                    <span class="job-salary"><i class="fas fa-money-bill-wave"></i> <?= htmlspecialchars($job['salary']) ?></span>
                                </div>
                                <a href="job_details.php?id=<?= $job['id'] ?>" class="apply-btn">View Details</a>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </main>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2025 JobSathi. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>