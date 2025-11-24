<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class DashboardModel
{
    protected $db;
    protected $appointments_table = 'appointments';
    protected $records_table = 'records';

    public function __construct()
    {
        // PDO connection
        $this->db = new PDO('mysql:host=localhost;dbname=civil_registry-1', 'root', '');
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    // ============================
    // Dashboard Stats
    // ============================
    public function getTotalAppointments()
    {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM {$this->appointments_table}");
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    }

    public function getTotalRecords()
    {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM {$this->records_table}");
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    }

    public function getRecentAppointments($limit = 5)
    {
        $stmt = $this->db->query("
            SELECT id, citizen_name, email, contact_number, appointment_type, appointment_date, status
            FROM {$this->appointments_table}
            WHERE status = 'Pending'
            ORDER BY id DESC 
            LIMIT $limit
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ✅ PROCESSING LIST
    public function getProcessingAppointments()
    {
        $stmt = $this->db->query("
            SELECT *
            FROM {$this->appointments_table}
            WHERE status = 'Processing'
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ✅ COMPLETED LIST
    public function getCompletedAppointments()
    {
        $stmt = $this->db->query("
            SELECT *
            FROM {$this->appointments_table}
            WHERE status = 'Completed'
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ✅ ✅ NEW: Recent Records for Dashboard
    public function getRecentRecords($limit = 5)
    {
        $stmt = $this->db->query("
            SELECT *
            FROM {$this->records_table}
            ORDER BY id DESC
            LIMIT $limit
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAppointmentsPerMonth()
    {
        $stmt = $this->db->query("
            SELECT MONTH(created_at) as month, COUNT(*) as total
            FROM {$this->appointments_table}
            GROUP BY MONTH(created_at)
            ORDER BY month ASC
        ");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $data = [];
        foreach ($rows as $row) {
            $monthNumber = $row['month'];
            $monthName = date('F', mktime(0, 0, 0, $monthNumber, 10));
            $data[$monthName] = $row['total'];
        }

        return $data;
    }

    // ============================
    // CRUD METHODS
    // ============================
    public function createAppointment($citizen_name, $appointment_type, $appointment_date, $email = null, $contact_number = null, $status = 'Pending')
    {
        $stmt = $this->db->prepare("
            INSERT INTO {$this->appointments_table} 
            (citizen_name, email, contact_number, appointment_type, appointment_date, status) 
            VALUES (:citizen_name, :email, :contact_number, :appointment_type, :appointment_date, :status)
        ");
        return $stmt->execute([
            ':citizen_name' => $citizen_name,
            ':email' => $email,
            ':contact_number' => $contact_number,
            ':appointment_type' => $appointment_type,
            ':appointment_date' => $appointment_date,
            ':status' => $status
        ]);
    }

    public function getAppointmentById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->appointments_table} WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateAppointment($id, $citizen_name, $appointment_type, $appointment_date, $email = null, $contact_number = null, $status = 'Pending')
    {
        $stmt = $this->db->prepare("
            UPDATE {$this->appointments_table} 
            SET citizen_name = :citizen_name,
                email = :email,
                contact_number = :contact_number,
                appointment_type = :appointment_type,
                appointment_date = :appointment_date,
                status = :status
            WHERE id = :id
        ");
        return $stmt->execute([
            ':id' => $id,
            ':citizen_name' => $citizen_name,
            ':email' => $email,
            ':contact_number' => $contact_number,
            ':appointment_type' => $appointment_type,
            ':appointment_date' => $appointment_date,
            ':status' => $status
        ]);
    }

    public function deleteAppointment($id)
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->appointments_table} WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    // ============================
    // STATUS METHODS
    // ============================
    public function approveAppointment($id)
    {
        $stmt = $this->db->prepare("
            UPDATE {$this->appointments_table}
            SET status = 'Processing'
            WHERE id = :id
        ");
        return $stmt->execute([':id' => $id]);
    }

    public function rejectAppointment($id)
    {
        $stmt = $this->db->prepare("
            UPDATE {$this->appointments_table}
            SET status = 'Rejected'
            WHERE id = :id
        ");
        return $stmt->execute([':id' => $id]);
    }

    // =============================================================
    // ✅ COMPLETE → INSERT TO RECORDS + UPDATE STATUS
    // =============================================================
    public function completeAppointment($id)
    {
        $appointment = $this->getAppointmentById($id);

        if (!$appointment) {
            return false;
        }

        // ✅ Generate unique document number
        $document_number = $this->generateDocumentNumber();

        // ✅ Insert into Records
        $stmt = $this->db->prepare("
            INSERT INTO {$this->records_table} 
            (citizen_name, record_type, details, date_of_record, document_number, created_at)
            VALUES 
            (:citizen_name, :record_type, :details, NOW(), :document_number, NOW())
        ");

        $stmt->execute([
            ':citizen_name'    => $appointment['citizen_name'],
            ':record_type'     => $appointment['appointment_type'],
            ':details'         => 'Completed appointment',
            ':document_number' => $document_number
        ]);

        // ✅ Update status to Completed
        $stmt = $this->db->prepare("
            UPDATE {$this->appointments_table}
            SET status = 'Completed'
            WHERE id = :id
        ");
        return $stmt->execute([':id' => $id]);
    }

    // =============================================================
    // ✅ Generate Unique Document Number
    // =============================================================
    private function generateDocumentNumber()
    {
        $year = date("Y");

        $stmt = $this->db->prepare("
            SELECT document_number 
            FROM {$this->records_table}
            WHERE document_number LIKE 'REC-{$year}-%'
            ORDER BY id DESC 
            LIMIT 1
        ");
        $stmt->execute();
        $last = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($last) {
            $lastNumber = intval(substr($last['document_number'], -4));
            $newNumber  = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = "0001";
        }

        return "REC-{$year}-{$newNumber}";
    }
}
