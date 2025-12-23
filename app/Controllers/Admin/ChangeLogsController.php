<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\ChangeLogsModel;

class ChangeLogsController extends BaseController
{
    protected $changeLogsModel;

    public function __construct()
    {
        $this->changeLogsModel = new ChangeLogsModel();
    }

    public function index()
    {
        $data['changeLogs'] = $this->changeLogsModel
            ->orderBy('created_at', 'DESC')
            ->findAll();

        return view('admin/change_log', $data);
    }

    public function save()
    {
        $this->changeLogsModel->insert([
            'version'     => $this->request->getPost('version'),
            'title'       => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'type'        => $this->request->getPost('type'),
            'created_by'  => user_id(), 
        ]);

        return redirect()->back()->with('success', 'Change log berhasil ditambahkan');
    }

    public function update($id)
    {
        $this->changeLogsModel->update($id, [
            'version'     => $this->request->getPost('version'),
            'title'       => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'type'        => $this->request->getPost('type'),
        ]);

        return redirect()->back()->with('success', 'Change log berhasil diperbarui');
    }

    public function delete($id)
    {
        $this->changeLogsModel->delete($id);
        return redirect()->back()->with('success', 'Change log berhasil dihapus');
    }
}
