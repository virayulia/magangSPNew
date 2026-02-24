<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UnitKerjaModel;
use App\Models\UserModel;

class UnitKerjaController extends BaseController
{
    protected $unitModel;
    protected $userModel; 

    public function __construct()
    {
        $this->unitModel = new UnitKerjaModel();
        $this->userModel = new UserModel();
    }
    
    public function index()
    {
        $data['units'] = $this->unitModel
            ->where('deleted_at', null)
            ->findAll();

        return view('admin/unit/index', $data);
    }

    public function save()
    {
        $id = $this->request->getPost('id');

        $data = [
            'kode'      => $this->request->getPost('kode'),
            'nama'      => $this->request->getPost('nama'),
            'is_active' => $this->request->getPost('is_active') ?? 1,
        ];

        if ($id) {
            $this->unitModel->update($id, $data);
            return $this->response->setJSON(['status' => 'updated']);
        } else {
            $this->unitModel->insert($data);
            return $this->response->setJSON(['status' => 'created']);
        }
    }

    public function edit($id)
    {
        $data = $this->unitModel->find($id);
        return $this->response->setJSON($data);
    }

    public function delete($id)
    {
        $this->unitModel->delete($id);
        return $this->response->setJSON(['status' => 'deleted']);
    }
}
