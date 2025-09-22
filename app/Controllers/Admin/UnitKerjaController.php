<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UnitKerjaModel;
use App\Models\UserModel;

class UnitKerjaController extends BaseController
{
    protected $unitKerjaModel;
    protected $userModel; 

    public function __construct()
    {
        $this->unitKerjaModel = new UnitKerjaModel();
        $this->userModel = new UserModel();
    }
    
    public function index()
    {
        // Ambil periode aktif saat ini
       $unit = $this->unitKerjaModel
                    ->orderBy('active', 'DESC')
                    ->orderBy('unit_kerja', 'ASC')
                    ->findAll();
                
        return view('admin/kelola_unit', ['unit' => $unit]);
    }

    public function save()
    {
        $data = [
            'unit_kerja' => $this->request->getPost('unit_kerja'),
            'nama_pimpinan' => $this->request->getPost('nama_pimpinan'),
            'email_pimpinan' => $this->request->getPost('email_pimpinan'),
            'safety' => $this->request->getPost('safety'),
            'pembimbing_id' => '1',
            'active' => $this->request->getPost('active'),
        ];

        $this->unitKerjaModel->insert($data);

        return redirect()->back()->with('success', 'Unit Kerja berhasil ditambahkan.');
    }


    public function update($id)
    {
        // Ambil periode aktif saat ini
        $data = [
            'unit_kerja'  => $this->request->getPost('unit_kerja'),
            'nama_pimpinan'  => $this->request->getPost('nama_pimpinan'),
            'email_pimpinan'  => $this->request->getPost('email_pimpinan'),
            'safety' => $this->request->getPost('safety'),
            'active' => $this->request->getPost('active'),
        ];

        $this->unitKerjaModel->update($id, $data);
   
        return redirect()->back()->with('success', 'Periode berhasil diperbarui.');
    }
}
