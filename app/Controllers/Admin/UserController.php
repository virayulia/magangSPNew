<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use App\Models\UserModel;
use App\Models\GroupModel;
use App\Models\GroupsUsersModel;
use App\Models\JurusanModel;
use App\Models\InstansiModel;
use App\Models\UnitKerjaModelModel;
use App\Entities\User;
use App\Models\UnitKerjaModel;
use App\Models\UnitUserModel;

class UserController extends BaseController
{
    protected $userModel;
    protected $groupModel;
    protected $groupsUsersModel;
    protected $jurusanModel;
    protected $instansiModel;
    protected $unitKerjaModel;
    protected $unitUserModel;

    public function __construct() {
        $this->userModel = new UserModel();
        $this->groupModel = new GroupModel();
        $this->groupsUsersModel = new GroupsUsersModel();
        $this->jurusanModel = new JurusanModel();
        $this->instansiModel = new InstansiModel();
        $this->unitKerjaModel = new UnitKerjaModel();
        $this->unitUserModel = new UnitUserModel();
    }

    public function index()
    {
         $db = \Config\Database::connect();

        // Ambil semua user JOIN ke auth_groups_users
        $builder = $db->table('users')
            ->select('users.*, agu.group_id, ag.name as role, instansi.nama_instansi, jurusan.nama_jurusan')
            ->join('auth_groups_users agu', 'agu.user_id = users.id', 'left')
            ->join('auth_groups ag', 'ag.id = agu.group_id', 'left')
            ->join('instansi', 'instansi.instansi_id = users.instansi_id')
            ->join('jurusan', 'jurusan.jurusan_id = users.jurusan_id')
            ->where('ag.name', 'user'); // filter hanya role user

        $users = $builder->get()->getResult();

        $jurusan = $this->jurusanModel->findAll();
        foreach ($users as $user){
            if($user->tingkat_pendidikan === 'SMK'){
                $instansi = $this->instansiModel->where('tingkat', 'smk')->findAll();
            } else {
                $instansi = $this->instansiModel->where('tingkat !=', 'smk')->findAll();

            }
        }
        
        // Ambil data role juga untuk select option
        $roles = $db->table('auth_groups')->get()->getResultArray();

        return view('admin/kelola_user', [
            'title' => 'Kelola User',
            'users' => $users,
            'roles' => $roles,
            'jurusan' => $jurusan,
            'instansi' => $instansi,
        ]);
    }

    public function update($id)
    {
        $data = $this->request->getPost();

        $this->userModel->update($id, [
            'email'         => $data['email'],
            'fullname'      => $data['fullname'],
            'no_hp'         => $data['no_hp'],
            'semester'      => $data['semester'],
            'nilai_ipk'     => $data['nilai_ipk'],
            'instansi_id'   => $data['instansi_id'],
            'jurusan_id'    => $data['jurusan_id'],
        ]);

        return redirect()->back()->with('success', 'Data user berhasil diperbarui.');
    }

    public function activate($id)
    {

        $user = $this->userModel->find($id);
        if (!$user) {
            return redirect()->back()->with('error', 'User tidak ditemukan');
        }

        $this->userModel->update($id, [
            'active' => 1,
            'activate_hash' => null,
        ]);

        return redirect()->back()->with('message', 'Akun berhasil diaktifkan');
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

        return redirect()->to(base_url('admin/manage-user'));
    }

    public function indexAdmin()
    {
        $db = \Config\Database::connect();

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
        return redirect()->to(base_url('admin/manage-user-admin'));
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
        return redirect()->to(base_url('admin/manage-user-admin'));
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

        return redirect()->to(base_url('admin/manage-user-admin'));
    }

    public function indexPembimbing()
    {
        $db = \Config\Database::connect();

        $builder = $db->table('users')
            ->select('users.*, ag.name as role, unit_kerja.unit_kerja, unit_kerja.unit_id')
            ->join('auth_groups_users agu', 'agu.user_id = users.id', 'left')
            ->join('auth_groups ag', 'ag.id = agu.group_id', 'left')
            ->join('unit_user', 'unit_user.user_id = users.id')
            ->join('unit_kerja', 'unit_user.unit_id = unit_kerja.unit_id')
            ->where('ag.name', 'pembimbing')
            ->get();

        $users = $builder->getResult();
        $units = $this->unitKerjaModel->findAll();

        // Ambil semua roles (untuk form select role)
        $roles = $db->table('auth_groups')->get()->getResultArray();

        return view('admin/kelola_user_pembimbing', [
            'title' => 'Kelola User',
            'pembimbing' => $users,
            'roles' => $roles,
            'units' => $units,
        ]);
    }
    
