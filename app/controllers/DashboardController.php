<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

if (!defined('APPPATH')) {
    define('APPPATH', __DIR__. '/../'); // points to app/ folder
}

require_once APPPATH . 'models/DashboardModel.php';

// PHPMailer Requirements
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require APPPATH . 'libraries/PHPMailer/Exception.php';
require APPPATH . 'libraries/PHPMailer/PHPMailer.php';
require APPPATH . 'libraries/PHPMailer/SMTP.php';

class DashboardController
{
    private $DashboardModel;

    public function __construct()
    {
        $this->DashboardModel = new DashboardModel();
    }

    // ============================
    // Dashboard Main Page
    // ============================
    public function index()
    {
        $totalAppointments = $this->DashboardModel->getTotalAppointments();
        $totalRecords = $this->DashboardModel->getTotalRecords();

        $recentAppointments = $this->DashboardModel->getRecentAppointments();
        $processingAppointments = $this->DashboardModel->getProcessingAppointments();
        $completedAppointments = $this->DashboardModel->getCompletedAppointments();
        $recentRecords = $this->DashboardModel->getRecentRecords();

        $monthlyData = $this->DashboardModel->getAppointmentsPerMonth();
        $chartLabels = json_encode(array_keys($monthlyData));
        $chartData = json_encode(array_values($monthlyData));

        $data = [
            'totalAppointments' => $totalAppointments,
            'totalRecords' => $totalRecords,
            'recentAppointments' => $recentAppointments,
            'processingAppointments' => $processingAppointments,
            'completedAppointments' => $completedAppointments,
            'recentRecords' => $recentRecords,
            'chartLabels' => $chartLabels,
            'chartData' => $chartData
        ];

        $viewPath = APPPATH . 'views/dashboard/index.php';
        if (file_exists($viewPath)) {
            extract($data);
            require $viewPath;
        } else {
            echo "View file not found: " . $viewPath;
        }
    }

    // ============================
    // CRUD Operations
    // ============================
    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $citizen_name = $_POST['citizen_name'] ?? '';
            $appointment_type = $_POST['appointment_type'] ?? '';
            $appointment_date = $_POST['appointment_date'] ?? '';
            $email = $_POST['email'] ?? null;
            $contact_number = $_POST['contact_number'] ?? null;
            $status = 'Pending';

            if ($this->DashboardModel->createAppointment($citizen_name, $appointment_type, $appointment_date, $email, $contact_number, $status)) {
                header('Location: /admin');
                exit;
            }
            echo "Failed to create appointment.";
        }

        $viewPath = APPPATH . 'views/dashboard/create.php';
        if (file_exists($viewPath)) {
            require $viewPath;
        } else {
            echo "Create view not found: " . $viewPath;
        }
    }

    public function edit($id)
    {
        $appointment = $this->DashboardModel->getAppointmentById($id);
        if (!$appointment) {
            echo "Appointment not found.";
            return;
        }

        $viewPath = APPPATH . 'views/dashboard/edit.php';
        if (file_exists($viewPath)) {
            extract($appointment);
            require $viewPath;
        } else {
            echo "Edit view not found: " . $viewPath;
        }
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            $citizen_name = $_POST['citizen_name'] ?? '';
            $appointment_type = $_POST['appointment_type'] ?? '';
            $appointment_date = $_POST['appointment_date'] ?? '';
            $email = $_POST['email'] ?? null;
            $contact_number = $_POST['contact_number'] ?? null;
            $status = $_POST['status'] ?? 'Pending';

            if ($id && $this->DashboardModel->updateAppointment($id, $citizen_name, $appointment_type, $appointment_date, $email, $contact_number, $status)) {
                header('Location: /admin');
                exit;
            }
            echo "Failed to update appointment.";
        }
    }

    public function delete($id)
    {
        if ($this->DashboardModel->deleteAppointment($id)) {
            header('Location: /admin');
            exit;
        }
        echo "Failed to delete appointment.";
    }

    // ============================
    // STATUS CHANGE ROUTES
    // ============================
    public function approve($id)
{
    // Update status to Processing
    $this->DashboardModel->approveAppointment($id);

    // Start session if not already started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Set a flash message
    $_SESSION['success_message'] = '✅ Appointment approved! It is now in Processing.';

    // Redirect back to dashboard
    header("Location: /admin/dashboard");
    exit;
}


    public function reject($id)
    {
        $this->DashboardModel->rejectAppointment($id);
        header('Location: /admin');
        exit;
    }

    public function complete($id)
    {
        // update status
        $this->DashboardModel->completeAppointment($id);

        // fetch resident email
        $appointment = $this->DashboardModel->getAppointmentById($id);
        if ($appointment && !empty($appointment['email'])) {

            $email = $appointment['email'];
            $name = $appointment['citizen_name'];
            $type = $appointment['appointment_type'];

            // -----------------------
            // SEND EMAIL
            // -----------------------
            $mail = new PHPMailer(true);

            try {
                $mail->isSMTP();
                    $mail->Host       = 'smtp.gmail.com';
                    $mail->SMTPAuth   = true;
                    $mail->Username   = 'jeffersoncarable8@gmail.com';
                    $mail->Password   = 'etvprhojpxroxqnr';
                    $mail->SMTPSecure = 'tls';
                    $mail->Port       = 587;

                $mail->setFrom('YOUR_EMAIL@gmail.com', 'Municipal Office');
                $mail->addAddress($email, $name);

                $mail->isHTML(true);
                $mail->Subject = "Your Appointment is Completed";
                $mail->Body    = "
                    Hello <b>$name</b>,<br><br>
                    Your request for <b>$type</b> is now <b>COMPLETED</b> and ready to be claimed at the Municipal Office.<br><br>
                    You may also choose to pay online using GCash.<br><br>
                    Thank you!
                ";

                $mail->send();

            } catch (Exception $e) {
                error_log("Email sending failed: " . $mail->ErrorInfo);
            }
        }

        header('Location: /admin');
        exit;
    }

    public function logout()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    // destroy session
    session_destroy();

    // redirect to login
    redirect(site_url('/resident/login'));
}


    // ============================
    // FILTERED VIEWS
    // ============================
    public function showPending()
    {
        $pendingAppointments = $this->DashboardModel->getRecentAppointments();
        $data['pendingAppointments'] = $pendingAppointments;

        $viewPath = APPPATH . 'views/dashboard/pendingAppointments.php';
        if (file_exists($viewPath)) {
            extract($data);
            require $viewPath;
        } else {
            echo "Pending appointments view not found: " . $viewPath;
        }
    }

    public function showProcessing()
    {
        $processingAppointments = $this->DashboardModel->getProcessingAppointments();
        $data['processingAppointments'] = $processingAppointments;

        $viewPath = APPPATH . 'views/dashboard/processingAppointments.php';
        if (file_exists($viewPath)) {
            extract($data);
            require $viewPath;
        } else {
            echo "Processing appointments view not found: " . $viewPath;
        }
    }

    public function showRecentRecords()
    {
        $recentRecords = $this->DashboardModel->getRecentRecords();
        $data['recentRecords'] = $recentRecords;

        $viewPath = APPPATH . 'views/dashboard/recentRecords.php';
        if (file_exists($viewPath)) {
            extract($data);
            require $viewPath;
        } else {
            echo "Recent records view not found: " . $viewPath;
        }
    }
}