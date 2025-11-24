<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Appointment Status - Government Portal</title>
    <!-- Load Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Custom Styles for a clean, modern government look */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap');
        
        :root {
            --color-primary: #1a4f8c;
            --color-primary-dark: #0e3a6b;
            --color-primary-light: #2c6cb0;
            --color-secondary: #2d3748;
            --color-background: #f8fafc;
            --color-surface: #ffffff;
            --color-border: #e2e8f0;
            --color-text: #2d3748;
            --color-text-light: #718096;
            --color-success: #28a745;
            --color-warning: #f59e0b;
            --color-error: #dc3545;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--color-background);
        }
        
        /* Custom scrollbar for better table experience on large data sets */
        .table-responsive::-webkit-scrollbar {
            height: 8px;
        }
        .table-responsive::-webkit-scrollbar-thumb {
            background: #cbd5e0;
            border-radius: 10px;
        }
        .table-responsive::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        /* Styles for the progress step circle and line */
        .progress-step .step {
            flex: 1;
            text-align: center;
            position: relative;
        }
        
        .progress-step .step-circle {
            display: inline-flex;
            width: 30px;
            height: 30px;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.3s ease;
            z-index: 10;
        }

        .progress-step .step-line {
            content: '';
            position: absolute;
            top: 15px;
            left: 50%;
            width: 100%;
            height: 4px;
            background-color: #e2e8f0;
            z-index: 5;
            transform: translateY(-50%);
            transition: background-color 0.3s ease;
        }

        .progress-step .step:first-child .step-line {
            width: 50%;
            left: 50%;
        }

        .progress-step .step:last-child .step-line {
            width: 50%;
            left: 0;
        }

        /* Government-themed button and card shadows */
        .gov-card {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border: 1px solid var(--color-border);
        }

        .gov-btn-primary {
            background: linear-gradient(135deg, var(--color-primary), var(--color-primary-dark));
            transition: all 0.3s ease;
        }

        .gov-btn-primary:hover {
            background: linear-gradient(135deg, var(--color-primary-dark), var(--color-primary));
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(26, 79, 140, 0.3);
        }
    </style>
</head>
<body class="flex min-h-screen">

<!-- Sidebar -->
<div class="w-64 bg-gray-800 text-white p-6 flex flex-col shadow-xl">
    <div class="flex items-center gap-3 mb-6 border-b border-gray-700 pb-3">
        <div class="w-8 h-8 bg-gradient-to-br from-blue-600 to-blue-800 rounded-lg flex items-center justify-center">
            <i class="fas fa-landmark text-white text-sm"></i>
        </div>
        <h3 class="text-xl font-bold">CiviLink</h3>
    </div>
    
    <nav class="space-y-2 flex-grow">
        <a href="<?= site_url('resident/dashboard') ?>" class="flex items-center px-4 py-2 rounded-lg transition duration-150 ease-in-out font-medium hover:bg-gray-700">
            <i class="fas fa-file-alt w-5 mr-3"></i>
            Request Form
        </a>
        <a href="<?= site_url('resident/status') ?>" class="flex items-center px-4 py-2 rounded-lg transition duration-150 ease-in-out font-medium bg-blue-600 hover:bg-blue-700 shadow-md">
            <i class="fas fa-tasks w-5 mr-3"></i>
            Status
        </a>
        <a href="<?= site_url('resident/payment') ?>" class="flex items-center px-4 py-2 rounded-lg transition duration-150 ease-in-out font-medium hover:bg-gray-700">
            <i class="fas fa-credit-card w-5 mr-3"></i>
            Payment
        </a>
    </nav>
 
    
<div class="mt-auto pt-4 border-t border-gray-700">
        <a href="<?php echo site_url('resident/adminAccess'); ?>">
    <button>Go to Admin Dashboard</button>
</a>
    </div>
    
    <div class="mt-auto pt-4 border-t border-gray-700">
        <a href="<?= site_url('resident/logout') ?>" class="block px-4 py-2 rounded-lg text-red-400 hover:bg-gray-700 transition duration-150 ease-in-out">
            Logout
        </a>
    </div>
</div>

