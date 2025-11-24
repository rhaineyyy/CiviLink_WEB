<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Resident Registration - Government Portal</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

:root {
    --color-primary: #1a4f8c;
    --color-primary-dark: #0e3a6b;
    --color-primary-light: #2c6cb0;
    --color-glow: #3a82ee;
    --color-secondary: #2d3748;
    --color-background: #f8fafc;
    --color-surface: #ffffff;
    --color-border: #e2e8f0;
    --color-text: #2d3748;
    --color-text-light: #718096;
    --color-success: #28a745;
    --color-error: #dc3545;
}

body {
    font-family: 'Inter', sans-serif;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%);
    overflow: hidden;
    position: relative;
}

/* Animated background with glowing orbs */
.background-glow {
    position: absolute;
    width: 100%;
    height: 100%;
    z-index: -1;
}

.glow-orb {
    position: absolute;
    border-radius: 50%;
    background: radial-gradient(circle, var(--color-glow) 0%, transparent 70%);
    opacity: 0.1;
    animation: floatOrb 8s infinite ease-in-out;
}

.glow-orb:nth-child(1) {
    width: 300px;
    height: 300px;
    top: -150px;
    left: -150px;
    animation-delay: 0s;
}

.glow-orb:nth-child(2) {
    width: 200px;
    height: 200px;
    bottom: -100px;
    right: -100px;
    animation-delay: -2s;
}

.glow-orb:nth-child(3) {
    width: 150px;
    height: 150px;
    top: 50%;
    left: 10%;
    animation-delay: -4s;
}

.glow-orb:nth-child(4) {
    width: 180px;
    height: 180px;
    bottom: 30%;
    right: 10%;
    animation-delay: -6s;
}

@keyframes floatOrb {
    0%, 100% { 
        transform: translateY(0) translateX(0) scale(1); 
        opacity: 0.08; 
    }
    33% { 
        transform: translateY(-20px) translateX(15px) scale(1.1); 
        opacity: 0.12; 
    }
    66% { 
        transform: translateY(15px) translateX(-10px) scale(0.9); 
        opacity: 0.15; 
    }
}

/* Card container with glow effect */
.card {
    position: relative;
    background: var(--color-surface);
    border-radius: 20px;
    padding: 48px 40px;
    width: 440px;
    box-shadow: 
        0 20px 40px rgba(0, 0, 0, 0.1),
        0 0 0 1px rgba(255, 255, 255, 0.05);
    text-align: center;
    transition: all 0.4s ease;
    border: 1px solid rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    overflow: hidden;
}

.card::before {
    content: '';
    position: absolute;
    top: -2px;
    left: -2px;
    right: -2px;
    bottom: -2px;
    background: linear-gradient(45deg, 
        transparent, 
        rgba(58, 130, 238, 0.1), 
        rgba(26, 79, 140, 0.2), 
        rgba(58, 130, 238, 0.1), 
        transparent);
    border-radius: 22px;
    z-index: -1;
    opacity: 0;
    transition: opacity 0.4s ease;
}

.card:hover::before {
    opacity: 1;
    animation: glowPulse 3s infinite;
}

@keyframes glowPulse {
    0%, 100% { 
        opacity: 0.7; 
        filter: brightness(1);
    }
    50% { 
        opacity: 1; 
        filter: brightness(1.2);
    }
}

.card:hover {
    box-shadow: 
        0 25px 50px rgba(0, 0, 0, 0.15),
        0 0 30px rgba(58, 130, 238, 0.3),
        0 0 0 1px rgba(255, 255, 255, 0.1);
    transform: translateY(-5px);
}

/* Header Section */
.header-section {
    margin-bottom: 32px;
    padding-bottom: 24px;
    border-bottom: 1px solid var(--color-border);
    position: relative;
}

.header-section::after {
    content: '';
    position: absolute;
    bottom: -1px;
    left: 25%;
    width: 50%;
    height: 2px;
    background: linear-gradient(90deg, 
        transparent, 
        var(--color-primary), 
        transparent);
}

.gov-logo {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    margin-bottom: 16px;
}

.logo-icon {
    width: 48px;
    height: 48px;
    background: linear-gradient(135deg, var(--color-primary), var(--color-primary-dark));
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 20px;
    box-shadow: 
        0 4px 12px rgba(26, 79, 140, 0.3),
        0 0 15px rgba(58, 130, 238, 0.4);
    position: relative;
    overflow: hidden;
}

