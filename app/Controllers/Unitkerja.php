<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UnitKerjaModel;
use App\Models\UserModel;

class Unitkerja extends BaseController
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
        $unitKerja = $this->unitKerjaModel->orderBy('unit_kerja')->findAll();

        return view('admin/kelola_unit', ['unitKerja' => $unitKerja]);
    }

    public function save()
    {

        $data = [
            'unit_kerja' => $this->request->getPost('unit_kerja'),
            'safety' => $this->request->getPost('safety'),
            'pembimbing_id' => '1',
            'active' => $this->request->getPost('active'),
        ];

        $this->unitKerjaModel->insert($data);

        return redirect()->back()->with('success', 'Unit Kerja berhasil ditambahkan.');
    }
}
