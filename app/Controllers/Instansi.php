<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\InstansiModel;
use App\Models\UserModel;

class Instansi extends BaseController
{
    protected $instansiModel;
    protected $userModel;

    public function __construct()
    {
        $this->instansiModel = new InstansiModel();
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $instansi = $this->instansiModel->orderBy('nama_instansi')->findAll();

        return view('admin/kelola_instansi', ['instansi' => $instansi]);
    }

    public function save()
    {

        $data = [
            'nama_instansi' => $this->request->getPost('nama_instansi'),
            'tingkat' => $this->request->getPost('tingkat'),
        ];

        $this->instansiModel->insert($data);

        return redirect()->back()->with('success', 'Instansi berhasil ditambahkan.');
    }

    public function update($id)
    {
        $data = [
            'nama_instansi'  => $this->request->getPost('nama_instansi'),
            'tingkat' => $this->request->getPost('tingkat'),
        ];

        $this->instansiModel->update($id, $data);

        return redirect()->back()->with('success', 'Instansi berhasil diperbarui.');
    }

    public function delete($id)
    {
        $instansi = $this->instansiModel->find($id);
        if (!$instansi) {
            return redirect()->back()->with('error', 'Instansi tidak ditemukan.');
        }

        // Hapus data
        $this->instansiModel->delete($id);

        return redirect()->back()->with('success', 'Instansi berhasil dihapus.');
    }
}
