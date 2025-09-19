console.log("Script.js loaded successfully!");

// --- CONFIGURATION ---
// API_BASE_URL should come from config.js

// --- STATE MANAGEMENT ---
let currentUser = null;
let currentJobs = [];

// --- INITIALIZATION --- (Single event listener)
document.addEventListener('DOMContentLoaded', () => {
    console.log("DOM loaded, API_BASE_URL is:", API_BASE_URL);
    checkAuthStatus();
    
    // Replace fetchJobs with filterJobs
    const activeFilterBtn = document.querySelector('.filter-btn.active');
    if (activeFilterBtn) {
        filterJobs('all', activeFilterBtn);
    } else {
        console.error("No active filter button found!");
    }
    
    // Add event listener to user profile for dropdown
    const userProfile = document.getElementById('userProfile');
    if(userProfile) {
        userProfile.addEventListener('click', () => {
            const dropdown = userProfile.querySelector('.user-dropdown');
            dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
        });
    }
});

// --- API CALLS ---
async function fetchJobs(category = 'all', searchTerm = '') {
    console.log("Fetching jobs:", category, searchTerm);
    
    const jobContainer = document.getElementById('jobContainer');
    const loadingState = document.getElementById('loadingState');
    const errorState = document.getElementById('errorState');
    
    loadingState.style.display = 'block';
    errorState.style.display = 'none';
    jobContainer.innerHTML = ''; // Clear previous jobs

    try {
        // Fix the URL path to include backend/
        let url = `${API_BASE_URL}get_jobs.php?category=${category}`;
        if (searchTerm) {
            url += `&search=${encodeURIComponent(searchTerm)}`;
        }

        console.log("Fetching from URL:", url);
        const response = await fetch(url);
        
        // Try to parse JSON, but handle non-JSON responses gracefully
        const responseText = await response.text();
        console.log("Raw response:", responseText.substring(0, 100)); // Log first 100 chars
        
        let data;
        try {
            data = JSON.parse(responseText);
        } catch (e) {
            console.error("Failed to parse JSON:", responseText);
            throw new Error(`Server returned non-JSON response. Check server logs.`);
        }

        console.log("Parsed data:", data);

        if (!response.ok || !data.success) {
            throw new Error(data.message || 'Failed to fetch jobs.');
        }

        currentJobs = data.jobs;
        console.log("Jobs received:", currentJobs.length);
        displayJobs(currentJobs);

    } catch (error) {
        console.error('Fetch Jobs Error:', error);
        loadingState.style.display = 'none';
        errorState.style.display = 'block';
        document.getElementById('errorMessage').textContent = 'Could not load jobs.';
        document.getElementById('errorDetail').textContent = error.message;
    } finally {
        loadingState.style.display = 'none';
    }
}


async function apiRequest(endpoint, formData) {
    try {
        const response = await fetch(API_BASE_URL + endpoint, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(formData)
        });
        const data = await response.json();
        if (!response.ok) throw new Error(data.message || 'An error occurred.');
        return data;
    } catch (error) {
        showNotification(error.message, 'error');
        throw error; // Re-throw to be caught by caller
    }
}

// --- AUTHENTICATION ---
function checkAuthStatus() {
    const user = localStorage.getItem('user');
    if (user) {
        currentUser = JSON.parse(user);
        updateUIAfterLogin();
    }
}

async function registerUser(event) {
    event.preventDefault();
    const form = event.target;
    const formData = {
        fullname: form.querySelector('#registerName').value,
        email: form.querySelector('#registerEmail').value,
        phone: form.querySelector('#registerPhone').value,
        password: form.querySelector('#registerPassword').value,
    };

    try {
        const data = await apiRequest('register.php', formData);
        showNotification(data.message, 'success');
        closeModal('registerModal');
        openModal('loginModal');
        form.reset();
    } catch (error) {
        // Notification is shown in apiRequest
    }
}

