<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class RecordsModel
{
    protected $db;
    protected $records_table = 'records';

    public function __construct()
    {
        $this->db = new PDO('mysql:host=localhost;dbname=civil_registry-1', 'root', '');
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    // Total Records
    public function getTotalRecords()
    {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM {$this->records_table}");
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    }

    // ✅ Fixed: Pull from appointments but alias columns to match your expected field names
    public function getRecentRecords($limit = 5)
    {
        $sql = "
            SELECT 
                id,
                citizen_name,
                appointment_type AS record_type,  -- alias to match dashboard
                appointment_date AS date_of_record
            FROM appointments
            ORDER BY id DESC
            LIMIT $limit
        ";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // CRUD Methods
    public function createRecord($citizen_name, $record_type, $details, $date_of_record)
    {
        $stmt = $this->db->prepare("
            INSERT INTO {$this->records_table} (citizen_name, record_type, details, date_of_record)
            VALUES (:citizen_name, :record_type, :details, :date_of_record)
        ");
        return $stmt->execute([
            ':citizen_name' => $citizen_name,
            ':record_type' => $record_type,
            ':details' => $details,
            ':date_of_record' => $date_of_record
        ]);
    }

    public function getRecordById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->records_table} WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateRecord($id, $citizen_name, $record_type, $details, $date_of_record)
    {
        $stmt = $this->db->prepare("
            UPDATE {$this->records_table} 
            SET citizen_name = :citizen_name, record_type = :record_type, details = :details, date_of_record = :date_of_record
            WHERE id = :id
        ");
        return $stmt->execute([
            ':id' => $id,
            ':citizen_name' => $citizen_name,
            ':record_type' => $record_type,
            ':details' => $details,
            ':date_of_record' => $date_of_record
        ]);
    }

    public function deleteRecord($id)
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->records_table} WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
