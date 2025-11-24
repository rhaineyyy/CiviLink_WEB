<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Secure Document Payment</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <style>
        /* Import a more modern, clean font */
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap');

        /* === 1. Base Styling and Background === */
        body {
            font-family: 'Poppins', sans-serif;
            /* Gradient background for depth and modernity */
            background: linear-gradient(135deg, #e0f7fa 0%, #bbdefb 100%);
            display: flex;
            justify-content: center;
            align-items: center; /* Center vertically on the screen */
            min-height: 100vh;
            padding: 40px 20px;
            margin: 0;
        }

        /* === 2. Main Payment Card (Container) === */
        .payment-card {
            background: #fff;
            padding: 50px 40px;
            border-radius: 20px;
            /* Deeper, more sophisticated shadow */
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            width: 750px; /* Slightly wider */
            max-width: 100%;
            text-align: center;
            position: relative;
            transition: transform 0.3s ease-in-out;
        }
        
        .payment-card:hover {
            transform: translateY(-5px); /* Subtle lift effect on hover */
        }

        .payment-card h2 {
            margin-bottom: 40px;
            /* Vibrant text color */
            color: #1e88e5; 
            font-size: 32px; 
            font-weight: 700;
            position: relative;
        }
        
        .payment-card h2::after {
            content: '';
            position: absolute;
            bottom: -15px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: #03a9f4;
            border-radius: 2px;
        }

        /* === 3. Return Button === */
        .return-btn {
            position: absolute;
            top: 25px;
            right: 25px;
            /* Primary button color */
            background: #1e88e5; 
            color: #fff;
            border: none;
            padding: 10px 18px;
            border-radius: 50px; /* Pill shape */
            text-decoration: none;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            box-shadow: 0 4px 10px rgba(30, 136, 229, 0.4);
        }
        
        .return-btn i {
            margin-right: 8px;
        }

        .return-btn:hover {
            background: #039be5;
            box-shadow: 0 6px 15px rgba(30, 136, 229, 0.6);
            transform: translateY(-2px);
        }

        /* === 4. Appointment List Items === */
        .appointment-list {
            text-align: left;
            margin-bottom: 30px;
        }

        .appointment-item {
            padding: 25px;
            margin-bottom: 25px;
            /* Light, clean background */
            background: #ffffff;
            border-radius: 15px;
            border: 1px solid #e0e0e0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        .appointment-item:hover {
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            border-color: #bbdefb;
        }

        .appointment-item div {
            font-size: 18px; 
            font-weight: 600;
            color: #424242;
        }

        /* Highlight the amount for visual emphasis */
        .appointment-item div span {
            color: #e53935; /* Use a strong color for the price */
            font-weight: 700;
        }

        /* === 5. Form & Input Styling === */
        .input-group {
            display: inline-block;
            margin: 0 5px;
            vertical-align: top;
        }

        .input-group input {
            width: 180px; /* Adjusted width for better fit */
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #bdbdbd;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }
        
        .input-group input:focus {
            outline: none;
            border-color: #1e88e5;
            box-shadow: 0 0 0 3px rgba(30, 136, 229, 0.2);
        }

        /* === 6. Pay Button (GCash) === */
        .pay-btn {
            /* GCash branding color */
            background: #03a9f4; 
            color: #fff;
            border: none;
            padding: 12px 25px;
            font-size: 15px;
            font-weight: 600;
            border-radius: 50px; /* Pill shape */
            cursor: pointer;
            margin-left: 10px;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(3, 169, 244, 0.4);
            text-transform: uppercase;
        }

        .pay-btn:hover {
            background: #0288d1;
            box-shadow: 0 7px 20px rgba(3, 169, 244, 0.6);
            transform: translateY(-2px);
        }
        
        /* === 7. No Appointments Message Styling === */
        .payment-card p {
            font-size: 18px;
            color: #616161;
            padding: 20px;
            background: #f5f5f5;
            border-radius: 10px;
        }
    </style>
</head>
<body>

<?php if (!empty($appointments)) : ?>
    <div class="payment-card">
        <a href="<?= site_url('resident/dashboard') ?>" class="return-btn">
            <i class="fas fa-arrow-left"></i>Return to Dashboard
        </a>
        <h2>Secure Payment Gateway</h2>

        <div class="appointment-list">
            <?php foreach ($appointments as $appointment): ?>
                <div class="appointment-item">
                    <div>
                        <?= htmlspecialchars($appointment['appointment_type']); ?> - <span>₱<?= number_format($appointment['amount'] ?? 100, 2); ?></span>
                    </div>
                    <form method="POST" action="<?= site_url('payment/process-gcash'); ?>">
                        <input type="hidden" name="appointment_id" value="<?= htmlspecialchars($appointment['id']); ?>">
                        <input type="hidden" name="amount" value="<?= htmlspecialchars($appointment['amount'] ?? 100); ?>">
                        <div class="input-group">
                            <input type="text" name="gcash_name" placeholder="GCash Name" required>
                        </div>
                        <div class="input-group">
                            <input type="text" name="gcash_number" placeholder="09XXXXXXXXX" required pattern="09\d{9}">
                        </div>
                        <button type="submit" class="pay-btn">Pay with GCash</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php else: ?>
    <div class="payment-card">
        <a href="<?= site_url('resident/dashboard') ?>" class="return-btn">
             <i class="fas fa-arrow-left"></i>Return to Dashboard
        </a>
        <h2>No unpaid completed appointments found.</h2>
        <p>Please check back later.</p>
    </div>
<?php endif; ?>

</body>
</html>