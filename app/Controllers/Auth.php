<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;

class Auth extends Controller
{
    public function index()
    {
        return view('login');
    }

    public function login()
    {
        $session = session();
        $userModel = new UserModel();

        $username = $this->request->getVar('username');
        $password = $this->request->getVar('password');

        $data = $userModel->getUserByUsername($username);

        if ($data) {
            $pass = $data['password'];
            $verify_pass = password_verify($password, $pass);
            if ($verify_pass) {
                $sessionData = [
                    'id' => $data['id'],
                    'username' => $data['username'],
                    'logged_in' => TRUE
                ];
                $session->set($sessionData);
                echo json_encode(['status' => 'success', 'message' => 'Login successful']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Wrong password']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Username not found']);
        }
    }

    public function logout()
    {
        $session = session();
        $session->destroy();
        return redirect()->to('/auth');
    }

    public function register()
    {
        return view('register');
    }

    public function createUser()
    {
        $userModel = new UserModel();
        $username = $this->request->getPost('username');
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        $hashPassword = password_hash((string)$password, PASSWORD_BCRYPT);

        if ($userModel->getUserByUsername($username)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Username already exists']);
        }

        if ($userModel->getUserByEmail($email)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Email already exists']);
        }

        $data = [
            'username' => $username,
            'email' => $email,
            'password' => $hashPassword
        ];

        if ($userModel->insert($data)) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Registration successful']);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Registration failed']);
        }
    }
}
