<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class ResidentDocumentsModel extends Model
{
    protected $table = 'appointments'; // your table for document requests

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Fetch all DONE documents for the logged-in resident
     */
    public function getCompletedDocuments($residentId)
    {
        $builder = $this->db->table($this->table);

        $result = $builder
            ->where('resident_id', $residentId)
            ->where('status', 'Completed')            // Only DONE documents
            ->order_by('appointment_date', 'DESC')
            ->get();

        if (is_object($result) && method_exists($result, 'getResultArray')) {
            return $result->getResultArray();
        }

        return [];
    }

    /**
     * Fetch specific DONE document by ID (for viewing details)
     */
    public function getCompletedDocumentById($residentId, $id)
    {
        $builder = $this->db->table($this->table);

        $result = $builder
            ->where('id', $id)
            ->where('resident_id', $residentId)
            ->where('status', 'Completed')
            ->get()
            ->row_array();

        return $result ?: null;
    }
}
