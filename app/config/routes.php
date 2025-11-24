<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');
/**
 * ------------------------------------------------------------------
 * LavaLust - an opensource lightweight PHP MVC Framework
 * ------------------------------------------------------------------
 *
 * MIT License
 *
 * Copyright (c) 2020 Ronald M. Marasigan
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package LavaLust
 * @author Ronald M. Marasigan <ronald.marasigan@yahoo.com>
 * @since Version 1
 * @link https://github.com/ronmarasigan/LavaLust
 * @license https://opensource.org/licenses/MIT MIT License
 */

/*
| -------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------
| Here is where you can register web routes for your application.
|
|
*/

$router->get('/', 'ResidentController::login');




// ===================== Appointment Routes =====================

// Dashboard and Main Appointment Views
$router->get('/admin', 'DashboardController@index');
$router->get('/admin/dashboard', 'DashboardController@index');
$router->get('/admin/pendingAppointments', 'DashboardController@showPending');
$router->get('/admin/processingAppointments', 'DashboardController@showProcessing');
$router->get('/admin/recentRecords', 'DashboardController@showRecentRecords');
$router->get('/admin/create', 'DashboardController@create'); // Show create form (optional if using modal)
$router->post('/admin/create', 'DashboardController@create'); // Handle appointment creation
$router->get('/admin/edit/(\d+)', 'DashboardController@edit'); // Show edit form
$router->post('/admin/update', 'DashboardController@update'); // Handle update
$router->get('/admin/delete/(\d+)', 'DashboardController@delete'); // Delete appointment
$router->get('admin/logout', 'DashboardController::logout');

// Appointment Actions (Approve, Reject, etc.)
$router->get('/admin/approve/(\d+)', 'DashboardController@approve'); // Approve appointment
$router->get('/admin/reject/(\d+)', 'DashboardController@reject'); // Reject appointment
$router->get('/admin/process/(\d+)', 'DashboardController@markProcessing'); // Mark as Processing
$router->get('/admin/complete/(\d+)', 'DashboardController@complete');
$router->get('/admin/logout', 'DashboardController::logout');



// Filtered Views for Each Status (Optional, for different cards or sections)
$router->get('/admin/appointments/pending', 'DashboardController@showPending'); // Pending list
$router->get('/admin/appointments/processing', 'DashboardController@showProcessing'); // Processing list
$router->get('/admin/appointments/done', 'DashboardController@showCompleted'); // Completed list
$router->get('/resident/adminAccess', 'ResidentController::adminAccess');
$router->post('/resident/adminAccess', 'ResidentController::adminAccess');


// ===================== Record Routes =====================
$router->get('/records', 'RecordsController@index'); // List all records
$router->get('/records/create', 'RecordsController@create'); // Show form (optional if using modal)
$router->post('/records/create', 'RecordsController@create'); // Create record
$router->get('/records/edit/(\d+)', 'RecordsController@edit'); // Edit form
$router->post('/records/update', 'RecordsController@update'); // Update record
$router->post('/records/delete/(\d+)', 'RecordsController@delete');
 // Delete record


// Resident Routes
// -----------------
$router->match('resident/register', 'ResidentController::register', ['GET', 'POST']);
$router->match('resident/login', 'ResidentController::login', ['GET', 'POST']);
$router->match('resident/verifyOtp', 'ResidentController::verifyOtp', ['GET', 'POST']);
$router->get('resident/dashboard', 'ResidentController::dashboard');
$router->get('resident/logout', 'ResidentController::logout');

$router->match('resident/requestAppointment', 'ResidentController::requestAppointment', ['GET', 'POST']);
$router->get('resident/status', 'ResidentStatusController@status');
$router->get('gotoAdmin', 'ResidentController::goToAdmin');

// Payment routes
$router->get('/resident/payment', 'PaymentController@pay');

$router->get('/payment/enter-number', 'PaymentController@enterGcashNumber'); // show GCash number form
$router->post('/payment/enter-number', 'PaymentController@enterGcashNumber'); // handle form submission

$router->post('/payment/process-gcash', 'PaymentController@processGcash');
$router->post('/payment/verify-otp', 'PaymentController@verifyGcashOtp');

$router->get('/payment/success', 'PaymentController@success');
$router->get('/payment/failed', 'PaymentController@failed');