<!-- Content -->
<div class="flex-1 p-4 md:p-8 lg:p-12">
    <header class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Welcome, <?= htmlspecialchars($name ?? 'Resident') ?>!</h1>
        <p class="text-gray-500 mt-1">Track your government service requests and their current status.</p>
    </header>

    <!-- Status Table -->
    <div class="bg-white gov-card rounded-xl">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-semibold text-gray-800">Your Service Requests</h2>
                <div class="flex items-center text-sm text-gray-500">
                    <i class="fas fa-info-circle mr-2"></i>
                    <span>Track your government service requests</span>
                </div>
            </div>
            
            <?php if (!empty($appointments) && is_array($appointments)): ?>
                <div class="table-responsive overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-800">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-white rounded-tl-xl">#</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-white">Service Type</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-white">Request Date</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-white">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-white">Payment Status</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-white rounded-tr-xl">Progress</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            <?php 
                            $count = 1;
                            foreach ($appointments as $appt): 
                                $status = $appt['status'] ?? 'Pending';

                                // Determine badge styling based on status
                                $badge_class = match($status) {
                                    'Pending' => 'bg-yellow-100 text-yellow-800 border border-yellow-200',
                                    'Processing' => 'bg-blue-100 text-blue-800 border border-blue-200',
                                    'Completed' => 'bg-green-100 text-green-800 border border-green-200',
                                    'Rejected' => 'bg-red-100 text-red-800 border border-red-200',
                                    default => 'bg-gray-100 text-gray-800 border border-gray-200',
                                };

                                // Determine payment badge styling
                                $payment_status = $appt['payment_status'] ?? 'unpaid';
                                $payment_badge = match($payment_status) {
                                    'unpaid' => 'bg-red-100 text-red-800 border border-red-200',
                                    'paid_online' => 'bg-green-100 text-green-800 border border-green-200',
                                    'paid_on_site' => 'bg-blue-100 text-blue-800 border border-blue-200',
                                    default => 'bg-gray-100 text-gray-800 border border-gray-200'
                                };

                                // Determine progress step classes
                                $step1_active = $status === 'Pending' || $status === 'Rejected';
                                $step1_completed = $status !== 'Pending' && $status !== 'Rejected';
                                
                                $step2_active = $status === 'Processing';
                                $step2_completed = in_array($status, ['Completed']);

                                $step3_completed = $status === 'Completed';
                            ?>
                                <tr class="hover:bg-gray-50 transition duration-150 ease-in-out">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= $count++ ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        <div class="flex items-center">
                                            <i class="fas fa-file-contract text-blue-500 mr-2"></i>
                                            <?= htmlspecialchars($appt['appointment_type'] ?? 'N/A') ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        <div class="flex items-center">
                                            <i class="fas fa-calendar text-gray-400 mr-2"></i>
                                            <?= htmlspecialchars($appt['appointment_date'] ?? 'N/A') ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <span class="px-3 py-1 inline-flex items-center text-xs leading-5 font-semibold rounded-full <?= $badge_class ?>">
                                            <?php 
                                                $status_icon = match($status) {
                                                    'Pending' => 'clock',
                                                    'Processing' => 'cog',
                                                    'Completed' => 'shield-check',
                                                    'Rejected' => 'times-circle',
                                                    default => 'question-circle'
                                                };
                                            ?>
                                            <i class="fas fa-<?= $status_icon ?> mr-1"></i>
                                            <?= $status ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <span class="px-3 py-1 inline-flex items-center text-xs leading-5 font-semibold rounded-full <?= $payment_badge ?>">
                                            <?php 
                                                $payment_icon = match($payment_status) {
                                                    'unpaid' => 'times-circle',
                                                    'paid_online' => 'check-circle',
                                                    'paid_on_site' => 'building',
                                                    default => 'question-circle'
                                                };
                                            ?>
                                            <i class="fas fa-<?= $payment_icon ?> mr-1"></i>
                                            <?= htmlspecialchars($payment_status) ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <?php if ($status !== 'Rejected'): ?>
                                            <div class="progress-step flex items-center justify-between text-xs sm:text-sm max-w-xs mx-auto pt-2">
                                                
                                                <!-- Step 1: Pending -->
                                                <div class="step relative z-10">
                                                    <div class="step-line <?= $step1_completed || $step2_completed || $step3_completed ? 'bg-blue-500' : 'bg-gray-300' ?>"></div>
                                                    <div class="step-circle inline-block text-white font-bold text-xs
                                                        <?= $step1_completed || $step2_completed || $step3_completed ? 'bg-green-500' : ($step1_active ? 'bg-blue-600' : 'bg-gray-300 text-gray-500') ?>">
                                                        <i class="fas fa-<?= $step1_completed || $step2_completed || $step3_completed ? 'check' : 'clock' ?> text-xs"></i>
                                                    </div>
                                                    <small class="block mt-1 text-gray-600">Submitted</small>
                                                </div>

                                                <!-- Step 2: Processing -->
                                                <div class="step relative z-10">
                                                    <div class="step-line <?= $step2_completed || $step3_completed ? 'bg-blue-500' : 'bg-gray-300' ?>"></div>
                                                    <div class="step-circle inline-block text-white font-bold text-xs
                                                        <?= $step2_completed || $step3_completed ? 'bg-green-500' : ($step2_active ? 'bg-blue-600' : 'bg-gray-300 text-gray-500') ?>">
                                                        <i class="fas fa-<?= $step2_completed || $step3_completed ? 'check' : 'cog' ?> text-xs"></i>
                                                    </div>
                                                    <small class="block mt-1 text-gray-600">Processing</small>
                                                </div>

                                                <!-- Step 3: Completed -->
                                                <div class="step relative z-10">
                                                    <div class="step-circle inline-block text-white font-bold text-xs
                                                        <?= $step3_completed ? 'bg-green-500' : 'bg-gray-300 text-gray-500' ?>">
                                                        <i class="fas fa-<?= $step3_completed ? 'check' : 'flag' ?> text-xs"></i>
                                                    </div>
                                                    <small class="block mt-1 text-gray-600">Completed</small>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <div class="flex items-center text-red-500">
                                                <i class="fas fa-times-circle mr-2"></i>
                                                <span class="text-sm font-medium">Request Rejected</span>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-12">
                    <i class="fas fa-inbox text-gray-300 text-5xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-500 mb-2">No Service Requests</h3>
                    <p class="text-gray-400">You haven't made any service requests yet.</p>
                    <a href="<?= site_url('resident/dashboard') ?>" class="inline-flex items-center px-4 py-2 mt-4 gov-btn-primary text-white rounded-lg transition duration-150 ease-in-out">
                        <i class="fas fa-plus-circle mr-2"></i>
                        Make Your First Request
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Additional Information Section -->
    <div class="mt-10 bg-white gov-card rounded-xl p-6">
        <div class="flex items-center justify-between mb-5 border-b pb-3">
            <h3 class="text-xl font-semibold text-gray-700 flex items-center">
                <i class="fas fa-info-circle text-blue-500 mr-3"></i>
                Status Information & Support
            </h3>
            <div class="text-sm text-gray-500 bg-blue-50 px-3 py-1 rounded-full">
                <i class="fas fa-info-circle mr-1"></i>
                Need assistance?
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h4 class="font-medium text-gray-700 mb-3">Request Status Guide</h4>
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <span class="px-3 py-1 inline-flex items-center text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 border border-yellow-200">
                            <i class="fas fa-clock mr-1"></i>Pending
                        </span>
                        <span class="text-sm text-gray-500">Under review by our team</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <span class="px-3 py-1 inline-flex items-center text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 border border-blue-200">
                            <i class="fas fa-cog mr-1"></i>Processing
                        </span>
                        <span class="text-sm text-gray-500">Request is being processed</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <span class="px-3 py-1 inline-flex items-center text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 border border-green-200">
                            <i class="fas fa-check-circle mr-1"></i>Completed
                        </span>
                        <span class="text-sm text-gray-500">Request has been fulfilled</span>
                    </div>
                </div>
            </div>
            <div>
                <h4 class="font-medium text-gray-700 mb-3">Need Help?</h4>
                <div class="space-y-3">
                    <div class="flex items-center p-3 bg-blue-50 rounded-lg">
                        <i class="fas fa-phone text-blue-500 mr-3 text-lg"></i>
                        <div>
                            <span class="text-sm font-medium text-gray-700">Contact Support</span>
                            <p class="text-xs text-gray-500">(02) 1234-5678</p>
                        </div>
                    </div>
                    <div class="flex items-center p-3 bg-blue-50 rounded-lg">
                        <i class="fas fa-envelope text-blue-500 mr-3 text-lg"></i>
                        <div>
                            <span class="text-sm font-medium text-gray-700">Email Support</span>
                            <p class="text-xs text-gray-500">support@civillink.gov</p>
                        </div>
                    </div>
                    <div class="flex items-center p-3 bg-blue-50 rounded-lg">
                        <i class="fas fa-map-marker-alt text-blue-500 mr-3 text-lg"></i>
                        <div>
                            <span class="text-sm font-medium text-gray-700">Visit Our Office</span>
                            <p class="text-xs text-gray-500">123 Government Center, City</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Security Notice -->
    <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
        <div class="flex items-center">
            <i class="fas fa-shield-alt text-blue-500 mr-3"></i>
            <div>
                <p class="text-sm text-blue-800 font-medium">Secure Government Portal</p>
                <p class="text-xs text-blue-600">All your service requests and personal information are protected with government-grade security measures.</p>
            </div>
        </div>
    </div>
</div>

<!-- Font Awesome Icons -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>
</body>
</html>