<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class StatusModel extends Model
{
    protected $table = 'appointments';
    protected $primary_key = 'id';

    protected $allowedFields = [
        'resident_id',
        'citizen_name',
        'email',
        'contact_number',
        'appointment_type',
        'appointment_date',
        'status',
        'created_at'
    ];

    public function __construct()
    {
        parent::__construct();
    }

    // ✅ Get all appointments by resident ID (LavaLust-compatible)
    public function getByResidentId($residentId)
    {
        // Fetch all appointments
        $allAppointments = $this->all();

        // Filter only for this resident
        $residentAppointments = array_filter($allAppointments, function($appt) use ($residentId) {
            return $appt['resident_id'] == $residentId;
        });

        // Sort descending by ID
        usort($residentAppointments, function($a, $b) {
            return $b['id'] - $a['id'];
        });

        return $residentAppointments;
    }
}
