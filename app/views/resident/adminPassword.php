<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Access - Government Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
            max-width: 440px;
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

        /* Header Section */
        .header-section {
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
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
            margin-bottom: 1rem;
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
        }

        .logo-text {
            font-size: 24px;
            font-weight: 700;
            color: var(--color-primary);
            letter-spacing: -0.5px;
        }

        h2 {
            font-size: 20px;
            font-weight: 600;
            color: var(--color-text);
            margin-bottom: 0.5rem;
        }

        .subtitle {
            font-size: 14px;
            color: var(--color-text-light);
            line-height: 1.5;
        }

        /* Error Message */
        .alert {
            background: #fee2e2;
            color: var(--color-error);
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            border: 1px solid #fecaca;
            font-size: 14px;
            font-weight: 500;
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .alert::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 3px;
            background: var(--color-error);
        }

        .alert i {
            font-size: 16px;
        }

        /* Form Styles */
        .form-group {
            margin-bottom: 1.5rem;
            text-align: left;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--color-text);
            font-weight: 500;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-input {
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

        .form-input:focus {
            outline: none;
            border-color: var(--color-primary);
            box-shadow: 
                0 0 0 4px rgba(26, 79, 140, 0.1),
                0 0 20px rgba(58, 130, 238, 0.3);
            background: #ffffff;
            transform: translateY(-1px);
        }

        .form-input::placeholder {
            color: #a0aec0;
        }

        /* Button */
        .btn-primary {
            background: linear-gradient(135deg, var(--color-primary), var(--color-primary-dark));
            color: #fff;
            border: none;
            padding: 14px 20px;
            border-radius: 8px;
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

        /* Footer Link */
        .footer-section {
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--color-border);
        }

        .back-link {
            text-decoration: none;
            color: var(--color-primary);
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 14px;
            position: relative;
        }

        .back-link::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--color-primary);
            transition: width 0.3s ease;
        }

        .back-link:hover {
            color: var(--color-primary-dark);
        }

        .back-link:hover::after {
            width: 100%;
        }

        /* Security Notice */
        .security-notice {
            margin-top: 1.5rem;
            padding: 12px;
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
                padding: 2rem 1.5rem;
                margin: 1rem;
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
            
            h2 {
                font-size: 18px;
            }
            
            .subtitle {
                font-size: 13px;
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
    <div class="header-section">
        <div class="gov-logo">
            <div class="logo-icon">
                <i class="fas fa-shield-alt"></i>
            </div>
            <div class="logo-text">CiviLink</div>
        </div>
        <h2>Administrator Access</h2>
        <div class="subtitle">Enter your credentials to access the administration dashboard</div>
    </div>

    <!-- ORIGINAL PHP ERROR LOGIC - KEPT INTACT -->
    <?php if (!empty($error)): ?>
        <div class="alert">
            <i class="fas fa-exclamation-circle"></i>
            <span><?php echo $error; ?></span>
        </div>
    <?php endif; ?>

    <!-- ORIGINAL FORM LOGIC - KEPT INTACT -->
    <form method="post" action="<?php echo site_url('resident/adminAccess'); ?>">
        <div class="form-group">
            <label for="admin_password">
                <i class="fas fa-lock"></i>
                Administrator Password
            </label>
            <input type="password" 
                   id="admin_password" 
                   name="admin_password" 
                   class="form-input" 
                   placeholder="Enter administrator password" 
                   required>
        </div>
        
        <button type="submit" class="btn-primary">
            <i class="fas fa-sign-in-alt"></i>
            Access Admin Dashboard
        </button>
    </form>

    <div class="footer-section">
        <a href="<?php echo site_url('resident/dashboard'); ?>" class="back-link">
            <i class="fas fa-arrow-left"></i>
            Back to Resident Dashboard
        </a>
    </div>

    <div class="security-notice">
        <i class="fas fa-shield-alt"></i>
        <span>This area is restricted to authorized personnel only</span>
    </div>
</div>

</body>
</html>