async function loginUser(event) {
    event.preventDefault();
    const form = event.target;
    const formData = {
        email: form.querySelector('#loginEmail').value,
        password: form.querySelector('#loginPassword').value,
    };

    try {
        const data = await apiRequest('login.php', formData);
        currentUser = data.user;
        localStorage.setItem('user', JSON.stringify(currentUser));
        updateUIAfterLogin();
        showNotification(data.message, 'success');
        closeModal('loginModal');
        form.reset();
    } catch (error) {
        // Notification is shown in apiRequest
    }
}

function logout() {
    currentUser = null;
    localStorage.removeItem('user');
    document.getElementById('authLinks').style.display = 'flex';
    document.getElementById('userProfile').style.display = 'none';
    showNotification('Logged out successfully.', 'success');
}

function updateUIAfterLogin() {
    if (!currentUser) return;
    document.getElementById('authLinks').style.display = 'none';
    const profile = document.getElementById('userProfile');
    profile.style.display = 'flex';
    document.getElementById('userAvatar').textContent = currentUser.fullname.charAt(0).toUpperCase();
    document.getElementById('userName').textContent = currentUser.fullname;
}

// Modal handling functions
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) modal.style.display = 'flex';
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) modal.style.display = 'none';
}

// Job search function
function searchJobs() {
    const searchTerm = document.getElementById('jobSearch').value.trim();
    
    if (!searchTerm) {
        showNotification('Please enter a search term', 'warning');
        return;
    }
    
    // Show loading state
    document.getElementById('loadingState').style.display = 'flex';
    document.getElementById('jobContainer').innerHTML = '';
    document.getElementById('errorState').style.display = 'none';
    
    console.log("Searching for:", searchTerm);
    
    // Fix: Use the correct endpoint path
    fetch(`${API_BASE_URL}get_jobs.php?search=${encodeURIComponent(searchTerm)}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('loadingState').style.display = 'none';
            if (data.success) {
                console.log("Search results:", data.jobs);
                displayJobs(data.jobs);
                
                // Update the active filter button to show we're in search mode
                document.querySelectorAll('.filter-btn').forEach(btn => {
                    btn.classList.remove('active');
                });
            } else {
                showError(data.message || "Failed to search jobs");
            }
        })
        .catch(error => {
            console.error("Search error:", error);
            document.getElementById('loadingState').style.display = 'none';
            showError('Failed to search jobs', error.message);
        });
}

// Job filtering function
function filterJobs(category, button) {
    console.log("Filter function called with category:", category);
    
    // Update active button
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    button.classList.add('active');
    
    // Show loading state
    const loadingState = document.getElementById('loadingState');
    const jobContainer = document.getElementById('jobContainer');
    const errorState = document.getElementById('errorState');
    
    loadingState.style.display = 'flex';
    jobContainer.innerHTML = '';
    errorState.style.display = 'none';
    
    // Fetch jobs with category filter
    console.log("Fetching from:", `${API_BASE_URL}get_jobs.php?category=${category}`);
    
    fetch(`${API_BASE_URL}get_jobs.php?category=${category}`)
        .then(response => {
            console.log("Response received:", response.status);
            return response.json();
        })
        .then(data => {
            console.log("Data received:", data);
            loadingState.style.display = 'none';
            if (data.success) {
                displayJobs(data.jobs);
            } else {
                showError(data.message || "Unknown error");
            }
        })
        .catch(error => {
            console.error("Error:", error);
            loadingState.style.display = 'none';
            showError('Failed to fetch jobs', error.message);
        });
}

// Helper function to show errors
function showError(message, details = '') {
    const errorState = document.getElementById('errorState');
    document.getElementById('errorMessage').textContent = message;
    document.getElementById('errorDetail').textContent = details;
    errorState.style.display = 'flex';
}

// Helper function to display jobs
function displayJobs(jobs) {
    console.log("Displaying jobs:", jobs);
    const jobContainer = document.getElementById('jobContainer');
    
    if (jobs.length === 0) {
        jobContainer.innerHTML = '<p class="no-jobs">No jobs found matching your criteria.</p>';
        return;
    }
    
    jobContainer.innerHTML = ''; // Clear previous jobs
    
    jobs.forEach(job => {
        const jobCard = document.createElement('div');
        jobCard.className = 'job-card';
        jobCard.innerHTML = `
            <div class="job-header">
                <h3 class="job-title">${job.title}</h3>
                <span class="job-company">${job.company}</span>
            </div>
            <div class="job-details">
                <span class="job-location"><i class="fas fa-map-marker-alt"></i> ${job.location}</span>
                <span class="job-experience"><i class="fas fa-briefcase"></i> ${job.experience}</span>
                <span class="job-salary"><i class="fas fa-money-bill-wave"></i> ${job.salary}</span>
            </div>
            <button class="fav-btn" onclick="addToFavourites(${job.id})"><i class="fa fa-heart"></i> Add to Favourites</button>
            <button class="apply-btn" onclick="openApplicationModal(${job.id}, '${job.title.replace(/'/g, "\\'")}')">Apply Now</button>
        `;
        jobContainer.appendChild(jobCard);
    });
}

// --- MODALS & NOTIFICATIONS ---
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) modal.style.display = 'flex';
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) modal.style.display = 'none';
}

function openApplicationModal(jobId, jobTitle) {
    if (!currentUser) {
        showNotification('Please log in to apply.', 'error');
        openModal('loginModal');
        return;
    }
    document.getElementById('applicationTitle').textContent = `Apply for ${jobTitle}`;
    document.getElementById('applicationForm').dataset.jobId = jobId;
    
    // Pre-fill user data
    document.getElementById('applicantName').value = currentUser.fullname;
    document.getElementById('applicantEmail').value = currentUser.email;
    document.getElementById('applicantPhone').value = currentUser.phone || '';

    openModal('applicationModal');
}

async function submitApplication(event) {
    event.preventDefault();
    const form = event.target;
    const formData = {
        job_id: form.dataset.jobId,
        user_id: currentUser.id,
        fullname: form.querySelector('#applicantName').value,
        email: form.querySelector('#applicantEmail').value,
        phone: form.querySelector('#applicantPhone').value,
        cover_letter: form.querySelector('#coverLetter').value,
    };
    
    try {
        const data = await apiRequest('apply_job.php', formData);
        showNotification(data.message, 'success');
        closeModal('applicationModal');
        form.reset();
    } catch(error) {
        // Notification shown in apiRequest
    }
}


function showNotification(message, type = 'info') {
    const notification = document.getElementById('notification');
    notification.textContent = message;
    notification.className = `notification show ${type}`;
    setTimeout(() => {
        notification.className = 'notification';
    }, 3000);
}




// --- CHATBOT (BASIC IMPLEMENTATION) ---
fetch('http://localhost/JobSathi/backend/chatbot.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ message: msg })
})
.then(res => res.json())
.then(data => {
    appendMessage('Bot', data.reply);
})
.catch(err => {
    appendMessage('Bot', 'Error connecting to assistant.');
    console.error(err);
});

toggle.addEventListener('click', () => {
    chatbot.style.display = chatbot.style.display === 'none' ? 'block' : 'none';
});

input.addEventListener('keypress', function (e) {
  if (e.key === 'Enter') {
    e.preventDefault(); // <-- Add this line to prevent page reload
    const msg = input.value.trim();
    if (!msg) return;

    appendMessage('You', msg);
    input.value = '';

    fetch('http://localhost/JobSathi/backend/chatbot.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ message: msg })
    })
    .then(res => res.json())
    .then(data => {
      appendMessage('Bot', data.reply);
    })
    .catch(err => {
      appendMessage('Bot', 'Error connecting to assistant.');
      console.error(err);
    });
  }
});

function addToFavourites(jobId) {
    fetch(`${API_BASE_URL}add_favourite.php`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ job_id: jobId })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showNotification('Added to favourites!', 'success');
        } else {
            showNotification(data.message || 'Could not add to favourites', 'error');
        }
    })
    .catch(() => showNotification('Error adding to favourites', 'error'));
}

