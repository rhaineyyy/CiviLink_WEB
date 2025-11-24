<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html>
<head>
    <title>Verify OTP</title>
    <link rel="stylesheet" href="<?= site_url('assets/css/payment.css'); ?>">
</head>
<body>
<div class="payment-card">
    <h2>Enter OTP Sent to Your GCash</h2>
    <?php if (!empty($error)) echo "<div class='error'>{$error}</div>"; ?>
    <form method="POST" action="<?= site_url('payment/verify-otp'); ?>">
        <div class="input-group">
            <label>OTP</label>
            <input type="number" name="otp" required>
        </div>
        <button class="pay-btn" type="submit">Verify & Pay</button>
    </form>
</div>
</body>
</html>