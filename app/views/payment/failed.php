<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html>
<head>
    <title>Payment Failed</title>
    <link rel="stylesheet" href="<?= site_url('assets/css/payment.css'); ?>">
</head>
<body>
<div class="payment-card">
    <h2>❌ Payment Failed</h2>
    <pre><?= htmlspecialchars($error ?? 'Unknown error'); ?></pre>
</div>
</body>
</html>