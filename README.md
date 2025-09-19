# Online-Job-Portal


JobSathi - Job Portal Platform
A modern job portal web application built with PHP, MySQL, and JavaScript that connects job seekers with employers. Features include job listings, user authentication, resume uploads, favorites system, and an AI-powered chatbot assistant.

🌟 Features
User Authentication: Secure login and registration system
Job Listings: Browse and search jobs by category (Tech, Marketing, Finance)
Resume Upload: Upload and manage resumes in PDF, DOC, DOCX formats
Favorites System: Save favorite jobs for later viewing
User Profile: Personal dashboard showing user info and favorite jobs
AI Chatbot: Powered by Google Gemini API for job assistance
Responsive Design: Works on desktop and mobile devices
Real-time Notifications: Toast notifications for user actions

🛠️ Technology Stack
Frontend: HTML5, CSS3, JavaScript (ES6+)
Backend: PHP 8.x
Database: MySQL 8.x
Server: Apache (XAMPP)
AI Integration: Google Gemini API
Icons: Font Awesome 6.4.0
Fonts: Google Fonts (Poppins)


JobSathi/
├── index.html              # Main landing page
├── profile.php             # User profile page
├── styles.css              # Main stylesheet
├── script.js               # Frontend JavaScript
├── config.js               # API configuration
├── backend/
│   ├── db.php              # Database connection
│   ├── login.php           # User login handler
│   ├── register.php        # User registration handler
│   ├── submit_application.php # Job application handler
│   ├── add_favourite.php   # Add to favorites handler
│   ├── chatbot.php         # AI chatbot backend
│   └── jobs.php            # Job listings API
└── README.md               # This file



Prerequisites
XAMPP (Apache, MySQL, PHP)
Web browser
Google Gemini API key


Step 2: Setup XAMPP
Install XAMPP
Start Apache and MySQL services
Place project folder in JobSathi
Step 3: Database Setup
Open phpMyAdmin: http://localhost/phpmyadmin
Create database named jobportal



🔄 Workflow
User Registration & Login Flow
Registration:

User fills registration form → register.php → Data saved to users table
Success response triggers login modal
Login:

User credentials → login.php → Session created → Page reload
Profile dropdown appears in navbar
Job Browsing & Application Flow
Job Listings:

Page loads → script.js fetches from backend/jobs.php → Jobs displayed
Filter buttons allow category-based filtering
Job Application:

Click "Apply Now" → Modal opens → Form submission with resume upload
Data sent to submit_application.php → Stored in database




Favorites System Flow
Add to Favorites:

Click heart icon → addToFavourites() → add_favourite.php
Job ID linked to user in favourites table
View Favorites:

Visit profile.php → PHP queries favourites + jobs tables
Displays user's favorite jobs
Chatbot Interaction Flow
Chat Initiation:

Click chat icon → Chat window opens
User types message → Enter key sends to chatbot.php
AI Response:

Message sent to Gemini API → Response received → Displayed in chat


📱 How to Use
For Job Seekers
Register/Login: Create account or login with existing credentials
Browse Jobs: Use filter buttons or search to find relevant positions
Apply for Jobs: Click "Apply Now", fill form, and upload resume
Save Favorites: Click heart icon to save interesting jobs
View Profile: Check applied jobs and favorites in profile section
Use Chatbot: Get help with job-related queries
For Administrators
Manage Jobs: Add/edit jobs directly in database via phpMyAdmin
View Applications: Check applications table for received applications
Download Resumes: Access uploaded resumes from database
Monitor Users: View registered users and their activity

🌐 API Endpoints
Endpoint	Method	Description
login.php	POST	User authentication
register.php	POST	User registration
/backend/jobs.php	GET	Fetch job listings
submit_application.php	POST	Submit job application
add_favourite.php	POST	Add job to favorites
chatbot.php	POST	Chatbot AI interaction

🔧 Key Functions
Frontend (script.js)
loginUser(event): Handles user login
registerUser(event): Handles user registration
fetchJobs(category): Fetches and displays jobs
addToFavourites(jobId): Adds job to user favorites
submitApplication(event): Submits job application
openModal(modalId): Opens modal dialogs
Backend Features
Session Management: Secure user sessions with PHP
File Upload: Resume upload with validation
Database Operations: PDO for secure database interactions
AI Integration: Google Gemini API for chatbot responses

🛡️ Security Features
SQL Injection Protection: Prepared statements with PDO
XSS Prevention: htmlspecialchars() for output sanitization
File Upload Security: Restricted file types for resumes
Session Security: Proper session management


🚨 Troubleshooting
Common Issues
"Cannot GET /profile.php": Ensure file is in root directory, not backend folder
"You must be logged in": Add window.location.reload() after AJAX login
Database connection errors: Check credentials in db.php
Chatbot not responding: Verify Gemini API key is correct
Resume not uploading: Check file size limits in php.ini


🤝 Support
For support, email singhvishwajeet958@gmail.com

Made with ❤️ by Vishwajeet Raj Singh

