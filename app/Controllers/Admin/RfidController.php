<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\RfidModel;
use App\Models\UserModel;

class RfidController extends BaseController
{
    protected $rfidModel;
    protected $userModel;

    public function __construct()
    {
        $this->rfidModel = new RfidModel();
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $rfid = $this->rfidModel->orderBy('rfid_no')->findAll();

        return view('admin/kelola_rfid', ['rfid' => $rfid]);
    }

    public function save()
    {

        $data = [
            'rfid_no' => $this->request->getPost('rfid_no'),
            'status' => $this->request->getPost('status'),
        ];

        $this->rfidModel->insert($data);

        return redirect()->back()->with('success', 'RFID berhasil ditambahkan.');
    }

    public function update($id)
    {
        $data = [
            'rfid_no'  => $this->request->getPost('rfid_no'),
            'status'  => $this->request->getPost('status'),
        ];

        $this->rfidModel->update($id, $data);

        return redirect()->back()->with('success', 'RFID berhasil diperbarui.');
    }

    public function delete($id)
    {
        $rfid = $this->rfidModel->find($id);
        if (!$rfid) {
            return redirect()->back()->with('error', 'RFID tidak ditemukan.');
        }

        // Hapus data
        $this->rfidModel->delete($id);

        return redirect()->back()->with('success', 'RFID berhasil dihapus.');
    }
}
