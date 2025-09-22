<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\JurusanModel;
use App\Models\UserModel;

class Jurusan extends BaseController
{
    protected $jurusanModel;
    protected $userModel;

    public function __construct()
    {
        $this->jurusanModel = new JurusanModel();
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $jurusan = $this->jurusanModel->orderBy('nama_jurusan')->findAll();

        return view('admin/kelola_jurusan', ['jurusan' => $jurusan]);
    }

    public function save()
    {

        $data = [
            'nama_jurusan' => $this->request->getPost('nama_jurusan'),
        ];

        $this->jurusanModel->insert($data);

        return redirect()->back()->with('success', 'Jurusan berhasil ditambahkan.');
    }

    public function update($id)
    {
        $data = [
            'nama_jurusan'  => $this->request->getPost('nama_jurusan'),
        ];

        $this->jurusanModel->update($id, $data);

        return redirect()->back()->with('success', 'Jurusan berhasil diperbarui.');
    }

    public function delete($id)
    {
        $jurusan = $this->jurusanModel->find($id);
        if (!$jurusan) {
            return redirect()->back()->with('error', 'Jurusan tidak ditemukan.');
        }

        // Hapus data
        $this->jurusanModel->delete($id);

        return redirect()->back()->with('success', 'Jurusan berhasil dihapus.');
    }
}