.logo-icon::before {
    content: '';
    position: absolute;
    width: 100%;
    height: 100%;
    background: linear-gradient(45deg, 
        transparent, 
        rgba(255,255,255,0.3), 
        transparent);
    transform: translateX(-100%);
    transition: transform 0.6s;
}

.logo-icon:hover::before {
    transform: translateX(100%);
}

.logo-text {
    font-size: 24px;
    font-weight: 700;
    color: var(--color-primary);
    letter-spacing: -0.5px;
    text-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

h3 {
    font-size: 20px;
    font-weight: 600;
    color: var(--color-text);
    margin-bottom: 8px;
}

/* Welcome message */
.welcome-message {
    font-size: 14px;
    color: var(--color-text-light);
    line-height: 1.5;
    max-width: 320px;
    margin: 0 auto;
}

/* Form Styles */
form {
    margin-top: 8px;
}

.form-group {
    margin-bottom: 20px;
    text-align: left;
    position: relative;
}

.form-group label {
    display: block;
    margin-bottom: 6px;
    color: var(--color-text);
    font-weight: 500;
    font-size: 14px;
}

input {
    width: 100%;
    padding: 14px 16px;
    border-radius: 8px;
    border: 2px solid var(--color-border);
    font-size: 15px;
    transition: all 0.3s ease;
    background: var(--color-surface);
    color: var(--color-text);
    font-family: 'Inter', sans-serif;
    position: relative;
    z-index: 1;
}

input:focus {
    outline: none;
    border-color: var(--color-primary);
    box-shadow: 
        0 0 0 4px rgba(26, 79, 140, 0.1),
        0 0 20px rgba(58, 130, 238, 0.3);
    background: #ffffff;
    transform: translateY(-1px);
}

input::placeholder {
    color: #a0aec0;
}

/* Glowing input effect */
.form-group::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 2px;
    background: linear-gradient(90deg, 
        transparent, 
        var(--color-glow), 
        transparent);
    border-radius: 2px;
    opacity: 0;
    transform: scaleX(0);
    transition: all 0.3s ease;
}

.form-group:focus-within::after {
    opacity: 1;
    transform: scaleX(1);
}

/* Button */
.btn-primary {
    background: linear-gradient(135deg, var(--color-primary), var(--color-primary-dark));
    color: #fff;
    border: none;
    padding: 16px 20px;
    border-radius: 12px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.4s ease;
    width: 100%;
    position: relative;
    overflow: hidden;
    font-family: 'Inter', sans-serif;
    letter-spacing: 0.3px;
    box-shadow: 
        0 4px 15px rgba(26, 79, 140, 0.3),
        0 0 0 1px rgba(255,255,255,0.1);
    margin-top: 10px;
}

.btn-primary::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, 
        transparent, 
        rgba(255,255,255,0.3), 
        transparent);
    transition: left 0.6s;
}

.btn-primary:hover::before {
    left: 100%;
}

.btn-primary:hover {
    background: linear-gradient(135deg, var(--color-primary-dark), var(--color-primary));
    transform: translateY(-2px);
    box-shadow: 
        0 8px 25px rgba(26, 79, 140, 0.4),
        0 0 20px rgba(58, 130, 238, 0.4);
}

.btn-primary:active {
    transform: translateY(0);
}

/* Footer link */
.footer-section {
    margin-top: 32px;
    padding-top: 24px;
    border-top: 1px solid var(--color-border);
    position: relative;
}

.footer-text {
    font-size: 14px;
    color: var(--color-text-light);
    margin-bottom: 8px;
}

.footer-link {
    text-decoration: none;
    color: var(--color-primary);
    font-weight: 600;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    position: relative;
}

.footer-link::after {
    content: '';
    position: absolute;
    bottom: -2px;
    left: 0;
    width: 0;
    height: 2px;
    background: var(--color-primary);
    transition: width 0.3s ease;
}

.footer-link:hover {
    color: var(--color-primary-dark);
}

.footer-link:hover::after {
    width: 100%;
}

