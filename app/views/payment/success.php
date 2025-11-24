<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Successful</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; display: flex; justify-content: center; align-items: center; height: 100vh; }
        .container { background: #fff; padding: 40px; border-radius: 10px; box-shadow: 0 0 15px #ccc; text-align: center; }
        h2 { color: green; }
        a.button { display: inline-block; padding: 10px 20px; background: #0073e6; color: #fff; text-decoration: none; border-radius: 5px; margin-top: 20px; }
        a.button:hover { background: #005bb5; }
    </style>
</head>
<body>
<div class="container">
    <h2>✅ Payment Successful!</h2>
    <p>Your appointment has been paid successfully.</p>
    <a class="button" href="<?= site_url('resident/dashboard'); ?>">Back to Dashboard</a>
</div>
</body>
</html>