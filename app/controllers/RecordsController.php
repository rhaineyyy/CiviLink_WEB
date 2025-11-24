<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

if (!defined('APPPATH')) {
    define('APPPATH', __DIR__ . '/../');
}

require_once APPPATH . 'models/RecordsModel.php';

class RecordsController
{
    private $RecordsModel;

    public function __construct()
    {
        $this->RecordsModel = new RecordsModel();
    }

    public function index()
    {
        $totalRecords = $this->RecordsModel->getTotalRecords();
        $recentRecords = $this->RecordsModel->getRecentRecords();

        $data = [
            'totalRecords' => $totalRecords,
            'recentRecords' => $recentRecords
        ];

        $viewPath = APPPATH . 'views/records/index.php';
        if (file_exists($viewPath)) {
            extract($data);
            require $viewPath;
        } else {
            echo "Records view not found: " . $viewPath;
        }
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $citizen_name = $_POST['citizen_name'] ?? '';
            $record_type = $_POST['record_type'] ?? '';
            $details = $_POST['details'] ?? '';
            $date_of_record = $_POST['date_of_record'] ?? '';

            if ($this->RecordsModel->createRecord($citizen_name, $record_type, $details, $date_of_record)) {
                header('Location: /records');
                exit;
            } else {
                echo "Failed to create record.";
            }
        }

        $viewPath = APPPATH . 'views/records/create.php';
        if (file_exists($viewPath)) {
            require $viewPath;
        } else {
            echo "Create view not found: " . $viewPath;
        }
    }

    public function edit($id)
    {
        $record = $this->RecordsModel->getRecordById($id);
        if (!$record) {
            echo "Record not found.";
            return;
        }

        $viewPath = APPPATH . 'views/records/edit.php';
        if (file_exists($viewPath)) {
            extract($record);
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
            $record_type = $_POST['record_type'] ?? '';
            $details = $_POST['details'] ?? '';
            $date_of_record = $_POST['date_of_record'] ?? '';

            if ($id && $this->RecordsModel->updateRecord($id, $citizen_name, $record_type, $details, $date_of_record)) {
                header('Location: /records');
                exit;
            } else {
                echo "Failed to update record.";
            }
        }
    }

public function delete($id)
{
    session_start();

    // Attempt to delete the record
    if ($this->RecordsModel->deleteRecord($id)) {
        $_SESSION['success'] = "Record deleted successfully.";
    } else {
        $_SESSION['error'] = "Failed to delete record.";
    }

    // Redirect back to admin dashboard
    header('Location: /admin');
    exit;
}



}
