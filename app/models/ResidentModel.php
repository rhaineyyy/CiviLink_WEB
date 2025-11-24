<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class ResidentModel extends Model
{
    protected $table = 'residents'; // ⚠️ Change if your table name is different (e.g., tbl_residents)

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Fetch resident by email safely
     */
    public function getResidentByEmail($email)
    {
        $builder = $this->db->table($this->table);

        if (!$builder) {
            throw new Exception("Database table '{$this->table}' not found or connection failed.");
        }

        $result = $builder->where('email', $email)->get();

        if (!$result) {
            return null;
        }

        if (is_object($result) && method_exists($result, 'getRowArray')) {
            return $result->getRowArray();
        }

        if (is_array($result) && !empty($result)) {
            return $result;
        }

        return null;
    }

    /**
     * Fetch resident by ID — used for OTP verification and login session
     */
    public function getResidentById($id)
    {
        $builder = $this->db->table($this->table);

        if (!$builder) {
            throw new Exception("Database table '{$this->table}' not found or connection failed.");
        }

        $result = $builder->where('id', $id)->get();

        if (!$result) {
            return null;
        }

        if (is_object($result) && method_exists($result, 'getRowArray')) {
            return $result->getRowArray();
        }

        if (is_array($result) && !empty($result)) {
            return $result;
        }

        return null;
    }

    /**
     * Create a new resident
     */
    public function createResident($full_name, $email, $password, $contact_number)
    {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $builder = $this->db->table($this->table);

        if (!$builder) {
            throw new Exception("Failed to access table '{$this->table}'. Check DB config.");
        }

        return $builder->insert([
            'full_name'      => $full_name,
            'email'          => $email,
            'password'       => $hashed_password,
            'contact_number' => $contact_number,
        ]);
    }
}
