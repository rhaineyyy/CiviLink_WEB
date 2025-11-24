<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

// Ensure session is started for flash messages
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Government Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
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

        .gov-card {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border: 1px solid var(--color-border);
        }

        .status-badge {
            padding: 0.4rem 0.8rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
        }

        .status-pending {
            background: #fef3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }

        .status-processing {
            background: #cce7ff;
            color: #004085;
            border: 1px solid #b3d7ff;
        }

        .status-completed {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .status-rejected {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .table-header {
            background: linear-gradient(135deg, var(--color-primary), var(--color-primary-dark));
        }

        .table-header th {
            color: white;
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .table-row:hover {
            background-color: #f8fafc;
            transform: translateX(2px);
            transition: all 0.2s ease;
        }

        /* Hide scrollbar for overflow */
        .overflow-x-auto {
            -ms-overflow-style: none;  /* IE and Edge */
            scrollbar-width: none;  /* Firefox */
        }
        
        .overflow-x-auto::-webkit-scrollbar {
            display: none; /* Chrome, Safari and Opera */
        }

        /* Larger overview cards */
        .overview-card {
            transition: all 0.3s ease;
            border-left: 4px solid;
        }

        .overview-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 25px -8px rgba(0, 0, 0, 0.15);
        }

        .overview-card-total {
            border-left-color: #3b82f6;
        }

        .overview-card-records {
            border-left-color: #10b981;
        }

        .overview-card-pending {
            border-left-color: #f59e0b;
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
        <a href="/admin/dashboard" class="flex items-center px-4 py-2 rounded-lg transition duration-150 ease-in-out font-medium 
            <?= (basename($_SERVER['PHP_SELF'], '.php') === 'dashboard') ? 'bg-blue-600 hover:bg-blue-700 shadow-md' : 'hover:bg-gray-700' ?>">
            <i class="fas fa-chart-line w-5 mr-3"></i>
            Dashboard
        </a>
        <a href="/admin/pendingAppointments" class="flex items-center px-4 py-2 rounded-lg transition duration-150 ease-in-out font-medium 
            <?= (!empty($pageSection) && $pageSection === 'pending') ? 'bg-blue-600 hover:bg-blue-700 shadow-md' : 'hover:bg-gray-700' ?>">
            <i class="fas fa-hourglass-start w-5 mr-3"></i>
            Pending Appointments
        </a>
        <a href="/admin/processingAppointments" class="flex items-center px-4 py-2 rounded-lg transition duration-150 ease-in-out font-medium 
            <?= (!empty($pageSection) && $pageSection === 'processing') ? 'bg-blue-600 hover:bg-blue-700 shadow-md' : 'hover:bg-gray-700' ?>">
            <i class="fas fa-sync-alt w-5 mr-3"></i>
            Processing Appointments
        </a>
        <a href="/admin/recentRecords" class="flex items-center px-4 py-2 rounded-lg transition duration-150 ease-in-out font-medium 
            <?= (!empty($pageSection) && $pageSection === 'records') ? 'bg-blue-600 hover:bg-blue-700 shadow-md' : 'hover:bg-gray-700' ?>">
            <i class="fas fa-book w-5 mr-3"></i>
            Recent Records
        </a>
    </nav>
    
    <div class="mt-auto pt-4 border-t border-gray-700">
        <a href="/admin/logout" class="flex items-center px-4 py-2 rounded-lg text-red-400 hover:bg-gray-700 transition duration-150 ease-in-out">
            <i class="fas fa-sign-out-alt w-5 mr-3"></i>
            Logout
        </a>
    </div>
</div>

<!-- Content -->
<div class="flex-1 p-4 md:p-8 lg:p-12">
    <header class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Administration Dashboard</h1>
        <p class="text-gray-500 mt-1">Manage government service requests and records</p>
    </header>

    <!-- Flash Messages -->
    <?php if (!empty($_SESSION['success_message']) || !empty($_SESSION['success'])): 
        $message = $_SESSION['success_message'] ?? $_SESSION['success'];
        unset($_SESSION['success_message'], $_SESSION['success']); ?>
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6 flex items-center justify-between">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-500 mr-3"></i>
                <span class="text-green-800 font-medium"><?= $message; ?></span>
            </div>
            <button type="button" class="text-green-500 hover:text-green-700" onclick="this.parentElement.style.display='none';">
                <i class="fas fa-times"></i>
            </button>
        </div>
    <?php elseif (!empty($_SESSION['error'])): 
        $message = $_SESSION['error'];
        unset($_SESSION['error']); ?>
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6 flex items-center justify-between">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
                <span class="text-red-800 font-medium"><?= $message; ?></span>
            </div>
            <button type="button" class="text-red-500 hover:text-red-700" onclick="this.parentElement.style.display='none';">
                <i class="fas fa-times"></i>
            </button>
        </div>
    <?php endif; ?>

    <!-- Dashboard Overview -->
    <?php if (basename($_SERVER['PHP_SELF'], '.php') === 'dashboard'): ?>
    <div class="mb-8">
        <h2 class="text-xl font-semibold text-gray-800 mb-6 flex items-center">
            <i class="fas fa-chart-bar text-blue-500 mr-3"></i>
            System Overview
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Total Appointments Card -->
            <div class="bg-white gov-card rounded-xl p-8 overview-card overview-card-total">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-lg font-semibold text-gray-500 mb-2">Total Appointments</p>
                        <p class="text-4xl font-bold text-gray-800 mb-4"><?= $totalAppointments ?? 0 ?></p>
                        <div class="flex items-center text-sm text-gray-500">
                            <i class="fas fa-info-circle mr-2"></i>
                            <span>All scheduled appointments</span>
                        </div>
                    </div>
                    <div class="w-20 h-20 bg-blue-100 rounded-2xl flex items-center justify-center ml-6">
                        <i class="fas fa-calendar-check text-blue-600 text-3xl"></i>
                    </div>
                </div>
            </div>
            
            <!-- Total Records Card -->
            <div class="bg-white gov-card rounded-xl p-8 overview-card overview-card-records">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-lg font-semibold text-gray-500 mb-2">Total Records</p>
                        <p class="text-4xl font-bold text-gray-800 mb-4"><?= $totalRecords ?? 0 ?></p>
                        <div class="flex items-center text-sm text-gray-500">
                            <i class="fas fa-info-circle mr-2"></i>
                            <span>Completed service records</span>
                        </div>
                    </div>
                    <div class="w-20 h-20 bg-green-100 rounded-2xl flex items-center justify-center ml-6">
                        <i class="fas fa-database text-green-600 text-3xl"></i>
                    </div>
                </div>
            </div>
            
            <!-- Pending Actions Card -->
            <div class="bg-white gov-card rounded-xl p-8 overview-card overview-card-pending">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-lg font-semibold text-gray-500 mb-2">Pending Actions</p>
                        <p class="text-4xl font-bold text-gray-800 mb-4"><?= $pendingActions ?? 0 ?></p>
                        <div class="flex items-center text-sm text-gray-500">
                            <i class="fas fa-info-circle mr-2"></i>
                            <span>Requiring attention</span>
                        </div>
                    </div>
                    <div class="w-20 h-20 bg-yellow-100 rounded-2xl flex items-center justify-center ml-6">
                        <i class="fas fa-clock text-yellow-600 text-3xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Page Content Sections -->
    <?php if (!empty($pageSection) && $pageSection === 'pending'): ?>
        <div class="bg-white gov-card rounded-xl">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-semibold text-gray-800 flex items-center">
                        <i class="fas fa-hourglass-start text-blue-500 mr-3"></i>
                        Pending Appointments
                    </h2>
                    <div class="text-sm text-gray-500 bg-blue-50 px-3 py-1 rounded-full">
                        <i class="fas fa-info-circle mr-1"></i>
                        Requires immediate attention
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr class="table-header">
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider rounded-tl-xl">#</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">Citizen Name</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">Contact Info</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">Service Type</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">Appointment Date</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider rounded-tr-xl">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            <?php if (!empty($pendingAppointments)): ?>
                                <?php foreach ($pendingAppointments as $idx => $appt): ?>
                                    <tr class="table-row transition duration-150 ease-in-out">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            <?= $idx + 1 ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            <div class="flex items-center">
                                                <i class="fas fa-user text-blue-500 mr-3"></i>
                                                <?= htmlspecialchars($appt['citizen_name']) ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            <div class="space-y-1">
                                                <div class="flex items-center">
                                                    <i class="fas fa-envelope text-gray-400 mr-2 text-xs"></i>
                                                    <?= htmlspecialchars($appt['email']) ?>
                                                </div>
                                                <div class="flex items-center">
                                                    <i class="fas fa-phone text-gray-400 mr-2 text-xs"></i>
                                                    <?= htmlspecialchars($appt['contact_number']) ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            <div class="flex items-center">
                                                <i class="fas fa-file-contract text-blue-500 mr-2"></i>
                                                <?= htmlspecialchars($appt['appointment_type']) ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            <div class="flex items-center">
                                                <i class="fas fa-calendar text-gray-400 mr-2"></i>
                                                <?= htmlspecialchars($appt['appointment_date']) ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <div class="flex gap-2">
                                                <a href="/admin/approve/<?= $appt['id'] ?>" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition duration-150 ease-in-out flex items-center gap-2">
                                                    <i class="fas fa-check"></i>
                                                    Approve
                                                </a>
                                                <a href="/admin/reject/<?= $appt['id'] ?>" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition duration-150 ease-in-out flex items-center gap-2">
                                                    <i class="fas fa-times"></i>
                                                    Reject
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                        <i class="fas fa-inbox text-4xl mb-3 opacity-50"></i>
                                        <p>No pending appointments found.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    <?php elseif (!empty($pageSection) && $pageSection === 'processing'): ?>
        <div class="bg-white gov-card rounded-xl">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-semibold text-gray-800 flex items-center">
                        <i class="fas fa-sync-alt text-blue-500 mr-3"></i>
                        Processing Appointments
                    </h2>
                    <div class="text-sm text-gray-500 bg-blue-50 px-3 py-1 rounded-full">
                        <i class="fas fa-info-circle mr-1"></i>
                        Currently being processed
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr class="table-header">
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider rounded-tl-xl">#</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">Citizen Name</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">Contact Info</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">Service Type</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">Appointment Date</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider rounded-tr-xl">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            <?php if (!empty($processingAppointments)): ?>
                                <?php foreach ($processingAppointments as $idx => $appt): ?>
                                    <tr class="table-row transition duration-150 ease-in-out">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            <?= $idx + 1 ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            <div class="flex items-center">
                                                <i class="fas fa-user text-blue-500 mr-3"></i>
                                                <?= htmlspecialchars($appt['citizen_name']) ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            <div class="space-y-1">
                                                <div class="flex items-center">
                                                    <i class="fas fa-envelope text-gray-400 mr-2 text-xs"></i>
                                                    <?= htmlspecialchars($appt['email']) ?>
                                                </div>
                                                <div class="flex items-center">
                                                    <i class="fas fa-phone text-gray-400 mr-2 text-xs"></i>
                                                    <?= htmlspecialchars($appt['contact_number']) ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            <div class="flex items-center">
                                                <i class="fas fa-file-contract text-blue-500 mr-2"></i>
                                                <?= htmlspecialchars($appt['appointment_type']) ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            <div class="flex items-center">
                                                <i class="fas fa-calendar text-gray-400 mr-2"></i>
                                                <?= htmlspecialchars($appt['appointment_date']) ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <div class="flex gap-2">
                                                <a href="/admin/complete/<?= $appt['id'] ?>" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition duration-150 ease-in-out flex items-center gap-2">
                                                    <i class="fas fa-check-circle"></i>
                                                    Complete
                                                </a>
                                                <a href="/admin/reject/<?= $appt['id'] ?>" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition duration-150 ease-in-out flex items-center gap-2">
                                                    <i class="fas fa-times"></i>
                                                    Reject
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                        <i class="fas fa-inbox text-4xl mb-3 opacity-50"></i>
                                        <p>No processing appointments found.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    <?php elseif (!empty($pageSection) && $pageSection === 'records'): ?>
        <div class="bg-white gov-card rounded-xl">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-semibold text-gray-800 flex items-center">
                        <i class="fas fa-book text-blue-500 mr-3"></i>
                        Recent Records
                    </h2>
                    <div class="text-sm text-gray-500 bg-blue-50 px-3 py-1 rounded-full">
                        <i class="fas fa-info-circle mr-1"></i>
                        Latest government records
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr class="table-header">
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider rounded-tl-xl">#</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">Citizen Name</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">Record Type</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">Date of Record</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider rounded-tr-xl">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            <?php if (!empty($recentRecords)): ?>
                                <?php foreach ($recentRecords as $idx => $rec): ?>
                                    <tr class="table-row transition duration-150 ease-in-out">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            <?= $idx + 1 ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            <div class="flex items-center">
                                                <i class="fas fa-user text-blue-500 mr-3"></i>
                                                <?= htmlspecialchars($rec['citizen_name'] ?? '') ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            <div class="flex items-center">
                                                <i class="fas fa-file-alt text-blue-500 mr-2"></i>
                                                <?= htmlspecialchars($rec['record_type'] ?? '') ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            <div class="flex items-center">
                                                <i class="fas fa-calendar text-gray-400 mr-2"></i>
                                                <?= htmlspecialchars($rec['date_of_record'] ?? '') ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <form method="POST" action="/records/delete/<?= $rec['id'] ?>" 
                                                  onsubmit="return confirm('Are you sure you want to delete this record?');">
                                                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition duration-150 ease-in-out flex items-center gap-2">
                                                    <i class="fas fa-trash"></i>
                                                    Delete
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                        <i class="fas fa-inbox text-4xl mb-3 opacity-50"></i>
                                        <p>No recent records found.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Security Notice -->
    <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
        <div class="flex items-center">
            <i class="fas fa-shield-alt text-blue-500 mr-3"></i>
            <div>
                <p class="text-sm text-blue-800 font-medium">Secure Government Administration Portal</p>
                <p class="text-xs text-blue-600">All administrative actions are logged and monitored for security purposes.</p>
            </div>
        </div>
    </div>
</div>
</body>
</html>