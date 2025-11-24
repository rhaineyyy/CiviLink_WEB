<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

use App\Models\AppointmentsModel;

class ResidentController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->call->database();
        $this->call->model('ResidentModel');
        $this->call->model('AppointmentsModel');
        $this->call->library('session');
    }

    // ==========================
    // REGISTER
    // ==========================
    public function register()
    {
        if ($this->io->method() === 'post') {
            $full_name        = $this->io->post('full_name');
            $email            = $this->io->post('email');
            $password         = $this->io->post('password');
            $confirm_password = $this->io->post('confirm_password');
            $contact_number   = $this->io->post('contact_number');

            if ($password !== $confirm_password) {
                $this->call->view('resident/register', ['error' => "❌ Passwords do not match!"]);
                return;
            }

            if ($this->ResidentModel->getResidentByEmail($email)) {
                $this->call->view('resident/register', ['error' => "⚠️ Email already exists!"]);
                return;
            }

            $this->ResidentModel->createResident($full_name, $email, $password, $contact_number);

            // ✅ Registration success — redirect like login method
            $this->session->set_userdata('success', "✅ Registered successfully! You can now login.");
            header('Location: ' . $this->getBaseUrl() . '/index.php/resident/login');
            exit;
        }

        // GET request — show register page like login method
        $this->call->view('/resident/register');
    }

    // ==========================
    // LOGIN with OTP
    // ==========================
    public function login()
    {
        if ($this->io->method() === 'post') {
            $email    = $this->io->post('email');
            $password = $this->io->post('password');

            $resident = $this->ResidentModel->getResidentByEmail($email);

            if ($resident && isset($resident['password']) && password_verify($password, $resident['password'])) {
                $otp = rand(100000, 999999);

                $this->session->set_userdata('otp_code', $otp);
                $this->session->set_userdata('resident_id_temp', $resident['id']);
                $this->session->set_userdata('resident_email_temp', $resident['email']);

                // PHPMailer
                require_once dirname(__DIR__) . '/libraries/PHPMailer/PHPMailer.php';
                require_once dirname(__DIR__) . '/libraries/PHPMailer/SMTP.php';
                require_once dirname(__DIR__) . '/libraries/PHPMailer/Exception.php';

                $mail = new PHPMailer\PHPMailer\PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host       = 'smtp.gmail.com';
                    $mail->SMTPAuth   = true;
                    $mail->Username   = 'jeffersoncarable8@gmail.com';
                    $mail->Password   = 'etvprhojpxroxqnr';
                    $mail->SMTPSecure = 'tls';
                    $mail->Port       = 587;

                    $mail->setFrom('no-reply@example.com', 'Resident System');
                    $mail->addAddress($resident['email'], $resident['full_name']);

                    $mail->isHTML(false);
                    $mail->Subject = 'Your Login OTP Code';
                    $mail->Body    = "Hello {$resident['full_name']},\n\nYour OTP code is: {$otp}\n\nEnter this code to complete login.";

                    $mail->send();

                    header('Location: ' . $this->getBaseUrl() . '/index.php/resident/verifyOtp');
                    exit;
                } catch (PHPMailer\PHPMailer\Exception $e) {
                    $this->call->view('resident/login', [
                        'error' => "❌ OTP could not be sent. Mailer Error: {$mail->ErrorInfo}"
                    ]);
                    return;
                }
            }

            $this->call->view('/resident/login', ['error' => "❌ Invalid email or password!"]);
        } else {
            $this->call->view('/resident/login');
        }
    }

    public function verifyOtp()
{
    if ($this->io->method() === 'post') {
        $inputOtp   = trim($this->io->post('otp'));
        $sessionOtp = $this->session->userdata('otp_code');
        $residentId = $this->session->userdata('resident_id_temp');

        // 🧩 (Optional Debug) — uncomment this section if still not redirecting
        /*
        echo "<pre>";
        echo "Input OTP: " . htmlspecialchars($inputOtp) . "\n";
        echo "Session OTP: " . htmlspecialchars($sessionOtp) . "\n";
        echo "Resident ID: " . htmlspecialchars($residentId) . "\n";
        echo "</pre>";
        exit();
        */

        if (!empty($residentId) && $inputOtp == $sessionOtp) {
            $resident = $this->ResidentModel->getResidentById($residentId);

            if ($resident) {
                // ✅ Store logged-in data
                $this->session->set_userdata('resident_id', $resident['id']);
                $this->session->set_userdata('resident_name', $resident['full_name']);
                $this->session->set_userdata('resident_email', $resident['email']); // NEW
    $this->session->set_userdata('resident_contact', $resident['contact_number']);

                // 🧹 Clear temporary OTP session
                $this->session->unset_userdata('otp_code');
                $this->session->unset_userdata('resident_id_temp');
                $this->session->unset_userdata('resident_email_temp');

                // ✅ Redirect to dashboard
                redirect(site_url('resident/dashboard'));
                return;
            }
        }

        // ❌ Invalid OTP — reload with error
        $otpView = APP_DIR . '/views/resident/verifyOtp.php';
        if (file_exists($otpView)) {
            $error = "❌ Invalid or expired OTP code.";
            require $otpView;
        } else {
            echo "<p>⚠️ OTP view not found at {$otpView}</p>";
        }
        return;
    }

    // 🧭 GET request — just show the verify form
    $otpView = APP_DIR . '/views/resident/verifyOtp.php';
    if (file_exists($otpView)) {
        require $otpView;
    } else {
        echo "<p>⚠️ OTP view not found at {$otpView}</p>";
    }
}

    // ==========================
    // LOGOUT
    // ==========================
    public function logout()
    {
        
        redirect(site_url('resident/login'));
        
    }

    // ==========================
    // DASHBOARD
    // ==========================
    public function dashboard()
    {
        if (!$this->session->has_userdata('resident_id')) {
            header('Location: ' . $this->getBaseUrl() . '/index.php/resident/login');
            exit;
        }

        $data = [
            'residentId'    => $this->session->userdata('resident_id'),
            'name'          => $this->session->userdata('resident_name'),
            'email'         => $this->session->userdata('resident_email'),
            'contact_number'=> $this->session->userdata('resident_contact'),
            'appointments'  => $this->AppointmentsModel->getByResidentId($this->session->userdata('resident_id')),
            'active_tab'    => 'dashboard'
        ];

        $this->call->view('/resident/dashboard', $data);
    }

    public function requestAppointment()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $residentId = $_SESSION['resident_id'] ?? 0;

        // Fetch resident details from session
        $residentName    = $_SESSION['resident_name'] ?? '';
        $residentEmail   = $_SESSION['resident_email'] ?? '';
        $residentContact = $_SESSION['resident_contact'] ?? '';

        // Get submitted appointment type and date
        $appointmentType = trim($_POST['appointment_type'] ?? '');
        $appointmentDate = trim($_POST['appointment_date'] ?? '');

        // Validate inputs
        if (empty($appointmentType) || empty($appointmentDate)) {
            $_SESSION['error'] = "⚠️ Please select an appointment type and date.";
            return redirect(site_url('resident/dashboard'));
        }

        $data = [
            'resident_id'      => $residentId,
            'citizen_name'     => $residentName,
            'email'            => $residentEmail,
            'contact_number'   => $residentContact,
            'appointment_type' => $appointmentType, 
            'appointment_date' => $appointmentDate,
            'status'           => 'Pending',
            'created_at'       => date('Y-m-d H:i:s')
        ];

        // Insert into database
        $this->AppointmentsModel->insert($data);

        // ==========================
        // EMAIL NOTIFICATION TO ADMIN
        // ==========================
        require_once dirname(__DIR__) . '/libraries/PHPMailer/PHPMailer.php';
        require_once dirname(__DIR__) . '/libraries/PHPMailer/SMTP.php';
        require_once dirname(__DIR__) . '/libraries/PHPMailer/Exception.php';

        $mail = new PHPMailer\PHPMailer\PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'jeffersoncarable8@gmail.com'; // your Gmail
            $mail->Password   = 'etvprhojpxroxqnr';           // your Gmail App Password
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;

            $mail->setFrom('no-reply@example.com', 'Resident System');
            $mail->addAddress('jeffersoncarable8@gmail.com', 'Admin'); // Admin email

            $mail->isHTML(true);
            $mail->Subject = 'New Appointment Request';
            $mail->Body    = "
                <p>Hello</p>
                <p>A new appointment request has been submitted.</p>
                <ul>
                    <li>Resident: {$residentName}</li>
                    <li>Email: {$residentEmail}</li>
                    <li>Contact Number: {$residentContact}</li>
                    <li>Appointment Type: {$appointmentType}</li>
                    <li>Date: {$appointmentDate}</li>
                </ul>
                <p>Please review the request in the admin dashboard.</p>
            ";

            $mail->send();
        } catch (PHPMailer\PHPMailer\Exception $e) {
            // Log error, appointment is already saved
            error_log("Email to admin failed: {$mail->ErrorInfo}");
        }

        // Add success message
        $_SESSION['success'] = "✅ Your appointment has been requested successfully!";
        return redirect(site_url('resident/dashboard'));
    }

    // Redirect if not POST
    return redirect(site_url('resident/dashboard'));
}

    public function goToAdmin()
    {
        redirect('/admin/dashboard');
    }

    public function adminAccess()
{
    if ($this->io->method() === 'post') {
        $inputPassword = trim($this->io->post('admin_password'));

        // Replace this with your secure password
        $adminPassword = 'SuperSecret123';

        if ($inputPassword === $adminPassword) {
            redirect(site_url('/admin/dashboard'));
            return;
        } else {
            $error = "❌ Incorrect password!";
            $this->call->view('/resident/adminPassword', ['error' => $error]);
            return;
        }
    }

    // GET request — show password form
    $this->call->view('/resident/adminPassword');
}

    // ==========================
    // STATUS
    // ==========================
    public function status()
    {
        if (!$this->session->has_userdata('resident_id')) {
            header('Location: ' . $this->getBaseUrl() . '/index.php/resident/login');
            exit;
        }

        $data = [
            'appointments' => $this->AppointmentsModel->getByResidentId($this->session->userdata('resident_id')),
            'active_tab'   => 'status'
        ];

        $this->call->view('resident/status', $data);
    }

    // ==========================
    // PAYMENT
    // ==========================
    public function payment()
    {
        if (!$this->session->has_userdata('resident_id')) {
            header('Location: ' . $this->getBaseUrl() . '/index.php/resident/login');
            exit;
        }

        $data = [
            'residentId'  => $this->session->userdata('resident_id'),
            'active_tab'  => 'payment'
        ];

        $this->call->view('payment/pay', $data);
    }

    // ==========================
    // HELPER: BASE URL
    // ==========================
    private function getBaseUrl()
    {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        $script = dirname($_SERVER['SCRIPT_NAME']);
        return rtrim($protocol . '://' . $host . $script, '/');
    }
}