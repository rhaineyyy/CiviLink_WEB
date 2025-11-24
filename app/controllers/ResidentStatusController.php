<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class ResidentStatusController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->call->library('session');
        $this->call->database();
        $this->call->model('StatusModel');
    }

    public function status()
    {
        // Check if resident is logged in
        if (!$this->session->has_userdata('resident_id')) {
            redirect('resident/login');
            return;
        }

        $residentId = $this->session->userdata('resident_id');

        // Fetch appointments
        $appointments = $this->StatusModel->getByResidentId($residentId);

        // Call the view with leading slash
        $this->call->view('/resident/status', [
            'residentId'   => $residentId,
            'appointments' => $appointments
        ]);
    }
}
