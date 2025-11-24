<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html>
<head>
    <title>Enter GCash Number</title>
    <link rel="stylesheet" href="<?= site_url('assets/css/payment.css'); ?>">
</head>
<body>
<div class="payment-card">
    <h2>Enter Your GCash Account</h2>
    <?php if (!empty($error)) echo "<div class='error'>{$error}</div>"; ?>
    <form method="POST" action="<?= site_url('payment/enter-number'); ?>">
        <input type="hidden" name="appointment_id" value="<?= htmlspecialchars($appointment['id']); ?>">
        <div class="input-group">
            <label>GCash Name</label>
            <input type="text" name="gcash_name" required>
        </div>
        <div class="input-group">
            <label>GCash Number</label>
            <input type="text" name="gcash_number" required>
        </div>
        <button class="pay-btn" type="submit">Send OTP</button>
    </form>
</div>
</body>
</html>