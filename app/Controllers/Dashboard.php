<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Dashboard extends Controller
{
    public function index()
    {
        $session = session();
        if (!$session->get('logged_in')) {
            return redirect()->to('/auth');
        }

        echo "Welcome, " . $session->get('username');
        echo "<br><a href='" . base_url('auth/logout') . "'>Logout</a>";
    }
}
