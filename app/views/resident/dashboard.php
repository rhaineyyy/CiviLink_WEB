<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resident Dashboard - Government Portal</title>
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
        <a href="<?= site_url('resident/dashboard') ?>" class="flex items-center px-4 py-2 rounded-lg transition duration-150 ease-in-out font-medium
            <?= ($active_tab === 'dashboard') ? 'bg-blue-600 hover:bg-blue-700 shadow-md' : 'hover:bg-gray-700' ?>">
            <i class="fas fa-file-alt w-5 mr-3"></i>
            Request Form
        </a>
        <a href="<?= site_url('resident/status') ?>" class="flex items-center px-4 py-2 rounded-lg transition duration-150 ease-in-out font-medium
            <?= ($active_tab === 'status') ? 'bg-blue-600 hover:bg-blue-700 shadow-md' : 'hover:bg-gray-700' ?>">
            <i class="fas fa-tasks w-5 mr-3"></i>
            Status
        </a>
        <a href="<?= site_url('resident/payment') ?>" class="flex items-center px-4 py-2 rounded-lg transition duration-150 ease-in-out font-medium
            <?= ($active_tab === 'payment') ? 'bg-blue-600 hover:bg-blue-700 shadow-md' : 'hover:bg-gray-700' ?>">
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
        <p class="text-gray-500 mt-1">Manage your government service requests and track their status.</p>
    </header>

    <!-- Request New Service Form -->
    <div class="bg-white gov-card rounded-xl p-6">
        <div class="flex items-center justify-between mb-5 border-b pb-3">
            <h3 class="text-xl font-semibold text-gray-700 flex items-center">
                <i class="fas fa-plus-circle text-blue-500 mr-3"></i>
                Request New Government Service
            </h3>
            <div class="text-sm text-gray-500 bg-blue-50 px-3 py-1 rounded-full">
                <i class="fas fa-info-circle mr-1"></i>
                Secure government service portal
            </div>
        </div>
        <form method="POST" action="<?= site_url('resident/requestAppointment') ?>" class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Hidden Fields -->
            <input type="hidden" name="resident_id" value="<?= htmlspecialchars($residentId ?? '') ?>">
            <input type="hidden" name="citizen_name" value="<?= htmlspecialchars($name ?? '') ?>">
            <input type="hidden" name="email" value="<?= htmlspecialchars($email ?? '') ?>">
            <input type="hidden" name="contact_number" value="<?= htmlspecialchars($contact_number ?? '') ?>">

            <!-- Service Type Select -->
            <div>
                <label for="appointment_type" class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                    <i class="fas fa-file-contract text-blue-500 mr-2"></i>
                    Service Type
                </label>
                <select id="appointment_type" name="appointment_type" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
                    <option value="">Select Service Type</option>
                    <!-- Civil Registry -->
                    <option value="Birth Certificate">Birth Certificate</option>
                    <option value="Marriage Contract">Marriage Contract</option>
                    <option value="Death Certificate">Death Certificate</option>
                    <option value="Late Registration of Birth">Late Registration of Birth</option>
                    
                    <!-- Barangay & Municipal Docs -->
                    <option value="Barangay Clearance">Barangay Clearance</option>
                    <option value="Certificate of Residency">Certificate of Residency</option>
                    <option value="Certificate of Indigency">Certificate of Indigency</option>
                    <option value="Certificate of Good Moral Character">Certificate of Good Moral Character</option>
                    <option value="Certificate of Solo Parent">Certificate of Solo Parent</option>
                    <option value="Community Tax Certificate (Cedula)">Community Tax Certificate (Cedula)</option>
                    
                    <!-- Other Legal Docs -->
                    <option value="Affidavit of Loss">Affidavit of Loss</option>
                    <option value="Affidavit of Undertaking">Affidavit of Undertaking</option>
                    <option value="Affidavit of Guardianship">Affidavit of Guardianship</option>
                    
                    <!-- Business & Employment -->
                    <option value="Business Permit">Business Permit</option>
                    <option value="Certificate of Employment Verification">Certificate of Employment Verification</option>
                </select>
            </div>

            <!-- Service Date Input -->
            <div>
                <label for="appointment_date" class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                    <i class="fas fa-calendar-alt text-blue-500 mr-2"></i>
                    Preferred Date
                </label>
                <input type="date" id="appointment_date" name="appointment_date" class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required
                    min="<?= date('Y-m-d') ?>">
            </div>

            <!-- Submit Button -->
            <div class="md:col-span-1 flex items-end">
                <button type="submit" class="w-full gov-btn-primary inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-md text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                    <i class="fas fa-paper-plane mr-2"></i>
                    Submit Request
                </button>
            </div>
        </form>
        
        <!-- Security Notice -->
        <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <div class="flex items-center">
                <i class="fas fa-shield-alt text-blue-500 mr-3"></i>
                <div>
                    <p class="text-sm text-blue-800 font-medium">Secure Government Portal</p>
                    <p class="text-xs text-blue-600">Your information is protected with government-grade security measures.</p>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Font Awesome Icons -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>
</body>
</html>