<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UserModel;
use App\Models\GroupModel;
use App\Models\GroupsUsersModel;
use App\Entities\User;

class DataUser extends BaseController
{
    protected $userModel;
    protected $groupModel;
    protected $groupsUsersModel;

    public function __construct() {
        $this->userModel = new UserModel();
        $this->groupModel = new GroupModel();
        $this->groupsUsersModel = new GroupsUsersModel();
    }

    public function index()
    {
         $db = \Config\Database::connect();

        // Ambil semua user JOIN ke auth_groups_users
        $builder = $db->table('users')
            ->select('users.*, agu.group_id, ag.name as role')
            ->join('auth_groups_users agu', 'agu.user_id = users.id', 'left')
            ->join('auth_groups ag', 'ag.id = agu.group_id', 'left')
            ->where('ag.name', 'user'); // filter hanya role user

        $users = $builder->get()->getResult();

        // Ambil data role juga untuk select option
        $roles = $db->table('auth_groups')->get()->getResultArray();

        return view('admin/kelola_user', [
            'title' => 'Kelola User',
            'users' => $users,
            'roles' => $roles,
        ]);
    }

    public function delete($id)
    {
        $db = \Config\Database::connect();

        // Hapus relasi group-user dulu (auth_groups_users)
        $db->table('auth_groups_users')->where('user_id', $id)->delete();

        // Hapus user
        $db->table('users')->where('id', $id)->delete();

        // Set flash message
        session()->setFlashdata('success', 'User berhasil dihapus.');

        return redirect()->to(base_url('/manage-user'));
    }

    public function indexAdmin()
    {
        $db = \Config\Database::connect();

        // Ambil user yang hanya role "user"
        $builder = $db->table('users')
            ->select('users.*, ag.name as role')
            ->join('auth_groups_users agu', 'agu.user_id = users.id', 'left')
            ->join('auth_groups ag', 'ag.id = agu.group_id', 'left')
            ->where('ag.name', 'admin')
            ->get();

        $users = $builder->getResult();

        // Ambil semua roles (untuk form select role)
        $roles = $db->table('auth_groups')->get()->getResultArray();

        return view('admin/kelola_user_admin', [
            'title' => 'Kelola User',
            'users' => $users,
            'roles' => $roles,
        ]);
    }

    public function deleteAdmin($id)
    {
        $db = \Config\Database::connect();

        // Hapus relasi user-group
        $db->table('auth_groups_users')->where('user_id', $id)->delete();

        // Hapus user
        $db->table('users')->where('id', $id)->delete();

        // Flash message
        session()->setFlashdata('success', 'User berhasil dihapus.');

        return redirect()->to(base_url('manage-user-admin'));
    }

    public function saveAdmin()
    {
        $validation = \Config\Services::validation();

        $rules = [
            'fullname' => 'required',
            'username' => 'required|is_unique[users.username]',
            'email'    => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[8]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $userData = [
            'fullname' => $this->request->getPost('fullname'),
            'username' => $this->request->getPost('username'),
            'email'    => $this->request->getPost('email'),
            'password' => $this->request->getPost('password'),
            'active'   => 1,
        ];

        // Pakai entitas dari App\Entities
        $user = new User($userData);
        $user->activate();

        if (!$this->userModel->save($user)) {
            return redirect()->back()->withInput()->with('errors', $this->userModel->errors());
        }

        $userId = $this->userModel->getInsertID();
        $group  = $this->groupModel->where('name', 'admin')->first();

        if ($group) {
            $this->groupsUsersModel->insert([
                'user_id'  => $userId,
                'group_id' => $group['id'],
            ]);
        }

        session()->setFlashdata('success', 'Admin baru berhasil ditambahkan.');
        return redirect()->to(base_url('manage-user-admin'));
    }

    public function updateAdmin($id)
    {
        $validation = \Config\Services::validation();

        // Validasi
        $rules = [
            'fullname' => 'required',
            'username' => "required|is_unique[users.username,id,$id]",
            'email'    => "required|valid_email|is_unique[users.email,id,$id]",
        ];

        if ($this->request->getPost('password')) {
            $rules['password'] = 'min_length[8]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        // Ambil user lama dulu
        $user = $this->userModel->find($id);
        if (!$user) {
            return redirect()->back()->with('error', 'User tidak ditemukan.');
        }

        // Update data
        $user->fullname = $this->request->getPost('fullname');
        $user->username = $this->request->getPost('username');
        $user->email    = $this->request->getPost('email');
        $user->active   = 1;

        // Kalau ada password baru
        if ($this->request->getPost('password')) {
            $user->password = $this->request->getPost('password');
        }

        if (!$this->userModel->save($user)) {
            return redirect()->back()->withInput()->with('errors', $this->userModel->errors());
        }

        session()->setFlashdata('success', 'Admin berhasil diupdate.');
        return redirect()->to(base_url('manage-user-admin'));
    }


}
