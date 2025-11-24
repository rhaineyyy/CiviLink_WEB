<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class AppointmentsModel extends Model
{
    protected $table = 'appointments';

    // ✅ Allow mass insert properly
    protected $allowedFields = [
        'resident_id',
        'citizen_name',
        'email',
        'contact_number',
        'appointment_type',
        'appointment_date',
        'status',
        'created_at',
        'payment_status' // for payment tracking
    ];

    public function __construct()
    {
        parent::__construct();
    }

    // ✅ Fetch all appointments for a resident
    public function getByResidentId($residentId)
    {
        $builder = $this->db->table($this->table);
        $result = $builder->where('resident_id', $residentId)->get();

        if (is_object($result) && method_exists($result, 'getResultArray')) {
            return $result->getResultArray();
        }
        return [];
    }

    // ✅ Optional: If you want to use create() manually
    public function create($data)
    {
        return $this->db->table($this->table)->insert($data);
    }

    // ✅ Update payment status for an appointment
    // ✅ Update payment status for an appointment with debug
public function updatePaymentStatus($appointmentId, $status)
{
    $builder = $this->db->table($this->table);
    $builder->where('id', $appointmentId);

    $result = $builder->update(['payment_status' => $status]);


    // Fetch again to verify
    $updated = $this->getById($appointmentId);
    return $result;
}

public function getById($appointmentId)
{
    $builder = $this->db->table($this->table);
    $builder->where('id', $appointmentId);

    $query = $builder->get();

    // If it's already a single array row (SOME LL versions do this)
    if (is_array($query)) {
        return $query ?: null;
    }

    // If we got a proper query object
    if (is_object($query)) {
        // Try getRowArray first
        if (method_exists($query, 'getRowArray')) {
            $row = $query->getRowArray();
            return $row ?: null;
        }

        // Fallback to getResultArray (multiple rows)
        if (method_exists($query, 'getResultArray')) {
            $result = $query->getResultArray();
            return $result[0] ?? null;
        }
    }

    // Nothing matched
    return null;
}

   // Fetch ALL completed + unpaid appointments for a resident
public function getCompletedUnpaidByResident($residentId)
{
    // Get all appointments
    $allAppointments = $this->all();

    if (empty($allAppointments)) {
        return [];
    }

    // Filter by resident_id, status Completed, payment_status unpaid
    $unpaidCompleted = array_filter($allAppointments, function($appt) use ($residentId) {
        return $appt['resident_id'] == $residentId 
            && $appt['status'] === 'Completed'
            && $appt['payment_status'] === 'unpaid';
    });

    // Optional: sort by appointment_date ascending or descending
    usort($unpaidCompleted, function($a, $b) {
        return strtotime($b['appointment_date']) - strtotime($a['appointment_date']);
    });

    return $unpaidCompleted; // returns an array of all unpaid completed appointments
}

}