/* Security Notice */
.security-notice {
    margin-top: 24px;
    padding: 14px;
    background: linear-gradient(135deg, #f0f9ff, #e6f3ff);
    border-radius: 8px;
    border: 1px solid #bae6fd;
    font-size: 12px;
    color: var(--color-primary);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    position: relative;
    overflow: hidden;
}

.security-notice::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 3px;
    height: 100%;
    background: var(--color-primary);
}

.security-notice i {
    font-size: 14px;
}

/* Responsive */
@media (max-width: 480px) {
    .card {
        width: 90%;
        padding: 32px 24px;
        margin: 20px;
    }
    
    .gov-logo {
        gap: 10px;
    }
    
    .logo-icon {
        width: 40px;
        height: 40px;
        font-size: 18px;
    }
    
    .logo-text {
        font-size: 20px;
    }
    
    h3 {
        font-size: 18px;
    }
    
    .welcome-message {
        font-size: 13px;
    }
    
    .glow-orb {
        display: none;
    }
}

/* Loading animation for form submission */
.btn-primary.loading {
    pointer-events: none;
    opacity: 0.9;
}

.btn-primary.loading::after {
    content: '';
    position: absolute;
    width: 18px;
    height: 18px;
    top: 50%;
    left: 50%;
    margin: -9px 0 0 -9px;
    border: 2px solid transparent;
    border-top: 2px solid #ffffff;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>
</head>
<body>

<!-- Background Glowing Orbs -->
<div class="background-glow">
    <div class="glow-orb"></div>
    <div class="glow-orb"></div>
    <div class="glow-orb"></div>
    <div class="glow-orb"></div>
</div>

<div class="card">
    <div class="header-section">
        <div class="gov-logo">
            <div class="logo-icon">
                <i class="fas fa-landmark"></i>
            </div>
            <div class="logo-text">CiviLink</div>
        </div>
        <h3>Resident Registration</h3>
        <div class="welcome-message">Create your government services account to access public services</div>
    </div>

    <form method="POST" action="/resident/register">
        <div class="form-group">
            <label for="full_name">Full Name</label>
            <input type="text" id="full_name" name="full_name" placeholder="Enter your full name" required>
        </div>
        
        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" placeholder="Enter your official email" required>
        </div>
        
        <div class="form-group">
            <label for="contact_number">Contact Number</label>
            <input type="text" id="contact_number" name="contact_number" placeholder="Enter your contact number" required>
        </div>
        
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Create a secure password" required>
        </div>
        
        <div class="form-group">
            <label for="confirm_password">Confirm Password</label>
            <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm your password" required>
        </div>
        
        <button type="submit" class="btn-primary">Create Account</button>
    </form>

    <div class="footer-section">
        <p class="footer-text">Already have an account?</p>
        <a href="/resident/login" class="footer-link">
            Sign in to your account
            <i class="fas fa-arrow-right"></i>
        </a>
    </div>

    <div class="security-notice">
        <i class="fas fa-shield-alt"></i>
        <span>Your information is secured with government-grade encryption</span>
    </div>
</div>

<script>
// Simple form submission loading state
document.querySelector('form').addEventListener('submit', function(e) {
    const btn = this.querySelector('.btn-primary');
    btn.classList.add('loading');
    btn.innerHTML = 'Creating Account...';
});

// Add subtle glow effect to inputs on focus
const inputs = document.querySelectorAll('input');
inputs.forEach(input => {
    input.addEventListener('focus', function() {
        this.style.boxShadow = '0 0 0 4px rgba(26, 79, 140, 0.1), 0 0 25px rgba(58, 130, 238, 0.4)';
    });
    
    input.addEventListener('blur', function() {
        this.style.boxShadow = '';
    });
});

// Password confirmation validation
const password = document.getElementById('password');
const confirmPassword = document.getElementById('confirm_password');

function validatePasswords() {
    if (password.value !== confirmPassword.value) {
        confirmPassword.style.borderColor = 'var(--color-error)';
        confirmPassword.style.boxShadow = '0 0 0 4px rgba(220, 53, 69, 0.1)';
    } else {
        confirmPassword.style.borderColor = 'var(--color-success)';
        confirmPassword.style.boxShadow = '0 0 0 4px rgba(40, 167, 69, 0.1)';
    }
}

password.addEventListener('input', validatePasswords);
confirmPassword.addEventListener('input', validatePasswords);
</script>

</body>
</html>