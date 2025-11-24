<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>No Appointments Found - Government Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        :root {
            --color-primary: #1a4f8c;
            --color-primary-dark: #0e3a6b;
            --color-primary-light: #2c6cb0;
            --color-background: #f8fafc;
            --color-surface: #ffffff;
            --color-border: #e2e8f0;
            --color-text: #2d3748;
            --color-text-light: #718096;
            --color-error: #dc3545;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            position: relative;
            overflow: hidden;
        }

        /* Background Glowing Orbs */
        .background-glow {
            position: absolute;
            width: 100%;
            height: 100%;
            z-index: -1;
        }

        .glow-orb {
            position: absolute;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(58, 130, 238, 0.1) 0%, transparent 70%);
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

        /* Main Card */
        .card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 3rem;
            width: 100%;
            max-width: 480px;
            box-shadow: 
                0 20px 40px rgba(0, 0, 0, 0.1),
                0 0 0 1px rgba(255, 255, 255, 0.05);
            text-align: center;
            transition: all 0.4s ease;
            border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            position: relative;
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

        /* Icon */
        .icon-container {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #f8d7da, #f5c6cb);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            color: var(--color-error);
            font-size: 2rem;
            box-shadow: 0 8px 20px rgba(220, 53, 69, 0.2);
        }

        /* Content */
        h2 {
            color: var(--color-text);
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1rem;
            line-height: 1.4;
        }

        p {
            color: var(--color-text-light);
            font-size: 1rem;
            line-height: 1.6;
            margin-bottom: 2rem;
        }

        /* Button */
        .btn-primary {
            background: linear-gradient(135deg, var(--color-primary), var(--color-primary-dark));
            color: #fff;
            border: none;
            padding: 0.875rem 2rem;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.4s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            box-shadow: 
                0 4px 15px rgba(26, 79, 140, 0.3),
                0 0 0 1px rgba(255,255,255,0.1);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--color-primary-dark), var(--color-primary));
            transform: translateY(-2px);
            box-shadow: 
                0 8px 25px rgba(26, 79, 140, 0.4),
                0 0 20px rgba(58, 130, 238, 0.4);
            text-decoration: none;
            color: #fff;
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        /* Security Notice */
        .security-notice {
            margin-top: 2rem;
            padding: 1rem;
            background: linear-gradient(135deg, #f0f9ff, #e6f3ff);
            border-radius: 8px;
            border: 1px solid #bae6fd;
            font-size: 0.875rem;
            color: var(--color-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
        }

        .security-notice i {
            font-size: 1rem;
        }

        /* Responsive */
        @media (max-width: 480px) {
            .card {
                padding: 2rem 1.5rem;
                margin: 1rem;
            }
            
            h2 {
                font-size: 1.25rem;
            }
            
            p {
                font-size: 0.9rem;
            }
            
            .icon-container {
                width: 60px;
                height: 60px;
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>

<!-- Background Glowing Orbs -->
<div class="background-glow">
    <div class="glow-orb"></div>
    <div class="glow-orb"></div>
</div>

<div class="card">
    <div class="icon-container">
        <i class="fas fa-calendar-times"></i>
    </div>
    
    <h2>No Unpaid Completed Appointments Found</h2>
    <p>Please complete an appointment before proceeding to payment processing.</p>
    
    <a href="<?= site_url('resident/dashboard'); ?>" class="btn-primary">
        <i class="fas fa-arrow-left"></i>
        Return to Dashboard
    </a>
    
    <div class="security-notice">
        <i class="fas fa-shield-alt"></i>
        <span>Secure Government Payment Portal</span>
    </div>
</div>

</body>
</html>