    public function savePembimbing()
    {
        $validation = \Config\Services::validation();

        $rules = [
            'fullname' => 'required',
            'username' => 'required|is_unique[users.username]',
            'email'    => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[8]',
            'eselon'   => 'required',
            'unit_id'   => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $userData = [
            'fullname' => $this->request->getPost('fullname'),
            'username' => $this->request->getPost('username'),
            'email'    => $this->request->getPost('email'),
            'password' => $this->request->getPost('password'),
            'eselon' => $this->request->getPost('eselon'),
            'active'   => 1,
        ];

        // Pakai entitas dari App\Entities
        $user = new User($userData);
        $user->activate();

        if (!$this->userModel->save($user)) {
            return redirect()->back()->withInput()->with('errors', $this->userModel->errors());
        }

        $userId = $this->userModel->getInsertID();
        $group  = $this->groupModel->where('name', 'pembimbing')->first();

        if ($group) {
            $this->groupsUsersModel->insert([
                'user_id'  => $userId,
                'group_id' => $group['id'],
            ]);
        }

        $unit_id = $this->request->getPost('unit_id');
        if(!empty($unit_id)){
            $this->unitUserModel->insert([
                'user_id'  => $userId,
                'unit_id'  => $unit_id,
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }

        session()->setFlashdata('success', 'Pembimbing baru berhasil ditambahkan.');
        return redirect()->to(base_url('admin/manage-user-pembimbing'));
    }

    public function importExcel()
    {
        $file = $this->request->getFile('file_excel');

        if (!$file->isValid()) {
            return redirect()->back()->with('error', 'File tidak valid.');
        }

        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $spreadsheet = $reader->load($file->getTempName());
        $sheet = $spreadsheet->getActiveSheet()->toArray();

        // Lewati baris header (mulai baris 2)
        foreach (array_slice($sheet, 1) as $row) {
            [$fullname, $username, $email, $password, $eselon, $unit_id] = $row;

            // Skip jika email kosong
            if (empty($email)) continue;

            // Simpan ke tabel users
            $userData = [
                'fullname' => $fullname,
                'username' => $username,
                'email'    => $email,
                'password' => $password,
                'eselon'   => $eselon,
                'active'   => 1,
            ];

            $user = new \App\Entities\User($userData);
            $user->activate();

            if ($this->userModel->save($user)) {
                $userId = $this->userModel->getInsertID();

                // Masukkan ke grup pembimbing
                $group = $this->groupModel->where('name', 'pembimbing')->first();
                if ($group) {
                    $this->groupsUsersModel->insert([
                        'user_id'  => $userId,
                        'group_id' => $group['id'],
                    ]);
                }

                // Simpan unit kerja
                if (!empty($unit_id)) {
                    $this->unitUserModel->insert([
                        'user_id'    => $userId,
                        'unit_id'    => $unit_id,
                        'created_at' => date('Y-m-d H:i:s')
                    ]);
                }
            }
        }

        return redirect()->to(base_url('admin/manage-user-pembimbing'))
            ->with('success', 'Import pembimbing selesai.');
    }

    public function updatePembimbing($id)
    {
        $validation = \Config\Services::validation();

        // Validasi input
        $rules = [
            'fullname' => 'required',
            'username' => "required|is_unique[users.username,id,{$id}]",
            'email'    => "required|valid_email|is_unique[users.email,id,{$id}]",
            'eselon'   => 'required',
            'unit_id'  => 'required',
        ];

        if ($this->request->getPost('password')) {
            $rules['password'] = 'min_length[8]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        // Ambil user lama
        $user = $this->userModel->find($id);
        if (!$user) {
            return redirect()->back()->with('error', 'User tidak ditemukan.');
        }

        // Update data user
        $user->fullname = $this->request->getPost('fullname');
        $user->username = $this->request->getPost('username');
        $user->email    = $this->request->getPost('email');
        $user->eselon   = $this->request->getPost('eselon');
        $user->active   = 1;

        if ($this->request->getPost('password')) {
            $user->password = $this->request->getPost('password');
        }

        if (!$this->userModel->save($user)) {
            return redirect()->back()->withInput()->with('errors', $this->userModel->errors());
        }

        // Update unit_id di tabel unit_user
        $unit_id = $this->request->getPost('unit_id');

        if (!empty($unit_id)) {
            // Hapus relasi unit lama
            $this->unitUserModel->where('user_id', $id)->delete();

            // Simpan relasi unit baru
            $this->unitUserModel->insert([
                'user_id'   => $id,
                'unit_id'   => $unit_id,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }

        session()->setFlashdata('success', 'Pembimbing berhasil diupdate.');
        return redirect()->to(base_url('admin/manage-user-pembimbing'));
    }

    public function deletePembimbing($id)
    {
        $db = \Config\Database::connect();

        // Hapus relasi user-group
        $db->table('auth_groups_users')->where('user_id', $id)->delete();

        // Hapus user
        $db->table('users')->where('id', $id)->delete();

        // Flash message
        session()->setFlashdata('success', 'Pembimbing berhasil dihapus.');

        return redirect()->to(base_url('admin/manage-user-pembimbing'));
    }


}
