<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\PeriodemagangModel;
use App\Models\UserModel;

class LowonganController extends BaseController
{
    protected $periodemagangModel;
    protected $userModel;

    public function __construct()
    {
        $this->periodemagangModel = new PeriodemagangModel();
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $periode = $this->periodemagangModel->findAll();

        return view('admin/kelola_lowongan', ['periode' => $periode]);
    }

    public function periodesave()
    {

        $data = [
            'tanggal_buka' => $this->request->getPost('tanggal_buka'),
            'tanggal_tutup' => $this->request->getPost('tanggal_tutup'),
        ];

        $this->periodemagangModel->insert($data);

        return redirect()->back()->with('success', 'Periode magang berhasil ditambahkan.');
    }

    public function update($id)
    {
        $data = [
            'tanggal_buka'  => $this->request->getPost('tanggal_buka'),
            'tanggal_tutup' => $this->request->getPost('tanggal_tutup'),
        ];

        $this->periodemagangModel->update($id, $data);

        return redirect()->back()->with('success', 'Periode berhasil diperbarui.');
    }

    public function delete($id)
    {
        // Hapus user
        $this->periodemagangModel->where('periode_id', $id)->delete();

        // Set flash message
        session()->setFlashdata('success', 'User berhasil dihapus.');

        return redirect()->back()->with('success', 'Periode berhasil dihapus.');
    }
}
