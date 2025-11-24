<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class TestDB extends Controller
{
    public function index()
    {
        $this->call->database();
        $db = $this->db->table('residents')->get();

        if ($db === false) {
            echo "❌ Database query failed!";
        } else {
            echo "✅ Database connected successfully!";
        }
    }
}
