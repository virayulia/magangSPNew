<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Models\KeywordModel;
use App\Models\UserModel;

class KeywordController extends BaseController
{
    protected $keywordModel;
    protected $userModel;

    public function __construct()
    {
        $this->keywordModel = new KeywordModel();
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $status = $this->request->getGet('status');

        $builder = $this->keywordModel->orderBy('keyword_id');

        if(!empty($status)){
            $builder->where('status', $status);
        }

        $keyword = $builder->findAll();
        return view('admin/kelola_keyword', ['keyword' => $keyword]);
    }

    public function export(){
        $status = $this->request->getGet('status');
        $builder = $this->keywordModel->orderBy('keyword_id');

        if(!empty($status)){
            $builder->where('status', $status);
        }

        $keyword = $builder->get()->getResultArray();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $sheet->setCellValue('A1', 'Nomor keyword');
        $sheet->setCellValue('B1', 'Status');

        // Isi
        $row = 2;
        foreach ($keyword as $d) {
            $sheet->setCellValue('A' . $row, $d['keyword_id']);
            $sheet->setCellValue('B' . $row, $d['status']);
            $row++;
        }

        // Download file
        $filename = 'data_keyword_'. date('Ymd_His') . '.xlsx';
        $writer = new Xlsx($spreadsheet);

        // header untuk download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;

    }

    public function save()
    {

        $data = [
            'keyword_nama' => $this->request->getPost('keyword_nama'),
            'status' => $this->request->getPost('status'),
        ];

        $this->keywordModel->insert($data);

        return redirect()->back()->with('success', 'keyword berhasil ditambahkan.');
    }

    public function update($id)
    {
        $data = [
            'keyword_nama'  => $this->request->getPost('keyword_nama'),
            'status'  => $this->request->getPost('status'),
        ];

        $this->keywordModel->update($id, $data);

        return redirect()->back()->with('success', 'keyword berhasil diperbarui.');
    }

    public function delete($id)
    {
        $keyword = $this->keywordModel->find($id);
        if (!$keyword) {
            return redirect()->back()->with('error', 'keyword tidak ditemukan.');
        }

        // Hapus data
        $this->keywordModel->delete($id);

        return redirect()->back()->with('success', 'keyword berhasil dihapus.');
    }
}
